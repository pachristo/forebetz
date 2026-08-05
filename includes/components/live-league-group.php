<?php
/**
 * Live league group — Figma Soccer-League-groups / listItemNew
 * @var array $liveGroup with country, league, count, matches[]
 */
$liveGroup = $liveGroup ?? ['country' => 'Spain', 'league' => 'La Liga', 'count' => 0, 'matches' => []];
?>
<section class="flex w-full flex-col gap-1 overflow-hidden rounded-[20px] bg-[#dcdee2] p-2 sm:gap-1.5 sm:rounded-[30px] sm:p-2.5">
    <div class="flex items-center justify-between rounded-[10px] px-2 py-1 sm:px-2.5 sm:py-1.5">
        <div class="flex items-center gap-3 sm:gap-5">
            <div class="flex items-center gap-1 sm:gap-1.5">
                <span class="size-[18px] shrink-0 overflow-hidden sm:size-6">
                    <img src="<?= $asset ?>/icons/premier.svg" alt="" class="h-full w-full object-contain">
                </span>
                <span class="text-[14px] font-medium capitalize text-[#5a5a5a] sm:text-[16px]"><?= htmlspecialchars($liveGroup['country']) ?></span>
                <span class="text-[14px] font-medium capitalize text-[#1e1e1e] sm:text-[16px]"><?= htmlspecialchars($liveGroup['league']) ?></span>
            </div>
            <button type="button" class="flex size-[26px] items-center justify-center rounded-full bg-[#f5f5f5] p-1" aria-label="Favourite league">
                <span class="size-[17px] overflow-hidden">
                    <img src="<?= $asset ?>/icons/star-outline.svg" alt="" class="h-full w-full object-contain">
                </span>
            </button>
        </div>
        <div class="flex items-center gap-2 sm:gap-2.5">
            <span class="text-[16px] font-medium capitalize text-[#ef1410] sm:text-[20px]">(<?= (int) $liveGroup['count'] ?>)</span>
            <span class="size-5 shrink-0 overflow-hidden sm:size-[21px]">
                <img src="<?= $asset ?>/icons/chevron-circle.svg" alt="" class="h-full w-full object-contain">
            </span>
        </div>
    </div>

    <div class="flex flex-col gap-1 sm:gap-1.5">
        <?php foreach ($liveGroup['matches'] as $liveMatch): ?>
            <?php include __DIR__ . '/live-match-row.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
