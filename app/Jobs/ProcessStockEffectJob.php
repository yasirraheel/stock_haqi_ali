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
            $sourceUrl = $effect->effect_url;
            if (preg_match('/(?:id=|file\/d\/|\/d\/)([a-zA-Z0-9_-]+)/', $sourceUrl, $driveMatch)) {
                $sourceUrl = 'https://drive.usercontent.google.com/download?id=' . $driveMatch[1] . '&export=download&confirm=t';
            }

            $effectsDir = storage_path('app/public/effects');
            if (!file_exists($effectsDir)) {
                mkdir($effectsDir, 0777, true);
            }

            $tempPath = "{$effectsDir}/{$this->effectId}_raw.tmp";
            $outputPath = "{$effectsDir}/{$this->effectId}.mp4";

            // 1. Download file
            $fp = fopen($tempPath, 'w+');
            $ch = curl_init($sourceUrl);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new \Exception('Curl error: ' . curl_error($ch));
            }
            curl_close($ch);
            fclose($fp);

            // 2. Convert via FFmpeg
            $ffmpegPath = '/home/u273790872/bin/ffmpeg';
            if (!file_exists($ffmpegPath)) {
                $ffmpegPath = 'ffmpeg'; // fallback
            }
            $cmd = $ffmpegPath . " -i " . escapeshellarg($tempPath) . " -y -vcodec libx264 -pix_fmt yuv420p -crf 23 -preset fast -acodec aac -b:a 128k -ac 2 " . escapeshellarg($outputPath) . " 2>&1";
            
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
