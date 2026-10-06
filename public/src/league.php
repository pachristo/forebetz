<?php
require_once __DIR__ . '/../includes/config.php';

$leagueName = 'Serie A';
$leagueCountry = 'Italy';
$leagueLogo = 'serie-a.png';
$leagueOptions = ['Serie A', 'Premier League', 'La Liga', 'Bundesliga', 'Ligue 1', 'Champions League'];

$pageTitle = 'Serie A Predictions — Dailysuretips Football Tips';
$pageDescription = 'Serie A football predictions, upcoming and previous match tips, stats and betting odds from Dailysuretips.';

$leagueStats = [
    [
        'title' => 'Home team wins',
        'pct' => '70%',
        'note' => 'In 38.28% of the game played at home.',
    ],
    [
        'title' => 'Number of draws',
        'pct' => '70%',
        'note' => 'In 38.28% of the game played at home.',
    ],
    [
        'title' => 'Away team wins',
        'pct' => '70%',
        'note' => 'In 38.28% of the game played at home.',
    ],
];

$upcomingMatches = $matches;
$previousMatches = [
    [
        'country' => 'Spain',
        'league' => 'La Liga',
        'home' => 'Benfica',
        'home_logo' => 'benfica.png',
        'away' => 'Club Brugge',
        'away_logo' => 'brugge.png',
        'time' => 'FT',
        'home_form' => ['w', 'w', 'd', 'w', 'l'],
        'away_form' => ['l', 'w', 'd', 'l', 'w'],
        'odds' => ['1' => '2.04', 'X' => '3.14', '2' => '2.00'],
        'prediction' => 'Over 2.5',
        'prediction_odds' => '2.14',
        'score' => '2 : 1',
    ],
    [
        'country' => 'Spain',
        'league' => 'La Liga',
        'home' => 'Liverpool',
        'home_logo' => 'liverpool.png',
        'away' => 'Chelsea',
        'away_logo' => 'chelsea.png',
        'time' => 'FT',
        'home_form' => ['w', 'w', 'd', 'w', 'w'],
        'away_form' => ['d', 'l', 'w', 'w', 'd'],
        'odds' => ['1' => '1.85', 'X' => '3.60', '2' => '4.20'],
        'prediction' => 'Home Win',
        'prediction_odds' => '1.85',
        'score' => '3 : 0',
    ],
    [
        'country' => 'England',
        'league' => 'Premier League',
        'home' => 'Arsenal',
        'home_logo' => 'arsenal.png',
        'away' => 'Brentford',
        'away_logo' => 'brentford.png',
        'time' => 'FT',
        'home_form' => ['w', 'd', 'w', 'w', 'w'],
        'away_form' => ['w', 'l', 'd', 'w', 'l'],
        'odds' => ['1' => '1.40', 'X' => '4.50', '2' => '7.00'],
        'prediction' => '1X',
        'prediction_odds' => '1.18',
        'score' => '2 : 2',
    ],
];

$leagueSeoBlocks = [
    [
        'title' => 'Acca tips',
        'body' => 'Dailysuretips is a sure football prediction site in the world and the only site that predicts football matches correctly and we are dedicated to providing sure win predictions for today more than any other site that claims to give 90 accurate football predictions for bettors to select and bet at their favourite bookmaker that offers the best odds. Football kits Among the top 5 prediction sites in the world Dailysuretips is rated no.1 100 sure football prediction site in the world and in Nigeria. We are good in what we are doing even more than a prediction site that never loses and we are consistent in giving 99 percent football prediction, that is why we will always remain the most sure football prediction site in the world. At Dailysuretips we work hand in hand with our football expert and professional tipster to provide free football predictions and betting tips to all bettors starting from both team to score popularly known as GG (Goal Goal), alongside double chance football predictions, half time over under 0.5, over 1.5 goals football predictions, over 2.5 goals football predictions, draw x football predictions, win either half, and single bets. We also offer VIP packages for those who are interested in our premium service which include daily 1.50 odds, sure 2 odds, sure 5 odds, sure 10 odds, and weekend tips. All our sure football prediction is safe and reliable this why we remain the only site that offers 90 accurate football predictions for today and weekend. Our 100 sure football predictions offered by football experts and tipsters is one of the reasons we are the best prediction site or the prediction site that never loses. If you ever think of surest prediction site in the world or any hot prediction site that offer 99 percent football prediction Dailysuretips is the best site for sure prediction. The main purpose why we started Dailysuretips is to educate our bettors on the various betting tips that we have offered by different bookmakers for you to select and bet at your favourite bookmaker that offers the best odds, all our 100 sure football predictions cut across all football league starting from UEFA Champions League, UEFA Europa League, UEFA Women Champions League, Serie A, French Ligue 1, Bundesliga and La Liga Ligue.',
    ],
    [
        'title' => 'Which site gives sure football predictions?',
        'body' => 'Dailysuretips is the most sure football prediction site in the world with 90 accurate football predictions this is why we continue to remain the only site that predict football matches correctly one can easily come in contact with us through our hotline lines and customer care and have access to our 100 sure football predictions for weekend and sure win prediction for today. At Dailysuretips we do it better than 99 percent football prediction sites in the world and you can always check on our platform to see our previous match result.',
    ],
    [
        'title' => 'What is the best football prediction site that never loses?',
        'body' => 'Dailysuretips is consistent in giving 99 percent accurate football prediction for our bettors to select and bet at their favourite bookmaker that offers the best odds, this is the more reason we are the best football prediction site that never lose at anytime of the day you can always have access to our 100 sure football predictions posted by our football expert and tipster without putting your stake at risk this is why we will continue to remain the only site that predict football matches correctly more than 99 percent football prediction site in the world today.',
    ],
    [
        'title' => 'Which website is 100% accurate in football predictions for today?',
        'body' => 'Dailysuretips is the only website that offers accurate football predictions for today and all our match predictions are 100% accurate. This is why Dailysuretips is the most sure football prediction site in the world and the only site that predict football matches correctly we does it more better than prediction site that never lose and we will continue to remain the best prediction site in the world when it comes to sure win prediction for today, 100 sure football predictions, and 90 accurate football predictions.',
    ],
];

