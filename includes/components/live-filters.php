<?php
/**
 * Livescore filters + date nav — Figma 261:15630
 * Override: $liveActiveTab, $liveDateLabel, $liveDateShort
 */
$liveActiveTab = $liveActiveTab ?? 'live';
$liveDateLabel = $liveDateLabel ?? 'Wed, Mar 19th 2025';
$liveDateShort = $liveDateShort ?? '19/12';
$liveTabs = [
    ['id' => 'all', 'label' => 'All'],
    ['id' => 'live', 'label' => 'Live (14)', 'icon' => true],
    ['id' => 'finished', 'label' => 'Finished'],
    ['id' => 'upcoming', 'label' => 'Upcoming'],
    ['id' => 'favourites', 'label' => 'Favourites'],
];
?>
<div class="flex flex-col gap-3 sm:gap-4">
    <div>
        <h2 class="text-[24px] font-medium text-[#1e1e1e] sm:text-[28px] lg:text-[32px]">Livescore Today</h2>
        <p class="text-[15px] leading-[28px] text-[#303030] sm:text-[16px] sm:leading-[30px] lg:text-[18px]">
            <?= htmlspecialchars($liveDateLabel) ?>
        </p>
    </div>

    <div class="flex flex-col gap-3 border-b border-[#d9d9d9] pb-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4 sm:pb-4">
        <div class="overflow-x-auto">
            <div class="inline-flex rounded-[40px] bg-[#fde8e7] p-1">
                <div class="flex items-center gap-1">
                    <?php foreach ($liveTabs as $tab): ?>
                        <?php $isActive = $tab['id'] === $liveActiveTab; ?>
                        <button
                            type="button"
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-[44px] px-3 py-2 text-[14px] tracking-[0.2px] sm:px-5 sm:py-2 sm:text-[17px] <?= $isActive ? 'bg-gradient-to-b from-[#ef1410] to-[#890b09] font-bold text-white' : 'bg-white font-medium capitalize text-[#303030]' ?>"
                        >
                            <?php if (!empty($tab['icon'])): ?>
                                <span class="size-[16px] shrink-0 overflow-hidden sm:size-[18px]">
                                    <img src="<?= $asset ?>/icons/live-dot.svg" alt="" class="h-full w-full object-contain">
                                </span>
                            <?php endif; ?>
                            <?= htmlspecialchars($tab['label']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2 self-end sm:self-auto">
            <button type="button" class="flex size-10 items-center justify-center rounded-full bg-white p-1.5 sm:size-[51px]" aria-label="Previous day">
                <span class="size-6 rotate-180 overflow-hidden sm:size-[30px]">
                    <img src="<?= $asset ?>/icons/chevron-right-line.svg" alt="" class="h-full w-full object-contain">
                </span>
            </button>
            <div class="flex h-10 items-center gap-2 rounded-full bg-white px-3 sm:h-[51px] sm:gap-3 sm:px-4">
                <span class="size-5 shrink-0 overflow-hidden sm:size-[27px]">
                    <img src="<?= $asset ?>/icons/calendar-broken.svg" alt="" class="h-full w-full object-contain">
                </span>
                <span class="text-[13px] font-semibold text-[#5a5a5a] sm:text-[15px]"><?= htmlspecialchars($liveDateShort) ?></span>
            </div>
            <button type="button" class="flex size-10 items-center justify-center rounded-full bg-white p-1.5 sm:size-[51px]" aria-label="Next day">
                <span class="size-6 overflow-hidden sm:size-[30px]">
                    <img src="<?= $asset ?>/icons/chevron-right-line.svg" alt="" class="h-full w-full object-contain">
                </span>
            </button>
        </div>
    </div>
</div>
