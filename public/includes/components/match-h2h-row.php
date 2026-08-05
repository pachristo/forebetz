<?php
/**
 * Single H2H / last-5 result row.
 * @var array $row
 * @var callable $resultDot
 */
?>
<article class="bg-white px-2.5 py-2.5 sm:px-4 sm:py-3">
    <div class="mb-1.5 flex flex-wrap items-center gap-1 text-[11px] text-[#767676] sm:mb-2 sm:gap-1.5 sm:text-[13px]">
        <span><?= htmlspecialchars($row['date']) ?> •</span>
        <span class="inline-flex items-center gap-1 sm:gap-1.5">
            <span class="size-[14px] shrink-0 overflow-hidden rounded-[2px] sm:size-[16px]">
                <img src="<?= $asset ?>/flags/spain.png" alt="" class="h-full w-full object-cover">
            </span>
            <?= htmlspecialchars($row['competition']) ?>
        </span>
    </div>
    <div class="flex items-center gap-1.5 sm:gap-3">
        <div class="min-w-0 flex-1 space-y-1.5 sm:space-y-2">
            <div class="flex items-center gap-1.5 sm:gap-2">
                <span class="size-4 shrink-0 overflow-hidden sm:size-[19px]">
                    <img src="<?= htmlspecialchars($resultDot($row['home_result'])) ?>" alt="" class="h-full w-full object-contain">
                </span>
                <span class="size-6 shrink-0 overflow-hidden sm:size-[28px]">
                    <img src="<?= $asset ?>/teams/<?= htmlspecialchars($row['home_logo']) ?>" alt="" class="h-full w-full object-contain">
                </span>
                <span class="truncate text-[13px] font-medium text-[#1e1e1e] sm:text-[15px]"><?= htmlspecialchars($row['home']) ?></span>
            </div>
            <div class="flex items-center gap-1.5 sm:gap-2">
                <span class="size-4 shrink-0 overflow-hidden sm:size-[19px]">
                    <img src="<?= htmlspecialchars($resultDot($row['away_result'])) ?>" alt="" class="h-full w-full object-contain">
                </span>
                <span class="size-6 shrink-0 overflow-hidden sm:size-[28px]">
                    <img src="<?= $asset ?>/teams/<?= htmlspecialchars($row['away_logo']) ?>" alt="" class="h-full w-full object-contain">
                </span>
                <span class="truncate text-[13px] font-medium text-[#1e1e1e] sm:text-[15px]"><?= htmlspecialchars($row['away']) ?></span>
            </div>
        </div>
        <div class="flex w-9 shrink-0 flex-col items-center justify-center gap-0.5 text-[14px] font-semibold text-[#1e1e1e] sm:w-12 sm:gap-1 sm:text-[16px]">
            <span><?= htmlspecialchars($row['home_score']) ?></span>
            <span class="h-px w-6 bg-[#dadde2] sm:w-8" aria-hidden="true"></span>
            <span><?= htmlspecialchars($row['away_score']) ?></span>
        </div>
    </div>
</article>
