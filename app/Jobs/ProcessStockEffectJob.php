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

            // 2. Convert via FFmpeg
            $ffmpegPath = '/home/u273790872/bin/ffmpeg';
            if (!file_exists($ffmpegPath)) {
                $ffmpegPath = 'ffmpeg'; // fallback
            }

            // Build command as array to avoid quoting issues with -vf filter
            $returnCode = 1;
            $outputLines = [];

            if (function_exists('proc_open')) {
                // proc_open is most reliable - no quoting issues, works even when shell_exec is disabled
                $cmdParts = [
                    $ffmpegPath, '-y', '-i', $tempPath,
                    '-map', '0:v:0',
                    '-vcodec', 'libx264',
                    '-preset', 'fast',
                    '-profile:v', 'high',
                    '-level', '5.1',
                    '-vf', 'scale=1920:-2,format=yuv420p',
                    '-crf', '26',
                    '-an',
                    $outputPath
                ];
                $descriptorspec = [0 => ['pipe','r'], 1 => ['pipe','w'], 2 => ['pipe','w']];
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
                $cmd = $ffmpegPath . ' -y -i ' . escapeshellarg($tempPath)
                    . ' -map 0:v:0 -vcodec libx264 -preset fast -profile:v high -level 5.1'
                    . ' -vf scale=1920:-2,format=yuv420p -crf 26 -an '
                    . escapeshellarg($outputPath) . ' 2>&1';
                $out = shell_exec($cmd);
                $returnCode = file_exists($outputPath) && filesize($outputPath) > 0 ? 0 : 1;
                $outputLines = [$out];
            }

            // Cleanup temp file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            if ($returnCode === 0 && file_exists($outputPath)) {
                DB::table('effects')->where('id', $this->effectId)->update([
                    'status' => 'ready',
                    'processed_url' => url("storage/effects/{$this->effectId}.mp4")
                ]);
            } else {
                throw new \Exception('FFmpeg conversion failed: ' . implode("\n", $outputLines));
            }

        } catch (\Exception $e) {
            \Log::error("Effect Processing Failed [ID: {$this->effectId}]: " . $e->getMessage());
            DB::table('effects')->where('id', $this->effectId)->update(['status' => 'error']);
        }
    }
}
