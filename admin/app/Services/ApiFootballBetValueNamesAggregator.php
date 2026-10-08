<?php

namespace App\Services;

use App\Models\ApiFootballBet;
use App\Models\ApiFootballBetValueName;
use App\Models\APIodds;

class ApiFootballBetValueNamesAggregator
{
    /**
     * Scan api_odds.api_bet_values and upsert distinct (bet_id, label) rows for known bet types.
     *
     * @return int Number of new rows created (updates to existing pairs are not double-counted)
     */
    public function syncFromStoredOdds(): int
    {
        $knownBetIds = ApiFootballBet::query()->pluck('bet_id')->all();
        $known = array_fill_keys(array_map('intval', $knownBetIds), true);

        $created = 0;

        APIodds::query()
            ->whereNotNull('api_bet_values')
            ->orderBy('id')
            ->chunkById(100, function ($rows) use ($known, &$created): void {
                foreach ($rows as $row) {
                    $blob = $row->api_bet_values;
                    if (! is_array($blob) || $blob === []) {
                        continue;
                    }
                    foreach ($blob as $entry) {
                        if (! is_array($entry)) {
                            continue;
                        }
                        $betId = isset($entry['bet_id']) ? (int) $entry['bet_id'] : null;
                        if ($betId === null || ! isset($known[$betId])) {
                            continue;
                        }
                        foreach ($entry['values'] ?? [] as $v) {
                            if (! is_array($v) || ! isset($v['value'])) {
                                continue;
                            }
                            $label = mb_substr(trim((string) $v['value']), 0, 512);
                            if ($label === '') {
                                continue;
                            }
                            $model = ApiFootballBetValueName::query()->firstOrCreate(
                                ['bet_id' => $betId, 'label' => $label],
                                []
                            );
                            if ($model->wasRecentlyCreated) {
                                $created++;
                            }
                        }
                    }
                }
            });

        return $created;
    }
}
