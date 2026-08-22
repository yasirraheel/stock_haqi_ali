<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Audio;
use App\Models\AudioDriveFile;
use App\Models\GoogleDriveApi;
use Illuminate\Http\Request;

class AudioApiController extends Controller
{
    /**
     * Get JSON list of audio tracks for Reel2Reel & public consumers.
     */
    public function index(Request $request)
    {
        try {
            $query = Audio::where('is_active', true);

            if ($request->has('search') && !empty($request->get('search'))) {
                $search = trim($request->get('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('genre') && $request->get('genre') !== 'all') {
                $genre = trim($request->get('genre'));
                $query->where('genre', 'LIKE', "%{$genre}%");
            }

            $audios = $query->orderBy('id', 'DESC')->get();

            $formatted = [];
            foreach ($audios as $audio) {
                $streamUrl = route('api.v1.audio.stream', $audio->id);
                $formatted[] = [
                    'audio_id' => $audio->id,
                    'title' => $audio->title ?: 'Untitled Audio Track',
                    'description' => $audio->description ?? '',
                    'audio_url' => $streamUrl,
                    'stream_url' => $streamUrl,
                    'duration' => $audio->duration ?? '0:00',
                    'file_size' => $audio->file_size ?? 'N/A',
                    'format' => $audio->format ?? 'mp3',
                    'genre' => $audio->genre ?? 'Stock Audio',
                    'is_premium' => false
                ];
            }

            // Append scanned GDrive Audio tracks
            $driveAudios = AudioDriveFile::whereNotIn('status', ['blocked'])->orderBy('id', 'DESC')->get();
            foreach ($driveAudios as $driveAudio) {
                $streamUrl = route('api.v1.audio.stream', 10000 + $driveAudio->id);
                $formatted[] = [
                    'audio_id' => 10000 + $driveAudio->id,
                    'title' => pathinfo($driveAudio->name, PATHINFO_FILENAME),
                    'description' => 'Google Drive Stock Audio',
                    'audio_url' => $streamUrl,
                    'stream_url' => $streamUrl,
                    'duration' => '0:00',
                    'file_size' => $driveAudio->formatted_size,
                    'format' => pathinfo($driveAudio->name, PATHINFO_EXTENSION) ?: 'mp3',
                    'genre' => 'GDrive Stock',
                    'is_premium' => false
                ];
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'AUDIOS_LIST' => $formatted,
                'data' => $formatted
            ], 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Access-Control-Allow-Headers' => 'Content-Type, X-API-KEY, Authorization'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ], 500, [
                'Access-Control-Allow-Origin' => '*'
            ]);
        }
    }

    /**
     * High-performance audio stream using Google Drive API media endpoint & local disk caching.
     */
    /**
     * High-performance audio stream using Google Drive API media endpoint & local disk caching.
     */
    public function stream($id)
    {
        $id = (int)$id;
        $fileId = null;
        $format = 'mp3';
        $driveRecord = null;
        $audio = null;

        if ($id > 10000) {
            $driveFileId = $id - 10000;
            $driveRecord = AudioDriveFile::find($driveFileId);
            if ($driveRecord && $driveRecord->file_id) {
                $fileId = $driveRecord->file_id;
                $format = pathinfo($driveRecord->name, PATHINFO_EXTENSION) ?: 'mp3';
            }
        } else {
            $audio = Audio::find($id);
            if ($audio) {
                $format = $audio->format ?: 'mp3';
                if ($audio->audio_path) {
                    if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $audio->audio_path, $matches)) {
                        $fileId = $matches[1];
                    } elseif (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $audio->audio_path, $matches)) {
                        $fileId = $matches[1];
                    } elseif (!filter_var($audio->audio_path, FILTER_VALIDATE_URL)) {
                        $localPath = public_path('storage/' . $audio->audio_path);
                        if (file_exists($localPath)) {
                            $mime = (strtolower($format) === 'wav') ? 'audio/wav' : 'audio/mpeg';
                            return response()->file($localPath, [
                                'Content-Type' => $mime,
                                'Accept-Ranges' => 'bytes',
                                'Access-Control-Allow-Origin' => '*',
                                'Access-Control-Allow-Methods' => 'GET, OPTIONS'
                            ]);
                        }
                    }
                }
            }
        }

        if (!$fileId) {
            return response()->json(['message' => 'Audio file not found'], 404, [
                'Access-Control-Allow-Origin' => '*'
            ]);
        }

        $format = strtolower($format);
        $mimeType = 'audio/mpeg';
        if ($format === 'wav') {
            $mimeType = 'audio/wav';
        } elseif ($format === 'ogg') {
            $mimeType = 'audio/ogg';
        } elseif ($format === 'aac' || $format === 'm4a') {
            $mimeType = 'audio/mp4';
        }

        // Check if cached locally on disk
        $cacheDir = public_path('audio_previews');
        if (!file_exists($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $possibleCacheFiles = [
            $cacheDir . '/' . $fileId . '.' . $format,
            $cacheDir . '/' . $fileId . '.mp3',
            $cacheDir . '/' . $fileId . '.wav',
            $cacheDir . '/' . $fileId . '.audio'
        ];

        foreach ($possibleCacheFiles as $cached) {
            if (file_exists($cached) && filesize($cached) > 1000) {
                return response()->file($cached, [
                    'Content-Type' => $mimeType,
                    'Accept-Ranges' => 'bytes',
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                    'Cache-Control' => 'public, max-age=31536000'
                ]);
            }
        }

        $saveCachePath = $cacheDir . '/' . $fileId . '.' . ($format ?: 'mp3');

        // Fetch via Google Drive API Key if available
        $apiRecord = GoogleDriveApi::whereNotNull('api_key')->where('api_key', '!=', '')->inRandomOrder()->first();
        if ($apiRecord && !empty($apiRecord->api_key)) {
            $gdriveApiUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key={$apiRecord->api_key}";

            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $gdriveApiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                curl_setopt($ch, CURLOPT_BUFFERSIZE, 128000);

                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Methods: GET, OPTIONS");
                header("Content-Type: {$mimeType}");
                header("Accept-Ranges: bytes");

                $fp = fopen($saveCachePath, 'w+');
                curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $chunk) use ($fp) {
                    if ($fp) fwrite($fp, $chunk);
                    echo $chunk;
                    if (ob_get_level()) ob_flush();
                    flush();
                    return strlen($chunk);
                });

                curl_exec($ch);
                curl_close($ch);
                if ($fp) fclose($fp);
                exit;

            } catch (\Exception $e) {
                // Fallback
            }
        }

        // Direct stream fallback via Google Drive download
        try {
            $downloadUrl = "https://drive.google.com/uc?export=download&id={$fileId}&confirm=t";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $downloadUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: GET, OPTIONS");
            header("Content-Type: {$mimeType}");
            header("Accept-Ranges: bytes");

            $fp = fopen($saveCachePath, 'w+');
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $chunk) use ($fp) {
                if ($fp) fwrite($fp, $chunk);
                echo $chunk;
                if (ob_get_level()) ob_flush();
                flush();
                return strlen($chunk);
            });

            curl_exec($ch);
            curl_close($ch);
            if ($fp) fclose($fp);
            exit;
        } catch (\Exception $e) {
            return redirect("https://drive.google.com/uc?export=open&id={$fileId}")->header('Access-Control-Allow-Origin', '*');
        }
    }
}
