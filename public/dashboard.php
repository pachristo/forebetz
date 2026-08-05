<?php
require_once __DIR__ . '/includes/config.php';

/**
 * Inactive dashboard — Figma 382:79989
 * Active dashboard — Figma 382:80587 (?active=1)
 */
$subscriptionActive = isset($_GET['active']) && $_GET['active'] === '1';

$pageTitle = 'Dashboard — Forebetz';
$pageDescription = 'Your Forebetz dashboard. Manage your plan, packages, and support.';

$loggedInUser = [
    'name' => 'Michael Adalikwu',
    'short_name' => 'Michael A.',
    'email' => 'michaeladalikwu@gmail.com',
    'avatar' => 'avatar.jpg',
];

$subscriptionPlan = $subscriptionActive ? 'Premium Plan' : 'Free Plan';
$dashboardActive = 'dashboard';
$dashboardMatches = array_slice($matches, 0, 2);
$sectionTitle = 'Set One Tips';
$sectionDate = 'Wed, Mar 19th 2025';
$includeInvestment = false;
$hideDateNav = true;

$inactivePackages = [
    [
        'badge' => 'Weekly | 2 Weeks | Monthly',
        'title' => 'Premium Plan',
        'features' => [
            'Sure 2 Odds daily',
            '24/7 Support',
            'Access to Risk Management Guide.',
        ],
        'plan' => 'weekly',
    ],
    [
        'badge' => '2 Weeks | Monthly',
        'title' => 'Correct Score',
        'features' => [
            'Sure 1.50 - 1.90 Odds daily',
            '24/7 Support',
            'Access to Risk Management Guide.',
        ],
        'plan' => '2weeks',
    ],
];

