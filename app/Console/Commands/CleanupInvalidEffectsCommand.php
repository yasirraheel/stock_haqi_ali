<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Effect;
use App\Models\GoogleDriveFile;

class CleanupInvalidEffectsCommand extends Command
{
    protected $signature = 'effects:cleanup';
    protected $description = 'Clean up and permanently remove invalid items (Google Drive folders, 0-byte files, non-videos) from imported effects';

    public function handle()
    {
        $invalidQuery = Effect::where(function($q) {
            $q->where('process_step', 'LIKE', '%Folder%')
              ->orWhere('process_step', 'LIKE', '%0 bytes%')
              ->orWhere('process_step', 'LIKE', '%Empty source%')
              ->orWhere('process_step', 'LIKE', '%Invalid Item%')
              ->orWhere('process_step', 'LIKE', '%Corrupted or Non-Video%')
              ->orWhere('process_step', 'LIKE', '%Unsupported Video Codec%');
        })->where('status', '!=', 'ready');

        $invalidEffects = $invalidQuery->get();
        $count = $invalidEffects->count();

        if ($count === 0) {
            $this->info('No invalid or non-video effects found to clean up.');
            return 0;
        }

        $this->warn("Found {$count} invalid / non-video effect items. Cleaning up...");

        foreach ($invalidEffects as $effect) {
            // Delete temp raw file if exists
            $tempPath = storage_path("app/public/effects/{$effect->id}_raw.tmp");
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
            // Delete output mp4 if exists
            $outputPath = storage_path("app/public/effects/{$effect->id}.mp4");
            if (file_exists($outputPath)) {
                @unlink($outputPath);
            }
            // Disconnect from google_drive_files if linked
            GoogleDriveFile::where('effect_id', $effect->id)->update([
                'effect_id' => null,
                'status' => 'scanned'
            ]);

            $this->line("  Removed Effect #{$effect->id}: {$effect->title} ({$effect->process_step})");
            $effect->delete();
        }

        $this->info("Successfully cleaned up and removed {$count} invalid / non-video effect items!");
        return 0;
    }
}
