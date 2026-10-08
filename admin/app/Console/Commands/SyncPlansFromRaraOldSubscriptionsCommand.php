<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\PlanCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPlansFromRaraOldSubscriptionsCommand extends Command
{
    protected $signature = 'plans:sync-from-rara-old-subscriptions
        {--dry-run : Show what would change without writing}';

    protected $description = 'Read rara_old.subscriptions (status=0 only) and upsert plan_categories + plans';

    public function handle(): int
    {
        $source = 'rara_old';

        try {
            DB::connection($source)->getPdo();
        } catch (\Throwable $e) {
            $this->error("Cannot connect to [{$source}]: ".$e->getMessage());

            return self::FAILURE;
        }

        $rows = DB::connection($source)->table('subscriptions')
            ->where('status', 0)
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            $this->warn('No subscriptions with status=0 in rara_old.');

            return self::SUCCESS;
        }

        $this->info('Found '.$rows->count().' legacy subscription row(s) with status=0.');

        foreach ($rows as $row) {
            $categoryName = trim((string) ($row->category ?? ''));
            if ($categoryName === '') {
                $this->warn('Skipping row id='.($row->id ?? '?').' (empty category).');

                continue;
            }

            $variation = $this->normalizeVariation((string) ($row->accessTime ?? $row->planName ?? ''));
            if ($variation === '') {
                $this->warn('Skipping row id='.($row->id ?? '?').' (empty accessTime/planName).');

                continue;
            }

            $benefits = $this->nullableText($row->planBenefits ?? null);
            $planDisplayName = trim((string) ($row->planName ?? '')) ?: $variation;

            $payload = [
                'title' => $categoryName,
                'benefits' => $benefits,
            ];

            if ($this->option('dry-run')) {
                $this->line("[dry-run] PlanCategory name={$categoryName} | Plan variation={$variation} | {$planDisplayName}");

                continue;
            }

            $category = PlanCategory::query()->updateOrCreate(
                ['name' => $categoryName],
                $payload,
            );

            $planData = [
                'name' => $planDisplayName,
                'price_ngn' => $this->toDecimal($row->nairaPrice ?? null),
                'price_kes' => $this->toDecimal($row->keshPrice ?? null),
                'price_usd' => $this->toDecimal($row->dollarPrice ?? null),
                'price_ghs' => $this->toDecimal($row->cedPrice ?? null),
                'price_ugx' => $this->nullableStringPrice($row->ugxPrice ?? null),
                'price_tzs' => $this->toDecimal($row->tzsPrice ?? null),
                'price_zar' => $this->toDecimal($row->zarPrice ?? null),
                'price_zmw' => $this->nullableStringPrice($row->zmwPrice ?? null),
                'notes' => $benefits,
            ];

            Plan::query()->updateOrCreate(
                [
                    'plan_category_id' => $category->id,
                    'variation' => $variation,
                ],
                $planData,
            );

            $this->info("Synced: {$categoryName} → plan variation \"{$variation}\" (legacy id {$row->id}).");
        }

        if ($this->option('dry-run')) {
            $this->warn('Dry run complete — no writes.');
        } else {
            $this->info('Done.');
        }

        return self::SUCCESS;
    }

    /**
     * Carbon::add() expects strings like "1 month", "30 days".
     */
    private function normalizeVariation(string $raw): string
    {
        $s = strtolower(trim($raw));
        if ($s === '') {
            return '';
        }

        return preg_replace('/\s+/', ' ', $s) ?? $s;
    }

    private function nullableText(mixed $v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }

        return (string) $v;
    }

    private function nullableStringPrice(mixed $v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }

        return (string) $v;
    }

    private function toDecimal(mixed $v): ?float
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_numeric($v)) {
            return round((float) $v, 2);
        }
        $clean = preg_replace('/[^\d.]/', '', (string) $v);

        return $clean !== '' && is_numeric($clean) ? round((float) $clean, 2) : null;
    }
}
