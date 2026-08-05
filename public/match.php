<?php
require_once __DIR__ . '/includes/config.php';

$matchDetail = [
    'page_title' => 'Benfica vs Club Brugge',
    'page_subtitle' => 'Football Prediction, Head to Head, Statistics and Betting Tips',
    'country' => 'Spain',
    'league' => 'La Liga',
    'flag' => 'spain.png',
    'date' => 'Jul 29 2025',
    'kickoff_display' => '7:00 PM',
    'venue' => 'Sandford Bridge',
    'home' => 'Benfica',
    'home_logo' => 'benfica.png',
    'away' => 'Club Brugge',
    'away_logo' => 'brugge.png',
    'time' => '13:00',
    'countdown' => '05: 22 : 42',
    'prediction' => 'Over 2.5',
    'prediction_odds' => '2.16',
    'score' => '- : -',
    'prediction_prob' => 85,
    'probs' => [
        ['label' => 'Home', 'pct' => 60, 'odds' => '2.04', 'ring' => 'prob-green.svg'],
        ['label' => 'Draw', 'pct' => 30, 'odds' => '3.28', 'ring' => 'prob-yellow.svg'],
        ['label' => 'Away', 'pct' => 10, 'odds' => '2.04', 'ring' => 'prob-red.svg'],
    ],
    'home_form' => ['l', 'w', 'd', 'l', 'w'],
    'away_form' => ['l', 'w', 'd', 'l', 'w'],
    'stats' => [
        ['label' => 'Current Form', 'home' => '56%', 'away' => '44%', 'home_w' => 60, 'away_w' => 19],
        ['label' => 'Head to Head', 'home' => '22', 'away' => '22', 'home_w' => 47, 'away_w' => 19],
        ['label' => 'Avg. Goals Scored', 'home' => '22', 'away' => '22', 'home_w' => 25, 'away_w' => 48],
        ['label' => 'Goal Difference', 'home' => '22', 'away' => '22', 'home_w' => 25, 'away_w' => 34],
    ],
    /* Compare bars: home green / away grey (Figma 360:38917) */
    'compare' => [
        [
            'label' => '10 - Wins in last 0 games',
            'home' => '10',
            'away' => '20',
            'icon' => 'check-white.svg',
            'icon_bg' => '#14ae5c',
            'bar' => '#14ae5c',
            'home_w' => 60,
            'away_w' => 19,
        ],
        [
            'label' => '10 - Draws in last 0 games',
            'home' => '4',
            'away' => '8',
            'icon' => 'dash.svg',
            'icon_bg' => '#767676',
            'bar' => '#14ae5c',
            'home_w' => 47,
            'away_w' => 19,
        ],
        [
            'label' => '10 - Losses in last 0 games',
            'home' => '8',
            'away' => '15',
            'icon' => 'close-bold.svg',
            'icon_bg' => '#ef1410',
            'bar' => '#14ae5c',
            'home_w' => 25,
            'away_w' => 48,
        ],
    ],
];

$h2hMatches = [
    [
        'date' => 'Jul 24, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Fulham',
        'home_logo' => 'fulham.png',
        'away' => 'Arsenal',
        'away_logo' => 'arsenal.png',
        'home_score' => '1',
        'away_score' => '1',
        'home_result' => 'd',
        'away_result' => 'd',
    ],
    [
        'date' => 'Jul 24, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Benfica',
        'home_logo' => 'benfica.png',
        'away' => 'Crystal Palace',
        'away_logo' => 'palace.png',
        'home_score' => '3',
        'away_score' => '1',
        'home_result' => 'w',
        'away_result' => 'l',
    ],
    [
        'date' => 'Jul 24, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Liverpool',
        'home_logo' => 'liverpool.png',
        'away' => 'Chelsea',
        'away_logo' => 'chelsea.png',
        'home_score' => '2',
        'away_score' => '0',
        'home_result' => 'w',
        'away_result' => 'l',
    ],
    [
        'date' => 'Jul 24, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Man City',
        'home_logo' => 'mancity.png',
        'away' => 'Man United',
        'away_logo' => 'manutd.png',
        'home_score' => '1',
        'away_score' => '2',
        'home_result' => 'l',
        'away_result' => 'w',
    ],
    [
        'date' => 'Jul 24, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Arsenal',
        'home_logo' => 'arsenal.png',
        'away' => 'Brentford',
        'away_logo' => 'brentford.png',
        'home_score' => '2',
        'away_score' => '2',
        'home_result' => 'd',
        'away_result' => 'd',
    ],
];

