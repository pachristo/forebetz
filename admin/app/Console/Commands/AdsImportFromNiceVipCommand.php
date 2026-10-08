<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdsImportFromNiceVipCommand extends Command
{
    protected $signature = 'ads:import-from-nice-vip
        {--source= : Source Laravel DB connection (default: env ADS_IMPORT_SOURCE_CONNECTION or nice_vip; use new_vip if that connection exists)}
        {--dry-run : Show counts only; no truncate, no inserts, no file copies}
        {--no-clear : Append without truncating (may duplicate)}
        {--force : Skip truncate confirmation}
        {--images-path=* : Extra directory(s) to search for legacy ad image files}';

    protected $description = 'Truncate ads on the default DB (new_nice), copy rows from source ads table (nice_vip or new_vip), copy image files into storage/app/public/ads, and mirror website/ads_image for new_front blades.';

    public function handle(): int
    {
        $source = (string) ($this->option('source') ?: env('ADS_IMPORT_SOURCE_CONNECTION', 'nice_vip'));
        $source = trim($source) !== '' ? trim($source) : 'nice_vip';

        if (! is_array(config("database.connections.{$source}"))) {
            $this->error("Unknown database connection [{$source}]. Define it in config/database.php or pass --source=nice_vip.");

            return self::FAILURE;
        }

        try {
            DB::connection($source)->getPdo();
        } catch (\Throwable $e) {
            $this->error("Cannot connect to [{$source}]: ".$e->getMessage());

            return self::FAILURE;
        }

        if (! Schema::connection($source)->hasTable('ads')) {
            $this->error("Source [{$source}] has no ads table.");

            return self::FAILURE;
        }

        if (! Schema::hasTable('ads')) {
            $this->error('Target database has no ads table.');

            return self::FAILURE;
        }

        $targetDb = (string) config('database.connections.'.config('database.default').'.database');
        $srcCount = (int) DB::connection($source)->table('ads')->count();
        $srcDb = (string) config("database.connections.{$source}.database");
        $this->info("Source [{$source}] database `{$srcDb}` ads rows: {$srcCount}. Target [".config('database.default')."] `{$targetDb}`.");

        $hasWebsite = Schema::hasColumn('ads', 'website');
        $hasAdsImage = Schema::hasColumn('ads', 'ads_image');
        if (! $hasWebsite || ! $hasAdsImage) {
            $this->warn('Target ads table is missing website and/or ads_image columns. Run: php artisan migrate');
        }

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no changes.');

            return self::SUCCESS;
        }

        if (! $this->option('no-clear')) {
            if (! $this->option('force') && ! $this->confirm("This will DELETE all rows in ads on the target database `{$targetDb}`, then import from [{$source}] `{$srcDb}`. Continue?", true)) {
                $this->warn('Aborted.');

                return self::FAILURE;
            }

            Schema::disableForeignKeyConstraints();
            try {
                DB::table('ads')->truncate();
            } finally {
                Schema::enableForeignKeyConstraints();
            }
            $this->info('Target ads truncated.');
        }

        $destDir = storage_path('app/public/ads');
        File::ensureDirectoryExists($destDir);

        $imageRoots = $this->legacyImageDirectories();

        $inserted = 0;
        $imagesCopied = 0;
        $imagesMissing = 0;

        DB::connection($source)->table('ads')->orderBy('id')->chunkById(200, function ($rows) use ($destDir, $imageRoots, $hasWebsite, $hasAdsImage, &$inserted, &$imagesCopied, &$imagesMissing): void {
            foreach ($rows as $row) {
                $position = strtolower(trim((string) ($row->position ?? '')));
                if (! in_array($position, ['image', 'code', 'text'], true)) {
                    $position = 'text';
                }

                $slot = trim((string) ($row->location ?? ''));
                if ($slot === '' && isset($row->name)) {
                    $slot = trim((string) $row->name);
                }
                if ($slot === '') {
                    $slot = 'unknown';
                }

                $website = (string) ($row->website ?? '');
                $adsImage = trim((string) ($row->ads_image ?? ''));

                $imagePath = null;
                $adsImageOut = null;

                if ($position === 'image' && $adsImage !== '') {
                    $resolved = $this->resolveLegacyImagePath($imageRoots, $adsImage);
                    if ($resolved !== null) {
                        $destName = $this->uniqueDestFilename($destDir, basename($adsImage));
                        $destFull = $destDir.DIRECTORY_SEPARATOR.$destName;
                        try {
                            File::copy($resolved, $destFull);
                            $imagesCopied++;
                            $adsImageOut = $destName;
                            $imagePath = 'ads/'.$destName;
                        } catch (\Throwable) {
                            $adsImageOut = basename($adsImage);
                            $imagePath = 'ads/'.$adsImageOut;
                            $imagesMissing++;
                        }
                    } else {
                        $adsImageOut = basename($adsImage);
                        $imagePath = 'ads/'.$adsImageOut;
                        $imagesMissing++;
                    }
                }

                $link = null;
                $code = null;
                if ($position === 'code') {
                    $code = $website !== '' ? $website : null;
                } elseif ($position === 'text') {
                    $link = $website !== '' ? $website : null;
                } else {
                    $link = $website !== '' ? $website : null;
                }

                $status = $this->normalizeStatus($row->status ?? null);
                $expiry = $this->normalizeExpiry($row->expiry ?? null);

                $payload = [
                    'image' => $imagePath,
                    'name' => $slot,
                    'description' => $row->description ?? null,
                    'link' => $link,
                    'status' => $status,
                    'location' => $slot,
                    'type' => $position,
                    'sort_order' => (int) ($row->id ?? $inserted),
                    'expiry' => $expiry,
                    'contact' => $row->contact ?? null,
                    'comment' => $row->comment ?? null,
                    'company' => $row->company ?? null,
                    'code' => $code,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ];

                if ($hasWebsite) {
                    $payload['website'] = $position === 'code' ? $code : $link;
                }
                if ($hasAdsImage) {
                    $payload['ads_image'] = $adsImageOut;
                }

                DB::table('ads')->insert($payload);
                $inserted++;
            }
        }, 'id');

        $this->info("Inserted: {$inserted}. Images copied: {$imagesCopied}. Missing source files (basename kept): {$imagesMissing}.");
        $this->comment('Ensure storage link exists: php artisan storage:link');
        $this->comment('new_front must query the same DB as new_nice for ads (or add a new_nice connection and switch blades) to see imported rows.');

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function legacyImageDirectories(): array
    {
        $paths = [];

        foreach ($this->option('images-path') as $p) {
            if (is_string($p) && $p !== '') {
                $paths[] = $p;
            }
        }

        $base = dirname(base_path());
        $grandparent = dirname($base);
        // Legacy uploads: `nice_front/admin/public/images/ads` (sibling of new_admin).
        $paths[] = $base.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'ads';
        $paths[] = $base.DIRECTORY_SEPARATOR.'new_front'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'ads';
        $paths[] = base_path('..'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'ads');
        $paths[] = base_path('..'.DIRECTORY_SEPARATOR.'new_front'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'ads');
        // Repo layouts where `admin` sits next to `nice_front` (e.g. tipskings_docker/admin).
        $paths[] = $grandparent.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'ads';

        $extra = env('ADS_LEGACY_IMAGES_PATH');
        if (is_string($extra) && $extra !== '') {
            $paths[] = $extra;
        }

        return array_values(array_unique(array_filter($paths)));
    }

    private function resolveLegacyImagePath(array $roots, string $filename): ?string
    {
        $raw = trim($filename);
        if ($raw === '') {
            return null;
        }

        if (is_file($raw)) {
            return $raw;
        }

        $normalized = str_replace('\\', '/', $raw);
        $normalized = ltrim($normalized, '/');
        if ($normalized === '' || str_contains($normalized, '..')) {
            return null;
        }

        $base = basename($normalized);
        if ($base === '' || $base === '.' || $base === '..') {
            return null;
        }

        $relative = str_replace('/', DIRECTORY_SEPARATOR, $normalized);

        foreach ($roots as $root) {
            if (! is_string($root) || $root === '') {
                continue;
            }
            $root = rtrim($root, DIRECTORY_SEPARATOR);
            $candidates = [
                $root.DIRECTORY_SEPARATOR.$base,
                $root.DIRECTORY_SEPARATOR.$relative,
            ];
            foreach ($candidates as $full) {
                if (is_file($full)) {
                    return $full;
                }
            }
        }

        return null;
    }

    private function uniqueDestFilename(string $destDir, string $basename): string
    {
        $basename = basename($basename);
        if ($basename === '' || $basename === '.' || $basename === '..') {
            $basename = 'ad.bin';
        }

        $dest = $destDir.DIRECTORY_SEPARATOR.$basename;
        if (! File::exists($dest)) {
            return $basename;
        }

        $ext = pathinfo($basename, PATHINFO_EXTENSION);
        $stem = pathinfo($basename, PATHINFO_FILENAME);
        $stem = $stem !== '' ? $stem : 'ad';

        for ($i = 1; $i < 5000; $i++) {
            $candidate = $stem.'_'.$i.($ext !== '' ? '.'.$ext : '');
            if (! File::exists($destDir.DIRECTORY_SEPARATOR.$candidate)) {
                return $candidate;
            }
        }

        return $stem.'_'.Str::random(8).($ext !== '' ? '.'.$ext : '');
    }

    private function normalizeStatus(mixed $status): string
    {
        $s = strtolower(trim((string) ($status ?? '')));
        // Legacy admin: status "0" = visible (HIDE action); non-zero = hidden.
        if ($s === '' || $s === '0' || $s === 'active') {
            return 'active';
        }

        return 'inactive';
    }

    private function normalizeExpiry(mixed $expiry): ?string
    {
        if ($expiry === null || $expiry === '') {
            return null;
        }
        try {
            return Carbon::parse($expiry)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }
}
