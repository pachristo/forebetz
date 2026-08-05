<?php
/**
 * Match list card — desktop + mobile (Figma 477:36868)
 * Mobile tuned so Odds / Predictions / Scores fit from 320px width.
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
<article class="w-full min-w-0 overflow-hidden rounded-[15px] bg-[#dcdee2] p-1.5 sm:rounded-[30px] sm:p-2.5">
    <a href="/match.php" class="flex min-w-0 items-center justify-between rounded-[10px] px-1.5 py-1 sm:px-2.5 sm:py-1.5">
        <div class="flex min-w-0 items-center gap-1 sm:gap-1.5">
            <span class="size-[18px] shrink-0 overflow-hidden sm:size-6">
                <img src="<?= $asset ?>/icons/premier.svg" alt="" class="h-full w-full object-contain">
            </span>
            <span class="truncate text-[14px] font-medium capitalize text-[#5a5a5a] sm:text-[16px]"><?= htmlspecialchars($match['country']) ?></span>
            <span class="truncate text-[14px] font-medium capitalize text-[#1e1e1e] sm:text-[16px]"><?= htmlspecialchars($match['league']) ?></span>
        </div>
        <span class="size-5 shrink-0 overflow-hidden opacity-70 sm:size-[22px]">
            <img src="<?= $asset ?>/icons/chevron-right.svg" alt="" class="h-full w-full object-contain">
        </span>
    </a>

    <div class="min-w-0 rounded-[15px] bg-white sm:rounded-[25px]">
        <a href="/match.php" class="flex min-w-0 flex-col items-start rounded-[15px] bg-white py-2.5 sm:rounded-[18px] sm:p-2.5">
            <div class="flex w-full min-w-0 flex-col gap-2.5 lg:flex-row lg:items-center lg:justify-between lg:gap-3">
                <?php /* Teams + time — Figma 477:36868 */ ?>
                <div class="flex w-full min-w-0 items-center justify-center gap-1.5 px-0.5 sm:gap-[15px] sm:px-1">
                    <div class="flex min-w-0 flex-1 items-center justify-end gap-0.5">
                        <div class="flex min-w-0 flex-col items-end gap-0.5">
                            <p class="w-full truncate text-right text-[11px] font-medium tracking-[0.16px] text-[#1e1e1e] sm:text-[17px]">
                                <?= htmlspecialchars($match['home']) ?>
                            </p>
                            <div class="flex w-[60px] items-center justify-between sm:w-[83px]">
                                <?php foreach ($homeForm as $f): ?>
                                    <span class="size-3 shrink-0 overflow-hidden sm:size-[17px]">
                                        <img src="<?= htmlspecialchars($formIcon($f)) ?>" alt="" class="h-full w-full object-contain">
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="size-7 shrink-0 overflow-hidden sm:size-[47px]">
                            <img src="<?= $asset ?>/teams/<?= htmlspecialchars($match['home_logo']) ?>" alt="" class="h-full w-full object-contain">
                        </div>
                    </div>

                    <div class="flex w-[44px] shrink-0 flex-col items-center overflow-hidden rounded-[4px] sm:w-auto sm:rounded-[5px]">
                        <div class="flex w-full gap-px">
                            <div class="flex-1 bg-[#f5f5f5] px-0.5 py-0.5 text-center text-[12px] font-medium text-[#757575] sm:px-1.5 sm:text-[17px]">-</div>
                            <div class="flex-1 bg-[#f5f5f5] px-0.5 py-0.5 text-center text-[12px] font-medium text-[#757575] sm:px-1.5 sm:text-[17px]">-</div>
                        </div>
                        <div class="w-full bg-[#f5f5f5] px-0.5 py-1 text-center text-[11px] font-semibold tracking-[0.16px] text-[#1e1e1e] sm:px-1.5 sm:text-[15px]">
                            <?= htmlspecialchars($match['time']) ?>
                        </div>
                    </div>

                    <div class="flex min-w-0 flex-1 items-center gap-0.5">
                        <div class="size-7 shrink-0 overflow-hidden sm:size-[47px]">
                            <img src="<?= $asset ?>/teams/<?= htmlspecialchars($match['away_logo']) ?>" alt="" class="h-full w-full object-contain">
                        </div>
                        <div class="flex min-w-0 flex-col items-start gap-0.5">
                            <p class="w-full truncate text-[11px] font-medium tracking-[0.16px] text-[#1e1e1e] sm:text-[17px]">
                                <?= htmlspecialchars($match['away']) ?>
                            </p>
                            <div class="flex w-[60px] items-center justify-between sm:w-[83px]">
                                <?php foreach ($awayForm as $f): ?>
                                    <span class="size-3 shrink-0 overflow-hidden sm:size-[17px]">
                                        <img src="<?= htmlspecialchars($formIcon($f)) ?>" alt="" class="h-full w-full object-contain">
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php /* Odds | Predictions | Scores — compressed for ≥320px */ ?>
                <div class="flex w-full min-w-0 items-end justify-between gap-0.5 px-1 sm:gap-1 sm:px-2.5 lg:w-auto lg:flex-wrap lg:justify-end lg:gap-2.5 lg:px-0">
                    <div class="flex min-w-0 flex-col items-center">
                        <p class="py-0.5 text-[10px] font-medium tracking-[0.02px] text-[#162640] sm:text-[15px]">Odds</p>
                        <div class="flex min-w-0 items-center rounded-[8px] border border-[#dadde2] bg-white p-1 sm:rounded-[11px] sm:p-2.5">
                            <?php foreach (['1', 'X', '2'] as $i => $key): ?>
                                <div class="flex items-center justify-center gap-0.5 px-1 py-0.5 sm:gap-2.5 sm:px-2.5 sm:py-1 <?= $i === 1 ? 'border-x border-[#e6e9ec]' : '' ?>">
                                    <span class="text-[11px] font-medium capitalize text-[#767676] sm:text-[15px]"><?= $key ?></span>
                                    <span class="text-[11px] font-semibold tracking-[0.16px] text-[#1e1e1e] sm:text-[15px]"><?= htmlspecialchars($match['odds'][$key]) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="flex min-w-0 flex-col items-center">
                        <p class="py-0.5 text-[10px] font-medium tracking-[0.02px] text-[#162640] sm:text-[15px]">Predictions</p>
                        <span
                            class="flex max-w-full items-center justify-center rounded-[8px] p-1 sm:rounded-[11px] sm:p-2.5"
                            style="background-image: linear-gradient(107deg, #fcbd02 0%, #e7ad01 23%, #e3a900 28%, #ebb209 79%, #d39e01 90%);"
                        >
                            <span class="flex min-w-0 items-center gap-0.5 px-1 py-0.5 sm:gap-2.5 sm:px-2.5 sm:py-1">
                                <span class="truncate text-[11px] font-bold tracking-[0.16px] text-[#162640] sm:text-[15px]"><?= htmlspecialchars($match['prediction']) ?></span>
                                <span class="shrink-0 text-[11px] font-medium capitalize text-[#162640] sm:text-[15px]">(<?= htmlspecialchars($match['prediction_odds']) ?>)</span>
                            </span>
                        </span>
                    </div>

                    <div class="flex shrink-0 flex-col items-center">
                        <p class="py-0.5 text-[10px] font-medium tracking-[0.02px] text-[#162640] sm:text-[15px]">Scores</p>
                        <div class="flex items-center justify-center rounded-[8px] border border-[#dadde2] bg-white p-1 sm:rounded-[11px] sm:p-2.5">
                            <span class="px-1 py-0.5 text-[11px] font-semibold tracking-[0.16px] text-[#1e1e1e] sm:px-2.5 sm:py-1 sm:text-[15px]"><?= htmlspecialchars($match['score'] ?? '- : -') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</article>
