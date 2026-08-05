<?php
require_once __DIR__ . '/includes/config.php';

$categoryTitle = 'Forebetz Livescores Today';
$categoryDesc = 'Stay on top of every moment with free Live Football Scores, lightning fast goals, cards, and match events all in one place.';
$pageTitle = 'Livescores Today — Forebetz Live Football Scores';
$pageDescription = $categoryDesc;

$liveActiveTab = 'live';
$liveDateLabel = 'Wed, Mar 19th 2025';
$liveDateShort = '19/12';

$liveGroups = [
    [
        'country' => 'Spain',
        'league' => 'La Liga',
        'count' => 3,
        'matches' => [
            [
                'minute' => "25'",
                'home' => 'Benfica',
                'home_logo' => 'benfica.png',
                'away' => 'Club Brugge',
                'away_logo' => 'brugge.png',
                'home_score' => 0,
                'away_score' => 0,
                'odds' => ['1' => '2.04', 'X' => '3.14', '2' => '2.00'],
                'active' => true,
            ],
            [
                'minute' => "25'",
                'home' => 'Benfica',
                'home_logo' => 'benfica.png',
                'away' => 'Club Brugge',
                'away_logo' => 'brugge.png',
                'home_score' => 0,
                'away_score' => 0,
                'odds' => ['1' => '2.04', 'X' => '3.14', '2' => '2.00'],
                'active' => false,
            ],
            [
                'minute' => 'HT',
                'home' => 'Liverpool',
                'home_logo' => 'liverpool.png',
                'away' => 'Chelsea',
                'away_logo' => 'chelsea.png',
                'home_score' => 1,
                'away_score' => 1,
                'odds' => ['1' => '1.85', 'X' => '3.60', '2' => '4.20'],
                'active' => false,
            ],
        ],
    ],
    [
        'country' => 'England',
        'league' => 'Premier League',
        'count' => 2,
        'matches' => [
            [
                'minute' => "67'",
                'home' => 'Arsenal',
                'home_logo' => 'arsenal.png',
                'away' => 'Brentford',
                'away_logo' => 'brentford.png',
                'home_score' => 2,
                'away_score' => 0,
                'odds' => ['1' => '1.40', 'X' => '4.50', '2' => '7.00'],
                'active' => false,
            ],
            [
                'minute' => "12'",
                'home' => 'Palace',
                'home_logo' => 'palace.png',
                'away' => 'Chelsea',
                'away_logo' => 'chelsea.png',
                'home_score' => 0,
                'away_score' => 1,
                'odds' => ['1' => '3.20', 'X' => '3.10', '2' => '2.25'],
                'active' => false,
            ],
        ],
    ],
    [
        'country' => 'Italy',
        'league' => 'Serie A',
        'count' => 2,
        'matches' => [
            [
                'minute' => "33'",
                'home' => 'Benfica',
                'home_logo' => 'benfica.png',
                'away' => 'Liverpool',
                'away_logo' => 'liverpool.png',
                'home_score' => 1,
                'away_score' => 0,
                'odds' => ['1' => '2.10', 'X' => '3.20', '2' => '3.40'],
                'active' => false,
            ],
            [
                'minute' => "88'",
                'home' => 'Arsenal',
                'home_logo' => 'arsenal.png',
                'away' => 'Club Brugge',
                'away_logo' => 'brugge.png',
                'home_score' => 2,
                'away_score' => 2,
                'odds' => ['1' => '2.50', 'X' => '3.00', '2' => '2.80'],
                'active' => false,
            ],
        ],
    ],
];