include __DIR__ . '/includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/includes/header.php'; ?>

        <?php /* Inactive mobile 382:83811 / desktop 382:79989. Active: ?active=1 */ ?>
        <main class="w-full px-2.5 py-5 sm:px-5 sm:py-6 lg:px-5 lg:pb-10 lg:pt-0">
            <div class="mx-auto flex w-full max-w-site flex-col gap-[22px] rounded-[20px] bg-[#dedede] p-2.5 sm:gap-[26px] sm:p-5 lg:flex-row lg:items-start lg:p-5">
                <div class="hidden lg:block">
                    <?php include __DIR__ . '/includes/components/dashboard-sidebar.php'; ?>
                </div>

                <div class="flex min-w-0 flex-1 flex-col gap-[22px] sm:gap-[26px]">

                    <section class="flex w-full flex-col gap-[9px] overflow-hidden rounded-[17px] bg-white p-[15px] sm:gap-2.5 sm:rounded-[20px] sm:p-6 lg:flex-row lg:items-start lg:gap-2.5 lg:p-[30px]">
                        <div class="flex min-w-0 flex-1 flex-col gap-[9px] sm:gap-2.5">
                            <h1 class="text-[24px] font-semibold leading-tight text-[#1e1e1e] sm:text-[28px] lg:text-[32px]">
                                Welcome back, <?= htmlspecialchars($loggedInUser['name']) ?>
                            </h1>
                            <p class="text-[14px] font-normal leading-normal text-[#5a5a5a] sm:text-[16px] lg:max-w-[608px] lg:text-[18px]">
                                Welcome back! The road to bigger wins starts with the next prediction. Let’s chase success together.
                            </p>
                        </div>

                        <div class="relative w-full shrink-0 overflow-hidden rounded-[17px] px-3 py-2 sm:rounded-[20px] sm:px-[15px] sm:py-2.5 lg:w-[419px]">
                            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                                <div class="absolute inset-0 rounded-[17px] bg-[#0a111d] sm:rounded-[20px]"></div>
                                <img src="<?= $asset ?>/images/hero-bg.png" alt="" class="absolute inset-0 size-full rounded-[17px] object-cover opacity-20 backdrop-blur-[3.65px] sm:rounded-[20px]">
                            </div>
                            <div class="relative z-10 flex flex-col gap-2 sm:gap-2.5">
                                <div class="flex gap-2 py-2 sm:gap-2.5 sm:py-2.5">
                                    <div class="flex min-w-0 flex-1 flex-col gap-1 sm:gap-[5px]">
                                        <p class="text-[12px] font-normal text-[#b7bcc4] sm:text-[14px]">Plan</p>
                                        <p class="text-[16px] font-semibold text-[#e8e9ec] sm:text-[18px]"><?= htmlspecialchars($subscriptionPlan) ?></p>
                                    </div>
                                    <div class="flex min-w-0 flex-1 flex-col gap-1 sm:gap-[5px]">
                                        <p class="text-[12px] font-normal text-[#b7bcc4] sm:text-[14px]">Subscription Status</p>
                                        <?php if ($subscriptionActive): ?>
                                            <span class="inline-flex w-fit items-center justify-center rounded-[17px] bg-gradient-to-b from-[#14ae5c] to-[#108245] px-4 py-0.5 text-[14px] font-semibold text-white sm:rounded-[20px] sm:px-[18px] sm:py-[3px] sm:text-[16px]">
                                                Active
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex w-fit items-center justify-center rounded-[17px] bg-gradient-to-b from-[#ffd700] to-[#d9b700] px-4 py-0.5 text-[14px] font-semibold text-[#1e1e1e] sm:rounded-[20px] sm:px-[18px] sm:py-[3px] sm:text-[16px]">
                                                Inactive
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <a
                                    href="/pricing.php"
                                    class="flex w-full items-center justify-center rounded-[13px] bg-gradient-to-b from-[#14ae5c] to-[#108245] px-[15px] py-3 text-[16px] font-semibold tracking-[0.17px] text-white sm:rounded-[15px] sm:px-[17px] sm:py-[15px]"
                                >
                                    Upgrade
                                </a>
                            </div>
                        </div>
                    </section>

                    <?php if ($subscriptionActive): ?>
                        <section class="rounded-[15px] bg-white p-2.5 backdrop-blur-[6px] sm:rounded-[20px] sm:p-4 md:rounded-[30px] md:p-[25px]">
                            <?php
                            $matches = $dashboardMatches;
                            include __DIR__ . '/includes/components/predictions.php';
                            ?>
                        </section>
                    <?php else: ?>
                        <?php /* Inactive mobile 382:83811 — stacked packages; tablet md+: 2 col */ ?>
                        <section class="flex flex-col gap-[17px] rounded-[15px] bg-white p-2.5 backdrop-blur-[5px] sm:gap-5 sm:rounded-[26px] sm:p-4 md:rounded-[30px] md:p-[25px]">
                            <div class="w-full text-center text-[17px] font-medium leading-6 text-[#1e1e1e] sm:text-[18px] sm:leading-7 md:text-[20px]">
                                <p>Sorry ,you are not currently on any plan.</p>
                                <p>kindly subscribe to get started!</p>
                            </div>

                            <div class="relative overflow-hidden rounded-[20px] sm:rounded-[26px] md:rounded-[30px]">
                                <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                                    <div class="absolute inset-0 rounded-[20px] bg-[#162640] sm:rounded-[26px] md:rounded-[30px]"></div>
                                    <img
                                        src="<?= $asset ?>/images/packages-bg.png"
                                        alt=""
                                        class="absolute inset-0 size-full rounded-[20px] object-cover opacity-20 backdrop-blur-[5px] sm:rounded-[26px] md:rounded-[30px]"
                                    >
                                </div>

                                <div class="relative z-10 overflow-hidden rounded-[20px] p-3 sm:rounded-[26px] sm:p-[13px] md:rounded-[30px] md:p-[15px]">
                                    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                                        <div class="absolute inset-0 rounded-[20px] bg-[#0a111d] sm:rounded-[26px] md:rounded-[30px]"></div>
                                        <img
                                            src="<?= $asset ?>/images/invest-bg.png"
                                            alt=""
                                            class="absolute inset-0 size-full rounded-[20px] object-cover opacity-20 sm:rounded-[26px] md:rounded-[30px]"
                                        >
                                    </div>

                                    <div class="relative z-10 flex flex-col gap-[17px] sm:gap-5">
                                        <h2 class="text-center text-[28px] font-bold text-white sm:text-[28px] lg:text-[32px]">Packages</h2>

                                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 md:gap-[15px]">
                                            <?php foreach ($inactivePackages as $pkg): ?>
                                                <article class="flex flex-col rounded-[22px] border-[1.5px] border-[#ff312d] bg-gradient-to-b from-[#162640] to-[#163540] p-1 backdrop-blur-[6px] sm:rounded-[25px] sm:border-2 sm:p-[5px]">
                                                    <div class="flex min-h-0 flex-col gap-[15px] p-4 sm:min-h-[244px] sm:gap-[17px] sm:p-5">
                                                        <div class="flex flex-col gap-2 pb-2 sm:gap-2.5 sm:pb-2.5">
                                                            <span class="inline-flex w-fit rounded-[4px] bg-[#fcbd02] px-2 py-1 text-[13px] font-medium text-black sm:rounded-[5px] sm:px-2.5 sm:text-[15px]">
                                                                <?= htmlspecialchars($pkg['badge']) ?>
                                                            </span>
                                                            <h3 class="text-[22px] font-medium text-[#f0f0f0] sm:text-[26px]">
                                                                <?= htmlspecialchars($pkg['title']) ?>
                                                            </h3>
                                                        </div>
                                                        <ul class="flex flex-col gap-2 border-t border-[#b7bcc4] py-2 sm:gap-[9px] sm:py-2.5">
                                                            <?php foreach ($pkg['features'] as $feature): ?>
                                                                <li class="flex items-center gap-2 text-[12px] text-[#e9e9e9] sm:gap-2.5 sm:text-[14px]">
                                                                    <span class="size-[19px] shrink-0 overflow-hidden sm:size-[22px]">
                                                                        <img src="<?= $asset ?>/icons/check-fill.svg" alt="" class="h-full w-full object-contain">
                                                                    </span>
                                                                    <?= htmlspecialchars($feature) ?>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                    <a
                                                        href="/payment.php?plan=<?= urlencode($pkg['plan']) ?>"
                                                        class="mb-1 mx-1 flex h-[47px] items-center justify-center gap-2 rounded-[17px] px-6 text-[14px] font-bold uppercase text-black sm:mb-[5px] sm:mx-[5px] sm:h-[54px] sm:rounded-[20px] sm:px-8 sm:text-[16px]"
                                                        style="background-image: linear-gradient(106deg, #ffc108 13%, #c39202 101%);"
                                                    >
                                                        subscribe
                                                        <span class="size-[21px] shrink-0 overflow-hidden sm:size-6">
                                                            <img src="<?= $asset ?>/icons/arrow-right.svg" alt="" class="h-full w-full object-contain">
                                                        </span>
                                                    </a>
                                                </article>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <section class="rounded-[15px] bg-white p-2.5 sm:rounded-[20px] md:rounded-[30px]">
                        <div class="flex h-[52px] items-center justify-center border-b border-black/8 px-2.5 sm:h-[72px]">
                            <h2 class="text-center text-[20px] font-semibold capitalize tracking-[0.2px] text-[#1e1e1e] sm:text-[24px] md:text-[28px]">
                                Need Help?
                            </h2>
                        </div>
                        <div class="px-3 py-4 sm:px-[30px] sm:py-5">
                            <div class="flex flex-col gap-3 rounded-[16px] border-l-4 border-[#ef1410] bg-[#fff8e6] p-4 sm:gap-6 sm:rounded-[20px] sm:p-[25px]">
                                <div class="flex min-w-0 flex-1 flex-col gap-2.5">
                                    <p class="text-[14px] font-normal leading-6 text-[#1e1e1e] sm:text-[18px] sm:leading-7 md:text-[20px] md:leading-7">
                                        Our support team is here to help with any questions or issues you might have.
                                    </p>
                                    <a href="mailto:forebetzesupport@gmail.com" class="inline-flex min-w-0 items-center gap-2 text-[14px] font-medium text-[#1e1e1e] underline sm:gap-2.5 sm:text-[18px] md:text-[20px] md:leading-[22px]">
                                        <span class="size-7 shrink-0 overflow-hidden sm:size-8">
                                            <img src="<?= $asset ?>/icons/email-noto.svg" alt="" class="h-full w-full object-contain">
                                        </span>
                                        <span class="min-w-0 break-all">forebetzesupport@gmail.com</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
