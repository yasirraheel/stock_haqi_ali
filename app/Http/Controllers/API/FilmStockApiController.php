<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FilmStockDriveFile;
use App\Models\GoogleDriveApi;
use Illuminate\Http\Request;

class FilmStockApiController extends Controller
{
    /**
     * Get JSON list of Film Stock video clips for Reel2Reel & public consumers.
     */
    public function index(Request $request)
    {
        try {
            $query = FilmStockDriveFile::whereNotIn('status', ['blocked']);

            if ($request->has('search') && !empty($request->get('search'))) {
                $search = trim($request->get('search'));
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('file_id', 'LIKE', "%{$search}%")
                      ->orWhere('folder_id', 'LIKE', "%{$search}%");
                });
            }

            $driveVideos = $query->orderBy('id', 'DESC')->get();

            $formatted = [];
            foreach ($driveVideos as $driveVideo) {
                $streamUrl = route('api.v1.film-stock.stream', 10000 + $driveVideo->id);
                $ext = pathinfo($driveVideo->name, PATHINFO_EXTENSION) ?: 'mp4';
                $cleanTitle = pathinfo($driveVideo->name, PATHINFO_FILENAME);
                $cleanTitle = str_replace(['_', '-'], ' ', $cleanTitle);
                $cleanTitle = ucwords(trim($cleanTitle));

                $formatted[] = [
                    'id' => 10000 + $driveVideo->id,
                    'video_id' => 10000 + $driveVideo->id,
                    'title' => $cleanTitle,
                    'description' => 'Google Drive Film Stock Video',
                    'video_url' => $streamUrl,
                    'stream_url' => $streamUrl,
                    'preview_url' => $streamUrl,
                    'duration' => '0:00',
                    'file_size' => $driveVideo->formatted_size,
                    'format' => $ext,
                    'category' => 'Film Stock',
                    'is_premium' => false
                ];
            }

            return response()->json([
                'status' => 200,
                'success' => true,
                'FILM_STOCK_LIST' => $formatted,
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
     * Stream Film Stock video clip using Google Drive API media endpoint & local disk caching.
     */
    public function stream($id)
    {
        $id = (int)$id;
        $fileId = null;

        if ($id > 10000) {
            $driveFileId = $id - 10000;
            $driveRecord = FilmStockDriveFile::find($driveFileId);
            if ($driveRecord && $driveRecord->file_id) {
                $fileId = $driveRecord->file_id;
            }
        }

        if (!$fileId) {
            return response()->json(['message' => 'Film Stock video file not found'], 404, [
                'Access-Control-Allow-Origin' => '*'
            ]);
        }

        // Check if cached locally on disk
        $cacheDir = public_path('film_stock_previews');
        if (!file_exists($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $cacheFile = $cacheDir . '/' . $fileId . '.mp4';

        if (file_exists($cacheFile) && filesize($cacheFile) > 1000) {
            return response()->file($cacheFile, [
                'Content-Type' => 'video/mp4',
                'Accept-Ranges' => 'bytes',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'GET, OPTIONS',
                'Cache-Control' => 'public, max-age=31536000'
            ]);
        }

        // Fetch via Google Drive API Key if available
        $apiRecord = GoogleDriveApi::inRandomOrder()->first();
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
                header("Content-Type: video/mp4");
                header("Accept-Ranges: bytes");

                $fp = fopen($cacheFile, 'w+');
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
                // Fallback to direct export=open redirect
            }
        }

        $fallbackUrl = "https://drive.google.com/uc?export=open&id={$fileId}";
        return redirect($fallbackUrl)->header('Access-Control-Allow-Origin', '*');
    }
}
