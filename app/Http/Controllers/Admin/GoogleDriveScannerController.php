<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoogleDriveApi;
use App\Models\GoogleDriveFile;
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
     * Display a listing of scanned Google Drive files (excluding blocked).
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = GoogleDriveFile::where('status', '!=', 'blocked');

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

        $files = $query->orderBy('id', 'DESC')->paginate(20);
        $totalFiles = GoogleDriveFile::where('status', '!=', 'blocked')->count();
        $blockedCount = GoogleDriveFile::where('status', 'blocked')->count();
        $foldersCount = GoogleDriveFile::where('status', '!=', 'blocked')->distinct('folder_id')->count('folder_id');
        $page_title = 'Google Drive Scanned Files';

        return view('admin.drive_files.index', compact('files', 'totalFiles', 'blockedCount', 'foldersCount', 'page_title'));
    }

    /**
     * Display a listing of blocked Google Drive files.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function blocked(Request $request)
    {
        $query = GoogleDriveFile::where('status', 'blocked');

        if ($request->has('s') && !empty($request->get('s'))) {
            $search = trim($request->get('s'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('file_id', 'LIKE', "%{$search}%")
                  ->orWhere('folder_id', 'LIKE', "%{$search}%");
            });
        }

        $files = $query->orderBy('id', 'DESC')->paginate(20);
        $totalFiles = GoogleDriveFile::where('status', '!=', 'blocked')->count();
        $blockedCount = GoogleDriveFile::where('status', 'blocked')->count();
        $foldersCount = GoogleDriveFile::distinct('folder_id')->count('folder_id');
        $page_title = 'Blocked Files';

        return view('admin.drive_files.blocked', compact('files', 'totalFiles', 'blockedCount', 'foldersCount', 'page_title'));
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

                    // Check if existing record
                    $record = GoogleDriveFile::where('file_id', $fileId)->first();
                    if (!$record) {
                        $newCount++;
                    }

                    // Preserve status if already imported or blocked
                    $currentStatus = 'scanned';
                    if ($record) {
                        if ($record->status === 'blocked') {
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

            Session::flash('flash_message', "Successfully scanned folder [{$folderId}]! Synced {$syncedCount} total files ({$newCount} new added).");
        } catch (\Exception $e) {
            Session::flash('error_message', 'Scan Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.drive-files.index', ['folder_id' => $folderId]);
    }

    /**
     * Import a scanned file as an Effect in the database and queue background conversion.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importFile($id)
    {
        $driveFile = GoogleDriveFile::findOrFail($id);

        if ($driveFile->status === 'blocked') {
            Session::flash('error_message', "File '{$driveFile->name}' is BLOCKED and cannot be imported! Unblock it first if needed.");
            return redirect()->back();
        }

        // Check if already imported
        if ($driveFile->status === 'imported' && $driveFile->effect_id) {
            $existingEffect = \App\Models\Effect::find($driveFile->effect_id);
            if ($existingEffect) {
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
