<?php
/**
 * Blog news card variants: large | medium | grid | compact | list
 * Mobile/tablet sizes from Figma 477:70692; desktop from 342:16904
 *
 * @var array  $post  image, title, meta, href
 * @var string $variant
 * @var string $asset
 */
$variant = $variant ?? 'grid';
$href = $post['href'] ?? '#';
$img = $asset . '/images/blog/' . ($post['image'] ?? 'card-1.png');
$title = $post['title'] ?? '';
$meta = $post['meta'] ?? '';
?>
<?php if ($variant === 'compact'): ?>
    <a href="<?= htmlspecialchars($href) ?>" class="flex h-[93px] w-full min-w-0 items-center overflow-hidden">
        <span class="relative h-full w-[86px] shrink-0 overflow-hidden rounded-[10px]">
            <img src="<?= htmlspecialchars($img) ?>" alt="" class="absolute inset-0 h-full w-full object-cover">
        </span>
        <span class="flex min-w-0 flex-1 flex-col gap-2.5 px-2.5 py-2.5">
            <span class="line-clamp-2 text-[16px] font-semibold leading-snug text-[#1e1e1e]">
                <?= htmlspecialchars($title) ?>
            </span>
            <span class="truncate text-[14px] text-[#5a5a5a]"><?= htmlspecialchars($meta) ?></span>
        </span>
    </a>
<?php elseif ($variant === 'list'): ?>
    <a href="<?= htmlspecialchars($href) ?>" class="flex h-[93px] w-full items-center overflow-hidden">
        <span class="relative h-full w-[121px] shrink-0 overflow-hidden rounded-[10px]">
            <img src="<?= htmlspecialchars($img) ?>" alt="" class="absolute inset-0 h-full w-full object-cover">
        </span>
        <span class="flex min-w-0 flex-1 flex-col gap-2.5 px-2.5 py-2.5">
            <span class="line-clamp-2 text-[16px] font-semibold leading-snug text-[#1e1e1e]">
                <?= htmlspecialchars($title) ?>
            </span>
            <span class="truncate text-[14px] text-[#5a5a5a]"><?= htmlspecialchars($meta) ?></span>
        </span>
    </a>
<?php elseif ($variant === 'large'): ?>
    <a
        href="<?= htmlspecialchars($href) ?>"
        class="flex w-full flex-col overflow-hidden rounded-[20px] border-[3px] border-white bg-white p-[5px] shadow-[0_1px_7.7px_rgba(0,0,0,0.15)] lg:p-2.5"
    >
        <span class="relative aspect-[378/165] w-full overflow-hidden rounded-[10px] lg:aspect-[776/206]">
            <img src="<?= htmlspecialchars($img) ?>" alt="" class="absolute inset-0 h-full w-full object-cover">
        </span>
        <span class="flex w-full flex-col gap-2.5 p-[5px] lg:px-[15px] lg:py-2.5">
            <span class="text-[17px] font-semibold leading-snug text-[#1e1e1e] lg:text-[24px]">
                <?= htmlspecialchars($title) ?>
            </span>
            <span class="text-[11px] font-medium text-[#5a5a5a] lg:text-[18px]"><?= htmlspecialchars($meta) ?></span>
        </span>
    </a>
<?php elseif ($variant === 'medium'): ?>
    <a
        href="<?= htmlspecialchars($href) ?>"
        class="flex w-full flex-col overflow-hidden rounded-[10px] border-[3px] border-white bg-white p-[5px] shadow-[0_1px_7.7px_rgba(0,0,0,0.15)] lg:rounded-[20px] lg:p-2.5"
    >
        <span class="relative aspect-[179/140] w-full overflow-hidden rounded-[10px] lg:aspect-[368/177]">
            <img src="<?= htmlspecialchars($img) ?>" alt="" class="absolute inset-0 h-full w-full object-cover">
        </span>
        <span class="flex w-full flex-col gap-2.5 py-2.5 lg:p-2.5">
            <span class="line-clamp-3 text-[13px] font-semibold leading-snug text-[#1e1e1e] lg:line-clamp-none lg:text-[24px]">
                <?= htmlspecialchars($title) ?>
            </span>
            <span class="text-[11px] font-medium text-[#5a5a5a] lg:text-[17px]"><?= htmlspecialchars($meta) ?></span>
        </span>
    </a>
<?php else: ?>
    <?php /* grid — stacked full-width on mobile/tablet; 3-col card on desktop */ ?>
    <a
        href="<?= htmlspecialchars($href) ?>"
        class="flex w-full flex-col overflow-hidden rounded-[20px] border-4 border-white bg-white shadow-[0_1px_7.7px_rgba(0,0,0,0.15)]"
    >
        <span class="relative aspect-[358/143] w-full overflow-hidden lg:aspect-[242/197]">
            <img src="<?= htmlspecialchars($img) ?>" alt="" class="absolute inset-0 h-full w-full object-cover">
        </span>
        <span class="flex w-full flex-col gap-2.5 bg-white p-2.5 lg:gap-[18px] lg:px-[15px] lg:py-[18px]">
            <span class="text-[16px] font-semibold leading-snug text-[#303030] lg:text-[20px]">
                <?= htmlspecialchars($title) ?>
            </span>
            <span class="text-[13px] font-medium text-[#5a5a5a] lg:text-[17px]"><?= htmlspecialchars($meta) ?></span>
        </span>
    </a>
<?php endif; ?>