$homeLast5 = [
    [
        'date' => 'Jul 24, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Benfica',
        'home_logo' => 'benfica.png',
        'away' => 'Crystal Palace',
        'away_logo' => 'palace.png',
        'home_score' => '3',
        'away_score' => '1',
        'home_result' => 'w',
        'away_result' => 'l',
    ],
    [
        'date' => 'Jul 20, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Arsenal',
        'home_logo' => 'arsenal.png',
        'away' => 'Benfica',
        'away_logo' => 'benfica.png',
        'home_score' => '1',
        'away_score' => '1',
        'home_result' => 'd',
        'away_result' => 'd',
    ],
    [
        'date' => 'Jul 16, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Benfica',
        'home_logo' => 'benfica.png',
        'away' => 'Chelsea',
        'away_logo' => 'chelsea.png',
        'home_score' => '2',
        'away_score' => '0',
        'home_result' => 'w',
        'away_result' => 'l',
    ],
    [
        'date' => 'Jul 12, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Liverpool',
        'home_logo' => 'liverpool.png',
        'away' => 'Benfica',
        'away_logo' => 'benfica.png',
        'home_score' => '2',
        'away_score' => '1',
        'home_result' => 'w',
        'away_result' => 'l',
    ],
    [
        'date' => 'Jul 08, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Benfica',
        'home_logo' => 'benfica.png',
        'away' => 'Fulham',
        'away_logo' => 'fulham.png',
        'home_score' => '1',
        'away_score' => '0',
        'home_result' => 'w',
        'away_result' => 'l',
    ],
];

$awayLast5 = [
    [
        'date' => 'Jul 24, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Club Brugge',
        'home_logo' => 'brugge.png',
        'away' => 'Everton',
        'away_logo' => 'everton.png',
        'home_score' => '2',
        'away_score' => '1',
        'home_result' => 'w',
        'away_result' => 'l',
    ],
    [
        'date' => 'Jul 20, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Newcastle',
        'home_logo' => 'newcastle.png',
        'away' => 'Club Brugge',
        'away_logo' => 'brugge.png',
        'home_score' => '0',
        'away_score' => '0',
        'home_result' => 'd',
        'away_result' => 'd',
    ],
    [
        'date' => 'Jul 16, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Club Brugge',
        'home_logo' => 'brugge.png',
        'away' => 'Burnley',
        'away_logo' => 'burnley.png',
        'home_score' => '1',
        'away_score' => '2',
        'home_result' => 'l',
        'away_result' => 'w',
    ],
    [
        'date' => 'Jul 12, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Man United',
        'home_logo' => 'manutd.png',
        'away' => 'Club Brugge',
        'away_logo' => 'brugge.png',
        'home_score' => '1',
        'away_score' => '3',
        'home_result' => 'l',
        'away_result' => 'w',
    ],
    [
        'date' => 'Jul 08, 2022',
        'competition' => 'Spain: La Liga',
        'home' => 'Club Brugge',
        'home_logo' => 'brugge.png',
        'away' => 'Luton',
        'away_logo' => 'luton.png',
        'home_score' => '2',
        'away_score' => '2',
        'home_result' => 'd',
        'away_result' => 'd',
    ],
];

