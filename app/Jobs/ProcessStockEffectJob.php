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
                $sourceUrl = 'https://drive.google.com/uc?export=download&id=' . $fileId;
            }

            $ch = curl_init($sourceUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, false);
            
            // We must use a cookie jar to store the session for the virus scan bypass
            $cookieFile = "{$effectsDir}/cookie_{$this->effectId}.txt";
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
            
            $response = curl_exec($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);
            
            // Check if it's the virus scan warning page
            if (strpos($body, 'confirm=') !== false && preg_match('/confirm=([a-zA-Z0-9_-]+)/', $body, $confirmMatch)) {
                $confirmToken = $confirmMatch[1];
                $finalUrl = "https://drive.google.com/uc?export=download&id={$fileId}&confirm={$confirmToken}";
                
                curl_setopt($ch, CURLOPT_URL, $finalUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                $fp = fopen($tempPath, 'w+');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_exec($ch);
                fclose($fp);
            } else {
                // If no virus scan warning, we just save the body we already downloaded
                file_put_contents($tempPath, $body);
            }
            
            if (curl_errno($ch)) {
                throw new \Exception('Curl error: ' . curl_error($ch));
            }
            curl_close($ch);
            
            if (file_exists($cookieFile)) {
                unlink($cookieFile);
            }

            // 2. Convert via FFmpeg
            $ffmpegPath = '/home/u273790872/bin/ffmpeg';
            if (!file_exists($ffmpegPath)) {
                $ffmpegPath = 'ffmpeg'; // fallback
            }
            $cmd = $ffmpegPath . " -y -i " . escapeshellarg($tempPath) . " -vcodec libx264 -preset fast -filter:v scale=1920:-2 -crf 26 -pix_fmt yuv420p -acodec aac -b:a 128k -ac 2 " . escapeshellarg($outputPath) . " 2>&1";
            
            if (function_exists('shell_exec')) {
                $output = shell_exec($cmd);
                $returnCode = file_exists($outputPath) ? 0 : 1;
                $output = [$output];
            } else {
                $process = \Symfony\Component\Process\Process::fromShellCommandline($cmd);
                $process->setTimeout(3600);
                $process->run();
                $output = [$process->getOutput(), $process->getErrorOutput()];
                $returnCode = $process->getExitCode();
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
                throw new \Exception('FFmpeg conversion failed: ' . implode("\n", $output));
            }

        } catch (\Exception $e) {
            \Log::error("Effect Processing Failed [ID: {$this->effectId}]: " . $e->getMessage());
            DB::table('effects')->where('id', $this->effectId)->update(['status' => 'error']);
        }
    }
}
