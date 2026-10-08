<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncPlansFromNiceVipSubscriptionsCommand extends Command
{
    /**
     * Map nice_vip.subscriptions.category → existing plan_categories.id (preserves FKs, e.g. vip_recent_winnings on id 1).
     */
    private const CATEGORY_TO_PLAN_CATEGORY_ID = [
        'Gold Plan' => 1,
        'Mega Plan' => 4,
        'Investment Tip' => 5,
    ];

    protected $signature = 'plans:sync-from-nice-vip-subscriptions
        {--dry-run : Show counts only, no writes}
        {--force : Skip confirmation before truncating plans}';

    protected $description = 'Rebuild plan_categories + plans from nice_vip.subscriptions; variation is a normalized time phrase (e.g. 1 week, 1 month)';

    public function handle(): int
    {
        $source = 'nice_vip';

        try {
            DB::connection($source)->getPdo();
        } catch (\Throwable $e) {
            $this->error("Cannot connect to [{$source}]: ".$e->getMessage());

            return self::FAILURE;
        }

        if (! Schema::hasTable('plans')) {
            $this->error('Target database has no plans table.');

            return self::FAILURE;
        }

        if (! Schema::connection($source)->hasTable('subscriptions')) {
            $this->error("Source database [{$source}] has no subscriptions table.");

            return self::FAILURE;
        }

        $count = (int) DB::connection($source)->table('subscriptions')->count();
        $targetDb = (string) config('database.connections.'.config('database.default').'.database');
        $this->info("Source [{$source}] subscriptions: {$count}. Target DB: {$targetDb}.");

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database changes.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('This will REPLACE all rows in plans and update plan_categories from nice_vip.subscriptions. Continue?', true)) {
            $this->warn('Aborted.');

            return self::FAILURE;
        }

        $rows = DB::connection($source)->table('subscriptions')->orderBy('id')->get();
        if ($rows->isEmpty()) {
            $this->warn('No subscription rows in source.');

            return self::SUCCESS;
        }

        $benefitsByCategory = $this->longestBenefitsByCategory($rows);
        $this->syncPlanCategories($rows, $benefitsByCategory);
        $categoryIdMap = $this->buildCategoryIdMap($rows);

        $planRows = $this->buildPlanRowsForInsert($rows, $categoryIdMap);
        if ($planRows === []) {
            $this->warn('No plan rows to insert (no mapped categories).');

            return self::SUCCESS;
        }

        Schema::disableForeignKeyConstraints();
        try {
            DB::table('plans')->truncate();
        } finally {
            Schema::enableForeignKeyConstraints();
        }
        $this->info('Target plans truncated.');

        $now = now()->format('Y-m-d H:i:s');
        foreach ($planRows as $row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            DB::table('plans')->insert($row);
        }

        $inserted = count($planRows);
        $this->info("Inserted {$inserted} plan row(s), ordered by category then access period. Variation uses normalized time (e.g. 1 week, 1 month). Plan categories updated.");

        return self::SUCCESS;
    }

    /**
     * Build insert payloads: sorted by category tier, then access duration, then source id.
     * Variation = access time; if several rows share the same category + plan name + access time, append option index and NGN hint.
     *
     * @param  Collection<int, object>  $rows
     * @param  array<string, int>  $categoryIdMap
     * @return list<array<string, mixed>>
     */
    private function buildPlanRowsForInsert(Collection $rows, array $categoryIdMap): array
    {
        $groupSizes = [];
        foreach ($rows as $row) {
            $cat = trim((string) ($row->category ?? ''));
            if ($cat === '' || ! isset($categoryIdMap[$cat])) {
                continue;
            }
            $gKey = $this->planGroupKey($categoryIdMap[$cat], $row);
            $groupSizes[$gKey] = ($groupSizes[$gKey] ?? 0) + 1;
        }

        $ordinalInGroup = [];

        $sorted = $rows->sort(function ($a, $b): int {
            $ca = trim((string) ($a->category ?? ''));
            $cb = trim((string) ($b->category ?? ''));
            if ($this->categoryOrder($ca) !== $this->categoryOrder($cb)) {
                return $this->categoryOrder($ca) <=> $this->categoryOrder($cb);
            }
            $da = $this->durationOrder(trim((string) ($a->accessTime ?? '')));
            $db = $this->durationOrder(trim((string) ($b->accessTime ?? '')));
            if ($da !== $db) {
                return $da <=> $db;
            }

            return (int) $a->id <=> (int) $b->id;
        })->values();

        $out = [];
        foreach ($sorted as $row) {
            $cat = trim((string) ($row->category ?? ''));
            if ($cat === '' || ! isset($categoryIdMap[$cat])) {
                continue;
            }

            $planCategoryId = $categoryIdMap[$cat];
            $planName = trim((string) ($row->planName ?? '')) ?: 'Plan';
            $planName = mb_substr($planName, 0, 255);
            $access = trim((string) ($row->accessTime ?? ''));
            $accessLabel = $access !== '' ? $access : '—';

            $gKey = $this->planGroupKey($planCategoryId, $row);
            $totalInGroup = $groupSizes[$gKey] ?? 1;
            $ordinalInGroup[$gKey] = ($ordinalInGroup[$gKey] ?? 0) + 1;
            $ord = $ordinalInGroup[$gKey];

            $variation = $accessLabel;
            if ($totalInGroup > 1) {
                $ngn = $this->parseDecimal($row->nairaPrice ?? null);
                $suffix = ' · '.$ord.'/'.$totalInGroup;
                if ($ngn !== null) {
                    $suffix .= ' · NGN '.number_format((float) $ngn, 0, '.', ',');
                }
                $variation = mb_substr($accessLabel.$suffix, 0, 255);
            } else {
                $variation = mb_substr($variation, 0, 255);
            }

            $out[] = [
                'plan_category_id' => $planCategoryId,
                'name' => $planName,
                'variation' => $variation,
                'price_ngn' => $this->parseDecimal($row->nairaPrice ?? null),
                'price_kes' => $this->parseDecimal($row->keshPrice ?? null),
                'price_ghs' => $this->parseDecimal($row->cedPrice ?? null),
                'price_usd' => $this->parseDecimal($row->dollarPrice ?? null),
                'price_xaf' => $this->parseDecimal($row->xafPrice ?? null),
                'price_rwf' => $this->parseDecimal($row->rwfPrice ?? null),
                'price_zar' => $this->parseDecimal($row->zarPrice ?? null),
                'price_tzs' => $this->parseDecimal($row->tzsPrice ?? null),
                'price_ugx' => $this->nullablePriceText($row->ugxPrice ?? null),
                'price_zmw' => $this->nullablePriceText($row->zmwPrice ?? null),
                'price_mwk' => $this->nullablePriceText($row->mwkPrice ?? null),
                'notes' => $this->nullableText($row->planBenefits ?? null),
            ];
        }

        return $out;
    }

    private function planGroupKey(int $planCategoryId, object $row): string
    {
        $planName = mb_strtolower(trim((string) ($row->planName ?? '')));
        $access = mb_strtolower(trim((string) ($row->accessTime ?? '')));

        return $planCategoryId.'|'.$planName.'|'.$access;
    }

    private function categoryOrder(string $category): int
    {
        return match ($category) {
            'Mega Plan' => 10,
            'Gold Plan' => 20,
            'Investment Tip' => 30,
            'Super Plan' => 40,
            default => 1000,
        };
    }

    /**
     * Order access periods: day < week < month < year.
     */
    private function durationOrder(string $accessTime): int
    {
        $a = mb_strtolower($accessTime);
        if (preg_match('/\bday\b|\bdaily\b|\b1\s*d\b/i', $a)) {
            return 5;
        }
        if (str_contains($a, 'week')) {
            return 15;
        }
        if (str_contains($a, 'month')) {
            return 25;
        }
        if (str_contains($a, 'year')) {
            return 35;
        }

        return 20;
    }

    /**
     * @param  Collection<int, object>  $rows
     * @return array<string, string>
     */
    private function longestBenefitsByCategory(Collection $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            $cat = trim((string) ($row->category ?? ''));
            $b = trim((string) ($row->planBenefits ?? ''));
            if ($cat === '' || $b === '') {
                continue;
            }
            if (! isset($out[$cat]) || mb_strlen($b) > mb_strlen($out[$cat])) {
                $out[$cat] = $b;
            }
        }

        return $out;
    }

    /**
     * @param  Collection<int, object>  $rows
     */
    private function syncPlanCategories(Collection $rows, array $benefitsByCategory): void
    {
        foreach (self::CATEGORY_TO_PLAN_CATEGORY_ID as $categoryName => $id) {
            $benefits = $benefitsByCategory[$categoryName] ?? null;
            $this->ensurePlanCategoryById((int) $id, $categoryName, $benefits);
        }

        $hasSuper = $rows->contains(fn ($r): bool => trim((string) ($r->category ?? '')) === 'Super Plan');
        if (! $hasSuper) {
            $this->bumpPlanCategoriesAutoIncrement();

            return;
        }

        $benefits = $benefitsByCategory['Super Plan'] ?? null;
        $existingId = DB::table('plan_categories')->where('name', 'Super Plan')->value('id');
        if ($existingId !== null) {
            DB::table('plan_categories')->where('id', $existingId)->update([
                'title' => 'Super Plan',
                'benefits' => $benefits,
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ]);
        } else {
            DB::table('plan_categories')->insert([
                'name' => 'Super Plan',
                'title' => 'Super Plan',
                'benefits' => $benefits,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ]);
        }

        $this->bumpPlanCategoriesAutoIncrement();
    }

    private function ensurePlanCategoryById(int $id, string $categoryName, ?string $benefits): void
    {
        $now = now()->format('Y-m-d H:i:s');
        $name = mb_substr($categoryName, 0, 255);

        if (DB::table('plan_categories')->where('id', $id)->exists()) {
            DB::table('plan_categories')->where('id', $id)->update([
                'name' => $name,
                'title' => $name,
                'benefits' => $benefits,
                'updated_at' => $now,
            ]);

            return;
        }

        DB::insert(
            'INSERT INTO plan_categories (id, name, title, benefits, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
            [$id, $name, $name, $benefits, $now, $now]
        );
    }

    private function bumpPlanCategoriesAutoIncrement(): void
    {
        $max = (int) DB::table('plan_categories')->max('id');

        DB::statement('ALTER TABLE plan_categories AUTO_INCREMENT = '.max($max + 1, 1));
    }

    /**
     * @param  Collection<int, object>  $rows
     * @return array<string, int>
     */
    private function buildCategoryIdMap(Collection $rows): array
    {
        $map = self::CATEGORY_TO_PLAN_CATEGORY_ID;

        $hasSuper = $rows->contains(fn ($r): bool => trim((string) ($r->category ?? '')) === 'Super Plan');
        if ($hasSuper) {
            $superId = DB::table('plan_categories')->where('name', 'Super Plan')->value('id');
            if ($superId === null) {
                throw new \RuntimeException('Super Plan category row missing after syncPlanCategories().');
            }
            $map['Super Plan'] = (int) $superId;
        }

        return $map;
    }

    private function parseDecimal(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = trim((string) $value);
        if ($s === '' || strtoupper($s) === 'NULL') {
            return null;
        }
        $clean = preg_replace('/[^0-9.]/', '', $s);
        if ($clean === '' || $clean === '.') {
            return null;
        }
        $n = (float) $clean;
        if ($n < 0) {
            return null;
        }

        return number_format($n, 2, '.', '');
    }

    private function nullablePriceText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = trim((string) $value);
        if ($s === '' || strtoupper($s) === 'NULL') {
            return null;
        }

        return $s;
    }

    private function nullableText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = trim((string) $value);

        return $s === '' ? null : $s;
    }
}
