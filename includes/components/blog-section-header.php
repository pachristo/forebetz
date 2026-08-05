<?php
/**
 * Blog section header — title + More link with gold underline.
 * Mobile/tablet: 16px title (Figma 477:70692). Desktop: 20px.
 *
 * @var string $title
 * @var string $moreHref
 * @var string $moreLabel
 * @var string $asset
 */
$moreHref = $moreHref ?? '#';
$moreLabel = $moreLabel ?? 'More';
/** @var string $titleClass Optional title size override */
$titleClass = $titleClass ?? 'text-[16px] lg:text-[20px]';
/** @var string $moreClass Optional more-link size override */
$moreClass = $moreClass ?? 'text-[10px] lg:text-[14px]';
?>
<div class="flex w-full items-center overflow-hidden border-b border-[#fcbd02] pb-2.5">
    <h2 class="min-w-0 flex-1 font-semibold tracking-[0.2px] text-[#303030] <?= htmlspecialchars($titleClass) ?>">
        <?= htmlspecialchars($title) ?>
    </h2>
    <a href="<?= htmlspecialchars($moreHref) ?>" class="inline-flex shrink-0 items-center tracking-[0.2px] text-[#fcbd02] <?= htmlspecialchars($moreClass) ?>">
        <?= htmlspecialchars($moreLabel) ?>
        <span class="size-6 shrink-0 overflow-hidden">
            <img src="<?= $asset ?>/icons/chevron-right-gold.svg" alt="" class="h-full w-full object-contain">
        </span>
    </a>
</div>
