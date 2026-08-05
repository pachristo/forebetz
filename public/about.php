<?php
/**
 * About Us — Figma 353:32380 (desktop); stacks for mobile/tablet
 */
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'About Us — Forebetz';
$pageDescription = 'Who Forebetz are and what we offer: free football predictions every day across top leagues.';

$markets = [
    'Home win',
    'Away win',
    'Over 1.5',
    'Over 2.5',
    'Over 3.5',
    'Double chance',
    'BTTS/GG',
];

include __DIR__ . '/includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden pb-20 lg:pb-0">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/includes/header.php'; ?>

        <section class="w-full px-2.5 pt-2 sm:px-8 sm:pt-2.5 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site py-3 sm:py-5">
                <h1 class="text-[32px] font-medium leading-tight text-[#fcbd02] sm:text-[40px] lg:text-[48px]">
                    About Us
                </h1>
                <p class="mt-1 text-[14px] font-normal leading-normal text-white sm:mt-1.5 sm:text-[16px] sm:leading-7 lg:text-[18px] lg:leading-7">
                    Who we are
                </p>
            </div>
        </section>

        <main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site rounded-[20px] bg-[#f0f0f0] px-[15px] py-5 text-[#1e1e1e] sm:rounded-[30px] sm:px-10 sm:py-8 lg:px-[150px] lg:py-10">
                <div class="flex flex-col gap-10 lg:flex-row lg:items-start lg:gap-[104px]">
                    <div class="flex min-w-0 flex-1 flex-col gap-10 text-[#1e1e1e]">
                        <section class="flex flex-col gap-2.5">
                            <h2 class="text-[20px] font-bold leading-normal sm:text-[22px] lg:text-[24px]">
                                Who Forebets are?
                            </h2>
                            <div class="space-y-3 text-[15px] font-normal leading-7 sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                                <p>
                                    Forebetz is a football prediction platform (a trading name of Jeotips Services Limited) created for just one purpose - to help bettors and football lovers make smarter betting decisions every day.
                                </p>
                                <p>
                                    In a world and a time where there are football prediction sites growing and springing up, Forebetz does not just come to add to the numbers.
                                </p>
                                <p>
                                    We are a dedicated team of football analysts, data lovers, and betting strategists that work to help you understand what it really takes to win.
                                </p>
                                <p>
                                    Despite the number of sites that have grown in the last decade, the problems of finding accurate and reliable football predictions have not been fixed. Also, bettors today are still using guesswork - by themselves and these so-called sites - to make bets.
                                </p>
                            </div>
                        </section>

                        <?php /* Logo card — between sections on mobile; sidebar on desktop via order */ ?>
                        <div class="flex justify-center lg:hidden">
                            <?php include __DIR__ . '/includes/components/about-logo-card.php'; ?>
                        </div>

                        <section class="flex flex-col gap-2.5">
                            <h2 class="text-[20px] font-bold leading-normal sm:text-[22px] lg:text-[24px]">
                                What We Offer: Free Football Predictions Every Day
                            </h2>
                            <div class="space-y-3 text-[15px] font-normal leading-7 sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                                <p>
                                    Yes, clearly put, we offer free football predictions every day. There are lots of leagues that we cover and draw predictions from and it is the reason we are always able to provide free football predictions each day.
                                </p>
                                <p>
                                    You do not have to pay to get dependable and reliable football predictions.
                                </p>
                                <p>
                                    Our free tips are not guesswork.
                                </p>
                                <p>
                                    They are products of the analysis of match statistics, current team form, goal averages, and betting trends. We don’t predict blindly — we study the game.
                                </p>
                                <p>
                                    On our free predictions for football matches around the world, you will find betting markets like:
                                </p>
                                <ol class="list-decimal space-y-0 pl-[21px] sm:pl-[27px]">
                                    <?php foreach ($markets as $market): ?>
                                        <li class="leading-8"><?= htmlspecialchars($market) ?></li>
                                    <?php endforeach; ?>
                                </ol>
                                <p>
                                    We keep things simple and every day, these predictions are updated daily.
                                </p>
                                <p>
                                    They cover the best leagues in the world like the EPL, Serie A, Ligue 1, La Liga, Bundesliga while still covering less known but profitable leagues from around the world.
                                </p>
                                <p>
                                    The one thing you can bank on is the fact that every of the predictions here are well-checked and you can bank on that.
                                </p>
                            </div>
                        </section>
                    </div>

                    <aside class="hidden shrink-0 lg:block">
                        <?php include __DIR__ . '/includes/components/about-logo-card.php'; ?>
                    </aside>
                </div>
            </div>
        </main>
<?php
$embedFooterInPageShell = true;
include __DIR__ . '/includes/footer.php';
?>
