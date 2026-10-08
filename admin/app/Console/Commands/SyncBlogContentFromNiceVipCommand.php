<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Copies {@code content} from {@code nice_vip.blogs} into {@code new_nice.blogs}
 * where slugs match (case-insensitive). Source rows are not modified.
 */
class SyncBlogContentFromNiceVipCommand extends Command
{
    protected $signature = 'blogs:sync-content-from-nice-vip
        {--source=nice_vip : Source connection (nice_vip.blogs)}
        {--target= : Target connection (default: app DB / new_nice)}
        {--dry-run : Show match counts only; no writes}
        {--force : Skip confirmation}
        {--only-empty : Only update target rows whose content is null or blank}';

    protected $description = 'Update new_nice.blogs.content from nice_vip.blogs where slug matches (case-insensitive)';

    public function handle(): int
    {
        $source = (string) $this->option('source');
        $target = (string) ($this->option('target') ?: config('database.default'));

        try {
            DB::connection($source)->getPdo();
            DB::connection($target)->getPdo();
        } catch (\Throwable $e) {
            $this->error('Database connection failed: '.$e->getMessage());

            return self::FAILURE;
        }

        foreach ([$source => 'source', $target => 'target'] as $conn => $label) {
            if (! Schema::connection($conn)->hasTable('blogs')) {
                $this->error("[{$label}] connection [{$conn}] has no `blogs` table.");

                return self::FAILURE;
            }
            if (! Schema::connection($conn)->hasColumn('blogs', 'content')
                || ! Schema::connection($conn)->hasColumn('blogs', 'slug')) {
                $this->error("[{$label}] connection [{$conn}] `blogs` is missing slug or content column.");

                return self::FAILURE;
            }
        }

        $sourceDb = DB::connection($source)->getDatabaseName();
        $targetDb = DB::connection($target)->getDatabaseName();

        /** @var array<string, string> $contentBySlugKey slug_key => content (latest id wins on source) */
        $contentBySlugKey = [];
        $sourceDupes = 0;
        $sourceEmpty = 0;

        DB::connection($source)->table('blogs')
            ->select(['id', 'slug', 'content'])
            ->whereNotNull('slug')
            ->whereRaw("NULLIF(TRIM(slug), '') IS NOT NULL")
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (&$contentBySlugKey, &$sourceDupes, &$sourceEmpty): void {
                foreach ($rows as $row) {
                    $key = Str::lower(trim((string) $row->slug));
                    if ($key === '') {
                        continue;
                    }
                    $content = $row->content;
                    if ($content === null || trim((string) $content) === '') {
                        $sourceEmpty++;

                        continue;
                    }
                    if (isset($contentBySlugKey[$key])) {
                        $sourceDupes++;
                    }
                    $contentBySlugKey[$key] = (string) $content;
                }
            });

        $targetTotal = (int) DB::connection($target)->table('blogs')->count();
        $wouldUpdate = 0;
        $unchanged = 0;
        $noSource = 0;
        $skippedEmptyTarget = 0;

        $onlyEmpty = (bool) $this->option('only-empty');
        $now = now()->format('Y-m-d H:i:s');

        /** @var list<array{id: int, content: string}> $updates */
        $updates = [];

        DB::connection($target)->table('blogs')
            ->select(['id', 'slug', 'content'])
            ->whereNotNull('slug')
            ->whereRaw("NULLIF(TRIM(slug), '') IS NOT NULL")
            ->orderBy('id')
            ->chunkById(500, function ($rows) use (
                $contentBySlugKey,
                $onlyEmpty,
                &$wouldUpdate,
                &$unchanged,
                &$noSource,
                &$skippedEmptyTarget,
                &$updates
            ): void {
                foreach ($rows as $row) {
                    $key = Str::lower(trim((string) $row->slug));
                    if ($key === '') {
                        continue;
                    }

                    if (! isset($contentBySlugKey[$key])) {
                        $noSource++;

                        continue;
                    }

                    $newContent = $contentBySlugKey[$key];
                    $current = $row->content;

                    if ($onlyEmpty && $current !== null && trim((string) $current) !== '') {
                        $skippedEmptyTarget++;

                        continue;
                    }

                    if ((string) $current === $newContent) {
                        $unchanged++;

                        continue;
                    }

                    $wouldUpdate++;
                    $updates[] = [
                        'id' => (int) $row->id,
                        'content' => $newContent,
                    ];
                }
            });

        $this->info("Source [{$source}] ({$sourceDb}): ".count($contentBySlugKey).' slug(s) with non-empty content.');
        if ($sourceDupes > 0) {
            $this->comment("Source duplicate slug keys (latest id used): {$sourceDupes}");
        }
        if ($sourceEmpty > 0) {
            $this->comment("Source rows skipped (empty content): {$sourceEmpty}");
        }
        $this->info("Target [{$target}] ({$targetDb}): {$targetTotal} blog row(s).");
        $this->info("Would update content: {$wouldUpdate} (unchanged: {$unchanged}, no source slug: {$noSource}".($onlyEmpty ? ", skipped non-empty target: {$skippedEmptyTarget}" : '').').');

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database changes.');

            return self::SUCCESS;
        }

        if ($wouldUpdate === 0) {
            $this->info('Nothing to update.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Update content on {$wouldUpdate} blog row(s) in [{$target}]?", true)) {
            $this->warn('Aborted.');

            return self::FAILURE;
        }

        $updated = 0;
        DB::connection($target)->transaction(function () use ($target, $updates, $now, &$updated): void {
            foreach (array_chunk($updates, 100) as $chunk) {
                foreach ($chunk as $row) {
                    DB::connection($target)->table('blogs')
                        ->where('id', $row['id'])
                        ->update([
                            'content' => $row['content'],
                            'updated_at' => $now,
                        ]);
                    $updated++;
                }
            }
        });

        $this->info("Updated {$updated} blog row(s).");

        return self::SUCCESS;
    }
}
