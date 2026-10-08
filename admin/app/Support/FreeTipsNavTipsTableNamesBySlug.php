<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Canonical {@see game_cats.slug} → {@code tips_table_name} for header / nav (same map as {@see new_front}).
 */
final class FreeTipsNavTipsTableNamesBySlug
{
    /**
     * @var array<string, string>
     */
    public const SLUG_TO_TIPS_TABLE_NAME = [
        'double-chance-predictions' => 'Double Chance Predictions',
        'over-1-5-goals-predictions' => 'Over 1.5 Goals Predictions',
        'over-2-5-goals-predictions' => 'Over 2.5 Goals Predictions',
        'both-team-to-score-gg-btts' => 'BTTS/GG',
        'btts_gg' => 'BTTS/GG',
        'half-time-full-time' => 'Over 0.5 Half Time (HT) Predictions',
        'over_05_halftime' => 'Over 0.5 Half Time (HT) Predictions',
        '0_5_ht' => 'Over 0.5 Half Time (HT) Predictions',
        'draw-prediction' => 'Draws Predictions',
        'draws' => 'Draws Predictions',
        'home-win-predictions' => 'Home Win Predictions',
        'under-3-5-goals-predictions' => 'Under 3.5 Goals Predictions',
        '3_5_goals' => 'Under 3.5 Goals Predictions',
        'away-win-predictions' => 'Away Win Prediction',
        'sure-banker-of-the-day' => 'Banker',
        'banker_of_the_day' => 'Banker',
    ];

    public static function applyToConnection(string $conn): int
    {
        try {
            if (! Schema::connection($conn)->hasTable('game_cats')) {
                return 0;
            }
            if (! Schema::connection($conn)->hasColumn('game_cats', 'tips_table_name')) {
                return 0;
            }
        } catch (Throwable) {
            return 0;
        }

        $total = 0;
        foreach (self::SLUG_TO_TIPS_TABLE_NAME as $slug => $name) {
            $slug = trim((string) $slug, '/');
            if ($slug === '') {
                continue;
            }
            $total += DB::connection($conn)->table('game_cats')
                ->where('slug', $slug)
                ->update(['tips_table_name' => $name]);
        }

        return $total;
    }
}
