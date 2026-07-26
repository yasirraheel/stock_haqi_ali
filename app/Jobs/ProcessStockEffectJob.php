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
        $effect = DB::table('effects')->where('id', $this->effectId)->first();
        
        if (!$effect || $effect->status === 'ready') {
            return;
        }

        DB::table('effects')->where('id', $this->effectId)->update(['status' => 'processing']);

        try {
            $effectsDir = storage_path('app/public/effects');
            if (!file_exists($effectsDir)) {
                mkdir($effectsDir, 0777, true);
            }
            $tempPath = "{$effectsDir}/{$this->effectId}_raw.tmp";
            $outputPath = "{$effectsDir}/{$this->effectId}.mp4";

            // 1. Download file
            $sourceUrl = $effect->effect_url;
            $fileId = '';
            if (preg_match('/(?:id=|file\/d\/|\/d\/)([a-zA-Z0-9_-]+)/', $sourceUrl, $driveMatch)) {
                $fileId = $driveMatch[1];
                
                // Fetch a random Google Drive API key from the database
                $apiRecord = \App\Models\GoogleDriveApi::inRandomOrder()->first();
                if ($apiRecord && !empty($apiRecord->api_key)) {
                    $apiKey = $apiRecord->api_key;
                    // Increment the API call count
                    \App\Models\GoogleDriveApi::where('id', $apiRecord->id)->increment('calls');
                    // Use the official Google Drive API to download the file directly, bypassing all virus scans
                    $sourceUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key={$apiKey}";
                } else {
                    // Fallback if no API key is available
                    $sourceUrl = "https://drive.google.com/uc?export=download&id={$fileId}";
                }
            }

            $ch = curl_init($sourceUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $fp = fopen($tempPath, 'w+');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            fclose($fp);
            
            if (curl_errno($ch)) {
                throw new \Exception('Curl error: ' . curl_error($ch));
            }
            curl_close($ch);

            $sourceBytes = file_exists($tempPath) ? filesize($tempPath) : 0;
            if ($sourceBytes <= 0) {
                throw new \Exception('Downloaded Google Drive file is empty.');
            }
            DB::table('effects')->where('id', $this->effectId)->update([
                'source_size_bytes' => $sourceBytes,
            ]);

            // 2. Convert via FFmpeg.
            // Keep the output compatible with Reel2Reel and the older Hostinger FFmpeg build.
            $ffmpegPath = '/home/u273790872/bin/ffmpeg';
            if (!file_exists($ffmpegPath) || !is_executable($ffmpegPath)) {
                $ffmpegPath = 'ffmpeg';
            }

            if (file_exists($outputPath)) {
                unlink($outputPath);
            }

            $cmdParts = [
                $ffmpegPath, '-y', '-hide_banner', '-loglevel', 'error',
                '-i', $tempPath,
                '-map', '0:v:0',
                '-vf', 'scale=1280:-2',
                '-c:v', 'libx264',
                '-preset', 'veryfast',
                '-threads', '1',
                '-crf', '28',
                '-pix_fmt', 'yuv420p',
                '-movflags', '+faststart',
                '-an',
                $outputPath,
            ];

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
                    $outputLines = [$stdoutContent, $stderrContent];
                }
            } elseif (function_exists('shell_exec')) {
                $quoted = array_map('escapeshellarg', $cmdParts);
                $out = shell_exec(implode(' ', $quoted) . ' 2>&1');
                $outputLines = [$out];
                $returnCode = 0;
            }

            if ($returnCode === 0 && file_exists($outputPath) && filesize($outputPath) > 0) {
                $convertedBytes = filesize($outputPath);
                DB::table('effects')->where('id', $this->effectId)->update([
                    'status' => 'ready',
                    'processed_url' => url("storage/effects/{$this->effectId}.mp4")
                ]);
                \Log::info("Effect Processing Complete [ID: {$this->effectId}]", [
                    'converted_bytes' => $convertedBytes,
                    'converted_size_mb' => round($convertedBytes / 1048576, 2),
                ]);
            } else {
                throw new \Exception('FFmpeg conversion failed: ' . implode("\n", $outputLines));
            }

        } catch (\Exception $e) {
            \Log::error("Effect Processing Failed [ID: {$this->effectId}]: " . $e->getMessage());
            DB::table('effects')->where('id', $this->effectId)->update(['status' => 'error']);
        } finally {
            // Never retain the downloaded Google Drive source after processing.
            if (isset($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
            if (isset($cookieFile) && file_exists($cookieFile)) {
                @unlink($cookieFile);
            }
        }
    }
}
