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
        $query = Audio::query();

        if ($request->has('search') && !empty($request->get('search'))) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('audio_title', 'LIKE', "%{$search}%")
                  ->orWhere('audio_description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('genre') && $request->get('genre') !== 'all') {
            $genre = trim($request->get('genre'));
            $query->where('audio_genre', 'LIKE', "%{$genre}%");
        }

        $audios = $query->orderBy('id', 'DESC')->get();

        $formatted = [];
        foreach ($audios as $audio) {
            $formatted[] = [
                'audio_id' => $audio->id,
                'title' => $audio->audio_title ?: 'Untitled Audio Track',
                'description' => $audio->audio_description ?? '',
                'audio_url' => route('api.v1.audio.stream', $audio->id),
                'stream_url' => route('api.v1.audio.stream', $audio->id),
                'duration' => $audio->audio_duration ?? '0:00',
                'file_size' => $audio->audio_size ?? 'N/A',
                'format' => $audio->audio_format ?? 'mp3',
                'genre' => $audio->audio_genre ?? 'Stock Audio',
                'is_premium' => false
            ];
        }

        // Append scanned GDrive Audio tracks if available
        $driveAudios = AudioDriveFile::whereNotIn('status', ['blocked', 'imported'])->orderBy('id', 'DESC')->get();
        foreach ($driveAudios as $driveAudio) {
            $formatted[] = [
                'audio_id' => 10000 + $driveAudio->id,
                'title' => pathinfo($driveAudio->name, PATHINFO_FILENAME),
                'description' => 'Google Drive Stock Audio',
                'audio_url' => route('api.v1.audio.stream', 10000 + $driveAudio->id),
                'stream_url' => route('api.v1.audio.stream', 10000 + $driveAudio->id),
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
            if ($driveRecord) {
                $targetUrl = $driveRecord->url;
                return redirect($targetUrl)->header('Access-Control-Allow-Origin', '*');
            }
        }

        $audio = Audio::find($id);
        if ($audio && $audio->audio_url) {
            $targetUrl = $audio->audio_url;
            return redirect($targetUrl)->header('Access-Control-Allow-Origin', '*');
        }

        return response()->json(['message' => 'Audio file not found'], 404, [
            'Access-Control-Allow-Origin' => '*'
        ]);
    }
}
