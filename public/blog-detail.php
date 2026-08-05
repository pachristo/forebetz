<?php
/**
 * Blog detail — Figma 347:19782 (desktop) / 477:74053 (mobile + tablet)
 */
require_once __DIR__ . '/includes/config.php';

$postTitle = '10 of the most wasted talents in football. The world at their feet';
$pageTitle = $postTitle . ' — Forebetz Blog';
$pageDescription = 'Stay on top of every moment with free Live Football Scores, lightning fast goals, cards, and match events all in one place.';
$postLead = 'Stay on top of every moment with free Live Football Scores, lightning fast goals, cards, and match events all in one place.';
$postDate = '21 Jan 2025';
$heroImage = 'detail-hero.png';

$metaStamp = 'December 8, 22:00 • 5d';
$topStories = [
    [
        'image' => 'top-1.png',
        'title' => 'Top 10 Best Offshore Sportsbooks - Sports Betting Abroad (2026)',
        'meta' => $metaStamp,
        'href' => '/blog-detail.php',
    ],
    [
        'image' => 'top-1.png',
        'title' => 'Top 10 Best Offshore Sportsbooks - Sports Betting Abroad (2026)',
        'meta' => $metaStamp,
        'href' => '/blog-detail.php',
    ],
    [
        'image' => 'top-2.png',
        'title' => 'Best Betting Sites Not on Gamstop: Top Non Gamstop Bookies Compared and Ranked for 2026',
        'meta' => $metaStamp,
        'href' => '/blog-detail.php',
    ],
    [
        'image' => 'top-3.png',
        'title' => 'SAFF Championship – The leading football league in South Asia.',
        'meta' => $metaStamp,
        'href' => '/blog-detail.php',
    ],
];

$bodyParagraphs = [
    'As training camps heat up and the Qualifiers loom large, all eyes are naturally drawn to the big names — first-round picks, high-profile trades, and breakout stars from last season. But behind the curtain of hype and headlines, there’s a quieter storm brewing: the rise of under-the-radar rookies. These are the players who may not have dominated mock drafts or grabbed headlines on draft day, but have quietly turned heads in OTAs, mini-camps, or pre-season scrimmages.',
    'In this camp preview, we take a closer look at the rookies flying under the radar — the sleepers with potential to make noise as the Qualifiers draw near.',
];

$shareLinks = [
    ['href' => '#x', 'icon' => 'x-twitter.svg', 'label' => 'Share on X', 'box' => 'h-[27px] w-[24px]'],
    ['href' => '#telegram', 'icon' => 'telegram-social.svg', 'label' => 'Share on Telegram', 'box' => 'size-[27px]'],
    ['href' => '#facebook', 'icon' => 'facebook.svg', 'label' => 'Share on Facebook', 'box' => 'size-[27px]'],
];

