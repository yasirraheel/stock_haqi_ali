<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FilmStockDriveFile;
use App\Models\GoogleDriveApi;
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
     * Display a listing of scanned Film Stock Google Drive files.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = FilmStockDriveFile::whereNotIn('status', ['blocked', 'imported'])->whereNull('film_stock_id');

        if ($request->has('s') && !empty($request->get('s'))) {
            $search = trim($request->get('s'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('file_id', 'LIKE', "%{$search}%")
                  ->orWhere('folder_id', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('folder_id') && !empty($request->get('folder_id'))) {
            $query->where('folder_id', $request->get('folder_id'));
        }

        $files = $query->orderBy('id', 'DESC')->paginate(20)->appends($request->query());
        $totalFiles = FilmStockDriveFile::whereNotIn('status', ['blocked', 'imported'])->whereNull('film_stock_id')->count();
        $importedCount = FilmStockDriveFile::where('status', 'imported')->orWhereNotNull('film_stock_id')->count();
        $blockedCount = FilmStockDriveFile::where('status', 'blocked')->count();
        $foldersCount = FilmStockDriveFile::whereNotIn('status', ['blocked', 'imported'])->whereNull('film_stock_id')->distinct('folder_id')->count('folder_id');
        $page_title = 'Google Drive Scanned Film Stock Files';

        return view('admin.film_stock_drive_files.index', compact('files', 'totalFiles', 'importedCount', 'blockedCount', 'foldersCount', 'page_title'));
    }

    /**
     * Display a listing of blocked Film Stock files.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function blocked(Request $request)
    {
        $query = FilmStockDriveFile::where('status', 'blocked');

        if ($request->has('s') && !empty($request->get('s'))) {
            $search = trim($request->get('s'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('file_id', 'LIKE', "%{$search}%")
                  ->orWhere('folder_id', 'LIKE', "%{$search}%");
            });
        }

        $files = $query->orderBy('id', 'DESC')->paginate(20)->appends($request->query());
        $totalFiles = FilmStockDriveFile::where('status', '!=', 'blocked')->count();
        $blockedCount = FilmStockDriveFile::where('status', 'blocked')->count();
        $foldersCount = FilmStockDriveFile::distinct('folder_id')->count('folder_id');
        $page_title = 'Blocked Film Stock Files';

        return view('admin.film_stock_drive_files.blocked', compact('files', 'totalFiles', 'blockedCount', 'foldersCount', 'page_title'));
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

            Session::flash('flash_message', "Successfully scanned folder ID '{$folderId}'. Synced {$syncedCount} Film Stock video clips ({$newCount} new).");
            return redirect()->route('admin.film-stock-drive-files.index', ['folder_id' => $folderId]);

        } catch (\Exception $e) {
            Session::flash('error_message', 'Error scanning Google Drive folder: ' . $e->getMessage());
            return redirect()->back();
        }
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
