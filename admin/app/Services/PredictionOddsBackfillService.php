<?php

namespace App\Services;

use App\Models\APIodds;
use App\Models\Prediction;
use App\Support\PredictionOddsResolver;
use Illuminate\Support\Facades\Log;

/**
 * Ensure fixture {@code api_odds} exist and fill empty {@code predictions.odds} from tip keys.
 */
class PredictionOddsBackfillService
{
    public function __construct(
        protected LoadOddService $loadOdds,
    ) {}

    public function isMissingOdds(?string $odds): bool
    {
        $odds = trim((string) $odds);
        if ($odds === '') {
            return true;
        }

        return is_numeric($odds) && (float) $odds <= 0.0;
    }

    /**
     * Load/refresh api_odds when missing or odd_data is empty.
     */
    public function ensureApiOdds(string $matchId, bool $forceRefresh = false): ?APIodds
    {
        $matchId = trim($matchId);
        if ($matchId === '') {
            return null;
        }

        $row = APIodds::query()->where('match_id', $matchId)->first();
        $oddData = PredictionOddsResolver::normalizeOddData($row?->odd_data);
        $hasUsableMap = $this->oddDataHasUsableValues($oddData);

        if ($row && $hasUsableMap && ! $forceRefresh) {
            return $row;
        }

        try {
            $this->loadOdds->refreshFromApiForMatch($matchId, true);
        } catch (\Throwable $e) {
            Log::warning('PredictionOddsBackfillService: failed to load api odds', [
                'match_id' => $matchId,
                'error' => $e->getMessage(),
            ]);
        }

        return APIodds::query()->where('match_id', $matchId)->first();
    }

    /**
     * Resolve a tip to a bookmaker odd, loading api_odds when needed.
     */
    public function resolveForTip(string $matchId, string $tips, bool $allowApiFetch = true): string
    {
        $tips = trim($tips);
        if ($tips === '' || $matchId === '') {
            return '';
        }

        $apiOdds = APIodds::query()->where('match_id', $matchId)->first();
        $resolved = PredictionOddsResolver::resolve($apiOdds?->odd_data, $tips, $apiOdds);
        if ($resolved !== '') {
            return $resolved;
        }

        if (! $allowApiFetch) {
            return '';
        }

        $apiOdds = $this->ensureApiOdds($matchId);
        if (! $apiOdds) {
            return '';
        }

        return PredictionOddsResolver::resolve($apiOdds->odd_data, $tips, $apiOdds);
    }

    /**
     * Fill empty odds on all tip rows for a fixture. Returns how many rows were updated.
     */
    public function fillMissingForMatch(string $matchId, bool $allowApiFetch = true): int
    {
        $matchId = trim($matchId);
        if ($matchId === '') {
            return 0;
        }

        $missing = Prediction::query()
            ->where('match_id', $matchId)
            ->whereNotNull('tips')
            ->where('tips', '<>', '')
            ->get()
            ->filter(fn (Prediction $p): bool => $this->isMissingOdds($p->odds ?? null));

        if ($missing->isEmpty()) {
            return 0;
        }

        $apiOdds = APIodds::query()->where('match_id', $matchId)->first();
        $fixed = $this->applyResolvedOdds($missing, $apiOdds);

        $stillMissing = Prediction::query()
            ->where('match_id', $matchId)
            ->whereNotNull('tips')
            ->where('tips', '<>', '')
            ->get()
            ->filter(fn (Prediction $p): bool => $this->isMissingOdds($p->odds ?? null));

        if ($stillMissing->isEmpty() || ! $allowApiFetch) {
            return $fixed;
        }

        $apiOdds = $this->ensureApiOdds($matchId);
        $fixed += $this->applyResolvedOdds($stillMissing, $apiOdds);

        return $fixed;
    }

    /**
     * @param  iterable<int, Prediction>  $predictions
     */
    private function applyResolvedOdds(iterable $predictions, ?APIodds $apiOdds): int
    {
        $fixed = 0;
        foreach ($predictions as $prediction) {
            $tips = trim((string) ($prediction->tips ?? ''));
            if ($tips === '') {
                continue;
            }

            $resolved = PredictionOddsResolver::resolve($apiOdds?->odd_data, $tips, $apiOdds);
            if ($resolved === '' || ! is_numeric($resolved)) {
                continue;
            }

            $prediction->update([
                'odds' => $resolved,
                'updated_at' => now(),
            ]);
            $fixed++;
        }

        return $fixed;
    }

    /**
     * @param  array<string, mixed>  $oddData
     */
    private function oddDataHasUsableValues(array $oddData): bool
    {
        foreach ($oddData as $value) {
            if (is_numeric($value) && (float) $value > 1.0) {
                return true;
            }
            if (is_string($value)) {
                $v = trim($value);
                if ($v !== '' && is_numeric($v) && (float) $v > 1.0) {
                    return true;
                }
            }
        }

        return false;
    }
}