$leagueSeoBlocks = [
    [
        'title' => 'Livescore',
        'body' => 'Forebetz is a sure football prediction site in the world and the only site that predicts football matches correctly and we are dedicated to providing sure win predictions for today more than any other site that claims to give 90 accurate football predictions for bettors to select and bet at their favourite bookmaker that offers the best odds. Football kits Among the top 5 prediction sites in the world Forebetz is rated no.1 100 sure football prediction site in the world and in Nigeria. We are good in what we are doing even more than a prediction site that never loses and we are consistent in giving 99 percent football prediction, that is why we will always remain the most sure football prediction site in the world. At Forebetz we work hand in hand with our football expert and professional tipster to provide free football predictions and betting tips to all bettors starting from both team to score popularly known as GG (Goal Goal), alongside double chance football predictions, half time over under 0.5, over 1.5 goals football predictions, over 2.5 goals football predictions, draw x football predictions, win either half, and single bets. We also offer VIP packages for those who are interested in our premium service which include daily 1.50 odds, sure 2 odds, sure 5 odds, sure 10 odds, and weekend tips. All our sure football prediction is safe and reliable this why we remain the only site that offers 90 accurate football predictions for today and weekend.',
    ],
    [
        'title' => 'Which site gives sure football predictions?',
        'body' => 'Forebetz is the most sure football prediction site in the world with 90 accurate football predictions this is why we continue to remain the only site that predict football matches correctly one can easily come in contact with us through our hotline lines and customer care and have access to our 100 sure football predictions for weekend and sure win prediction for today. At Forebetz we do it better than 99 percent football prediction sites in the world and you can always check on our platform to see our previous match result.',
    ],
    [
        'title' => 'What is the best football prediction site that never loses?',
        'body' => 'Forebetz is consistent in giving 99 percent accurate football prediction for our bettors to select and bet at their favourite bookmaker that offers the best odds, this is the more reason we are the best football prediction site that never lose at anytime of the day you can always have access to our 100 sure football predictions posted by our football expert and tipster without putting your stake at risk this is why we will continue to remain the only site that predict football matches correctly more than 99 percent football prediction site in the world today.',
    ],
    [
        'title' => 'Which website is 100% accurate in football predictions for today?',
        'body' => 'Forebetz is the only website that offers accurate football predictions for today and all our match predictions are 100% accurate. This is why Forebetz is the most sure football prediction site in the world and the only site that predict football matches correctly we does it more better than prediction site that never lose and we will continue to remain the best prediction site in the world when it comes to sure win prediction for today, 100 sure football predictions, and 90 accurate football predictions.',
    ],
];

$leagueFaqIntro = 'Where can I find 99 percent football prediction site? Forebetz is the only place you can find 100 sure football predictions that you can easily select and bet at your favourite bookmaker that offers the best odds and is the reason we are the most sure football prediction site in the world for today and weekend tips. What is the prediction site that never lose? Forebetz continue to remain consistent in giving 90 accurate football predictions more than 99 percent football prediction site in the world today that is why we will continue to remain the only site that predict football matches correctly for you to select and bet at your favourite bookmaker that offers the best odds.';

$leagueConclusion = 'Forebetz is the most sure football prediction site in the world and the only site that predicts football matches correctly. We do more than prediction sites that never lose and we make a responsibility to give you a sure win prediction for today. When next you are searching for 100 sure football prediction site, sure football prediction site for today, sure football prediction site free, 99 percent football prediction site, and sure win prediction today — Forebetz remains your best choice for free live football scores and tips.';

include __DIR__ . '/includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/includes/header.php'; ?>
        <?php include __DIR__ . '/includes/components/category-hero.php'; ?>

        <main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site rounded-[20px] bg-[#f0f0f0] p-2.5 text-[#1e1e1e] sm:rounded-[30px] sm:p-8 lg:p-10">
                <div class="flex flex-col gap-6 sm:gap-8 lg:flex-row lg:items-start lg:gap-[30px]">
                    <div class="min-w-0 flex-1">
                        <?php include __DIR__ . '/includes/components/live-filters.php'; ?>

                        <div class="mt-4 flex flex-col gap-2.5 sm:mt-5 sm:gap-2.5">
                            <?php foreach ($liveGroups as $liveGroup): ?>
                                <?php include __DIR__ . '/includes/components/live-league-group.php'; ?>
                            <?php endforeach; ?>
                        </div>

                        <?php include __DIR__ . '/includes/components/league-seo.php'; ?>
                    </div>

                    <?php include __DIR__ . '/includes/components/sidebar.php'; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
