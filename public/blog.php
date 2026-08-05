<?php
/**
 * Blog listing — Figma 342:16904 (desktop) / 477:70692 (mobile + tablet)
 */
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Forebetz Blog — Latest Sport News & Betting Insights';
$pageDescription = 'Stay on top of every moment with the latest sport news, match previews, and betting insights from Forebetz.';

$metaStamp = 'December 8, 22:00 • 5d';
$featuredTitle = 'Nigeria Football Club Officially launches its websites with amazing features';
$gridTitle = 'Super Eagles Win AFCON Qualifier';
$listTitle = 'Best Betting Sites Not on Gamstop: Top Non Gamstop Bookies Compared and Ranked for 2026';
$topTitleA = 'Top 10 Best Offshore Sportsbooks - Sports Betting Abroad (2026)';
$topTitleB = 'Best Betting Sites Not on Gamstop: Top Non Gamstop Bookies Compared and Ranked for 2026';
$topTitleC = 'SAFF Championship – The leading football league in South Asia.';

$detailHref = '/blog-detail.php';

$topStories = [
    ['image' => 'top-1.png', 'title' => $topTitleA, 'meta' => $metaStamp, 'href' => $detailHref],
    ['image' => 'top-1.png', 'title' => $topTitleA, 'meta' => $metaStamp, 'href' => $detailHref],
    ['image' => 'top-2.png', 'title' => $topTitleB, 'meta' => $metaStamp, 'href' => $detailHref],
    ['image' => 'top-3.png', 'title' => $topTitleC, 'meta' => $metaStamp, 'href' => $detailHref],
];

$featured = [
    'image' => 'featured.png',
    'title' => $featuredTitle,
    'meta' => $metaStamp,
    'href' => $detailHref,
];

$featuredPair = [
    ['image' => 'featured-2.png', 'title' => $featuredTitle, 'meta' => $metaStamp, 'href' => $detailHref],
    ['image' => 'featured-2.png', 'title' => $featuredTitle, 'meta' => $metaStamp, 'href' => $detailHref],
];

$categoryPosts = [
    ['image' => 'card-1.png', 'title' => $gridTitle, 'meta' => $metaStamp, 'href' => $detailHref],
    ['image' => 'card-2.png', 'title' => $gridTitle, 'meta' => $metaStamp, 'href' => $detailHref],
    ['image' => 'card-3.png', 'title' => $gridTitle, 'meta' => $metaStamp, 'href' => $detailHref],
];

$blogCategories = [
    ['title' => 'Football News', 'posts' => $categoryPosts],
    ['title' => 'Match Preview', 'posts' => $categoryPosts],
    ['title' => 'Other Sports', 'posts' => $categoryPosts],
];

$biography = array_fill(0, 5, [
    'image' => 'bio-1.png',
    'title' => $listTitle,
    'meta' => $metaStamp,
    'href' => $detailHref,
]);