$leagueFaqIntro = 'Where can I find 99 percent football prediction site? Dailysuretips is the only place you can find 100 sure football predictions that you can easily select and bet at your favourite bookmaker that offers the best odds and is the reason we are the most sure football prediction site in the world for today and weekend tips. What is the prediction site that never lose? Dailysuretips continue to remain consistent in giving 90 accurate football predictions more than 99 percent football prediction site in the world today that is why we will continue to remain the only site that predict football matches correctly for you to select and bet at your favourite bookmaker that offers the best odds. Prediction website membership Where can I get 100 sure football predictions Among the free football predictions platform we have in the world Dailysuretips remain consistent in giving out 100 sure football predictions more than 99 percent football prediction site that claims to offer 90 accurate football predictions.';

$leagueConclusion = 'Dailysuretips is the most sure football prediction site in the world and the only site that predicts football matches correctly. We do more than prediction sites that never lose and we make a responsibility to give you a sure win prediction for today. When next you are searching for 100 sure football prediction site, sure football prediction site for today, sure football prediction site free, 99 percent football prediction site, sure football prediction site correct score, hot prediction site, surest prediction site, top 5 prediction site, sure prediction, 90 accurate football predictions, 100 sure football predictions for weekend, 100 sure football prediction site in the world, site that predict football matches correctly, 100 sure football predictions, most sure football prediction site in the world, prediction site that never lose, best prediction site, and sure win prediction today. At Dailysuretips we offer reliable free football predictions for our punters to select and bet at their favourite bookmaker that offers the best odds.';

include __DIR__ . '/../includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/../includes/header.php'; ?>
        <?php include __DIR__ . '/../includes/components/league-hero.php'; ?>

        <main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site rounded-[20px] bg-[#f0f0f0] p-2.5 text-[#1e1e1e] sm:rounded-[24px] sm:p-6 md:rounded-[30px] md:p-8 lg:p-10">
                <div class="flex flex-col gap-5 sm:gap-7 lg:flex-row lg:items-start lg:gap-[30px]">
                    <div class="min-w-0 flex-1">
                        <?php include __DIR__ . '/../includes/components/league-stats.php'; ?>

                        <section class="mt-4 sm:mt-6">
                            <h2 class="mb-2 text-[18px] font-medium leading-tight text-[#1e1e1e] sm:mb-4 sm:text-[24px] md:text-[28px] lg:text-[32px]">
                                Upcoming Matches Prediction
                            </h2>
                            <div class="flex flex-col gap-2.5">
                                <?php foreach ($upcomingMatches as $match): ?>
                                    <?php include __DIR__ . '/../includes/components/match-card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        </section>

                        <section class="mt-5 sm:mt-8">
                            <h2 class="mb-2 text-[18px] font-medium leading-tight text-[#1e1e1e] sm:mb-4 sm:text-[24px] md:text-[28px] lg:text-[32px]">
                                Previous Matches Prediction
                            </h2>
                            <div class="flex flex-col gap-2.5">
                                <?php foreach ($previousMatches as $match): ?>
                                    <?php include __DIR__ . '/../includes/components/match-card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        </section>

                        <?php include __DIR__ . '/../includes/components/league-seo.php'; ?>
                    </div>

                    <?php include __DIR__ . '/../includes/components/sidebar.php'; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
