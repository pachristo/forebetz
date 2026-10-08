<?php

namespace App\Console\Commands;

use App\Services\LegalPagesFromNiceFrontJsonSyncer;
use Illuminate\Console\Command;

/**
 * Seeds / updates {@see \App\Models\SeoPage} legal rows from the same JSON files the public site
 * loads via {@see \App\Http\Controllers\TipsCategoryController::loadCategoryJson}
 * (nice_front/admin/public/*.json), plus the fixed refund copy used on StaticSitePage.
 */
class SyncLegalPagesFromNiceFrontJsonCommand extends Command
{
    protected $signature = 'legal-pages:sync-from-nice-front-json
        {--admin-public= : Absolute path to admin/public (default: ../admin/public next to new_admin)}';

    protected $description = 'Upsert legal SeoPage rows (title, meta_keywords, meta_description, content) from Nice front JSON + fixed refund text';

    public function handle(LegalPagesFromNiceFrontJsonSyncer $syncer): int
    {
        $result = $syncer->sync(trim((string) $this->option('admin-public')) ?: null);
        foreach ($result['messages'] as $line) {
            str_contains($line, 'Skipped') ? $this->warn($line) : $this->info($line);
        }
        if (! $result['ok']) {
            $this->error($result['error'] ?? 'Sync failed.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->comment('Edit under Filament: Legal & site pages.');

        return self::SUCCESS;
    }
}
