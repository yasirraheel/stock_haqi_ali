<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AudioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Audio::query();

        // Search functionality
        if ($request->has('s') && !empty($request->s)) {
            $search = $request->s;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $audios = $query->orderBy('created_at', 'desc')->paginate(20);
        $page_title = 'Audio Management';
        return view('admin.audio.index', compact('audios', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $page_title = 'Add New Audio';
        return view('admin.audio.create', compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audio_file' => 'required|file|mimes:mp3,wav,ogg,aac,flac|max:50000', // 50MB max
            'license_price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|string',
            'file_size' => 'nullable|string',
            'format' => 'nullable|string',
            'bitrate' => 'nullable|integer|min:0',
            'sample_rate' => 'nullable|integer|min:0',
            'genre' => 'nullable|string',
            'mood' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $audio = new Audio();
        $audio->fill([
            'title' => $request->title,
            'description' => $request->description,
            'license_price' => $request->license_price ? round((float)$request->license_price, 2) : null,
            'duration' => $request->duration,
            'file_size' => $request->file_size,
            'format' => $request->format,
            'bitrate' => $request->bitrate,
            'sample_rate' => $request->sample_rate,
            'genre' => $request->genre,
            'mood' => $request->mood,
            'tags' => $request->tags,
            'is_active' => $request->has('is_active')
        ]);

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            $file = $request->file('audio_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('audio', $filename, 'public');
            $audio->setAttribute('audio_path', $path);
        }

        $audio->save();

        return redirect()->route('admin.audio.index')
            ->with('success', 'Audio created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $audio = Audio::findOrFail($id);
        $page_title = 'Audio Details';
        return view('admin.audio.show', compact('audio', 'page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $audio = Audio::findOrFail($id);
        $page_title = 'Edit Audio';
        return view('admin.audio.edit', compact('audio', 'page_title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $audio = Audio::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audio_file' => 'nullable|file|mimes:mp3,wav,ogg,aac,flac|max:50000',
            'license_price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|string',
            'file_size' => 'nullable|string',
            'format' => 'nullable|string',
            'bitrate' => 'nullable|integer|min:0',
            'sample_rate' => 'nullable|integer|min:0',
            'genre' => 'nullable|string',
            'mood' => 'nullable|string',
            'tags' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $audio->fill([
            'title' => $request->title,
            'description' => $request->description,
            'license_price' => $request->license_price ? round((float)$request->license_price, 2) : null,
            'duration' => $request->duration,
            'file_size' => $request->file_size,
            'format' => $request->format,
            'bitrate' => $request->bitrate,
            'sample_rate' => $request->sample_rate,
            'genre' => $request->genre,
            'mood' => $request->mood,
            'tags' => $request->tags,
            'is_active' => $request->has('is_active')
        ]);

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            // Delete old file
            if ($audio->getAttribute('audio_path') && Storage::disk('public')->exists($audio->getAttribute('audio_path'))) {
                Storage::disk('public')->delete($audio->getAttribute('audio_path'));
            }

            $file = $request->file('audio_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('audio', $filename, 'public');
            $audio->setAttribute('audio_path', $path);
        }

        $audio->save();

        return redirect()->route('admin.audio.index')
            ->with('success', 'Audio updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $audio = Audio::findOrFail($id);

        // Delete audio file
        if ($audio->getAttribute('audio_path') && Storage::disk('public')->exists($audio->getAttribute('audio_path'))) {
            Storage::disk('public')->delete($audio->getAttribute('audio_path'));
        }

        $audio->delete();

        return redirect()->route('admin.audio.index')
            ->with('success', 'Audio deleted successfully.');
    }


    /**
     * Extract audio metadata from uploaded file
     */
    public function getAudioMetadata(Request $request)
    {
        if (!$request->hasFile('audio')) {
            return response()->json(['error' => 'No audio file provided'], 400);
        }

        try {
            $file = $request->file('audio');
            $metadata = $this->extractAudioMetadata($file);

            return response()->json(['success' => true, 'metadata' => $metadata]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Extract audio metadata from file
     */
    private function extractAudioMetadata($file)
    {
        $metadata = [];

        try {
            // Basic file information
            $metadata['file_name'] = $file->getClientOriginalName();
            $metadata['file_size'] = $this->formatFileSize($file->getSize());
            $metadata['file_type'] = $file->getClientOriginalExtension();
            $metadata['mime_type'] = $file->getMimeType();

            // Get audio file path for analysis
            $tempPath = $file->getPathname();

            // Try multiple methods to extract audio metadata
            $this->extractAudioInfo($tempPath, $metadata);

            // Auto-suggest title from filename
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $metadata['suggested_title'] = $this->formatTitle($filename);

            // Auto-suggest genre based on filename patterns
            $metadata['suggested_genre'] = $this->suggestGenre($filename);

            // Auto-suggest mood based on filename patterns
            $metadata['suggested_mood'] = $this->suggestMood($filename);

        } catch (\Exception $e) {
            \Log::error('Audio metadata extraction error: ' . $e->getMessage());
        }

        return $metadata;
    }

    /**
     * Extract audio information using multiple methods
     */
    private function extractAudioInfo($filePath, &$metadata)
    {
        // Always set some basic metadata first
        $this->setBasicAudioDefaults($metadata);

        // Method 1: Try ffprobe (most reliable)
        if (function_exists('shell_exec')) {
            $ffprobeCommand = "ffprobe -v quiet -print_format json -show_format -show_streams \"$filePath\" 2>/dev/null";
            $output = shell_exec($ffprobeCommand);

            if ($output && !empty(trim($output))) {
                $data = json_decode($output, true);
                if ($data && isset($data['format'])) {
                    if (isset($data['format']['duration'])) {
                        $metadata['duration'] = $this->formatDuration($data['format']['duration']);
                    }
                    if (isset($data['format']['bit_rate'])) {
                        $metadata['bitrate'] = round($data['format']['bit_rate'] / 1000);
                    }
                }
                if ($data && isset($data['streams'][0])) {
                    $stream = $data['streams'][0];
                    if (isset($stream['sample_rate'])) {
                        $metadata['sample_rate'] = $stream['sample_rate'];
                    }
                    if (isset($stream['channels'])) {
                        $metadata['channels'] = $stream['channels'];
                    }
                    if (isset($stream['codec_name'])) {
                        $metadata['codec'] = $stream['codec_name'];
                    }
                }
                return; // If ffprobe worked, we're done
            }
        }

        // Method 2: Try mediainfo
        if (function_exists('shell_exec')) {
            $mediainfoCommand = "mediainfo --Output=JSON \"$filePath\" 2>/dev/null";
            $output = shell_exec($mediainfoCommand);

            if ($output && !empty(trim($output))) {
                $data = json_decode($output, true);
                if ($data && isset($data['media']['track'])) {
                    foreach ($data['media']['track'] as $track) {
                        if (isset($track['@type']) && $track['@type'] === 'Audio') {
                            if (isset($track['Duration'])) {
                                $metadata['duration'] = $this->formatDurationFromString($track['Duration']);
                            }
                            if (isset($track['BitRate'])) {
                                $metadata['bitrate'] = $this->extractBitrate($track['BitRate']);
                            }
                            if (isset($track['SamplingRate'])) {
                                $metadata['sample_rate'] = $this->extractSampleRate($track['SamplingRate']);
                            }
                            if (isset($track['Channels'])) {
                                $metadata['channels'] = $track['Channels'];
                            }
                        }
                    }
                    return; // If mediainfo worked, we're done
                }
            }
        }

        // Method 3: Try getID3 if available
        if (class_exists('getID3')) {
            try {
                $getID3 = new \getID3;
                $fileInfo = $getID3->analyze($filePath);

                if (isset($fileInfo['playtime_string'])) {
                    $metadata['duration'] = $fileInfo['playtime_string'];
                }

                if (isset($fileInfo['audio']['bitrate'])) {
                    $metadata['bitrate'] = $fileInfo['audio']['bitrate'];
                }

                if (isset($fileInfo['audio']['sample_rate'])) {
                    $metadata['sample_rate'] = $fileInfo['audio']['sample_rate'];
                }

                if (isset($fileInfo['audio']['channels'])) {
                    $metadata['channels'] = $fileInfo['audio']['channels'];
                }

                if (isset($fileInfo['audio']['codec'])) {
                    $metadata['codec'] = $fileInfo['audio']['codec'];
                }
                return; // If getID3 worked, we're done
            } catch (\Exception $e) {
                // Continue to next method
            }
        }

        // Method 4: Enhanced file analysis with better defaults
        $this->analyzeAudioFileEnhanced($filePath, $metadata);
    }

    /**
     * Format file size in human readable format
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Format duration from seconds to readable format
     */
    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%d:%02d', $minutes, $seconds);
        }
    }

    /**
     * Format title from filename
     */
    private function formatTitle($filename)
    {
        // Remove common separators and replace with spaces
        $title = str_replace(['_', '-', '.'], ' ', $filename);
        // Capitalize first letter of each word
        $title = ucwords(strtolower($title));
        return trim($title);
    }

    /**
     * Suggest genre based on filename patterns
     */
    private function suggestGenre($filename)
    {
        $filename = strtolower($filename);

        $genrePatterns = [
            'Electronic' => ['electronic', 'edm', 'techno', 'house', 'trance', 'dubstep', 'drum', 'bass', 'synth'],
            'Rock' => ['rock', 'metal', 'punk', 'alternative', 'grunge', 'indie', 'hardcore'],
            'Jazz' => ['jazz', 'blues', 'soul', 'funk', 'swing', 'bebop'],
            'Classical' => ['classical', 'orchestra', 'symphony', 'piano', 'violin', 'cello', 'chamber'],
            'Pop' => ['pop', 'mainstream', 'radio', 'hit', 'chart'],
            'Ambient' => ['ambient', 'chill', 'relaxing', 'meditation', 'zen', 'peaceful'],
            'Corporate' => ['corporate', 'business', 'professional', 'office', 'meeting', 'presentation'],
            'Cinematic' => ['cinematic', 'film', 'movie', 'soundtrack', 'score', 'epic', 'dramatic'],
            'Hip Hop' => ['hip', 'hop', 'rap', 'urban', 'street'],
            'Country' => ['country', 'folk', 'bluegrass', 'western'],
            'Reggae' => ['reggae', 'ska', 'dub', 'jamaican'],
            'World' => ['world', 'ethnic', 'traditional', 'cultural']
        ];

        foreach ($genrePatterns as $genre => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($filename, $pattern) !== false) {
                    return $genre;
                }
            }
        }

        return null;
    }

    /**
     * Suggest mood based on filename patterns
     */
    private function suggestMood($filename)
    {
        $filename = strtolower($filename);

        $moodPatterns = [
            'Upbeat' => ['upbeat', 'energetic', 'happy', 'positive', 'cheerful', 'joyful', 'bright', 'lively', 'vibrant'],
            'Calm' => ['calm', 'peaceful', 'relaxing', 'soft', 'gentle', 'serene', 'tranquil', 'meditation', 'zen'],
            'Dramatic' => ['dramatic', 'intense', 'powerful', 'epic', 'heroic', 'grand', 'majestic', 'stirring'],
            'Melancholic' => ['sad', 'melancholic', 'emotional', 'nostalgic', 'melancholy', 'sorrowful', 'mournful'],
            'Mysterious' => ['mysterious', 'dark', 'moody', 'atmospheric', 'eerie', 'haunting', 'ominous', 'sinister'],
            'Romantic' => ['romantic', 'love', 'tender', 'sweet', 'passionate', 'intimate', 'affectionate'],
            'Aggressive' => ['aggressive', 'angry', 'fierce', 'intense', 'violent', 'harsh', 'brutal'],
            'Playful' => ['playful', 'fun', 'quirky', 'whimsical', 'silly', 'humorous', 'lighthearted'],
            'Triumphant' => ['triumphant', 'victory', 'success', 'achievement', 'celebration', 'victorious'],
            'Melancholy' => ['melancholy', 'bittersweet', 'wistful', 'pensive', 'contemplative']
        ];

        foreach ($moodPatterns as $mood => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($filename, $pattern) !== false) {
                    return $mood;
                }
            }
        }

        return null;
    }

    /**
     * Format duration from string (e.g., "3:45" or "3 min 45 s")
     */
    private function formatDurationFromString($durationString)
    {
        // Handle various duration formats
        if (preg_match('/(\d+):(\d+):(\d+)/', $durationString, $matches)) {
            // HH:MM:SS format
            return $matches[1] . ':' . $matches[2] . ':' . $matches[3];
        } elseif (preg_match('/(\d+):(\d+)/', $durationString, $matches)) {
            // MM:SS format
            return $matches[1] . ':' . $matches[2];
        } elseif (preg_match('/(\d+)\s*min\s*(\d+)\s*s/', $durationString, $matches)) {
            // "X min Y s" format
            return $matches[1] . ':' . str_pad($matches[2], 2, '0', STR_PAD_LEFT);
        }
        return $durationString;
    }

    /**
     * Extract bitrate from string
     */
    private function extractBitrate($bitrateString)
    {
        if (preg_match('/(\d+)/', $bitrateString, $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    /**
     * Extract sample rate from string
     */
    private function extractSampleRate($sampleRateString)
    {
        if (preg_match('/(\d+)/', $sampleRateString, $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    /**
     * Set basic audio defaults
     */
    private function setBasicAudioDefaults(&$metadata)
    {
        // Always set some basic defaults
        $metadata['sample_rate'] = 44100;
        $metadata['channels'] = 2;
        $metadata['bitrate'] = 128;
        $metadata['duration'] = '0:00';
    }

    /**
     * Enhanced audio file analysis for common formats
     */
    private function analyzeAudioFileEnhanced($filePath, &$metadata)
    {
        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // Set enhanced default values based on file type
        switch ($fileExtension) {
            case 'mp3':
                $metadata['format'] = 'MP3';
                $metadata['sample_rate'] = 44100;
                $metadata['channels'] = 2;
                $metadata['bitrate'] = 128;
                $metadata['codec'] = 'MP3';
                break;
            case 'wav':
                $metadata['format'] = 'WAV';
                $metadata['sample_rate'] = 44100;
                $metadata['channels'] = 2;
                $metadata['bitrate'] = 1411; // CD quality
                $metadata['codec'] = 'PCM';
                break;
            case 'ogg':
                $metadata['format'] = 'OGG';
                $metadata['sample_rate'] = 44100;
                $metadata['channels'] = 2;
                $metadata['bitrate'] = 128;
                $metadata['codec'] = 'Vorbis';
                break;
            case 'aac':
                $metadata['format'] = 'AAC';
                $metadata['sample_rate'] = 44100;
                $metadata['channels'] = 2;
                $metadata['bitrate'] = 128;
                $metadata['codec'] = 'AAC';
                break;
            case 'flac':
                $metadata['format'] = 'FLAC';
                $metadata['sample_rate'] = 44100;
                $metadata['channels'] = 2;
                $metadata['bitrate'] = 1000; // Lossless
                $metadata['codec'] = 'FLAC';
                break;
            case 'm4a':
                $metadata['format'] = 'M4A';
                $metadata['sample_rate'] = 44100;
                $metadata['channels'] = 2;
                $metadata['bitrate'] = 128;
                $metadata['codec'] = 'AAC';
                break;
            case 'wma':
                $metadata['format'] = 'WMA';
                $metadata['sample_rate'] = 44100;
                $metadata['channels'] = 2;
                $metadata['bitrate'] = 128;
                $metadata['codec'] = 'WMA';
                break;
        }

        // Try to estimate duration from file size and bitrate
        if (isset($metadata['bitrate']) && !isset($metadata['duration'])) {
            $fileSize = filesize($filePath);
            if ($fileSize > 0 && $metadata['bitrate'] > 0) {
                $estimatedDuration = ($fileSize * 8) / ($metadata['bitrate'] * 1000);
                $metadata['duration'] = $this->formatDuration($estimatedDuration);
            }
        }

        // Set a default duration if still not set
        if (!isset($metadata['duration']) || $metadata['duration'] === '0:00') {
            $metadata['duration'] = '3:00'; // Default 3 minutes
        }
    }
}
