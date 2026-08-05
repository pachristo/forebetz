<?php

$asset = '/assets';

$pageTitle = 'Forebetz — Football Predictions & Betting Tips';
$pageDescription = 'Forebetz offers best football predictions and soccer betting tips in different betting markets.';

$matches = [
    [
        'country' => 'Spain',
        'league' => 'La Liga',
        'home' => 'Benfica',
        'home_logo' => 'benfica.png',
        'away' => 'Club Brugge',
        'away_logo' => 'brugge.png',
        'time' => '13:00',
        'home_form' => ['w', 'w', 'w', 'd', 'l'],
        'away_form' => ['w', 'w', 'd', 'd', 'l'],
        'odds' => ['1' => '2.04', 'X' => '3.14', '2' => '2.00'],
        'prediction' => 'Over 2.5',
        'prediction_odds' => '2.14',
    ],
    [
        'country' => 'England',
        'league' => 'Premier League',
        'home' => 'Liverpool',
        'home_logo' => 'liverpool.png',
        'away' => 'Chelsea',
        'away_logo' => 'chelsea.png',
        'time' => '15:30',
        'home_form' => ['w', 'w', 'd', 'w', 'w'],
        'away_form' => ['d', 'l', 'w', 'w', 'd'],
        'odds' => ['1' => '1.85', 'X' => '3.60', '2' => '4.20'],
        'prediction' => 'Home Win',
        'prediction_odds' => '1.85',
    ],
    [
        'country' => 'England',
        'league' => 'Premier League',
        'home' => 'Man City',
        'home_logo' => 'mancity.png',
        'away' => 'Man United',
        'away_logo' => 'manutd.png',
        'time' => '17:00',
        'home_form' => ['w', 'w', 'w', 'w', 'd'],
        'away_form' => ['l', 'w', 'd', 'l', 'w'],
        'odds' => ['1' => '1.55', 'X' => '4.10', '2' => '5.50'],
        'prediction' => 'BTTS Yes',
        'prediction_odds' => '1.72',
    ],
    [
        'country' => 'England',
        'league' => 'Premier League',
        'home' => 'Arsenal',
        'home_logo' => 'arsenal.png',
        'away' => 'Brentford',
        'away_logo' => 'brentford.png',
        'time' => '19:00',
        'home_form' => ['w', 'd', 'w', 'w', 'w'],
        'away_form' => ['w', 'l', 'd', 'w', 'l'],
        'odds' => ['1' => '1.40', 'X' => '4.50', '2' => '7.00'],
        'prediction' => '1X',
        'prediction_odds' => '1.18',
    ],
];

$topLeagues = [
    'UEFA Champions League',
    'Premier League',
    'La Liga',
    'Serie A',
    'Bundesliga',
    'Ligue 1',
    'Europa League',
    'Eredivisie',
];

$countries = [
    ['name' => 'Albania', 'flag' => 'albania.svg'],
    ['name' => 'Algeria', 'flag' => 'algeria.svg'],
    ['name' => 'Andorra', 'flag' => 'andorra.svg'],
    ['name' => 'Angola', 'flag' => 'angola.svg'],
];

$leagueTable = [
    ['pos' => 1, 'club' => 'Liverpool', 'logo' => 'liverpool.png', 'p' => 28, 'gd' => 42, 'pts' => 67],
    ['pos' => 2, 'club' => 'Man City', 'logo' => 'mancity.png', 'p' => 28, 'gd' => 38, 'pts' => 63],
    ['pos' => 3, 'club' => 'Arsenal', 'logo' => 'arsenal.png', 'p' => 28, 'gd' => 35, 'pts' => 61],
    ['pos' => 4, 'club' => 'Chelsea', 'logo' => 'chelsea.png', 'p' => 28, 'gd' => 18, 'pts' => 52],
    ['pos' => 5, 'club' => 'Man United', 'logo' => 'manutd.png', 'p' => 28, 'gd' => 12, 'pts' => 48],
    ['pos' => 6, 'club' => 'Newcastle', 'logo' => 'newcastle.png', 'p' => 28, 'gd' => 10, 'pts' => 46],
];

