<?php

namespace App\Console\Commands;

use App\Services\LegalPagesFromNiceFrontJsonSyncer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ImportSeoPagesFromNiceVipCommand extends Command
{
    protected $signature = 'seo-pages:import-from-nice-vip
        {--source=nice_vip : Source DB connection (e.g. nice_vip, winsureodds_old)}
        {--dry-run : Show counts only, no writes}
        {--no-clear : Append without truncating target (still resolves duplicate slugs by appending -id)}
        {--force : Skip confirmation when truncating target table}
        {--skip-legal-json : Do not re-upsert About/Privacy/Terms/Disclaimer/Refund/Partners from admin/public JSON after import}';

    protected $description = 'Truncate target seo_pages (optional), then copy rows from a legacy source connection (nice_vip or winsureodds_old) with column mapping. By default, then re-imports legal & site pages from admin/public JSON.';

    public function handle(LegalPagesFromNiceFrontJsonSyncer $legalPagesFromNiceFrontJsonSyncer): int
    {
        $source = (string) $this->option('source');
        if (! array_key_exists($source, config('database.connections', []))) {
            $this->error("Unknown database connection [{$source}].");

            return self::FAILURE;
        }

        try {
            DB::connection($source)->getPdo();
        } catch (\Throwable $e) {
            $this->error("Cannot connect to [{$source}]: ".$e->getMessage());

            return self::FAILURE;
        }

        if (! Schema::hasTable('seo_pages')) {
            $this->error('Target database has no seo_pages table.');

            return self::FAILURE;
        }

        $total = (int) DB::connection($source)->table('seo_pages')->count();
        $this->info("Source [{$source}] seo_pages rows: {$total}. Target: [".config('database.default').'] '.config('database.connections.'.config('database.default').'.database').'.');

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database changes.');

            return self::SUCCESS;
        }

        if (! $this->option('no-clear')) {
            if (! $this->option('force') && ! $this->confirm('This will DELETE all rows in seo_pages on the target database. Continue?', true)) {
                $this->warn('Aborted.');

                return self::FAILURE;
            }

            Schema::disableForeignKeyConstraints();
            try {
                DB::table('seo_pages')->truncate();
            } finally {
                Schema::enableForeignKeyConstraints();
            }
            $this->info('Target seo_pages truncated.');
        }

        $now = now()->format('Y-m-d H:i:s');
        $imported = 0;
        $seenSlugs = [];
        if ($this->option('no-clear')) {
            foreach (DB::table('seo_pages')->pluck('slug') as $existing) {
                $seenSlugs[(string) $existing] = true;
            }
        }

        DB::connection($source)->table('seo_pages')->orderBy('id')->chunkById(200, function ($rows) use (&$imported, &$seenSlugs, $now): void {
            foreach ($rows as $row) {
                $slug = $this->uniqueSlug($this->normalizeSlug((string) ($row->slug ?? ''), (int) $row->id), $seenSlugs);
                $titleRaw = trim((string) ($row->title ?? ''));
                $title = mb_substr($titleRaw !== '' ? $titleRaw : Str::headline(str_replace('-', ' ', $slug)), 0, 255);
                if ($title === '') {
                    $title = 'Page '.$row->id;
                }

                $footer = (string) ($row->footer ?? '');
                $investment = (string) ($row->investment ?? '');
                $content = $this->buildContent($footer, $investment);

                DB::table('seo_pages')->insert([
                    'creator' => null,
                    'title' => $title,
                    'slug' => $slug,
                    'category' => 'Imported',
                    'content' => $content !== '' ? $content : null,
                    'head1' => $this->nullableText($row->head1 ?? null),
                    'head2' => $this->nullableText($row->head2 ?? null),
                    'head3' => null,
                    'display_image' => null,
                    'status' => 'published',
                    'date' => $now,
                    'likes' => 0,
                    'meta_keywords' => $this->nullableText($row->meta_keywords ?? null),
                    'meta_description' => $this->nullableText($row->meta_description ?? null),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $imported++;
            }
        }, 'id');

        $this->info("Imported: {$imported}.");

        if (! $this->option('skip-legal-json')) {
            $legal = $legalPagesFromNiceFrontJsonSyncer->sync();
            if ($legal['ok']) {
                $this->newLine();
                $this->info('Legal & site pages (public /about, /privacy-policy, /refund-policy, /terms-and-condition, /disclaimer, /partners) synced from admin/public JSON:');
                foreach ($legal['messages'] as $line) {
                    $this->line('  '.$line);
                }
            } else {
                $this->newLine();
                $this->warn('Legal pages JSON sync failed: '.($legal['error'] ?? 'unknown').' — run `php artisan legal-pages:sync-from-nice-front-json` after fixing paths.');
            }
        }

        return self::SUCCESS;
    }

    private function normalizeSlug(string $slug, int $sourceId): string
    {
        $slug = trim($slug);
        if ($slug === '') {
            return 'page-'.$sourceId;
        }

        return Str::slug($slug) ?: 'page-'.$sourceId;
    }

    /**
     * @param  array<string, true>  $seen
     */
    private function uniqueSlug(string $base, array &$seen): string
    {
        $slug = mb_substr($base, 0, 255);
        $candidate = $slug;
        $n = 2;
        while (isset($seen[$candidate])) {
            $suffix = '-'.$n;
            $candidate = mb_substr($slug, 0, 255 - mb_strlen($suffix)).$suffix;
            $n++;
        }
        $seen[$candidate] = true;

        return $candidate;
    }

    private function buildContent(string $footer, string $investment): string
    {
        $parts = [];
        if (trim($footer) !== '') {
            $parts[] = $footer;
        }
        if (trim($investment) !== '') {
            $parts[] = '<div class="seo-page-investment">'.$investment.'</div>';
        }

        return implode("\n\n", $parts);
    }

    private function nullableText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = is_string($value) ? $value : (string) $value;

        return trim($s) === '' ? null : $s;
    }
}
