<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Canonical public URL path segments for tip categories (game_cats.slug),
 * aligned with production sitemap paths. Internal prediction_slug / cat_type
 * stay unchanged so predictions pipelines keep working.
 */
final class TipCategoryPublicPathSlugs
{
    /**
     * Public path slug (no leading slash) => internal prediction key.
     *
     * @var array<string, string>
     */
    public const PATH_TO_PREDICTION_KEY = [
        'btts_gg' => 'btts',
        'double_chance' => 'double_chance',
        '0_5_ht' => '0_5_goal',
        'over_15_goals' => '1_5_goal',
        'over_25_goals' => '2_5_goal',
        '3_5_goals' => '3_5_goal',
        'draw_no_bet' => 'dnb',
        'handicap' => 'handicap',
        'draws' => 'draw',
        'win_eithe_half' => 'weh',
        'win_either_half' => 'weh',
        'banker_of_the_day' => 'banker',
    ];

    /**
     * Internal prediction key => canonical public path slug for game_cats.slug.
     * Keys that share one public slug (dnb/dnd) are listed separately in {@see syncSlugRows()}.
     *
     * @return array<string, string>
     */
    public static function publicPathByPredictionKey(): array
    {
        $legacy = [
            'btts' => 'btts_gg',
            'double_chance' => 'double_chance',
            '0_5_goal' => '0_5_ht',
            '1_5_goal' => 'over_15_goals',
            '2_5_goal' => 'over_25_goals',
            '3_5_goal' => '3_5_goals',
            'dnb' => 'draw_no_bet',
            'dnd' => 'draw_no_bet',
            'handicap' => 'handicap',
            'draw' => 'draws',
            'weh' => 'win_eithe_half',
            'banker' => 'banker_of_the_day',
        ];

        $fromNiceFront = [];
        foreach (NiceFrontTipCategoryPageDefinitions::pages() as $row) {
            $fromNiceFront[$row['prediction_slug']] = $row['path_slug'];
        }

        return array_merge($legacy, $fromNiceFront);
    }

    public static function predictionKeyFromPublicPath(string $pathSlug): string
    {
        $pathSlug = strtolower(trim($pathSlug));
        $map = self::PATH_TO_PREDICTION_KEY;
        foreach (NiceFrontTipCategoryPageDefinitions::pages() as $row) {
            $map[$row['path_slug']] = $row['prediction_slug'];
        }

        return $map[$pathSlug] ?? str_replace('-', '_', $pathSlug);
    }

    /**
     * Apply slug updates using DB facade (bypasses GameCat model guards on is_system).
     */
    public static function syncSlugRows(): int
    {
        if (! Schema::hasTable('game_cats')) {
            return 0;
        }

        $updated = 0;
        foreach (self::publicPathByPredictionKey() as $predictionKey => $publicSlug) {
            $affected = DB::table('game_cats')
                ->where(function ($q) use ($predictionKey): void {
                    $q->where('prediction_slug', $predictionKey)
                        ->orWhere('cat_type', $predictionKey);
                })
                ->update(['slug' => $publicSlug]);
            $updated += $affected;
        }

        return $updated;
    }
}
