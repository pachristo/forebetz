<?php

namespace App\Support;

/**
 * Short fixture-list headings for game_cats.fixture_heading (public predictions panel h2).
 */
final class GameCatFixtureHeadings
{
    /**
     * @var array<string, string> slug or prediction/cat key → short label
     */
    public const SIMPLE_BY_KEY = [
        'homeslug' => 'Free picks',
        'home' => 'Free picks',
        'over-1-5-goals-predictions' => '1.5 Goals',
        '1_5_goal' => '1.5 Goals',
        'over-2-5-goals-predictions' => '2.5 Goals',
        '2_5_goal' => '2.5 Goals',
        'under-3-5-goals-predictions' => '3.5 Goals',
        '3_5_goal' => '3.5 Goals',
        'both-team-to-score-gg-btts' => 'Both Team to score',
        'btts_gg' => 'Both Team to score',
        'btts' => 'Both Team to score',
        'correct-score' => 'Correct score',
        'correct_score' => 'Correct score',
        'double-chance-predictions' => 'Double Chance',
        'double_chance' => 'Double Chance',
        'draw-prediction' => 'Draw',
        'draws' => 'Draw',
        'draw' => 'Draw',
        'home-win-predictions' => 'Home Win',
        'home_win' => 'Home Win',
        'away-win-predictions' => 'Away Win',
        'away_win' => 'Away Win',
        'sure-banker-of-the-day' => 'Banker',
        'banker_of_the_day' => 'Banker',
        'banker' => 'Banker',
        'half-time-full-time' => 'Over 0.5 HT',
        'over_05_halftime' => 'Over 0.5 HT',
        '0_5_ht' => 'Over 0.5 HT',
        '0_5_goal' => 'Over 0.5 HT',
        'win-either-half' => 'Win Either Half',
        'weh' => 'Win Either Half',
        'single-bets' => 'Single Bet',
        'super_single' => 'Single Bet',
        'big_odds' => 'Big Odds',
        'handicap' => 'Handicap',
        'dnd' => 'Draw No Bet',
        'acca' => 'Acca',
        'free' => 'Free picks',
    ];

    public static function forKeys(string ...$keys): ?string
    {
        foreach ($keys as $key) {
            $key = strtolower(trim($key, '/'));
            if ($key !== '' && isset(self::SIMPLE_BY_KEY[$key])) {
                return self::SIMPLE_BY_KEY[$key];
            }
        }

        return null;
    }
}
