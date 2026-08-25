<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleDriveApi;
use App\Models\GoogleDriveFile;
use App\Models\ScannedFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class GoogleDriveScannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get or sync tracked scanned folders from database.
     *
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getScannedFolders($type = 'effect')
    {
        // Auto-sync any existing distinct folders from google_drive_files
        $existingFolderIds = GoogleDriveFile::whereNotNull('folder_id')
            ->where('folder_id', '!=', '')
            ->distinct('folder_id')
            ->pluck('folder_id');

        foreach ($existingFolderIds as $fid) {
            $total = GoogleDriveFile::where('folder_id', $fid)->count();
            $imported = GoogleDriveFile::where('folder_id', $fid)->where(function($q) {
                $q->where('status', 'imported')->orWhereNotNull('effect_id');
            })->count();
            $blocked = GoogleDriveFile::where('folder_id', $fid)->where('status', 'blocked')->count();
            $pending = GoogleDriveFile::where('folder_id', $fid)->whereNotIn('status', ['blocked', 'imported'])->whereNull('effect_id')->count();
            $latest = GoogleDriveFile::where('folder_id', $fid)->max('updated_at');

            ScannedFolder::updateOrCreate(
                ['type' => $type, 'folder_id' => $fid],
                [
                    'folder_name' => 'Folder ' . substr($fid, 0, 8) . '...',
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
     * Display a listing of scanned Google Drive files with persistent DB storage and status tabs.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'all'); // 'all', 'pending', 'imported', 'blocked'
        $activeFolder = $request->get('folder_id', '');

        $baseQuery = GoogleDriveFile::where('mime_type', 'NOT LIKE', 'audio/%')
            ->where('name', 'NOT LIKE', '%.mp3')
            ->where('name', 'NOT LIKE', '%.wav')
            ->where('name', 'NOT LIKE', '%.flac')
            ->where('name', 'NOT LIKE', '%.aac')
            ->where('name', 'NOT LIKE', '%.ogg')
            ->where('name', 'NOT LIKE', '%.m4a')
            ->where('name', 'NOT LIKE', '%.wma');

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

        // Calculate counts for badges and tabs
        $totalFiles = (clone $baseQuery)->count();
        $pendingCount = (clone $baseQuery)->whereNotIn('status', ['blocked', 'imported'])->whereNull('effect_id')->count();
        $importedCount = (clone $baseQuery)->where(function($q) {
            $q->where('status', 'imported')->orWhereNotNull('effect_id');
        })->count();
        $blockedCount = (clone $baseQuery)->where('status', 'blocked')->count();

        // Apply tab filtering to query
        $query = clone $baseQuery;
        if ($activeTab === 'pending') {
            $query->whereNotIn('status', ['blocked', 'imported'])->whereNull('effect_id');
        } elseif ($activeTab === 'imported') {
            $query->where(function($q) {
                $q->where('status', 'imported')->orWhereNotNull('effect_id');
            });
        } elseif ($activeTab === 'blocked') {
            $query->where('status', 'blocked');
        }
        // 'all' shows all files in database matching folder & search criteria

        $files = $query->orderBy('id', 'DESC')->paginate(20)->appends($request->query());
        $scannedFolders = $this->getScannedFolders('effect');
        $foldersCount = $scannedFolders->count();
        $page_title = 'Google Drive Scanned Files';

        return view('admin.drive_files.index', compact(
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
     * Display a listing of blocked Google Drive files.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function blocked(Request $request)
    {
        return redirect()->route('admin.drive-files.index', array_merge($request->query(), ['tab' => 'blocked']));
    }

    /**
     * Scan a Google Drive folder and save/update all files in the database.
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

        // Get an API key from database
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

                    // Check if file is a ZIP archive or non-video compressed file
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    // Skip audio files in Effects Scanner
                    $audioExtensions = ['mp3', 'wav', 'flac', 'aac', 'ogg', 'm4a', 'wma', 'aiff'];
                    $isAudio = in_array($ext, $audioExtensions) || strpos(strtolower($mimeType), 'audio') !== false;
                    if ($isAudio) {
                        continue;
                    }

                    $isArchive = in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'iso'])
                        || strpos(strtolower($mimeType), 'zip') !== false
                        || strpos(strtolower($mimeType), 'compressed') !== false;

                    // Check if existing record
                    $record = GoogleDriveFile::where('file_id', $fileId)->first();
                    if (!$record) {
                        $newCount++;
                    }

                    // Preserve status or auto-block ZIP archives
                    $currentStatus = $isArchive ? 'blocked' : 'scanned';
                    if ($record) {
                        if ($record->status === 'blocked' || $isArchive) {
                            $currentStatus = 'blocked';
                        } elseif ($record->status === 'imported') {
                            $currentStatus = 'imported';
                        }
                    }

                    // Upsert by file_id to prevent duplicates
                    GoogleDriveFile::updateOrCreate(
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

            // Increment API call counter
            GoogleDriveApi::where('id', $apiRecord->id)->increment('calls');

            // Update ScannedFolder tracking record
            $totalInFolder = GoogleDriveFile::where('folder_id', $folderId)->count();
            $importedInFolder = GoogleDriveFile::where('folder_id', $folderId)->where(function($q) {
                $q->where('status', 'imported')->orWhereNotNull('effect_id');
            })->count();
            $blockedInFolder = GoogleDriveFile::where('folder_id', $folderId)->where('status', 'blocked')->count();
            $pendingInFolder = GoogleDriveFile::where('folder_id', $folderId)->whereNotIn('status', ['blocked', 'imported'])->whereNull('effect_id')->count();

            ScannedFolder::updateOrCreate(
                ['type' => 'effect', 'folder_id' => $folderId],
                [
                    'folder_name' => 'Folder ' . substr($folderId, 0, 8) . '...',
                    'folder_url' => $folderInput,
                    'total_files' => $totalInFolder,
                    'imported_files' => $importedInFolder,
                    'pending_files' => $pendingInFolder,
                    'blocked_files' => $blockedInFolder,
                    'last_scanned_at' => now(),
                ]
            );

            Session::flash('flash_message', "Successfully scanned folder [{$folderId}]! Synced {$syncedCount} total files ({$newCount} new added). ZIP archives were automatically blocked.");
        } catch (\Exception $e) {
            Session::flash('error_message', 'Scan Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.drive-files.index', ['folder_id' => $folderId, 'tab' => 'all']);
    }

    /**
     * Delete a scanned folder entry from history.
     *
     * @param Request $request
     * @param string $folderId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFolder(Request $request, $folderId)
    {
        $removeFiles = (int)$request->input('remove_files', 0);

        if ($removeFiles === 1) {
            GoogleDriveFile::where('folder_id', $folderId)->whereNull('effect_id')->where('status', '!=', 'imported')->delete();
        }

        ScannedFolder::where('type', 'effect')->where('folder_id', $folderId)->delete();

        Session::flash('flash_message', "Scanned folder [{$folderId}] removed from history.");
        return redirect()->route('admin.drive-files.index');
    }

    /**
     * Import a scanned file as an Effect in the database and queue background conversion.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importFile(Request $request, $id)
    {
        $driveFile = GoogleDriveFile::findOrFail($id);

        // Check if file is a ZIP archive
        $ext = strtolower(pathinfo($driveFile->name, PATHINFO_EXTENSION));
        if (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz', 'bz2']) || strpos(strtolower($driveFile->mime_type), 'zip') !== false) {
            $driveFile->status = 'blocked';
            $driveFile->save();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => "File '{$driveFile->name}' is a ZIP archive and cannot be converted to video! It has been moved to Blocked."], 400);
            }
            Session::flash('error_message', "File '{$driveFile->name}' is a ZIP archive and cannot be converted to video!");
            return redirect()->back();
        }

        if ($driveFile->status === 'blocked') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => "File '{$driveFile->name}' is BLOCKED and cannot be imported!"], 400);
            }
            Session::flash('error_message', "File '{$driveFile->name}' is BLOCKED and cannot be imported! Unblock it first if needed.");
            return redirect()->back();
        }

        // Check if already imported
        if ($driveFile->status === 'imported' && $driveFile->effect_id) {
            $existingEffect = \App\Models\Effect::find($driveFile->effect_id);
            if ($existingEffect) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => "File '{$driveFile->name}' is already imported as Effect #{$existingEffect->id}!",
                        'effect_id' => $existingEffect->id,
                        'effect_url' => route('admin.effects.edit', $existingEffect->id)
                    ]);
                }
                Session::flash('error_message', "File '{$driveFile->name}' is already imported as Effect #{$existingEffect->id}!");
                return redirect()->back();
            }
        }

        // Clean name (strip extension)
        $cleanTitle = pathinfo($driveFile->name, PATHINFO_FILENAME);
        if (empty($cleanTitle)) {
            $cleanTitle = $driveFile->name;
        }

        $directUrl = "https://drive.google.com/uc?export=download&id={$driveFile->file_id}";

        // Create Effect
        $effect = new \App\Models\Effect();
        $effect->title = $cleanTitle;
        $effect->description = $cleanTitle;
        $effect->effect_url = $directUrl;
        $effect->category = 'General';
        $effect->license_price = 0;
        $effect->is_premium = 0;
        $effect->is_active = 1;
        $effect->status = 'pending';
        $effect->save();

        // Dispatch background processing job
        \App\Jobs\ProcessStockEffectJob::dispatch($effect->id);

        // Update GDrive file status
        $driveFile->status = 'imported';
        $driveFile->effect_id = $effect->id;
        $driveFile->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully imported '{$cleanTitle}' as Effect #{$effect->id}!",
                'effect_id' => $effect->id,
                'effect_url' => route('admin.effects.edit', $effect->id)
            ]);
        }

        Session::flash('flash_message', "Successfully imported '{$cleanTitle}' as Effect #{$effect->id}! It is queued for background processing.");
        return redirect()->back();
    }

    /**
     * Mark a scanned file as blocked.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function blockFile($id)
    {
        $driveFile = GoogleDriveFile::findOrFail($id);
        $driveFile->status = 'blocked';
        $driveFile->save();

        Session::flash('flash_message', "File '{$driveFile->name}' has been blocked and moved to Blocked Files.");
        return redirect()->back();
    }

    /**
     * Unblock a blocked file.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unblockFile($id)
    {
        $driveFile = GoogleDriveFile::findOrFail($id);
        $driveFile->status = $driveFile->effect_id ? 'imported' : 'scanned';
        $driveFile->save();

        Session::flash('flash_message', "File '{$driveFile->name}' unblocked successfully.");
        return redirect()->back();
    }

    /**
     * Delete a scanned file record from database.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $file = GoogleDriveFile::findOrFail($id);
        $file->delete();

        Session::flash('flash_message', 'Scanned file record removed successfully.');
        return redirect()->back();
    }

    /**
     * Delete all blocked file records from database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearBlocked()
    {
        $count = GoogleDriveFile::where('status', 'blocked')->count();
        GoogleDriveFile::where('status', 'blocked')->delete();

        Session::flash('flash_message', "Successfully deleted {$count} blocked file records.");
        return redirect()->back();
    }

    /**
     * Helper to extract Google Drive Folder ID from raw input or URL.
     *
     * @param string $input
     * @return string|null
     */
    private function extractFolderId($input)
    {
        if (preg_match('/folders\/([a-zA-Z0-9_-]+)/', $input, $matches)) {
            return $matches[1];
        }

        if (preg_match('/id=([a-zA-Z0-9_-]+)/', $input, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^[a-zA-Z0-9_-]{10,}$/', $input)) {
            return $input;
        }

        return null;
    }
}
