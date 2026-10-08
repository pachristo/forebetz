<?php

namespace App\Support;

/**
 * Tip category pages driven by {@see \App\Http\Livewire\TipsCategory} / {@see game_cats}
 * — only the free-tip routes from old/winner web.php (~308–373).
 */
final class NiceFrontTipCategoryPageDefinitions
{
    /**
     * @return list<array{route_name: string, path_slug: string, prediction_slug: string, list_title: string, seo_json: string}>
     */
    public static function pages(): array
    {
        return [
            ['route_name' => '3_5_goals', 'path_slug' => 'under-3-5-goals-predictions', 'prediction_slug' => '3_5_goal', 'list_title' => 'Under 3.5 Goals Predictions', 'seo_json' => 'sure3.json'],
            ['route_name' => '0_5_ht', 'path_slug' => 'half-time-full-time', 'prediction_slug' => '0_5_goal', 'list_title' => '0.5 Goals HT', 'seo_json' => 'ht05.json'],
            ['route_name' => '/double_chance', 'path_slug' => 'double-chance-predictions', 'prediction_slug' => 'double_chance', 'list_title' => 'Double Chance', 'seo_json' => 'dc.json'],
            ['route_name' => '/over_15_goals', 'path_slug' => 'over-1-5-goals-predictions', 'prediction_slug' => '1_5_goal', 'list_title' => '1.5 Goals', 'seo_json' => 'c15.json'],
            ['route_name' => '/over_25_goals', 'path_slug' => 'over-2-5-goals-predictions', 'prediction_slug' => '2_5_goal', 'list_title' => 'Over 2.5 Goals', 'seo_json' => 'c25.json'],
            ['route_name' => '/big_odds', 'path_slug' => 'big_odds', 'prediction_slug' => 'big_odds', 'list_title' => 'Big Odds', 'seo_json' => 's10.json'],
            ['route_name' => '/btts_gg', 'path_slug' => 'both-team-to-score-gg-btts', 'prediction_slug' => 'btts', 'list_title' => 'BTTS/GG', 'seo_json' => 'btts.json'],
            ['route_name' => '/draws', 'path_slug' => 'draw-prediction', 'prediction_slug' => 'draw', 'list_title' => 'Draw', 'seo_json' => 'draw.json'],
            ['route_name' => '/single-bets', 'path_slug' => 'single-bets', 'prediction_slug' => 'super_single', 'list_title' => 'Single Bet', 'seo_json' => 'single.json'],
            ['route_name' => '/handicap', 'path_slug' => 'handicap', 'prediction_slug' => 'handicap', 'list_title' => 'Handicap', 'seo_json' => 'single.json'],
            ['route_name' => '/banker_of_the_day', 'path_slug' => 'sure-banker-of-the-day', 'prediction_slug' => 'banker', 'list_title' => 'Banker of The Day', 'seo_json' => 'banker.json'],
            ['route_name' => '/win-ether-half', 'path_slug' => 'win-either-half', 'prediction_slug' => 'weh', 'list_title' => 'Win Either Half', 'seo_json' => 'weh.json'],
            ['route_name' => '/awin_goals', 'path_slug' => 'away-win-predictions', 'prediction_slug' => 'away_win', 'list_title' => 'Away Win', 'seo_json' => 'awin.json'],
            ['route_name' => '/hwin_goals', 'path_slug' => 'home-win-predictions', 'prediction_slug' => 'home_win', 'list_title' => 'Home Win', 'seo_json' => 'hwin.json'],
        ];
    }

    /**
     * Published tip category slugs to keep (homeslug is separate / system).
     *
     * @return list<string>
     */
    public static function keptPathSlugs(): array
    {
        return array_values(array_map(
            static fn (array $p): string => $p['path_slug'],
            self::pages()
        ));
    }
}
