<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FilmStockDriveFile;
use App\Models\GoogleDriveApi;
use App\Models\ScannedFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class FilmStockDriveScannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get or sync tracked scanned folders for Film Stock from database.
     *
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getScannedFolders($type = 'film_stock')
    {
        $existingFolderIds = FilmStockDriveFile::whereNotNull('folder_id')
            ->where('folder_id', '!=', '')
            ->distinct('folder_id')
            ->pluck('folder_id');

        foreach ($existingFolderIds as $fid) {
            $total = FilmStockDriveFile::where('folder_id', $fid)->count();
            $imported = FilmStockDriveFile::where('folder_id', $fid)->where(function($q) {
                $q->where('status', 'imported')->orWhereNotNull('film_stock_id');
            })->count();
            $blocked = FilmStockDriveFile::where('folder_id', $fid)->where('status', 'blocked')->count();
            $pending = FilmStockDriveFile::where('folder_id', $fid)->whereNotIn('status', ['blocked', 'imported'])->whereNull('film_stock_id')->count();
            $latest = FilmStockDriveFile::where('folder_id', $fid)->max('updated_at');

            ScannedFolder::updateOrCreate(
                ['type' => $type, 'folder_id' => $fid],
                [
                    'folder_name' => 'Film Stock Folder ' . substr($fid, 0, 8) . '...',
                    'folder_url' => 'https://drive.google.com/drive/folders/' . $fid,
                    'total_files' => $total,
                    'imported_files' => $imported,
                    'pending_files' => $pending,
                    'blocked_files' => $blocked,
                    'last_scanned_at' => $latest ?? now(),
                ]
            );
        }

        return ScannedFolder::where('type', $type)->orderBy('last_scanned_at', 'DESC')->get();
    }

    /**
     * Display a listing of scanned Film Stock Google Drive files.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'all');
        $activeFolder = $request->get('folder_id', '');

        $baseQuery = FilmStockDriveFile::query();

        if (!empty($activeFolder)) {
            $baseQuery->where('folder_id', $activeFolder);
        }

        if ($request->has('s') && !empty($request->get('s'))) {
            $search = trim($request->get('s'));
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('file_id', 'LIKE', "%{$search}%")
                  ->orWhere('folder_id', 'LIKE', "%{$search}%");
            });
        }

        $totalFiles = (clone $baseQuery)->count();
        $pendingCount = (clone $baseQuery)->whereNotIn('status', ['blocked', 'imported'])->whereNull('film_stock_id')->count();
        $importedCount = (clone $baseQuery)->where(function($q) {
            $q->where('status', 'imported')->orWhereNotNull('film_stock_id');
        })->count();
        $blockedCount = (clone $baseQuery)->where('status', 'blocked')->count();

        $query = clone $baseQuery;
        if ($activeTab === 'pending') {
            $query->whereNotIn('status', ['blocked', 'imported'])->whereNull('film_stock_id');
        } elseif ($activeTab === 'imported') {
            $query->where(function($q) {
                $q->where('status', 'imported')->orWhereNotNull('film_stock_id');
            });
        } elseif ($activeTab === 'blocked') {
            $query->where('status', 'blocked');
        }

        $files = $query->orderBy('id', 'DESC')->paginate(20)->appends($request->query());
        $scannedFolders = $this->getScannedFolders('film_stock');
        $foldersCount = $scannedFolders->count();
        $page_title = 'Google Drive Scanned Film Stock Files';

        return view('admin.film_stock_drive_files.index', compact(
            'files',
            'totalFiles',
            'pendingCount',
            'importedCount',
            'blockedCount',
            'foldersCount',
            'scannedFolders',
            'activeTab',
            'activeFolder',
            'page_title'
        ));
    }

    /**
     * Display a listing of blocked Film Stock files.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function blocked(Request $request)
    {
        return redirect()->route('admin.film-stock-drive-files.index', array_merge($request->query(), ['tab' => 'blocked']));
    }

    /**
     * Scan a Google Drive folder for Film Stock VIDEO files ONLY.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function scanFolder(Request $request)
    {
        $request->validate([
            'folder_input' => 'required|string'
        ]);

        $folderInput = trim($request->input('folder_input'));
        $folderId = $this->extractFolderId($folderInput);

        if (!$folderId) {
            Session::flash('error_message', 'Invalid Google Drive Folder ID or URL provided.');
            return redirect()->back();
        }

        $apiRecord = GoogleDriveApi::inRandomOrder()->first();
        if (!$apiRecord || empty($apiRecord->api_key)) {
            Session::flash('error_message', 'No valid Google Drive API Key found in settings! Please add one in Google Drive API settings.');
            return redirect()->back();
        }

        $apiKey = $apiRecord->api_key;
        $nextPageToken = null;
        $syncedCount = 0;
        $newCount = 0;

        try {
            do {
                $queryParams = [
                    'q' => "'{$folderId}' in parents and trashed = false",
                    'fields' => 'nextPageToken, files(id, name, mimeType, size, webViewLink, webContentLink)',
                    'pageSize' => 1000,
                    'key' => $apiKey
                ];

                if ($nextPageToken) {
                    $queryParams['pageToken'] = $nextPageToken;
                }

                $response = Http::get('https://www.googleapis.com/drive/v3/files', $queryParams);

                if ($response->failed()) {
                    $errorData = $response->json();
                    $msg = $errorData['error']['message'] ?? 'Failed to connect to Google Drive API.';
                    Session::flash('error_message', 'Google Drive API Error: ' . $msg);
                    return redirect()->back();
                }

                $data = $response->json();
                $driveFiles = $data['files'] ?? [];
                $nextPageToken = $data['nextPageToken'] ?? null;

                foreach ($driveFiles as $file) {
                    $fileId = $file['id'] ?? null;
                    if (!$fileId) continue;

                    $fileName = $file['name'] ?? 'Untitled';
                    $mimeType = $file['mimeType'] ?? '';
                    $size = isset($file['size']) ? (int)$file['size'] : 0;
                    $directUrl = "https://drive.google.com/uc?export=download&id={$fileId}";

                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    // STRICT VIDEO FILTERING FOR FILM STOCK:
                    // Accept ONLY video extensions or video/* MIME types
                    $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'm4v', 'flv', 'wmv', '3gp'];
                    $isVideo = in_array($ext, $videoExtensions) || strpos(strtolower($mimeType), 'video') !== false;

                    if (!$isVideo) {
                        continue;
                    }

                    $record = FilmStockDriveFile::where('file_id', $fileId)->first();
                    if (!$record) {
                        $newCount++;
                    }

                    $currentStatus = 'scanned';
                    if ($record) {
                        if ($record->status === 'blocked') {
                            $currentStatus = 'blocked';
                        } elseif ($record->status === 'imported') {
                            $currentStatus = 'imported';
                        }
                    }

                    FilmStockDriveFile::updateOrCreate(
                        ['file_id' => $fileId],
                        [
                            'folder_id' => $folderId,
                            'name' => $fileName,
                            'mime_type' => $mimeType,
                            'size' => $size,
                            'url' => $directUrl,
                            'status' => $currentStatus
                        ]
                    );

                    $syncedCount++;
                }

            } while ($nextPageToken);

            $totalInFolder = FilmStockDriveFile::where('folder_id', $folderId)->count();
            $importedInFolder = FilmStockDriveFile::where('folder_id', $folderId)->where(function($q) {
                $q->where('status', 'imported')->orWhereNotNull('film_stock_id');
            })->count();
            $blockedInFolder = FilmStockDriveFile::where('folder_id', $folderId)->where('status', 'blocked')->count();
            $pendingInFolder = FilmStockDriveFile::where('folder_id', $folderId)->whereNotIn('status', ['blocked', 'imported'])->whereNull('film_stock_id')->count();

            ScannedFolder::updateOrCreate(
                ['type' => 'film_stock', 'folder_id' => $folderId],
                [
                    'folder_name' => 'Film Stock Folder ' . substr($folderId, 0, 8) . '...',
                    'folder_url' => $folderInput,
                    'total_files' => $totalInFolder,
                    'imported_files' => $importedInFolder,
                    'pending_files' => $pendingInFolder,
                    'blocked_files' => $blockedInFolder,
                    'last_scanned_at' => now(),
                ]
            );

            Session::flash('flash_message', "Successfully scanned folder ID '{$folderId}'. Synced {$syncedCount} Film Stock video clips ({$newCount} new).");
            return redirect()->route('admin.film-stock-drive-files.index', ['folder_id' => $folderId, 'tab' => 'all']);

        } catch (\Exception $e) {
            Session::flash('error_message', 'Error scanning Google Drive folder: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Delete a scanned film stock folder from history.
     *
     * @param Request $request
     * @param string $folderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFolder(Request $request, $folderId)
    {
        $removeFiles = (int)$request->input('remove_files', 0);

        if ($removeFiles === 1) {
            FilmStockDriveFile::where('folder_id', $folderId)->whereNull('film_stock_id')->where('status', '!=', 'imported')->delete();
        }

        ScannedFolder::where('type', 'film_stock')->where('folder_id', $folderId)->delete();

        Session::flash('flash_message', "Scanned film stock folder [{$folderId}] removed from history.");
        return redirect()->route('admin.film-stock-drive-files.index');
    }

    /**
     * Block a Film Stock file.
     */
    public function blockFile($id)
    {
        $file = FilmStockDriveFile::findOrFail($id);
        $file->status = 'blocked';
        $file->save();

        Session::flash('flash_message', "Film Stock file '{$file->name}' blocked successfully.");
        return redirect()->back();
    }

    /**
     * Unblock a Film Stock file.
     */
    public function unblockFile($id)
    {
        $file = FilmStockDriveFile::findOrFail($id);
        $file->status = 'scanned';
        $file->save();

        Session::flash('flash_message', "Film Stock file '{$file->name}' unblocked successfully.");
        return redirect()->back();
    }

    /**
     * Delete a Film Stock scanned record.
     */
    public function deleteFile($id)
    {
        $file = FilmStockDriveFile::findOrFail($id);
        $fileName = $file->name;
        $file->delete();

        Session::flash('flash_message', "Film Stock record '{$fileName}' removed.");
        return redirect()->back();
    }

    /**
     * Remove all non-imported scanned Film Stock records.
     */
    public function clearAllScanned()
    {
        $count = FilmStockDriveFile::whereNotIn('status', ['blocked', 'imported'])->whereNull('film_stock_id')->delete();
        Session::flash('flash_message', "Successfully removed {$count} scanned Film Stock records.");
        return redirect()->route('admin.film-stock-drive-files.index');
    }

    /**
     * Helper to extract folder ID from full GD URL or raw string.
     */
    private function extractFolderId($input)
    {
        if (preg_match('/folders\/([a-zA-Z0-9_-]+)/', $input, $matches)) {
            return $matches[1];
        }
        if (preg_match('/id=([a-zA-Z0-9_-]+)/', $input, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^[a-zA-Z0-9_-]{15,}$/', $input)) {
            return $input;
        }
        return null;
    }
}
