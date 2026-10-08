<?php

namespace App\Support;

use App\Models\GameCat;
use App\Models\PlanCategory;
use App\Models\Prediction;
use Illuminate\Support\Collection;

/**
 * Builds Filament filter options for the Predictions list: only `predictions.type`
 * values that exist in the DB, labelled from tip categories, plan categories, then fallbacks.
 */
final class PredictionResourceFilterOptions
{
    /**
     * @return array<string, array<string, string>> Group title => [ type => label ]
     */
    public static function groupedBySource(): array
    {
        $types = self::distinctTypes();

        if ($types->isEmpty()) {
            return [];
        }

        $titleByTipKey = self::tipCategoryTitleByPredictionKey();
        $titleByPlanType = self::planCategoryTitleByTypeKey();

        $fallback = self::fallbackLabels();

        $tip = [];
        $vip = [];
        $other = [];

        foreach ($types as $type) {
            $label = self::resolveLabel((string) $type, $titleByTipKey, $titleByPlanType, $fallback);

            if (preg_match('/^v\d+$/', (string) $type) === 1) {
                $vip[$type] = $label;

                continue;
            }

            if (isset($titleByTipKey[$type])) {
                $tip[$type] = $label;

                continue;
            }

            // Distinct type not mapped to a tip category row — still show under Tip markets if key is a known preset.
            if (in_array($type, TipCategoryPredictionPresets::presetSlugs(), true)) {
                $tip[$type] = $label;

                continue;
            }

            $other[$type] = $label;
        }

        $out = [];
        if ($tip !== []) {
            ksort($tip);
            $out['Tip categories'] = $tip;
        }
        if ($vip !== []) {
            ksort($vip);
            $out['Plan / VIP'] = $vip;
        }
        if ($other !== []) {
            ksort($other);
            $out['Other'] = $other;
        }

        return $out;
    }

    /**
     * Flat [ type => label ] (merge of all groups), for non-grouped UIs.
     *
     * @return array<string, string>
     */
    public static function flat(): array
    {
        $flat = [];
        foreach (self::groupedBySource() as $group) {
            foreach ($group as $type => $label) {
                $flat[$type] = $label;
            }
        }

        return $flat;
    }

    /**
     * @return Collection<int, string>
     */
    protected static function distinctTypes(): Collection
    {
        try {
            return Prediction::query()
                ->whereNotNull('type')
                ->where('type', '!=', '')
                ->distinct()
                ->orderBy('type')
                ->pluck('type');
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * @return array<string, string> prediction key => tip category title
     */
    protected static function tipCategoryTitleByPredictionKey(): array
    {
        try {
            $rows = GameCat::query()->forQuickPickModal()->orderBy('title')->get();
        } catch (\Throwable) {
            return [];
        }

        $map = [];
        foreach ($rows as $cat) {
            $k = $cat->predictionTypeKey();
            if ($k === '') {
                continue;
            }
            if (! isset($map[$k])) {
                $map[$k] = (string) ($cat->title ?? $k);
            }
        }

        return $map;
    }

    /**
     * @return array<string, string> e.g. v3 => Plan name
     */
    protected static function planCategoryTitleByTypeKey(): array
    {
        try {
            $plans = PlanCategory::query()->orderBy('id')->get();
        } catch (\Throwable) {
            return [];
        }

        $map = [];
        foreach ($plans as $p) {
            $id = $p->id ?? null;
            if ($id === null) {
                continue;
            }
            $map['v'.$id] = trim((string) ($p->name ?? $p->title ?? ('Plan '.$id)));
        }

        return $map;
    }

    /**
     * @param  array<string, string>  $titleByTipKey
     * @param  array<string, string>  $titleByPlanType
     * @param  array<string, string>  $fallback
     */
    protected static function resolveLabel(string $type, array $titleByTipKey, array $titleByPlanType, array $fallback): string
    {
        if (isset($titleByTipKey[$type])) {
            return $titleByTipKey[$type];
        }
        if (isset($titleByPlanType[$type])) {
            return $titleByPlanType[$type];
        }
        if (isset($fallback[$type])) {
            return $fallback[$type];
        }

        return ucwords(str_replace(['_', '-'], [' ', ' '], $type));
    }

    /**
     * @return array<string, string>
     */
    protected static function fallbackLabels(): array
    {
        return [
            '0_5_goal' => 'O/U 0.5 Goals',
            '1_5_goal' => '1.5 Goals',
            '2_5_goal' => '2.5 Goals',
            '3_5_goal' => '3.5 Goals',
            '4_5_goal' => '4.5 Goals',
            'acca' => 'Acca tips',
            'away_win' => 'Away Team to Win',
            'btts' => 'Both Teams to Score',
            'dnd' => 'Draw No Bet',
            'double_chance' => 'Double Chance',
            'draw' => 'Draw',
            'free' => 'Free Prediction',
            'home_win' => 'Home Team to Win',
            'super_single' => 'Super Single',
            'banker' => 'Banker of the Day',
            'weh' => 'Win Either Half',
            'sure_2_odds' => 'Sure 2 Odds',
            'sure_3_odds' => 'Sure 3 Odds',
            'sure_5_odds' => 'Sure 5 Odds',
            'sure_10_odds' => 'Sure 10 Odds',
            'home_1_5_goals' => 'Home 1.5 Goals',
            'away_1_5_goals' => 'Away 1.5 Goals',
            'correct_score' => 'Correct Score',
            'ht_ft' => 'Half time / Full time',
            'corners' => 'Corners',
            'cards' => 'Cards / Bookings',
        ];
    }
}
