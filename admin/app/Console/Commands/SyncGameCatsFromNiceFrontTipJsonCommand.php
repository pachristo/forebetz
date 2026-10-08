<?php

namespace App\Console\Commands;

use App\Models\GameCat;
use App\Support\NiceFrontTipCategoryPageDefinitions;
use App\Support\TipSportOptions;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncGameCatsFromNiceFrontTipJsonCommand extends Command
{
    protected $signature = 'game-cats:sync-from-nice-front-json
        {--dry-run : Show changes without writing to the database}
        {--json-dir= : Override directory containing *.json (default: ../admin/public next to new_admin)}';

    protected $description = 'Upsert tip categories (game_cats) from nice_front admin/public SEO JSON: title, head1, head2, slug, meta_description, meta_keywords, content (footer); also applies homepage.json to the system home category (homeslug)';

    public function handle(): int
    {
        $dir = $this->option('json-dir') ?: env('NICE_FRONT_ADMIN_JSON', dirname(base_path()).DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'public');
        $dir = rtrim((string) $dir, DIRECTORY_SEPARATOR);

        if (! is_dir($dir)) {
            $this->error("JSON directory not found: {$dir}");

            return self::FAILURE;
        }

        $this->info("Reading SEO JSON from: {$dir}");

        $dry = (bool) $this->option('dry-run');
        $updated = 0;
        $created = 0;

        foreach (NiceFrontTipCategoryPageDefinitions::pages() as $def) {
            $path = $dir.DIRECTORY_SEPARATOR.$def['seo_json'];
            if (! is_readable($path)) {
                $this->warn("Missing file {$def['seo_json']} — skipped {$def['path_slug']}.");

                continue;
            }

            $raw = File::get($path);
            $seo = json_decode($raw, false);
            if (! is_object($seo)) {
                $this->warn("Invalid JSON in {$def['seo_json']} — skipped {$def['path_slug']}.");

                continue;
            }

            $metaHtml = (string) ($seo->meta ?? '');
            $footerHtml = (string) ($seo->footer ?? '');
            $jsonTitle = trim(strip_tags((string) ($seo->title ?? '')));
            $title = $jsonTitle !== '' ? mb_substr($jsonTitle, 0, 255) : mb_substr($def['list_title'], 0, 255);
            $metaDescription = trim((string) ($seo->meta_description ?? ''));
            if ($metaDescription === '') {
                $metaDescription = $this->metaDescriptionFromLegacyMeta($metaHtml);
            }
            $metaDescription = mb_substr($metaDescription, 0, 900);
            $metaKeywords = trim((string) ($seo->meta_keywords ?? ''));
            if ($metaKeywords === '') {
                $metaKeywords = $this->metaKeywordsFromLegacyMeta($metaHtml, $title);
            }
            $metaKeywords = mb_substr($metaKeywords, 0, 900);

            $head1Raw = trim(strip_tags((string) ($seo->head1 ?? '')));
            $head2Raw = trim(strip_tags((string) ($seo->head2 ?? '')));
            if ($head1Raw !== '' && $head2Raw !== '') {
                $head1 = mb_substr($head1Raw, 0, 555);
                $head2 = mb_substr($head2Raw, 0, 555);
            } elseif ($head1Raw !== '') {
                $head1 = mb_substr($head1Raw, 0, 555);
                [$_, $head2] = $this->headingsFromJsonTitleAndFooter(
                    $jsonTitle !== '' ? $jsonTitle : $def['list_title'],
                    $footerHtml,
                    $def['list_title'],
                    $metaDescription
                );
            } else {
                [$head1, $head2] = $this->headingsFromJsonTitleAndFooter(
                    $jsonTitle !== '' ? $jsonTitle : $def['list_title'],
                    $footerHtml,
                    $def['list_title'],
                    $metaDescription
                );
            }

            $predictionSlug = $def['prediction_slug'];
            $pathSlug = $def['path_slug'];

            $record = GameCat::query()
                ->where(function ($q) use ($predictionSlug): void {
                    $q->where('prediction_slug', $predictionSlug)
                        ->orWhere('cat_type', $predictionSlug);
                })
                ->first();

            if ($record === null) {
                $record = GameCat::query()->where('slug', $pathSlug)->first();
            }

            $listTitle = mb_substr($def['list_title'], 0, 255);

            $payload = [
                'title' => $title,
                'head1' => $head1,
                'head2' => $head2,
                'slug' => $pathSlug,
                'prediction_slug' => $predictionSlug,
                'cat_type' => $predictionSlug,
                'meta_description' => $metaDescription !== '' ? $metaDescription : null,
                'meta_keywords' => $metaKeywords !== '' ? $metaKeywords : null,
                'content' => $footerHtml !== '' ? $footerHtml : null,
                'tips_table_name' => $title,
                'tips_button_name' => $listTitle,
                'sport' => TipSportOptions::DEFAULT,
                'status' => 'published',
                'date' => Carbon::now()->toDateString(),
            ];

            if ($dry) {
                $this->line(($record ? 'UPDATE' : 'CREATE')."  slug={$pathSlug}  prediction_slug={$predictionSlug}  title=".mb_substr($title, 0, 60).'…');

                continue;
            }

            if ($record !== null) {
                if ($record->is_system) {
                    $payload = array_diff_key($payload, array_flip(['slug', 'prediction_slug', 'cat_type']));
                }
                $record->fill($payload);
                $record->save();
                $updated++;
            } else {
                GameCat::query()->create(array_merge($payload, [
                    'creator' => 1,
                    'category' => 'Game Category',
                    'likes' => 0,
                ]));
                $created++;
            }
        }

        $homeSynced = $this->syncHomeGameCatFromHomepageJson($dir, $dry);
        if (! $dry && $homeSynced) {
            $updated++;
        }

        if ($dry) {
            $this->warn('Dry run complete — no rows written.');
        } else {
            $msg = "Done. Updated: {$updated}, created: {$created}.";
            if ($homeSynced) {
                $msg .= ' Home (homeslug) synced from homepage.json.';
            }
            $this->info($msg);
        }

        return self::SUCCESS;
    }

    /**
     * Apply homepage.json to the system home tip category (`homeslug`, prediction key `home`).
     */
    private function syncHomeGameCatFromHomepageJson(string $dir, bool $dry): bool
    {
        $path = $dir.DIRECTORY_SEPARATOR.'homepage.json';
        if (! is_readable($path)) {
            $this->warn('Missing homepage.json — skipped home (homeslug) game cat.');

            return false;
        }

        $raw = File::get($path);
        $seo = json_decode($raw, false);
        if (! is_object($seo)) {
            $this->warn('Invalid homepage.json — skipped home game cat.');

            return false;
        }

        $record = GameCat::query()
            ->where(function ($q): void {
                $q->where('slug', 'homeslug')
                    ->orWhere('prediction_slug', 'home')
                    ->orWhere('cat_type', 'home');
            })
            ->first();

        if ($record === null) {
            $this->warn('No game_cat row for home (slug homeslug or prediction_slug home) — skipped homepage.json.');

            return false;
        }

        $metaHtml = (string) ($seo->meta ?? '');
        $footerHtml = (string) ($seo->footer ?? '');
        $jsonTitle = trim(strip_tags((string) ($seo->title ?? '')));
        $title = $jsonTitle !== '' ? mb_substr($jsonTitle, 0, 255) : (string) $record->title;

        $metaDescription = trim((string) ($seo->meta_description ?? ''));
        if ($metaDescription === '') {
            $metaDescription = $this->metaDescriptionFromLegacyMeta($metaHtml);
        }
        $metaDescription = mb_substr($metaDescription, 0, 900);

        $metaKeywords = trim((string) ($seo->meta_keywords ?? ''));
        if ($metaKeywords === '') {
            $metaKeywords = $this->metaKeywordsFromLegacyMeta($metaHtml, $title);
        }
        $metaKeywords = mb_substr($metaKeywords, 0, 900);

        $head1Raw = trim(strip_tags((string) ($seo->head1 ?? '')));
        $head2Raw = trim(strip_tags((string) ($seo->head2 ?? '')));
        if ($head2Raw === '') {
            $head2Raw = trim(strip_tags((string) ($seo->head3 ?? '')));
        }

        if ($head1Raw !== '') {
            $head1 = mb_substr($head1Raw, 0, 555);
            if ($head2Raw !== '') {
                $head2 = mb_substr($head2Raw, 0, 555);
            } else {
                $head2 = mb_substr(
                    $this->firstPlainHeadingFromFooter($footerHtml)
                        ?? ($metaDescription !== '' ? trim(strip_tags($metaDescription)) : null)
                        ?? $head1,
                    0,
                    555
                );
            }
        } else {
            [$head1, $head2] = $this->headingsFromJsonTitleAndFooter(
                $jsonTitle !== '' ? $jsonTitle : (string) $record->title,
                $footerHtml,
                (string) $record->title,
                $metaDescription
            );
        }

        $payload = [
            'title' => $title,
            'head1' => $head1,
            'head2' => $head2,
            'meta_description' => $metaDescription !== '' ? $metaDescription : null,
            'meta_keywords' => $metaKeywords !== '' ? $metaKeywords : null,
            'content' => $footerHtml !== '' ? $footerHtml : null,
            'tips_table_name' => $title,
            'tips_button_name' => $title,
            'sport' => TipSportOptions::DEFAULT,
            'status' => 'published',
            'date' => Carbon::now()->toDateString(),
        ];

        if ($record->is_system) {
            $payload = array_diff_key($payload, array_flip(['slug', 'prediction_slug', 'cat_type']));
        }

        if ($dry) {
            $this->line('UPDATE  slug=homeslug (home)  from homepage.json  title='.mb_substr($title, 0, 60).'…');

            return true;
        }

        $record->fill($payload);
        $record->save();

        return true;
    }

    private function metaDescriptionFromLegacyMeta(string $metaHtml): string
    {
        if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']*)["\']/i', $metaHtml, $m)) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if (preg_match('/<meta\s+property=["\']og:description["\']\s+content=["\']([^"\']*)["\']/i', $metaHtml, $m)) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return '';
    }

    private function metaKeywordsFromLegacyMeta(string $metaHtml, string $fallbackTitle): string
    {
        if (preg_match('/<meta\s+name=["\']keywords["\']\s+content=["\']([^"\']*)["\']/i', $metaHtml, $m)) {
            return html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if (preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\']([^"\']*)["\']/i', $metaHtml, $m)) {
            return html_entity_decode(trim(strip_tags($m[1])), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return trim($fallbackTitle);
    }

    /**
     * @return array{0: string, 1: string} Plain-text headings (max 555 chars each, Filament limit).
     */
    private function headingsFromJsonTitleAndFooter(
        string $jsonOrListTitle,
        string $footerHtml,
        string $listTitleFallback,
        string $metaDescription
    ): array {
        $source = trim($jsonOrListTitle) !== '' ? trim($jsonOrListTitle) : trim($listTitleFallback);
        $segments = array_values(array_filter(
            array_map('trim', explode('|', $source)),
            static fn (string $s): bool => $s !== ''
        ));

        $head1 = $segments[0] ?? $source;
        $head2 = $segments[1] ?? $this->firstPlainHeadingFromFooter($footerHtml)
            ?? ($metaDescription !== '' ? mb_substr(trim(strip_tags($metaDescription)), 0, 400) : null)
            ?? $head1;

        return [
            mb_substr($head1, 0, 555),
            mb_substr($head2, 0, 555),
        ];
    }

    private function firstPlainHeadingFromFooter(string $html): ?string
    {
        if ($html === '') {
            return null;
        }

        if (preg_match_all('/<h[23][^>]*>(.*?)<\/h[23]>/is', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $t = trim(html_entity_decode(strip_tags($match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if ($t !== '') {
                    return $t;
                }
            }
        }

        return null;
    }
}
