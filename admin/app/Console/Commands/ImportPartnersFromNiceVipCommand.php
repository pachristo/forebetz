<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportPartnersFromNiceVipCommand extends Command
{
    protected $signature = 'partners:import-from-nice-vip
        {--dry-run : Show counts only, no writes}
        {--no-clear : Append without truncating (may duplicate if same data re-run)}
        {--force : Skip confirmation when truncating target table}';

    protected $description = 'Truncate new_nice.partners (optional), then copy rows from nice_vip.partners (name, link → url)';

    public function handle(): int
    {
        $source = 'nice_vip';

        try {
            DB::connection($source)->getPdo();
        } catch (\Throwable $e) {
            $this->error("Cannot connect to [{$source}]: ".$e->getMessage());

            return self::FAILURE;
        }

        if (! Schema::hasTable('partners')) {
            $this->error('Target database has no partners table.');

            return self::FAILURE;
        }

        $total = (int) DB::connection($source)->table('partners')->count();
        $targetDb = (string) config('database.connections.'.config('database.default').'.database');
        $this->info("Source [{$source}] partners rows: {$total}. Target [".config('database.default')."] {$targetDb}.");

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database changes.');

            return self::SUCCESS;
        }

        if (! $this->option('no-clear')) {
            if (! $this->option('force') && ! $this->confirm('This will DELETE all rows in partners on the target database. Continue?', true)) {
                $this->warn('Aborted.');

                return self::FAILURE;
            }

            Schema::disableForeignKeyConstraints();
            try {
                DB::table('partners')->truncate();
            } finally {
                Schema::enableForeignKeyConstraints();
            }
            $this->info('Target partners truncated.');
        }

        $now = now()->format('Y-m-d H:i:s');
        $imported = 0;
        $skipped = 0;
        $sort = 0;

        $hasLogo = Schema::hasColumn('partners', 'logo');
        $hasClicks = Schema::hasColumn('partners', 'clicks');

        DB::connection($source)->table('partners')->orderBy('id')->chunkById(200, function ($rows) use ($now, &$imported, &$skipped, &$sort, $hasLogo, $hasClicks): void {
            foreach ($rows as $row) {
                $name = trim((string) ($row->name ?? ''));
                $url = trim((string) ($row->link ?? ''));
                if ($url === '') {
                    $skipped++;

                    continue;
                }
                if ($name === '') {
                    $name = 'Partner #'.$row->id;
                }
                $name = mb_substr($name, 0, 255);
                $url = mb_substr($url, 0, 255);

                $insert = [
                    'name' => $name,
                    'url' => $url,
                    'description' => null,
                    'sort_order' => $sort,
                    'is_active' => 1,
                    'created_at' => $row->created_at ?? $now,
                    'updated_at' => $row->updated_at ?? $now,
                ];
                if ($hasClicks) {
                    $insert['clicks'] = 0;
                }
                if ($hasLogo) {
                    $insert['logo'] = null;
                }

                DB::table('partners')->insert($insert);
                $imported++;
                $sort++;
            }
        }, 'id');

        $this->info("Imported: {$imported}".($skipped > 0 ? ", skipped (empty link): {$skipped}" : '.'));

        return self::SUCCESS;
    }
}
