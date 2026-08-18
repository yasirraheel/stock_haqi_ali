<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Audio;
use App\Models\AudioDriveFile;
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
     * Non-blocking stream or redirect for an audio track.
     */
    public function stream($id)
    {
        $id = (int)$id;

        if ($id > 10000) {
            $driveFileId = $id - 10000;
            $driveRecord = AudioDriveFile::find($driveFileId);
            if ($driveRecord && $driveRecord->url) {
                return redirect($driveRecord->url)->header('Access-Control-Allow-Origin', '*');
            }
        }

        $audio = Audio::find($id);
        if ($audio) {
            $audioUrl = $audio->audio_url ?: $audio->audio_path;
            if ($audioUrl) {
                return redirect($audioUrl)->header('Access-Control-Allow-Origin', '*');
            }
        }

        return response()->json(['message' => 'Audio file not found'], 404, [
            'Access-Control-Allow-Origin' => '*'
        ]);
    }
}
