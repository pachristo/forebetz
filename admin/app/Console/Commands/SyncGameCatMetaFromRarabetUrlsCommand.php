<?php

namespace App\Console\Commands;

use App\Models\GameCat;
use App\Services\RarabetGameCatMetaSyncService;
use App\Support\TipCategoryPublicPathSlugs;
use Illuminate\Console\Command;

class SyncGameCatMetaFromRarabetUrlsCommand extends Command
{
    protected $signature = 'game-cats:sync-meta-from-rarabet
                            {--base-url=https://rarabet.com : Site origin (no trailing slash)}
                            {--path=* : Extra path segment(s) to sync (repeatable); defaults to standard tip URLs}
                            {--dry-run : Show changes without saving}';

    protected $description = 'Fetch live Rarabet tip category pages and sync title, meta description, meta keywords, and banner headings into game_cats.';

    /** @var list<string> */
    private const DEFAULT_PATHS = [
        'btts_gg',
        'double_chance',
        '0_5_ht',
        'over_15_goals',
        'over_25_goals',
        '3_5_goals',
        'draw_no_bet',
        'handicap',
        'draws',
        'win_eithe_half',
        'banker_of_the_day',
    ];

    public function handle(RarabetGameCatMetaSyncService $sync): int
    {
        $base = rtrim((string) $this->option('base-url'), '/');
        $dry = (bool) $this->option('dry-run');
        /** @var array<int, string> $extra */
        $extra = array_values(array_filter((array) $this->option('path')));
        $paths = $extra !== [] ? $extra : self::DEFAULT_PATHS;

        $ok = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($paths as $path) {
            $path = trim(strtolower((string) $path), '/');
            if ($path === '') {
                continue;
            }

            $url = $base.'/'.$path;
            $cat = GameCat::query()->where('slug', $path)->first();
            if (! $cat) {
                $predictionKey = TipCategoryPublicPathSlugs::predictionKeyFromPublicPath($path);
                $keys = $predictionKey === 'dnb' ? ['dnb', 'dnd'] : [$predictionKey];
                $cat = GameCat::query()
                    ->where(function ($q) use ($keys): void {
                        $q->whereIn('prediction_slug', $keys)
                            ->orWhereIn('cat_type', $keys);
                    })
                    ->orderBy('id')
                    ->first();
            }
            if (! $cat) {
                $this->warn("No game_cats row matched slug or prediction key [{$path}] — skipped.");
                $skipped++;

                continue;
            }

            $payload = $sync->extractFromUrl($url);
            if ($payload === null) {
                $this->error("Failed to fetch or parse: {$url}");
                $failed++;

                continue;
            }

            if ($dry) {
                $this->line("[dry-run] {$path} → title: ".substr($payload['title'], 0, 80));
                $ok++;

                continue;
            }

            if ($sync->applyToGameCat($cat, $payload, false)) {
                $this->info("Updated: {$path} (game_cats #{$cat->id})");
                $ok++;
            } else {
                $this->warn("Nothing to update for: {$path}");
                $skipped++;
            }
        }

        $this->newLine();
        $this->info("Done. Updated: {$ok}, skipped: {$skipped}, failed: {$failed}".($dry ? ' (dry-run)' : ''));

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
