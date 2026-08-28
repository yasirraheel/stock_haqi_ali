<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Effect;
use App\Jobs\ProcessStockEffectJob;

class RetryFailedEffectsCommand extends Command
{
    protected $signature = 'effects:retry {--limit=0 : Number of effects to retry (0 for all)} {--force : Force retry even if not failed}';
    protected $description = 'Retry failed or pending stock effects conversion';

    public function handle()
    {
        $limit = (int)$this->option('limit') ?: 30;
        $force = (bool)$this->option('force');

        $query = Effect::where(function($q) use ($force) {
            if ($force) {
                $q->whereNull('processed_url')->orWhere('processed_url', '');
            } else {
                $q->whereIn('status', ['failed', 'error'])
                  ->where('status', '!=', 'ready');
            }
        })->orderBy('id', 'asc');

        $totalFound = $query->count();
        if ($totalFound === 0) {
            $this->info('No failed effects to retry.');
            return 0;
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $effects = $query->get();
        $this->info("Re-queueing {$effects->count()} failed effects (out of {$totalFound} total) with 10s staggered delays...");

        $bar = $this->output->createProgressBar($effects->count());
        $bar->start();

        foreach ($effects as $index => $effect) {
            $effect->status = 'pending';
            $effect->process_step = 'Queued for retry...';
            $effect->process_percent = 0;
            $effect->save();

            $staggerSeconds = $index * 10;
            ProcessStockEffectJob::dispatch($effect->id)->delay(now()->addSeconds($staggerSeconds));
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully re-queued {$effects->count()} failed effects with staggered delays!");

        return 0;
    }
}
