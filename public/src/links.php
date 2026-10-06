<?php
/**
 * Site links index — for client review / QA
 */
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Site Links — Dailysuretips';
$pageDescription = 'All Dailysuretips pages in one place for review.';

$linkGroups = [
    'Main' => [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Livescores', 'href' => '/live.php'],
        ['label' => 'League', 'href' => '/league.php'],
        ['label' => 'Match detail', 'href' => '/match.php'],
        ['label' => 'Blog', 'href' => '/blog.php'],
        ['label' => 'Blog article', 'href' => '/blog-detail.php'],
    ],
    'Tip categories' => array_map(
        static fn (array $cat): array => [
            'label' => $cat['label'],
            'href' => '/category.php?cat=' . urlencode($cat['slug']),
        ],
        $tipCategories
    ),
    'Account & VIP' => [
        ['label' => 'Register', 'href' => '/register.php'],
        ['label' => 'Login', 'href' => '/login.php'],
        ['label' => 'Forgot password', 'href' => '/forgot-password.php'],
        ['label' => 'Dashboard', 'href' => '/dashboard.php'],
        ['label' => 'Profile / My Account', 'href' => '/profile.php'],
        ['label' => 'VIP Packages / Pricing', 'href' => '/pricing.php'],
        ['label' => 'Payment', 'href' => '/payment.php'],
    ],
    'Company' => [
        ['label' => 'About Us', 'href' => '/about.php'],
        ['label' => 'Contact Us', 'href' => '/contact.php'],
        ['label' => 'Partners', 'href' => '/partners.php'],
    ],
    'Legal' => [
        ['label' => 'Disclaimer', 'href' => '/disclaimer.php'],
        ['label' => 'Terms & Conditions', 'href' => '/terms.php'],
        ['label' => 'Privacy Policy', 'href' => '/privacy.php'],
        ['label' => 'Refund Policy', 'href' => '/refund.php'],
    ],
];

include __DIR__ . '/../includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden pb-20 lg:pb-0">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <section class="w-full px-2.5 pt-2 sm:px-8 sm:pt-2.5 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site py-3 sm:py-5">
                <h1 class="text-[32px] font-medium leading-tight text-[#ff6900] sm:text-[40px] lg:text-[48px]">
                    Site Links
                </h1>
                <p class="mt-1 text-[14px] font-normal leading-normal text-white sm:mt-1.5 sm:text-[16px] sm:leading-7 lg:text-[18px] lg:leading-7">
                    Every page on Dailysuretips — tap a link to open it
                </p>
            </div>
        </section>

        <main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
            <div class="mx-auto flex w-full max-w-site flex-col gap-6 rounded-[20px] bg-[#f0f0f0] px-[15px] py-5 text-[#1e1e1e] sm:gap-8 sm:rounded-[30px] sm:px-10 sm:py-8 lg:px-[80px] lg:py-10">
                <?php foreach ($linkGroups as $groupTitle => $links): ?>
                    <section class="flex flex-col gap-3">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[22px]">
                            <?= htmlspecialchars($groupTitle) ?>
                        </h2>
                        <ul class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            <?php foreach ($links as $link): ?>
                                <li>
                                    <a
                                        href="<?= htmlspecialchars($link['href']) ?>"
                                        class="flex items-center justify-between gap-3 rounded-[12px] border border-[#dadde2] bg-white px-4 py-3 transition hover:border-[#ff6900] hover:bg-[#fff0e6]"
                                    >
                                        <span class="min-w-0">
                                            <span class="block text-[15px] font-semibold text-[#1e1e1e] sm:text-[16px]">
                                                <?= htmlspecialchars($link['label']) ?>
                                            </span>
                                            <span class="mt-0.5 block truncate text-[12px] text-[#767676] sm:text-[13px]">
                                                <?= htmlspecialchars($link['href']) ?>
                                            </span>
                                        </span>
                                        <span class="shrink-0 text-[18px] text-[#ff6900]" aria-hidden="true">→</span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
