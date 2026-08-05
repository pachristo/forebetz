<?php
require_once __DIR__ . '/../includes/config.php';

$catSlug = $_GET['cat'] ?? 'acca-tips';
$activeCat = null;
foreach ($tipCategories as $cat) {
    if ($cat['slug'] === $catSlug) {
        $activeCat = $cat;
        break;
    }
}
if ($activeCat === null) {
    $activeCat = $tipCategories[1] ?? ['label' => 'Acca tips', 'slug' => 'acca-tips'];
}

$categoryTitle = 'Forebetz ' . $activeCat['label'];
$categoryDesc = 'Forebetz ' . $activeCat['label'] . ' tips predictions today.';
$sectionTitle = $activeCat['label'];
$sectionDate = 'Wed, Mar 19th 2025';
$includeInvestment = false;
$pageTitle = $activeCat['label'] . ' — Forebetz Football Predictions';
$pageDescription = $categoryDesc;

$seoHeading = $activeCat['label'];
$seoIntro = 'Forebetz is a sure football prediction site in the world and the only site that predicts football matches correctly and we are dedicated to providing sure win predictions for today. Use ' . $activeCat['label'] . ' on this page to build smarter selections with researched tips across top leagues.';

include __DIR__ . '/../includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/../includes/header.php'; ?>
        <?php include __DIR__ . '/../includes/components/category-hero.php'; ?>

        <main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site rounded-[20px] bg-[#f0f0f0] p-2.5 text-[#1e1e1e] sm:rounded-[30px] sm:p-8 lg:p-10">
                <div class="flex flex-col gap-6 sm:gap-8 lg:flex-row lg:items-start lg:gap-[30px]">
                    <div class="min-w-0 flex-1">
                        <?php include __DIR__ . '/../includes/components/predictions.php'; ?>
                    </div>
                    <?php include __DIR__ . '/../includes/components/sidebar.php'; ?>
                </div>

                <section class="mt-8 rounded-[30px] bg-white px-5 py-8 text-[#1e1e1e] sm:px-8 sm:py-10">
                    <h2 class="mb-4 text-center text-[22px] font-bold text-[#ef1410] sm:text-[26px]">
                        <?= htmlspecialchars($seoHeading) ?>
                    </h2>
                    <p class="mb-6 text-[15px] leading-7 text-[#303030] sm:text-[16px]">
                        <?= htmlspecialchars($seoIntro) ?>
                    </p>
                    <h3 class="mb-4 text-[20px] font-bold text-[#ef1410] sm:text-[22px]">
                        Frequently Asked Questions
                    </h3>
                    <div class="flex flex-col gap-4">
                        <?php foreach ($faqs as $faq): ?>
                            <details class="rounded-[14px] border border-[#e6e9ec] bg-[#fafafa] px-4 py-3 open:bg-white">
                                <summary class="cursor-pointer list-none text-[16px] font-semibold text-[#1e1e1e]">
                                    <?= htmlspecialchars($faq['q']) ?>
                                </summary>
                                <p class="mt-2 text-[15px] leading-7 text-[#303030]">
                                    <?= htmlspecialchars($faq['a']) ?>
                                </p>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