$topScorers = [
    ['player' => 'Erling Haaland', 'club' => 'mancity.png', 'goals' => 24],
    ['player' => 'Mohamed Salah', 'club' => 'liverpool.png', 'goals' => 21],
    ['player' => 'Bukayo Saka', 'club' => 'arsenal.png', 'goals' => 16],
    ['player' => 'Cole Palmer', 'club' => 'chelsea.png', 'goals' => 15],
    ['player' => 'Alexander Isak', 'club' => 'newcastle.png', 'goals' => 14],
];

$winnings = [
    ['home' => 'Liverpool', 'away' => 'Chelsea', 'home_logo' => 'liverpool.png', 'away_logo' => 'chelsea.png', 'score' => '2 - 1', 'pick' => '1', 'odds' => '1.85'],
    ['home' => 'Arsenal', 'away' => 'Brentford', 'home_logo' => 'arsenal.png', 'away_logo' => 'brentford.png', 'score' => '3 - 0', 'pick' => 'Over 2.5', 'odds' => '1.72'],
    ['home' => 'Man City', 'away' => 'Luton', 'home_logo' => 'mancity.png', 'away_logo' => 'luton.png', 'score' => '4 - 1', 'pick' => '1', 'odds' => '1.25'],
    ['home' => 'Newcastle', 'away' => 'Everton', 'home_logo' => 'newcastle.png', 'away_logo' => 'everton.png', 'score' => '2 - 2', 'pick' => 'BTTS', 'odds' => '1.65'],
    ['home' => 'Fulham', 'away' => 'Burnley', 'home_logo' => 'fulham.png', 'away_logo' => 'burnley.png', 'score' => '1 - 0', 'pick' => '1X', 'odds' => '1.35'],
];

$articles = [
    [
        'image' => 'article-1.png',
        'title' => 'Best Premier League Predictions for This Weekend',
        'date' => 'Mar 18, 2025',
    ],
    [
        'image' => 'article-2.png',
        'title' => 'How to Find Value Bets with Correct Score Tips',
        'date' => 'Mar 17, 2025',
    ],
    [
        'image' => 'article-3.png',
        'title' => 'Champions League Banker Tips and Analysis',
        'date' => 'Mar 16, 2025',
    ],
];

$faqs = [
    [
        'q' => 'Which site gives sure football predictions?',
        'a' => 'Forebetz provides researched football predictions across major leagues, combining form analysis, stats, and expert tips to help bettors make informed decisions.',
    ],
    [
        'q' => 'Are Forebetz predictions free?',
        'a' => 'Yes. Free Football Predictions are available daily with Yesterday, Today, and Tomorrow fixtures. Premium packages unlock deeper markets and correct score tips.',
    ],
    [
        'q' => 'How accurate are the predictions?',
        'a' => 'No tipster can guarantee 100% accuracy. Forebetz focuses on strong probabilities, recent form, and transparent recent winnings so you can track performance.',
    ],
    [
        'q' => 'What markets do you cover?',
        'a' => 'We cover 1X2, double chance, over/under 2.5, BTTS/GG, correct score, draw, and banker tips across top European and international leagues.',
    ],
];

$tipCategories = [
    ['label' => 'Free Pick', 'slug' => 'free-pick'],
    ['label' => 'Acca tips', 'slug' => 'acca-tips'],
    ['label' => 'Over/Under Goals', 'slug' => 'over-under'],
    ['label' => 'Home/Away win', 'slug' => 'home-away'],
    ['label' => 'Double Chance', 'slug' => 'double-chance'],
    ['label' => 'BTTS/GG', 'slug' => 'btts'],
    ['label' => 'Correct score', 'slug' => 'correct-score'],
];

$planResults = [
    ['day' => 'tues', 'date' => '12/24'],
    ['day' => 'weds', 'date' => '12/24'],
    ['day' => 'thurs', 'date' => '12/24'],
    ['day' => 'thurs', 'date' => '12/24'],
    ['day' => 'thurs', 'date' => '12/24'],
    ['day' => 'fri', 'date' => '12/24'],
    ['day' => 'fri', 'date' => '12/24'],
];
