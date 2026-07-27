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
     * Display a listing of scanned Google Drive files.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = GoogleDriveFile::query();

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
        $totalFiles = GoogleDriveFile::count();
        $foldersCount = GoogleDriveFile::distinct('folder_id')->count('folder_id');
        $page_title = 'Google Drive Scanned Files';

        return view('admin.drive_files.index', compact('files', 'totalFiles', 'foldersCount', 'page_title'));
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

                    // Upsert by file_id to prevent duplicates
                    GoogleDriveFile::updateOrCreate(
                        ['file_id' => $fileId],
                        [
                            'folder_id' => $folderId,
                            'name' => $fileName,
                            'mime_type' => $mimeType,
                            'size' => $size,
                            'url' => $directUrl,
                            'status' => 'scanned'
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