include __DIR__ . '/../includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden pb-20 lg:pb-0">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <?php /* Mobile/tablet hero — Figma 477:70692: Forebetz gold, Blog white */ ?>
        <section class="w-full px-2.5 pt-5 sm:px-8 lg:px-[100px] lg:pt-2.5">
            <div class="mx-auto w-full max-w-site py-0 lg:py-5">
                <h1 class="text-[32px] font-medium leading-tight lg:text-[48px]">
                    <span class="text-[#fcbd02]">Forebetz</span>
                    <span class="text-white"> Blog</span>
                </h1>
                <p class="mt-1 text-[14px] font-normal leading-normal text-white lg:mt-1.5 lg:text-[18px] lg:leading-7">
                    Stay on top of every moment with latest sport news
                </p>
            </div>
        </section>

        <main class="w-full px-2.5 pb-8 pt-[15px] sm:px-8 sm:pb-10 lg:px-[100px] lg:pt-0">
            <div class="mx-auto flex w-full max-w-site flex-col gap-10 rounded-[20px] bg-[#f0f0f0] px-2.5 py-5 text-[#1e1e1e] sm:rounded-[30px] lg:gap-5 lg:p-10">

                <?php /* Top Stories + featured block (mobile groups these tightly) */ ?>
                <div class="flex flex-col gap-[15px] lg:gap-5">
                    <section class="rounded-[20px] border border-[#d9d9d9] bg-white p-2.5 lg:p-[15px]">
                        <?php
                        $title = 'Top Stories';
                        $moreHref = '#top-stories';
                        include __DIR__ . '/../includes/components/blog-section-header.php';
                        ?>
                        <div class="-mx-0.5 mt-2.5 flex gap-2.5 overflow-x-auto pb-1 lg:mx-0 lg:grid lg:grid-cols-4 lg:overflow-visible lg:pb-0">
                            <?php foreach ($topStories as $post): ?>
                                <div class="w-[250px] shrink-0 lg:w-auto lg:min-w-0">
                                    <?php
                                    $variant = 'compact';
                                    include __DIR__ . '/../includes/components/blog-news-card.php';
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <div class="flex flex-col gap-2.5 lg:hidden">
                        <?php
                        $variant = 'large';
                        $post = $featured;
                        include __DIR__ . '/../includes/components/blog-news-card.php';
                        ?>
                        <div class="grid grid-cols-2 gap-2.5">
                            <?php foreach ($featuredPair as $post): ?>
                                <?php
                                $variant = 'medium';
                                include __DIR__ . '/../includes/components/blog-news-card.php';
                                ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-10 lg:flex-row lg:items-start lg:gap-[30px]">
                    <div class="flex min-w-0 flex-1 flex-col gap-10 lg:gap-5">
                        <?php /* Desktop featured (hidden on mobile/tablet — shown above) */ ?>
                        <div class="hidden flex-col gap-5 lg:flex">
                            <?php
                            $variant = 'large';
                            $post = $featured;
                            include __DIR__ . '/../includes/components/blog-news-card.php';
                            ?>
                            <div class="grid grid-cols-2 gap-5">
                                <?php foreach ($featuredPair as $post): ?>
                                    <?php
                                    $variant = 'medium';
                                    include __DIR__ . '/../includes/components/blog-news-card.php';
                                    ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php foreach ($blogCategories as $category): ?>
                            <section class="rounded-[20px] bg-white p-[15px]">
                                <?php
                                $title = $category['title'];
                                $moreHref = '#' . strtolower(str_replace(' ', '-', $category['title']));
                                include __DIR__ . '/../includes/components/blog-section-header.php';
                                ?>
                                <?php /* Stack until desktop; 3-col from lg */ ?>
                                <div class="mt-2.5 flex flex-col gap-5 lg:grid lg:grid-cols-3">
                                    <?php foreach ($category['posts'] as $post): ?>
                                        <?php
                                        $variant = 'grid';
                                        include __DIR__ . '/../includes/components/blog-news-card.php';
                                        ?>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endforeach; ?>

                        <?php /* Biography — mobile + tablet */ ?>
                        <section class="rounded-[20px] bg-white p-[15px] lg:hidden">
                            <?php
                            $title = 'Biography';
                            $moreHref = '#biography';
                            include __DIR__ . '/../includes/components/blog-section-header.php';
                            ?>
                            <div class="mt-2.5 flex flex-col gap-2.5">
                                <?php foreach ($biography as $post): ?>
                                    <?php
                                    $variant = 'list';
                                    include __DIR__ . '/../includes/components/blog-news-card.php';
                                    ?>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    </div>

                    <aside class="hidden w-full shrink-0 lg:block lg:max-w-[382px]">
                        <section class="rounded-[20px] bg-white p-[15px]">
                            <?php
                            $title = 'Biography';
                            $moreHref = '#biography';
                            include __DIR__ . '/../includes/components/blog-section-header.php';
                            ?>
                            <div class="mt-2.5 flex flex-col gap-2.5">
                                <?php foreach ($biography as $post): ?>
                                    <?php
                                    $variant = 'list';
                                    include __DIR__ . '/../includes/components/blog-news-card.php';
                                    ?>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
