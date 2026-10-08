<?php

namespace App\Support;

use App\Filament\Resources\FixtureResource;

/**
 * Option lists for the fixture Quick Edit modal — keyed by `predictions.type` / tip category prediction key.
 */
final class TipQuickPickOptions
{
    /**
     * @return array<string, string> value => label for HTML <option>
     */
    public static function forPredictionKey(string $predictionKey): array
    {
        $k = strtolower(trim($predictionKey));
        if ($k === '') {
            return [];
        }

        // Legacy: `cat_type` / key `home` — same curated list as homepage Quick Edit.
        if ($k === 'home') {
            return PredictionTipOptions::basicHomeQuickPickOptions();
        }

        $curated = PredictionTipOptions::forQuickPickCategory($k);
        if ($curated !== null) {
            return $curated;
        }

        if (in_array($k, ['free', 'banker', 'acca', 'super_single', 'sure_2_odds', 'sure_3_odds', 'sure_5_odds', 'sure_10_odds'], true)) {
            return PredictionTipOptions::basicHomeQuickPickOptions();
        }

        return match ($k) {
            'home_win' => ['1' => 'Home win'],
            'away_win' => ['2' => 'Away win'],
            'draw' => ['x' => 'Draw'],
            'double_chance' => [
                '12' => 'Home Win or Away Win',
                '1x' => 'Home Win or Draw',
                'x2' => 'Away Win or Draw',
            ],
            'btts' => ['yes' => 'BTTS Yes', 'no' => 'BTTS No'],
            'weh' => ['hweh' => 'Home Wins Either Half', 'aweh' => 'Away Wins Either Half'],
            'dnd' => ['dnd1' => 'Draw No Bet (Home)', 'dnd2' => 'Draw No Bet (Away)'],
            'home_1_5_goals' => ['HTu1.5' => 'Home Under 1.5', 'HTo1.5' => 'Home Over 1.5'],
            'away_1_5_goals' => ['ATu1.5' => 'Away Under 1.5', 'ATo1.5' => 'Away Over 1.5'],
            'correct_score' => FixtureResource::predictionOptions(),
            'ht_ft' => array_intersect_key(FixtureResource::predictionOptions(), array_flip([
                '1/1', '2/2', 'X/1', '1/2', '2/1', 'X/2', 'X/X', '1/X', '2/X',
            ])),
            'corners' => PredictionTipOptions::basicHomeQuickPickOptions(),
            'cards' => PredictionTipOptions::basicHomeQuickPickOptions(),
            'free' => PredictionTipOptions::basicHomeQuickPickOptions(),
            default => self::goalLineOrFallback($k, PredictionTipOptions::basicHomeQuickPickOptions()),
        };
    }

    /**
     * @param  array<string, string>  $all
     * @return array<string, string>
     */
    protected static function goalLineOrFallback(string $k, array $all): array
    {
        if (preg_match('/^(\d+)_(\d+)_goal$/', $k, $m) === 1) {
            $line = $m[1].'.'.$m[2];

            return [
                '-'.$line => 'Under '.$line,
                '+'.$line => 'Over '.$line,
            ];
        }

        return $all;
    }
}