include __DIR__ . '/includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden pb-20 lg:pb-0">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/includes/header.php'; ?>

        <?php /* Mobile hero — Figma 477:74053 */ ?>
        <section class="w-full px-2.5 pt-5 sm:px-8 lg:px-[100px] lg:pt-2.5">
            <div class="mx-auto flex w-full max-w-site flex-col gap-[17px] py-0 lg:gap-1.5 lg:py-5">
                <div class="flex flex-col gap-1">
                    <h1 class="text-[24px] font-medium leading-normal text-[#f3f3f3] lg:text-[48px] lg:leading-tight lg:text-white">
                        <?= htmlspecialchars($postTitle) ?>
                    </h1>
                    <p class="text-[14px] font-normal leading-normal text-white lg:hidden">
                        <?= htmlspecialchars($postLead) ?>
                    </p>
                </div>
                <nav
                    class="flex items-center gap-1 py-[7px] text-center tracking-[0.14px] lg:gap-1.5 lg:py-2.5 lg:tracking-[0.2px]"
                    aria-label="Breadcrumb"
                >
                    <a href="/blog.php" class="text-[14px] font-normal text-white lg:text-[20px]">Forebetz Blog</a>
                    <span class="text-[11px] font-semibold text-[#767676] lg:text-[16px]" aria-hidden="true">/</span>
                    <span class="text-[14px] font-normal text-[#fcbd02] lg:text-[20px]">Blog</span>
                </nav>
            </div>
        </section>

        <main class="w-full px-2.5 pb-8 pt-[15px] sm:px-8 sm:pb-10 lg:px-[100px] lg:pt-0">
            <div class="mx-auto flex w-full max-w-site flex-col gap-[30px] rounded-[20px] bg-[#f0f0f0] px-2.5 py-5 text-[#1e1e1e] sm:rounded-[30px] lg:px-[150px] lg:py-10">

                <article class="flex w-full flex-col gap-[25px] border-b border-[#d9d9d9] pb-[30px]">
                    <?php /* Figma mobile hero: 388×362 */ ?>
                    <div class="relative aspect-[388/362] w-full overflow-hidden rounded-[20px] lg:aspect-auto lg:h-[362px]">
                        <img
                            src="<?= $asset ?>/images/blog/<?= htmlspecialchars($heroImage) ?>"
                            alt=""
                            class="absolute inset-0 h-full w-full object-cover"
                        >
                        <div class="pointer-events-none absolute inset-0 rounded-[20px] bg-gradient-to-b from-transparent to-black/80" aria-hidden="true"></div>
                    </div>

                    <div class="flex flex-col gap-5">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <div class="flex items-center gap-2 pr-[9px] pt-0.5">
                                <span class="size-6 shrink-0 overflow-hidden">
                                    <img src="<?= $asset ?>/icons/calendar-duotone.svg" alt="" class="h-full w-full object-contain">
                                </span>
                                <time class="text-[16px] font-normal text-[#757575]" datetime="2025-01-21">
                                    <?= htmlspecialchars($postDate) ?>
                                </time>
                            </div>
                            <div class="flex items-center gap-5">
                                <?php foreach ($shareLinks as $share): ?>
                                    <a
                                        href="<?= htmlspecialchars($share['href']) ?>"
                                        class="<?= htmlspecialchars($share['box']) ?> shrink-0 overflow-hidden"
                                        aria-label="<?= htmlspecialchars($share['label']) ?>"
                                    >
                                        <img src="<?= $asset ?>/icons/<?= htmlspecialchars($share['icon']) ?>" alt="" class="h-full w-full object-contain">
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="space-y-3 text-[17px] font-light leading-[30px] text-[#1e1e1e]">
                            <?php for ($i = 0; $i < 3; $i++): ?>
                                <p class="mb-3 last:mb-0"><?= htmlspecialchars($bodyParagraphs[0]) ?></p>
                                <p><?= htmlspecialchars($bodyParagraphs[1]) ?></p>
                            <?php endfor; ?>
                        </div>
                    </div>
                </article>

                <section class="rounded-[20px] border border-[#d9d9d9] bg-white p-[15px]">
                    <?php
                    $title = 'Top Stories';
                    $moreHref = '/blog.php';
                    $titleClass = 'text-[20px]';
                    $moreClass = 'text-[14px]';
                    include __DIR__ . '/includes/components/blog-section-header.php';
                    unset($titleClass, $moreClass);
                    ?>

                    <?php /* Mobile/tablet: 4 thumbnail strip (Figma 477:74053). Desktop: compact cards. */ ?>
                    <div class="mt-2.5 grid grid-cols-4 gap-2.5 lg:hidden">
                        <?php foreach ($topStories as $post): ?>
                            <a
                                href="<?= htmlspecialchars($post['href']) ?>"
                                class="relative aspect-square w-full overflow-hidden rounded-[10px]"
                                aria-label="<?= htmlspecialchars($post['title']) ?>"
                            >
                                <img
                                    src="<?= $asset ?>/images/blog/<?= htmlspecialchars($post['image']) ?>"
                                    alt=""
                                    class="absolute inset-0 h-full w-full object-cover"
                                >
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-2.5 hidden gap-2.5 lg:grid lg:grid-cols-4">
                        <?php foreach ($topStories as $post): ?>
                            <div class="min-w-0">
                                <?php
                                $variant = 'compact';
                                include __DIR__ . '/includes/components/blog-news-card.php';
                                ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
