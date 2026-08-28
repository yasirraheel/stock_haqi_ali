<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessStockEffectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $effectId;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($effectId)
    {
        $this->effectId = $effectId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');
        $effect = DB::table('effects')->where('id', $this->effectId)->first();
        
        if (!$effect || $effect->status === 'ready') {
            return;
        }

        DB::table('effects')->where('id', $this->effectId)->update([
            'status' => 'downloading',
            'process_percent' => 0,
            'process_step' => 'Starting Download...'
        ]);

        try {
            $effectsDir = storage_path('app/public/effects');
            if (!file_exists($effectsDir)) {
                mkdir($effectsDir, 0777, true);
            }
            $tempPath = "{$effectsDir}/{$this->effectId}_raw.tmp";
            $outputPath = "{$effectsDir}/{$this->effectId}.mp4";

            // 1. Download file (Skip if already downloaded on disk from a previous attempt)
            $isCacheValid = false;
            if (file_exists($tempPath) && filesize($tempPath) > 50000) {
                $headBytes = @file_get_contents($tempPath, false, null, 0, 150);
                if (stripos($headBytes, '<html') === false && stripos($headBytes, '<!doctype') === false) {
                    $isCacheValid = true;
                } else {
                    @unlink($tempPath);
                }
            }

            if ($isCacheValid) {
                $sourceBytes = filesize($tempPath);
                $sourceMb = number_format($sourceBytes / 1048576, 1);
                \Log::info("Skipping Google Drive download for Effect ID {$this->effectId} - local file already cached ({$sourceMb} MB)");
                DB::table('effects')->where('id', $this->effectId)->update([
                    'source_size_bytes' => $sourceBytes,
                    'status' => 'processing',
                    'process_percent' => 100,
                    'process_step' => "Downloaded ({$sourceMb} MB) - Converting MP4..."
                ]);
            } else {
                $sourceUrl = $effect->effect_url;
                $fileId = '';
                if (preg_match('/(?:id=|file\/d\/|\/d\/)([a-zA-Z0-9_-]+)/', $sourceUrl, $driveMatch)) {
                    $fileId = $driveMatch[1];
                }

                $downloadSuccess = false;
                $lastDownloadError = '';

                // If it is a Google Drive file, rotate across all available API keys with rate-limiting backoff
                if (!empty($fileId)) {
                    // Respectful delay before Google Drive API request to prevent rate limiting
                    sleep(2);

                    $apiKeys = \App\Models\GoogleDriveApi::orderBy('calls', 'asc')->get();

                    // Pre-check metadata using available API keys to detect if item is a Folder instead of a file!
                    foreach ($apiKeys as $apiRecord) {
                        $apiKey = trim($apiRecord->api_key);
                        if (empty($apiKey)) continue;

                        $metaUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?fields=id,name,mimeType,size&key={$apiKey}";
                        $ctx = stream_context_create(['http' => ['timeout' => 10]]);
                        $metaJson = @file_get_contents($metaUrl, false, $ctx);
                        if ($metaJson) {
                            $meta = json_decode($metaJson, true);
                            if (isset($meta['mimeType']) && $meta['mimeType'] === 'application/vnd.google-apps.folder') {
                                throw new \Exception('Invalid Item: Google Drive link is a Folder, not a video file.');
                            }
                            break;
                        }
                    }

                    foreach ($apiKeys as $apiRecord) {
                        $apiKey = trim($apiRecord->api_key);
                        if (empty($apiKey)) continue;

                        $apiUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key={$apiKey}";
                        
                        $downloadRes = $this->downloadFileToPath($apiUrl, $tempPath);
                        if ($downloadRes['success']) {
                            \App\Models\GoogleDriveApi::where('id', $apiRecord->id)->increment('calls');
                            $downloadSuccess = true;
                            break;
                        }

                        $lastDownloadError = $downloadRes['error'];
                        // If rate limited (429) or quota exceeded (403), log warning, sleep 5 seconds, and switch to next key
                        if ($downloadRes['http_code'] == 429 || $downloadRes['http_code'] == 403) {
                            \Log::warning("Google Drive API key ID {$apiRecord->id} rate limited (HTTP {$downloadRes['http_code']}). Backing off 5 seconds and rotating to next key...");
                            sleep(5);
                        }
                    }

                    // Fallback to direct CDN if API keys didn't succeed
                    if (!$downloadSuccess) {
                        sleep(4);
                        $cdnUrl = "https://drive.usercontent.google.com/download?id={$fileId}&export=download&confirm=t";
                        $downloadRes = $this->downloadFileToPath($cdnUrl, $tempPath);
                        if ($downloadRes['success']) {
                            $downloadSuccess = true;
                        } else {
                            $lastDownloadError = $downloadRes['error'];
                        }
                    }
                } else {
                    // Non-GDrive standard URL
                    $downloadRes = $this->downloadFileToPath($sourceUrl, $tempPath);
                    if ($downloadRes['success']) {
                        $downloadSuccess = true;
                    } else {
                        $lastDownloadError = $downloadRes['error'];
                    }
                }

                if (!$downloadSuccess) {
                    if (file_exists($tempPath)) @unlink($tempPath);
                    throw new \Exception($lastDownloadError ?: 'Failed to download effect video from source.');
                }

                $sourceBytes = file_exists($tempPath) ? filesize($tempPath) : 0;
                DB::table('effects')->where('id', $this->effectId)->update([
                    'source_size_bytes' => $sourceBytes,
                    'status' => 'processing',
                    'process_percent' => 100,
                    'process_step' => 'Converting & Compressing MP4...'
                ]);
            }

            // 2. Convert via FFmpeg (Single-threaded decoder + encoder to prevent Hostinger EAGAIN limits)
            $ffmpegPath = '/home/u273790872/bin/ffmpeg';
            if (!file_exists($ffmpegPath) || !is_executable($ffmpegPath)) {
                $ffmpegPath = 'ffmpeg';
            }

            if (file_exists($outputPath)) {
                @unlink($outputPath);
            }

            // Command with -threads 1 -thread_type slice BEFORE -i (Decoder) and AFTER -i (Encoder)
            $cmdParts = [
                $ffmpegPath, '-y', '-hide_banner', '-loglevel', 'error',
                '-threads', '1',
                '-thread_type', 'slice',
                '-i', $tempPath,
                '-map', '0:v:0?',
                '-vf', 'scale=trunc(min(max(iw\,ih*dar)\,1280)/2)*2:-2',
                '-c:v', 'libx264',
                '-preset', 'veryfast',
                '-threads', '1',
                '-crf', '28',
                '-pix_fmt', 'yuv420p',
                '-movflags', '+faststart',
                '-an',
                $outputPath,
            ];

            $res = $this->runFfmpegProcess($cmdParts);

            // Fallback 1: If scaling filter fails, try without scale filter
            if ((!file_exists($outputPath) || filesize($outputPath) <= 0) && file_exists($tempPath) && filesize($tempPath) > 0) {
                $cmdFallback = [
                    $ffmpegPath, '-y', '-hide_banner', '-loglevel', 'error',
                    '-threads', '1',
                    '-thread_type', 'slice',
                    '-i', $tempPath,
                    '-map', '0:v:0?',
                    '-c:v', 'libx264',
                    '-preset', 'veryfast',
                    '-threads', '1',
                    '-crf', '28',
                    '-pix_fmt', 'yuv420p',
                    '-movflags', '+faststart',
                    '-an',
                    $outputPath,
                ];
                $res = $this->runFfmpegProcess($cmdFallback);
            }

            // Fallback 2: Stream copy if already h264/mp4 container
            if ((!file_exists($outputPath) || filesize($outputPath) <= 0) && file_exists($tempPath) && filesize($tempPath) > 0) {
                $cmdCopy = [
                    $ffmpegPath, '-y', '-hide_banner', '-loglevel', 'error',
                    '-threads', '1',
                    '-i', $tempPath,
                    '-map', '0:v:0?',
                    '-c:v', 'copy',
                    '-movflags', '+faststart',
                    '-an',
                    $outputPath,
                ];
                $res = $this->runFfmpegProcess($cmdCopy);
            }

            if (file_exists($outputPath) && filesize($outputPath) > 0) {
                $convertedBytes = filesize($outputPath);
                DB::table('effects')->where('id', $this->effectId)->update([
                    'status' => 'ready',
                    'process_percent' => 100,
                    'process_step' => 'Ready',
                    'processed_url' => url("storage/effects/{$this->effectId}.mp4")
                ]);
                \Log::info("Effect Processing Complete [ID: {$this->effectId}]", [
                    'converted_bytes' => $convertedBytes,
                    'converted_size_mb' => round($convertedBytes / 1048576, 2),
                ]);

                // Cool down delay between conversions to prevent rapid queue churn
                sleep(3);
            } else {
                throw new \Exception('FFmpeg conversion failed: ' . implode("\n", $res['output'] ?? []));
            }

        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $specificError = $this->formatSpecificError($msg);
            \Log::error("Effect Processing Failed [ID: {$this->effectId}]: {$msg}");
            DB::table('effects')->where('id', $this->effectId)->update([
                'status' => 'failed',
                'process_step' => 'Failed: ' . $specificError
            ]);
        } finally {
            // Unlink temporary raw download file only when processing successfully completes (ready).
            // On retries or failures, retain raw file so Google Drive is NOT re-queried/downloaded again.
            if (isset($tempPath) && file_exists($tempPath)) {
                $checkEffect = DB::table('effects')->where('id', $this->effectId)->first();
                if ($checkEffect && $checkEffect->status === 'ready') {
                    @unlink($tempPath);
                }
            }
            if (isset($cookieFile) && file_exists($cookieFile)) {
                @unlink($cookieFile);
            }
        }
    }

    /**
     * Download file with streaming progress and HTML/rate-limit verification.
     */
    protected function downloadFileToPath(string $url, string $destPath): array
    {
        if (file_exists($destPath)) {
            @unlink($destPath);
        }

        $fp = fopen($destPath, 'w+');
        if (!$fp) {
            return ['success' => false, 'error' => 'Cannot open temp file for writing', 'http_code' => 0];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 1048576);
        curl_setopt($ch, CURLOPT_TCP_NODELAY, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt($ch, CURLOPT_FILE, $fp);

        $effectId = $this->effectId;
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function($resource, $downloadSize, $downloaded) use ($effectId) {
            if ($downloadSize > 0) {
                $percent = min(99, (int)round(($downloaded / $downloadSize) * 100));
                $downloadedMb = number_format($downloaded / 1048576, 1);
                $totalMb = number_format($downloadSize / 1048576, 1);

                static $lastUpdate = 0;
                if (time() - $lastUpdate >= 1 || $percent == 99) {
                    $lastUpdate = time();
                    DB::table('effects')->where('id', $effectId)->update([
                        'status' => 'downloading',
                        'process_percent' => $percent,
                        'process_step' => "Downloading {$percent}% ({$downloadedMb} MB / {$totalMb} MB)"
                    ]);
                }
            }
        });

        $execResult = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        fclose($fp);
        curl_close($ch);

        if (!$execResult || !empty($curlError)) {
            if (file_exists($destPath)) @unlink($destPath);
            return ['success' => false, 'error' => "Curl error: {$curlError}", 'http_code' => $httpCode];
        }

        if ($httpCode >= 400) {
            if (file_exists($destPath)) @unlink($destPath);
            return ['success' => false, 'error' => "HTTP {$httpCode} error from Google Drive API", 'http_code' => $httpCode];
        }

        $size = file_exists($destPath) ? filesize($destPath) : 0;
        if ($size <= 0) {
            if (file_exists($destPath)) @unlink($destPath);
            return ['success' => false, 'error' => 'Downloaded file is 0 bytes', 'http_code' => $httpCode];
        }

        // Check if Google returned an HTML error/rate limit page instead of video
        if ($size <= 25000) {
            $snippet = @file_get_contents($destPath, false, null, 0, 400);
            if (stripos($snippet, '<html') !== false || stripos($snippet, '<!doctype') !== false) {
                if (file_exists($destPath)) @unlink($destPath);
                return ['success' => false, 'error' => 'Google Drive rate limit or virus warning HTML page returned instead of video', 'http_code' => 429];
            }
        }

        return ['success' => true, 'error' => '', 'http_code' => $httpCode];
    }

    /**
     * Run an FFmpeg command with safe process handling.
     */
    protected function runFfmpegProcess(array $cmdParts): array
    {
        $returnCode = 1;
        $outputLines = [];

        if (function_exists('proc_open')) {
            $descriptorspec = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
            $process = proc_open($cmdParts, $descriptorspec, $pipes);
            if (is_resource($process)) {
                fclose($pipes[0]);
                $stdoutContent = stream_get_contents($pipes[1]);
                $stderrContent = stream_get_contents($pipes[2]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                $returnCode = proc_close($process);
                $outputLines = array_filter([trim($stdoutContent), trim($stderrContent)]);
            }
        } elseif (function_exists('shell_exec')) {
            $quoted = array_map('escapeshellarg', $cmdParts);
            $out = shell_exec(implode(' ', $quoted) . ' 2>&1');
            $outputLines = [trim($out)];
            $returnCode = 0;
        }

        return ['code' => $returnCode, 'output' => $outputLines];
    }

    /**
     * Convert raw error string into a specific, human-readable status description.
     */
    protected function formatSpecificError(string $rawMsg): string
    {
        $raw = strtolower($rawMsg);

        if (str_contains($raw, 'folder, not a video') || str_contains($raw, 'is a folder') || str_contains($raw, 'link is a folder')) {
            return 'URL is a Google Drive Folder (Not a video file)';
        }
        if (str_contains($raw, '429') || str_contains($raw, 'rate limit')) {
            return 'Google Drive Rate Limit (429) - Cooldown required';
        }
        if (str_contains($raw, 'virus scan') || str_contains($raw, 'large file')) {
            return 'Google Drive Virus Scan Warning HTML returned';
        }
        if (str_contains($raw, '403') || str_contains($raw, 'access denied') || str_contains($raw, 'permission')) {
            return 'Google Drive Access Denied (Private File / No Permission)';
        }
        if (str_contains($raw, '404') || str_contains($raw, 'not found')) {
            return 'Google Drive File Not Found (Deleted or Invalid ID)';
        }
        if (str_contains($raw, '0 bytes') || str_contains($raw, 'empty')) {
            return 'Downloaded file is 0 bytes (Empty source file)';
        }
        if (str_contains($raw, 'resource temporarily unavailable') || str_contains($raw, 'eagain')) {
            return 'Server Resource/Thread Limit (EAGAIN - Process busy)';
        }
        if (str_contains($raw, 'invalid data found') || str_contains($raw, 'corrupt')) {
            return 'Corrupted or Non-Video File Format';
        }
        if (str_contains($raw, 'opening decoder') || str_contains($raw, 'no decoder')) {
            return 'Unsupported Video Codec in Source';
        }
        if (str_contains($raw, 'curl error')) {
            return 'Network/Connection Timeout downloading from Google Drive';
        }

        // Clean up any FFmpeg local path prefix
        $clean = preg_replace('/\/home\/[^\/]+\/[^\s:]+:\s*/', '', $rawMsg);
        $clean = trim(preg_replace('/\s+/', ' ', $clean));

        return \Illuminate\Support\Str::limit($clean, 120);
    }
}
