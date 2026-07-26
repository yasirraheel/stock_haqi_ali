<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ProcessEffectCommand extends Command
{
    protected $signature = 'effect:process {id}';
    protected $description = 'Download and process a stock effect to MP4';

    public function handle()
    {
        $id = $this->argument('id');
        $cacheKey = "effect_progress_{$id}";

        Cache::put($cacheKey, ['status' => 'downloading', 'progress' => 0], 3600);

        $effect = DB::table('effects')->where('id', $id)->first();
        if (!$effect) {
            Cache::put($cacheKey, ['status' => 'error', 'message' => 'Effect not found'], 3600);
            return;
        }

        $sourceUrl = $effect->effect_url;
        if (preg_match('/(?:id=|file\/d\/|\/d\/)([a-zA-Z0-9_-]+)/', $sourceUrl, $driveMatch)) {
            $sourceUrl = 'https://drive.usercontent.google.com/download?id=' . $driveMatch[1] . '&export=download&confirm=t';
        }

        $effectsDir = storage_path('app/public/effects');
        if (!file_exists($effectsDir)) {
            mkdir($effectsDir, 0777, true);
        }
        $tempPath = "{$effectsDir}/{$id}_raw.tmp";
        $outputPath = "{$effectsDir}/{$id}.mp4";

        $fp = fopen($tempPath, 'w+');
        $ch = curl_init($sourceUrl);
        
        $lastProgressUpdate = time();
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function($resource, $download_size, $downloaded, $upload_size, $uploaded) use ($cacheKey, &$lastProgressUpdate) {
            if ($download_size > 0) {
                $now = time();
                if ($now > $lastProgressUpdate || $downloaded == $download_size) {
                    $progress = round(($downloaded / $download_size) * 100);
                    Cache::put($cacheKey, ['status' => 'downloading', 'progress' => $progress], 3600);
                    $lastProgressUpdate = $now;
                }
            }
        });
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        Cache::put($cacheKey, ['status' => 'converting', 'progress' => 0], 3600);
        
        $cmd = "ffmpeg -i " . escapeshellarg($tempPath) . " -y -vcodec libx264 -crf 23 -preset fast -acodec aac " . escapeshellarg($outputPath) . " 2>&1";
        
        $descriptorspec = array(
           0 => array("pipe", "r"),
           1 => array("pipe", "w"),
           2 => array("pipe", "w")
        );
        $process = proc_open($cmd, $descriptorspec, $pipes);
        
        if (is_resource($process)) {
            while ($s = fgets($pipes[2])) {
                // Just keeping the process alive
            }
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
        }

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        if (file_exists($outputPath)) {
            Cache::put($cacheKey, ['status' => 'ready', 'url' => url("storage/effects/{$id}.mp4")], 3600);
        } else {
            Cache::put($cacheKey, ['status' => 'error', 'message' => 'Conversion failed'], 3600);
        }
    }
}
