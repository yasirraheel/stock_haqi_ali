<?php

namespace App\Console\Commands;

use App\Models\Effect;
use Illuminate\Console\Command;

class CategorizeEffectsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'effects:categorize {--force : Overwrite existing custom categories}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Intelligently categorize all stock effects based on title and filename keywords';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $this->info('Starting intelligent effect categorization...');

        $query = Effect::query();
        if (!$force) {
            // Only categorize items that are currently 'General' or null/empty
            $query->where(function ($q) {
                $q->whereNull('category')
                  ->orWhere('category', '')
                  ->orWhere('category', 'General')
                  ->orWhere('category', 'general');
            });
        }

        $effects = $query->get();
        $this->info("Found {$effects->count()} effects to evaluate.");

        $categorizedCounts = [];
        $updated = 0;

        foreach ($effects as $effect) {
            $category = $this->determineCategory($effect->title, $effect->description);

            if ($effect->category !== $category) {
                $effect->category = $category;
                $effect->save();
                $updated++;
            }

            $categorizedCounts[$category] = ($categorizedCounts[$category] ?? 0) + 1;
        }

        $this->info("Successfully updated {$updated} effects!");
        $this->table(['Category', 'Total Effects'], collect($categorizedCounts)->map(fn($count, $cat) => [$cat, $count])->values()->all());

        return Command::SUCCESS;
    }

    /**
     * Determine category based on title, description, and keywords.
     */
    public static function determineCategory(?string $title, ?string $description = ''): string
    {
        $text = strtolower(trim(($title ?? '') . ' ' . ($description ?? '')));

        if (preg_match('/matte|luma/i', $text)) {
            return 'Luma Mattes';
        }

        if (preg_match('/neon|icon|symbol|sign|hud|interface|lower.?third|motionvfx|typography/i', $text)) {
            return 'Motion Graphics & Neon';
        }

        if (preg_match('/smoke|fog|steam|cloud|haze/i', $text)) {
            return 'Smoke & Fog';
        }

        if (preg_match('/fire|flame|burn|explosion|blast|torch|inferno/i', $text)) {
            return 'Fire & Explosions';
        }

        if (preg_match('/gun|shot|bullet|cal|muzzle|rifle|pistol|smg|recoil|reload|ammo|sniper/i', $text)) {
            return 'Gun FX & Weapons';
        }

        if (preg_match('/warzone|debris|rubble|combat|battle|crater/i', $text)) {
            return 'Warzone & Debris';
        }

        if (preg_match('/city|ancient|rome|ruins|warehouse|atlantis|environment|landscape|temple|castle|street|dungeon/i', $text)) {
            return '3D Environments';
        }

        if (preg_match('/spark|shock|electric|lightning|volt|plasma|zap/i', $text)) {
            return 'Sparks & Electricity';
        }

        if (preg_match('/light|leak|flare|lens|flash|glow|prism|anamorphic|bokeh.?light/i', $text)) {
            return 'Light Leaks & Flares';
        }

        if (preg_match('/transition|wipe|swipe|zoom|morph|dissolve/i', $text)) {
            return 'Transitions';
        }

        if (preg_match('/glitch|distort|vhs|noise|crt|static|pixel/i', $text)) {
            return 'Glitch & VHS';
        }

        if (preg_match('/dust|scratch|grain|film|retro|vintage|grunge/i', $text)) {
            return 'Film Damage & Dust';
        }

        if (preg_match('/blood|gore|splatter|wound|bleed/i', $text)) {
            return 'Blood & Splatter';
        }

        if (preg_match('/magic|portal|energy|marvel|spell|shield|sorcery|aura|forcefield/i', $text)) {
            return 'Magic & Energy';
        }

        if (preg_match('/sci.?fi|space|galaxy|nebula|star|spaceship|planet|alien|cosmos|asteroid|orbit/i', $text)) {
            return 'Sci-Fi & Space';
        }

        if (preg_match('/particle|bokeh|ambient|overlay|floating|snow|rain/i', $text)) {
            return 'Particles & Overlays';
        }

        if (preg_match('/tree|leaf|nature|sky|forest|water|river|ocean/i', $text)) {
            return 'Nature & Backgrounds';
        }

        return 'Cinematic VFX';
    }
}
