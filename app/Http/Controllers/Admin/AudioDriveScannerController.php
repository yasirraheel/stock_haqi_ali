<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audio;
use App\Models\AudioDriveFile;
use App\Models\GoogleDriveApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AudioDriveScannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of scanned Audio Google Drive files.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = AudioDriveFile::whereNotIn('status', ['blocked', 'imported'])->whereNull('audio_id');

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
        $totalFiles = AudioDriveFile::whereNotIn('status', ['blocked', 'imported'])->whereNull('audio_id')->count();
        $importedCount = AudioDriveFile::where('status', 'imported')->orWhereNotNull('audio_id')->count();
        $blockedCount = AudioDriveFile::where('status', 'blocked')->count();
        $foldersCount = AudioDriveFile::whereNotIn('status', ['blocked', 'imported'])->whereNull('audio_id')->distinct('folder_id')->count('folder_id');
        $page_title = 'Google Drive Scanned Audio Files';

        return view('admin.audio_drive_files.index', compact('files', 'totalFiles', 'importedCount', 'blockedCount', 'foldersCount', 'page_title'));
    }

    /**
     * Display a listing of blocked Audio files.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function blocked(Request $request)
    {
        $query = AudioDriveFile::where('status', 'blocked');

        if ($request->has('s') && !empty($request->get('s'))) {
            $search = trim($request->get('s'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('file_id', 'LIKE', "%{$search}%")
                  ->orWhere('folder_id', 'LIKE', "%{$search}%");
            });
        }

        $files = $query->orderBy('id', 'DESC')->paginate(20)->appends($request->query());
        $totalFiles = AudioDriveFile::where('status', '!=', 'blocked')->count();
        $blockedCount = AudioDriveFile::where('status', 'blocked')->count();
        $foldersCount = AudioDriveFile::distinct('folder_id')->count('folder_id');
        $page_title = 'Blocked Audio Files';

        return view('admin.audio_drive_files.blocked', compact('files', 'totalFiles', 'blockedCount', 'foldersCount', 'page_title'));
    }

    /**
     * Scan a Google Drive folder for Audio files ONLY.
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

                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    // STRICT AUDIO FILTERING:
                    // Accept ONLY audio extensions or audio/* MIME types
                    $audioExtensions = ['mp3', 'wav', 'flac', 'aac', 'ogg', 'm4a', 'wma', 'aiff', 'alac'];
                    $isAudio = in_array($ext, $audioExtensions) || strpos(strtolower($mimeType), 'audio') !== false;

                    // If file is NOT audio (e.g. .mov, .mp4, .zip, image), SKIP IT ENTIRELY
                    if (!$isAudio) {
                        continue;
                    }

                    // Check if existing record
                    $record = AudioDriveFile::where('file_id', $fileId)->first();
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

                    // Upsert into audio_drive_files table
                    AudioDriveFile::updateOrCreate(
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

            Session::flash('flash_message', "Successfully scanned folder ID '{$folderId}'. Synced {$syncedCount} audio tracks ({$newCount} new).");
            return redirect()->route('admin.audio-drive-files.index', ['folder_id' => $folderId]);

        } catch (\Exception $e) {
            Session::flash('error_message', 'Error scanning Google Drive folder: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Import a scanned Audio drive file directly into the Audio library.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function importAudioFile(Request $request, $id)
    {
        $driveFile = AudioDriveFile::findOrFail($id);

        if ($driveFile->audio_id) {
            $audio = Audio::find($driveFile->audio_id);
            if ($audio) {
                return response()->json([
                    'success' => true,
                    'message' => 'Audio file already imported.',
                    'audio_id' => $audio->id,
                    'audio_url' => route('admin.audio.edit', $audio->id)
                ]);
            }
        }

        // Clean track title (strip extension)
        $rawTitle = pathinfo($driveFile->name, PATHINFO_FILENAME);
        $cleanTitle = str_replace(['_', '-'], ' ', $rawTitle);
        $cleanTitle = ucwords(trim($cleanTitle));

        // Create new Audio record
        $audio = new Audio();
        $audio->title = $cleanTitle;
        $audio->audio_path = $driveFile->url;
        $audio->duration = '0:00';
        $audio->file_size = $driveFile->formatted_size;
        $audio->format = pathinfo($driveFile->name, PATHINFO_EXTENSION) ?: 'mp3';
        $audio->genre = 'GDrive Stock';
        $audio->is_active = true;
        $audio->save();

        // Update audio_drive_files table link
        $driveFile->audio_id = $audio->id;
        $driveFile->status = 'imported';
        $driveFile->save();

        return response()->json([
            'success' => true,
            'message' => "Audio track '{$cleanTitle}' imported successfully!",
            'audio_id' => $audio->id,
            'audio_url' => route('admin.audio.edit', $audio->id)
        ]);
    }

    /**
     * Stream non-blocking audio preview for inline HTML5 player.
     *
     * @param string $file_id
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function streamAudioPreview($file_id)
    {
        $cachedFile = public_path('audio_previews/' . $file_id . '.mp3');
        if (file_exists($cachedFile) && filesize($cachedFile) > 1000) {
            return response()->file($cachedFile, [
                'Content-Type' => 'audio/mpeg',
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public, max-age=31536000'
            ]);
        }

        return redirect("https://drive.google.com/uc?export=open&id={$file_id}");
    }

    /**
     * Block an Audio file from import.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function block($id)
    {
        $file = AudioDriveFile::findOrFail($id);
        $file->status = 'blocked';
        $file->save();

        Session::flash('flash_message', "Audio file '{$file->name}' blocked.");
        return redirect()->back();
    }

    /**
     * Unblock an Audio file.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unblock($id)
    {
        $file = AudioDriveFile::findOrFail($id);
        $file->status = 'scanned';
        $file->save();

        Session::flash('flash_message', "Audio file '{$file->name}' unblocked.");
        return redirect()->back();
    }

    /**
     * Remove an Audio drive file record.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $file = AudioDriveFile::findOrFail($id);
        $file->delete();

        Session::flash('flash_message', 'Audio record removed.');
        return redirect()->back();
    }

    /**
     * Remove all scanned (non-imported) Audio drive file records.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearAllScanned()
    {
        $count = AudioDriveFile::whereNotIn('status', ['imported'])->whereNull('audio_id')->delete();

        Session::flash('flash_message', "Successfully removed {$count} scanned audio records.");
        return redirect()->route('admin.audio-drive-files.index');
    }

    /**
     * Helper to extract Google Drive Folder ID from input string or URL.
     *
     * @param string $input
     * @return string|null
     */
    private function extractFolderId($input)
    {
        $input = trim($input);
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
