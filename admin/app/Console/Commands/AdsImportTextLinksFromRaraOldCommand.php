<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdsImportTextLinksFromRaraOldCommand extends Command
{
    protected $signature = 'ads:import-text-links-from-rara-old
        {--dry-run : Show counts only}
        {--no-clear : Do not remove existing text header/footer ads before import}';

    protected $description = 'Import rara_old.ads rows where position is text and location is header or footer into ads';

    public function handle(): int
    {
        $source = 'rara_old';

        try {
            DB::connection($source)->getPdo();
        } catch (\Throwable $e) {
            $this->error("Cannot connect to [{$source}]: ".$e->getMessage());

            return self::FAILURE;
        }

        $query = DB::connection($source)->table('ads')
            ->whereRaw('LOWER(TRIM(position)) = ?', ['text'])
            ->whereRaw('LOWER(TRIM(location)) IN (?, ?)', ['header', 'footer'])
            ->whereNotNull('website')
            ->where('website', '!=', '')
            ->orderByRaw("CASE WHEN LOWER(TRIM(location)) = 'header' THEN 0 ELSE 1 END")
            ->orderBy('id');

        $count = (clone $query)->count();
        $this->info("Legacy text header/footer ads with URL: {$count}");

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        if (! $this->option('no-clear')) {
            Ad::query()
                ->where('type', 'text')
                ->where(function ($q): void {
                    $q->whereRaw('LOWER(COALESCE(name, "")) IN (?, ?)', ['header', 'footer'])
                        ->orWhereRaw('LOWER(COALESCE(location, "")) IN (?, ?)', ['header', 'footer']);
                })
                ->delete();
            $this->info('Removed existing text ads for header/footer slots.');
        }

        $sortHeader = 0;
        $sortFooter = 0;
        $inserted = 0;

        foreach ($query->cursor() as $row) {
            $loc = strtolower(trim((string) ($row->location ?? '')));
            if (! in_array($loc, ['header', 'footer'], true)) {
                continue;
            }

            $link = trim((string) ($row->website ?? ''));
            if ($link === '') {
                continue;
            }
            if (! Str::startsWith($link, ['http://', 'https://'])) {
                $link = 'https://'.ltrim($link, '/');
            }

            $anchor = trim((string) ($row->description ?? ''));
            if ($anchor === '') {
                $anchor = parse_url($link, PHP_URL_HOST) ?: $link;
            }
            $anchor = Str::limit($anchor, 250, '');
            $expiry = $this->normalizeExpiry($row->expiry ?? null);
            $isExpired = $expiry !== null && Carbon::parse($expiry)->lt(Carbon::now());

            Ad::query()->create([
                'name' => $loc,
                'location' => $loc,
                'type' => 'text',
                'status' => $isExpired ? 'inactive' : 'active',
                'link' => $link,
                'description' => $anchor,
                'sort_order' => $loc === 'header' ? $sortHeader++ : $sortFooter++,
                'image' => null,
                'code' => null,
                'company' => null,
                'contact' => $this->nullableText($row->contact ?? null),
                'comment' => $this->nullableText($row->comment ?? null),
                'expiry' => $expiry,
            ]);
            $inserted++;
        }

        $this->info("Inserted {$inserted} ad row(s).");

        return self::SUCCESS;
    }

    private function normalizeExpiry(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }

    private function nullableText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }
}
