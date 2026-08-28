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
        $limit = (int)$this->option('limit');
        $force = (bool)$this->option('force');

        $query = Effect::where(function($q) use ($force) {
            if ($force) {
                $q->whereNull('processed_url')->orWhere('processed_url', '');
            } else {
                $q->whereIn('status', ['failed', 'error', 'pending'])
                  ->where(function($q2) {
                      $q2->whereNull('processed_url')->orWhere('processed_url', '');
                  });
            }
        });

        $totalFound = $query->count();
        if ($totalFound === 0) {
            $this->info('No failed or pending effects to retry.');
            return 0;
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $effects = $query->get();
        $this->info("Re-queueing {$effects->count()} effects (out of {$totalFound} total) for background processing...");

        $bar = $this->output->createProgressBar($effects->count());
        $bar->start();

        foreach ($effects as $effect) {
            $effect->status = 'pending';
            $effect->process_step = 'Queued for processing...';
            $effect->process_percent = 0;
            $effect->save();

            ProcessStockEffectJob::dispatch($effect->id);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully re-queued {$effects->count()} effects into the job queue!");

        return 0;
    }
}
