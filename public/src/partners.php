<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Partners — Dailysuretips Sponsorship and Partners';
$pageDescription = 'Dailysuretips sponsorship and partner brands. Explore our betting and tips partners.';

$partners = [
    'Bet Winning Tips',
    'Bet Winning Tips',
    'Bet Winning Tips',
    'Bet Winning Tips',
    'Bet Winning Tips',
    'Bet Winning Tips',
    'Bet Winning Tips',
    'Bet Winning Tips',
];

include __DIR__ . '/../includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <section class="w-full px-2.5 pt-2 sm:px-8 sm:pt-2.5 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site py-3 sm:py-5">
                <h1 class="text-[32px] font-medium leading-tight text-[#ff6900] sm:text-[40px] lg:text-[48px]">
                    Partners
                </h1>
                <p class="mt-1 text-[15px] font-normal leading-[17px] text-white sm:mt-1.5 sm:text-[16px] sm:leading-7 lg:text-[18px]">
                    Sponsorship and Partners
                </p>
            </div>
        </section>

        <main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site rounded-[20px] bg-[#f0f0f0] px-[15px] py-5 text-[#1e1e1e] sm:rounded-[30px] sm:px-10 sm:py-8 lg:px-[150px] lg:py-10">
                <?php /* Mobile 477:89337: single column. Desktop: 2 columns */ ?>
                <div class="grid grid-cols-1 gap-[30px] lg:grid-cols-2">
                    <?php foreach ($partners as $partner): ?>
                        <a
                            href="#"
                            class="flex w-full items-center border-l-4 border-[#ef1410] bg-[#fff0e6] p-5 shadow-[0_0_0.8px_rgba(0,0,0,0.25)] transition hover:brightness-[0.98]"
                        >
                            <span class="flex items-center gap-2 pt-0.5 pr-2">
                                <span class="size-6 shrink-0 overflow-hidden">
                                    <img src="<?= $asset ?>/icons/calendar-duotone.svg" alt="" class="h-full w-full object-contain">
                                </span>
                                <span class="text-[18px] font-semibold text-[#1e1e1e] lg:text-[20px]">
                                    <?= htmlspecialchars($partner) ?>
                                </span>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
