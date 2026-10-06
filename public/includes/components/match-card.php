<?php
/**
 * Match list card
 * Mobile/tablet: stacked (Figma 477:36868)
 * Desktop lg+: teams + odds/predictions/scores on one row
 *
 * @var array $match
 */
$homeForm = $match['home_form'] ?? [];
$awayForm = $match['away_form'] ?? [];
$formIcon = static function (string $f) use ($asset): string {
    return match ($f) {
        'w' => $asset . '/icons/form-w.svg',
        'd' => $asset . '/icons/form-draw.svg',
        'l' => $asset . '/icons/form-lose.svg',
        default => $asset . '/icons/form-draw.svg',
    };
};
?>
<article class="w-full min-w-0 overflow-hidden rounded-[15px] bg-[#dcdee2] p-1.5 md:rounded-[20px] md:p-2 lg:rounded-[30px] lg:p-2.5">
    <a href="/match.php" class="flex min-w-0 items-center justify-between gap-2 rounded-[10px] px-1.5 py-1 md:px-2 md:py-1.5">
        <div class="flex min-w-0 items-center gap-1 md:gap-1.5">
            <span class="size-[18px] shrink-0 overflow-hidden md:size-6">
                <img src="<?= $asset ?>/icons/premier.svg" alt="" class="h-full w-full object-contain">
            </span>
            <span class="truncate text-[13px] font-medium capitalize text-[#5a5a5a] md:text-[16px]"><?= htmlspecialchars($match['country']) ?></span>
            <span class="truncate text-[13px] font-medium capitalize text-[#1e1e1e] md:text-[16px]"><?= htmlspecialchars($match['league']) ?></span>
        </div>
        <span class="size-5 shrink-0 overflow-hidden opacity-70 md:size-[22px]">
            <img src="<?= $asset ?>/icons/chevron-right.svg" alt="" class="h-full w-full object-contain">
        </span>
    </a>

    <div class="min-w-0 rounded-[15px] bg-white md:rounded-[20px] lg:rounded-[25px]">
        <a href="/match.php" class="flex min-w-0 flex-col rounded-[15px] bg-white px-1 py-2.5 md:rounded-[18px] md:px-2 md:py-2.5 lg:p-2.5">
            <div class="flex w-full min-w-0 flex-col gap-2.5 lg:flex-row lg:items-center lg:justify-between lg:gap-4">

                <?php /* —— Teams + kickoff —— */ ?>
                <div class="flex w-full min-w-0 items-center justify-center gap-1.5 md:gap-3 lg:min-w-0 lg:flex-1 lg:gap-[15px]">
                    <?php /* Home */ ?>
                    <div class="flex min-w-0 flex-1 items-center justify-end gap-1 md:gap-1.5">
                        <div class="flex min-w-0 flex-col items-end gap-0.5">
                            <p class="max-w-full truncate text-right text-[11px] font-medium leading-tight tracking-[0.16px] text-[#1e1e1e] md:text-[15px] lg:text-[17px]">
                                <?= htmlspecialchars($match['home']) ?>
                            </p>
                            <div class="flex items-center gap-0.5 md:gap-1">
                                <?php foreach ($homeForm as $f): ?>
                                    <span class="size-3 shrink-0 overflow-hidden md:size-[15px] lg:size-[17px]">
                                        <img src="<?= htmlspecialchars($formIcon($f)) ?>" alt="" class="h-full w-full object-contain">
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="size-7 shrink-0 overflow-hidden md:size-9 lg:size-[47px]">
                            <img src="<?= $asset ?>/teams/<?= htmlspecialchars($match['home_logo']) ?>" alt="" class="h-full w-full object-contain">
                        </div>
                    </div>

                    <?php /* Score / time */ ?>
                    <div class="flex w-[42px] shrink-0 flex-col items-center overflow-hidden rounded-[4px] md:w-[52px] md:rounded-[5px] lg:w-auto">
                        <div class="flex w-full gap-px">
                            <div class="min-w-0 flex-1 bg-[#f5f5f5] px-0.5 py-0.5 text-center text-[12px] font-medium text-[#757575] md:px-1 md:text-[15px] lg:px-1.5 lg:text-[17px]">-</div>
                            <div class="min-w-0 flex-1 bg-[#f5f5f5] px-0.5 py-0.5 text-center text-[12px] font-medium text-[#757575] md:px-1 md:text-[15px] lg:px-1.5 lg:text-[17px]">-</div>
                        </div>
                        <div class="w-full bg-[#f5f5f5] px-0.5 py-1 text-center text-[10px] font-semibold tracking-[0.16px] text-[#1e1e1e] md:px-1 md:text-[13px] lg:px-1.5 lg:text-[15px]">
                            <?= htmlspecialchars($match['time']) ?>
                        </div>
                    </div>

                    <?php /* Away */ ?>
                    <div class="flex min-w-0 flex-1 items-center gap-1 md:gap-1.5">
                        <div class="size-7 shrink-0 overflow-hidden md:size-9 lg:size-[47px]">
                            <img src="<?= $asset ?>/teams/<?= htmlspecialchars($match['away_logo']) ?>" alt="" class="h-full w-full object-contain">
                        </div>
                        <div class="flex min-w-0 flex-col items-start gap-0.5">
                            <p class="max-w-full truncate text-[11px] font-medium leading-tight tracking-[0.16px] text-[#1e1e1e] md:text-[15px] lg:text-[17px]">
                                <?= htmlspecialchars($match['away']) ?>
                            </p>
                            <div class="flex items-center gap-0.5 md:gap-1">
                                <?php foreach ($awayForm as $f): ?>
                                    <span class="size-3 shrink-0 overflow-hidden md:size-[15px] lg:size-[17px]">
                                        <img src="<?= htmlspecialchars($formIcon($f)) ?>" alt="" class="h-full w-full object-contain">
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php /* —— Odds | Predictions | Scores —— */ ?>
                <div class="flex w-full min-w-0 items-end justify-between gap-1 px-0.5 md:gap-2 md:px-1 lg:w-auto lg:shrink-0 lg:justify-end lg:gap-2.5 lg:px-0">
                    <div class="flex min-w-0 flex-col items-center">
                        <p class="py-0.5 text-[10px] font-medium tracking-[0.02px] text-[#1a1a1a] md:text-[13px] lg:text-[15px]">Odds</p>
                        <div class="flex min-w-0 items-center rounded-[8px] border border-[#dadde2] bg-white p-1 md:rounded-[10px] md:p-1.5 lg:rounded-[11px] lg:p-2.5">
                            <?php foreach (['1', 'X', '2'] as $i => $key): ?>
                                <div class="flex items-center justify-center gap-0.5 px-1 py-0.5 md:gap-1.5 md:px-1.5 lg:gap-2.5 lg:px-2.5 lg:py-1 <?= $i === 1 ? 'border-x border-[#e6e9ec]' : '' ?>">
                                    <span class="text-[10px] font-medium capitalize text-[#767676] md:text-[13px] lg:text-[15px]"><?= $key ?></span>
                                    <span class="text-[10px] font-semibold tracking-[0.16px] text-[#1e1e1e] md:text-[13px] lg:text-[15px]"><?= htmlspecialchars($match['odds'][$key]) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="flex min-w-0 flex-1 flex-col items-center lg:flex-none">
                        <p class="py-0.5 text-[10px] font-medium tracking-[0.02px] text-[#1a1a1a] md:text-[13px] lg:text-[15px]">Predictions</p>
                        <span
                            class="flex w-full max-w-full items-center justify-center rounded-[8px] p-1 md:rounded-[10px] md:p-1.5 lg:w-auto lg:rounded-[11px] lg:p-2.5"
                            style="background-image: linear-gradient(107deg, #ff6900 0%, #f26300 23%, #ee6100 28%, #f56500 79%, #dd5b00 90%);"
                        >
                            <span class="flex min-w-0 items-center justify-center gap-0.5 px-1 py-0.5 md:gap-1.5 md:px-1.5 lg:gap-2.5 lg:px-2.5 lg:py-1">
                                <span class="truncate text-[10px] font-bold tracking-[0.16px] text-[#1a1a1a] md:text-[13px] lg:text-[15px]"><?= htmlspecialchars($match['prediction']) ?></span>
                                <span class="shrink-0 text-[10px] font-medium capitalize text-[#1a1a1a] md:text-[13px] lg:text-[15px]">(<?= htmlspecialchars($match['prediction_odds']) ?>)</span>
                            </span>
                        </span>
                    </div>

                    <div class="flex shrink-0 flex-col items-center">
                        <p class="py-0.5 text-[10px] font-medium tracking-[0.02px] text-[#1a1a1a] md:text-[13px] lg:text-[15px]">Scores</p>
                        <div class="flex items-center justify-center rounded-[8px] border border-[#dadde2] bg-white p-1 md:rounded-[10px] md:p-1.5 lg:rounded-[11px] lg:p-2.5">
                            <span class="whitespace-nowrap px-1 py-0.5 text-[10px] font-semibold tracking-[0.16px] text-[#1e1e1e] md:px-1.5 md:text-[13px] lg:px-2.5 lg:py-1 lg:text-[15px]"><?= htmlspecialchars($match['score'] ?? '- : -') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</article>
