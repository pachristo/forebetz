<?php
/**
 * Recent Winnings — same layout as match rows (match-card),
 * specially marked green / WON so they read apart from normal tips.
 */
?>
<section class="mt-6 overflow-hidden rounded-[20px] bg-white md:mt-8 md:rounded-[24px]">
    <div class="flex items-center justify-between border-b border-[#e6e9ec] px-3 py-3 md:px-6 md:py-4">
        <h2 class="text-[20px] font-semibold text-[#1e1e1e] md:text-[24px] lg:text-[28px]">Recent Winnings</h2>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#14ae5c]/15 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.04px] text-[#108245] md:text-[12px]">
            <span class="size-3.5 shrink-0 overflow-hidden md:size-4">
                <img src="<?= $asset ?>/icons/trophy-emoji.svg" alt="" class="h-full w-full object-contain">
            </span>
            Won tips
        </span>
    </div>

    <div class="flex flex-col gap-2.5 p-2 md:gap-3 md:p-3 lg:gap-3.5 lg:p-4">
        <?php foreach ($winnings as $row): ?>
            <?php
            $scoreParts = preg_split('/\s*[-:]\s*/', (string) ($row['score'] ?? ''), 2);
            $homeScore = trim((string) ($scoreParts[0] ?? '-'));
            $awayScore = trim((string) ($scoreParts[1] ?? '-'));
            $date = $row['date'] ?? '';
            $pick = $row['pick'] ?? '';
            $oddsVal = $row['odds'] ?? '';
            ?>
            <article class="w-full min-w-0 overflow-hidden rounded-[15px] border-l-4 border-[#14ae5c] bg-[#dcdee2] p-1.5 md:rounded-[20px] md:p-2 lg:rounded-[25px] lg:p-2.5">
                <div class="min-w-0 rounded-[12px] bg-white md:rounded-[16px] lg:rounded-[20px]">
                    <a href="/match.php" class="flex min-w-0 flex-col rounded-[12px] bg-white px-1 py-2.5 md:rounded-[16px] md:px-2 md:py-2.5 lg:p-2.5">
                        <div class="flex w-full min-w-0 flex-col gap-2.5 lg:flex-row lg:items-center lg:justify-between lg:gap-4">

                            <?php /* —— Teams + final score (same shell as match-card) —— */ ?>
                            <div class="flex w-full min-w-0 items-center justify-center gap-1.5 md:gap-3 lg:min-w-0 lg:flex-1 lg:gap-[15px]">
                                <div class="flex min-w-0 flex-1 items-center justify-end gap-1 md:gap-1.5">
                                    <p class="max-w-full truncate text-right text-[11px] font-medium leading-tight tracking-[0.16px] text-[#1e1e1e] md:text-[15px] lg:text-[17px]">
                                        <?= htmlspecialchars($row['home']) ?>
                                    </p>
                                    <div class="size-7 shrink-0 overflow-hidden md:size-9 lg:size-[47px]">
                                        <img src="<?= $asset ?>/teams/<?= htmlspecialchars($row['home_logo']) ?>" alt="" class="h-full w-full object-contain">
                                    </div>
                                </div>

                                <div class="flex w-[56px] shrink-0 flex-col items-center md:w-[64px] lg:w-auto">
                                    <?php if ($date !== ''): ?>
                                        <p class="mb-0.5 text-center text-[9px] font-normal tracking-[0.14px] text-[#3d3d3d] md:text-[10px] lg:mb-1 lg:text-[12px] lg:text-[#757575]">
                                            <?= htmlspecialchars($date) ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="flex w-full gap-px overflow-hidden rounded-[4px] md:rounded-[5px]">
                                        <div class="min-w-0 flex-1 bg-[#f5f5f5] px-0.5 py-0.5 text-center text-[12px] font-semibold text-[#1e1e1e] md:px-1 md:text-[15px] lg:px-1.5 lg:text-[17px]">
                                            <?= htmlspecialchars($homeScore) ?>
                                        </div>
                                        <div class="min-w-0 flex-1 bg-[#f5f5f5] px-0.5 py-0.5 text-center text-[12px] font-semibold text-[#1e1e1e] md:px-1 md:text-[15px] lg:px-1.5 lg:text-[17px]">
                                            <?= htmlspecialchars($awayScore) ?>
                                        </div>
                                    </div>
                                    <span class="mt-0.5 size-4 overflow-hidden md:size-5" aria-hidden="true">
                                        <img src="<?= $asset ?>/icons/check-green.svg" alt="" class="h-full w-full object-contain">
                                    </span>
                                </div>

                                <div class="flex min-w-0 flex-1 items-center gap-1 md:gap-1.5">
                                    <div class="size-7 shrink-0 overflow-hidden md:size-9 lg:size-[47px]">
                                        <img src="<?= $asset ?>/teams/<?= htmlspecialchars($row['away_logo']) ?>" alt="" class="h-full w-full object-contain">
                                    </div>
                                    <p class="max-w-full truncate text-[11px] font-medium leading-tight tracking-[0.16px] text-[#1e1e1e] md:text-[15px] lg:text-[17px]">
                                        <?= htmlspecialchars($row['away']) ?>
                                    </p>
                                </div>
                            </div>

                            <?php /* —— Odds | Predictions (green WON) | Scores —— */ ?>
                            <div class="flex w-full min-w-0 items-end justify-between gap-1 px-0.5 md:gap-2 md:px-1 lg:w-auto lg:shrink-0 lg:justify-end lg:gap-2.5 lg:px-0">
                                <div class="flex min-w-0 flex-col items-center">
                                    <p class="py-0.5 text-[10px] font-medium tracking-[0.02px] text-[#1a1a1a] md:text-[13px] lg:text-[15px]">Odds</p>
                                    <div class="flex min-w-0 items-center rounded-[8px] border border-[#dadde2] bg-white p-1 md:rounded-[10px] md:p-1.5 lg:rounded-[11px] lg:p-2.5">
                                        <span class="whitespace-nowrap px-1.5 py-0.5 text-[10px] font-semibold tracking-[0.16px] text-[#1e1e1e] md:px-2 md:text-[13px] lg:px-2.5 lg:py-1 lg:text-[15px]">
                                            <?= htmlspecialchars((string) $oddsVal) ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex min-w-0 flex-1 flex-col items-center lg:flex-none">
                                    <p class="py-0.5 text-[10px] font-medium tracking-[0.02px] text-[#1a1a1a] md:text-[13px] lg:text-[15px]">Predictions</p>
                                    <span class="flex w-full max-w-full items-center justify-center rounded-[8px] bg-gradient-to-b from-[#14ae5c] to-[#108245] p-1 md:rounded-[10px] md:p-1.5 lg:w-auto lg:rounded-[11px] lg:p-2.5">
                                        <span class="flex min-w-0 items-center justify-center gap-1 px-1 py-0.5 md:gap-1.5 md:px-1.5 lg:gap-2 lg:px-2.5 lg:py-1">
                                            <span class="truncate text-[10px] font-bold tracking-[0.16px] text-white md:text-[13px] lg:text-[15px]">
                                                <?= htmlspecialchars($pick) ?>
                                            </span>
                                            <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-white px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-[0.04px] text-[#14ae5c] md:text-[11px] lg:text-[12px]">
                                                WON
                                                <span class="size-3 shrink-0 overflow-hidden md:size-3.5">
                                                    <img src="<?= $asset ?>/icons/trophy-emoji.svg" alt="" class="h-full w-full object-contain">
                                                </span>
                                            </span>
                                        </span>
                                    </span>
                                </div>

                                <div class="flex shrink-0 flex-col items-center">
                                    <p class="py-0.5 text-[10px] font-medium tracking-[0.02px] text-[#1a1a1a] md:text-[13px] lg:text-[15px]">Scores</p>
                                    <div class="flex items-center justify-center rounded-[8px] border border-[#dadde2] bg-white p-1 md:rounded-[10px] md:p-1.5 lg:rounded-[11px] lg:p-2.5">
                                        <span class="whitespace-nowrap px-1 py-0.5 text-[10px] font-semibold tracking-[0.16px] text-[#1e1e1e] md:px-1.5 md:text-[13px] lg:px-2.5 lg:py-1 lg:text-[15px]">
                                            <?= htmlspecialchars($row['score']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="flex justify-center border-t border-[#eee] px-4 py-4 md:py-5">
        <a href="#winnings" class="inline-flex items-center gap-2 rounded-[12px] bg-[#ff6900] px-5 py-2.5 text-[14px] font-bold text-[#1a1a1a] md:px-6 md:py-3 md:text-[15px]">
            View all
            <span class="size-5 shrink-0 overflow-hidden">
                <img src="<?= $asset ?>/icons/chevron-right.svg" alt="" class="h-full w-full object-contain">
            </span>
        </a>
    </div>
</section>