$standings = [
    ['pos' => 1, 'club' => 'Liverpool', 'logo' => 'liverpool.png', 'gp' => 14, 'w' => 9, 'd' => 4, 'l' => 1, 'g' => '40 - 14', 'gd' => '+26', 'pts' => 31, 'form' => ['w', 'w', 'd', 'w', 'w'], 'zone' => 'cl'],
    ['pos' => 2, 'club' => 'Arsenal', 'logo' => 'arsenal.png', 'gp' => 14, 'w' => 8, 'd' => 4, 'l' => 2, 'g' => '28 - 14', 'gd' => '+14', 'pts' => 28, 'form' => ['w', 'd', 'w', 'w', 'l'], 'zone' => 'cl2'],
    ['pos' => 3, 'club' => 'Chelsea', 'logo' => 'chelsea.png', 'gp' => 14, 'w' => 7, 'd' => 5, 'l' => 2, 'g' => '26 - 16', 'gd' => '+10', 'pts' => 26, 'form' => ['d', 'w', 'w', 'd', 'w'], 'zone' => 'cl3'],
    ['pos' => 4, 'club' => 'Man City', 'logo' => 'mancity.png', 'gp' => 14, 'w' => 7, 'd' => 4, 'l' => 3, 'g' => '30 - 18', 'gd' => '+12', 'pts' => 25, 'form' => ['w', 'w', 'l', 'd', 'w'], 'zone' => ''],
    ['pos' => 5, 'club' => 'Newcastle', 'logo' => 'newcastle.png', 'gp' => 14, 'w' => 6, 'd' => 5, 'l' => 3, 'g' => '22 - 16', 'gd' => '+6', 'pts' => 23, 'form' => ['d', 'w', 'd', 'w', 'l'], 'zone' => ''],
    ['pos' => 6, 'club' => 'Man United', 'logo' => 'manutd.png', 'gp' => 14, 'w' => 5, 'd' => 5, 'l' => 4, 'g' => '20 - 18', 'gd' => '+2', 'pts' => 20, 'form' => ['l', 'd', 'w', 'w', 'd'], 'zone' => ''],
    ['pos' => 7, 'club' => 'Fulham', 'logo' => 'fulham.png', 'gp' => 14, 'w' => 5, 'd' => 4, 'l' => 5, 'g' => '18 - 19', 'gd' => '-1', 'pts' => 19, 'form' => ['w', 'l', 'd', 'l', 'w'], 'zone' => ''],
    ['pos' => 8, 'club' => 'Crystal Palace', 'logo' => 'palace.png', 'gp' => 14, 'w' => 4, 'd' => 5, 'l' => 5, 'g' => '16 - 18', 'gd' => '-2', 'pts' => 17, 'form' => ['d', 'l', 'w', 'd', 'l'], 'zone' => ''],
    ['pos' => 9, 'club' => 'Everton', 'logo' => 'everton.png', 'gp' => 14, 'w' => 3, 'd' => 4, 'l' => 7, 'g' => '14 - 22', 'gd' => '-8', 'pts' => 13, 'form' => ['l', 'l', 'd', 'w', 'l'], 'zone' => 'rel'],
    ['pos' => 10, 'club' => 'Burnley', 'logo' => 'burnley.png', 'gp' => 14, 'w' => 2, 'd' => 4, 'l' => 8, 'g' => '12 - 26', 'gd' => '-14', 'pts' => 10, 'form' => ['l', 'd', 'l', 'l', 'd'], 'zone' => 'rel'],
    ['pos' => 11, 'club' => 'Brentford', 'logo' => 'brentford.png', 'gp' => 14, 'w' => 2, 'd' => 3, 'l' => 9, 'g' => '12 - 28', 'gd' => '-16', 'pts' => 9, 'form' => ['l', 'l', 'd', 'l', 'l'], 'zone' => 'rel'],
    ['pos' => 12, 'club' => 'Luton', 'logo' => 'luton.png', 'gp' => 14, 'w' => 1, 'd' => 3, 'l' => 10, 'g' => '10 - 30', 'gd' => '-20', 'pts' => 6, 'form' => ['l', 'd', 'l', 'l', 'l'], 'zone' => 'rel'],
];

$conclusion = 'Forebetz is the most sure football prediction site in the world and the only site that predicts football matches correctly. We do more than prediction sites that never lose and we make a responsibility to give you a sure win prediction for today. When next you are searching for 100 sure football prediction site, sure football prediction site for today, sure football prediction site free, 99 percent football prediction site, sure football prediction site correct score, hot prediction site, surest prediction site, top 5 prediction site, sure prediction, 90 accurate football predictions, 100 sure football predictions for weekend, 100 sure football prediction site in the world, site that predict football matches correctly, 100 sure football predictions, most sure football prediction site in the world, prediction site that never lose, best prediction site, and sure win prediction today. At Forebetz we offer reliable free football predictions for our punters to select and bet at their favourite bookmaker that offers the best odds.';

$pageTitle = $matchDetail['page_title'] . ' — Forebetz Football Predictions';
$pageDescription = $matchDetail['page_subtitle'];

include __DIR__ . '/includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/includes/header.php'; ?>

        <section class="w-full px-2.5 pt-2 sm:px-8 lg:px-[100px]">
            <div class="mx-auto flex w-full max-w-site flex-col items-center px-1 py-3 text-center sm:px-0 sm:py-5">
                <h1 class="text-[24px] font-semibold leading-tight text-[#fcbd02] sm:text-[40px] lg:text-[54px]">
                    <?= htmlspecialchars($matchDetail['page_title']) ?>
                </h1>
                <p class="mt-1.5 max-w-[836px] text-[13px] leading-snug text-white sm:mt-2.5 sm:text-[16px] lg:text-[18px]">
                    <?= htmlspecialchars($matchDetail['page_subtitle']) ?>
                </p>
            </div>
        </section>

        <main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site rounded-[16px] bg-[#f0f0f0] p-2.5 text-[#1e1e1e] sm:rounded-[24px] sm:p-6 md:rounded-[30px] md:p-8 lg:px-[150px] lg:py-10">
                <div class="mx-auto flex w-full max-w-[1228px] flex-col gap-3 sm:gap-4 lg:gap-5">
                    <?php include __DIR__ . '/includes/components/match-detail.php'; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
