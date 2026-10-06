<?php
/**
 * Live match row — Figma listItemNew / Soccer_listItemNew
 * @var array $liveMatch
 * Keys: minute, home, home_logo, away, away_logo, home_score, away_score, odds, active
 */
$active = !empty($liveMatch['active']);
$odds = $liveMatch['odds'] ?? ['1' => '2.04', 'X' => '3.14', '2' => '2.00'];
?>
<a
    href="/match.php"
    class="flex w-full min-w-0 flex-col items-center overflow-hidden rounded-[15px] p-2 md:rounded-[20px] md:p-2 lg:rounded-[25px] lg:p-2.5 <?= $active ? 'border-2 border-[#ef1410] bg-[#f5f5f5]' : 'bg-white' ?>"
>
    <div class="flex w-full min-w-0 flex-col gap-2.5 md:flex-row md:items-center md:gap-4 lg:gap-[30px]">
        <div class="flex min-w-0 flex-1 items-center justify-center gap-1 md:gap-2">
            <div class="w-8 shrink-0 md:w-10">
                <p class="text-center text-[11px] font-semibold tracking-[0.2px] text-[#ef1410] md:text-[14px]">
                    <?= htmlspecialchars($liveMatch['minute']) ?>
                </p>
            </div>

            <div class="flex min-w-0 flex-1 items-center gap-1.5 md:gap-3 lg:gap-[15px]">
                <div class="flex min-w-0 flex-1 items-center justify-end gap-1">
                    <p class="min-w-0 truncate text-right text-[11px] font-medium tracking-[0.2px] text-[#1e1e1e] md:text-[15px] lg:text-[17px] lg:leading-[18px]">
                        <?= htmlspecialchars($liveMatch['home']) ?>
                    </p>
                    <div class="size-7 shrink-0 overflow-hidden md:size-9 lg:size-[47px]">
                        <img src="<?= $asset ?>/teams/<?= htmlspecialchars($liveMatch['home_logo']) ?>" alt="" class="h-full w-full object-contain">
                    </div>
                </div>

                <div class="flex w-[44px] shrink-0 overflow-hidden rounded-[4px] md:w-[56px] md:rounded-[5px] lg:w-[64px]">
                    <div class="flex w-full gap-px">
                        <div class="flex min-w-0 flex-1 items-center justify-center bg-[#f5f5f5] px-0.5 py-0.5 md:px-1.5">
                            <span class="text-[12px] font-medium tracking-[0.2px] text-[#ef1410] md:text-[15px] lg:text-[17px]">
                                <?= htmlspecialchars((string) $liveMatch['home_score']) ?>
                            </span>
                        </div>
                        <div class="flex min-w-0 flex-1 items-center justify-center bg-[#f5f5f5] px-0.5 py-0.5 md:px-1.5">
                            <span class="text-[12px] font-medium tracking-[0.2px] text-[#ef1410] md:text-[15px] lg:text-[17px]">
                                <?= htmlspecialchars((string) $liveMatch['away_score']) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex min-w-0 flex-1 items-center gap-1">
                    <div class="size-7 shrink-0 overflow-hidden md:size-9 lg:size-[47px]">
                        <img src="<?= $asset ?>/teams/<?= htmlspecialchars($liveMatch['away_logo']) ?>" alt="" class="h-full w-full object-contain">
                    </div>
                    <p class="min-w-0 truncate text-[11px] font-medium tracking-[0.2px] text-[#1e1e1e] md:text-[15px] lg:text-[17px] lg:leading-[18px]">
                        <?= htmlspecialchars($liveMatch['away']) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="flex w-full shrink-0 items-center justify-center md:w-auto md:justify-end">
            <div class="flex flex-col items-center">
                <p class="py-0.5 text-center text-[10px] font-medium tracking-[0.03px] text-[#1a1a1a] md:text-[13px] lg:text-[15px]">Odds</p>
                <div class="flex items-center justify-center rounded-[8px] border border-[#dadde2] bg-white p-1 md:rounded-[10px] md:p-1.5 lg:rounded-[11px] lg:p-2.5">
                    <div class="flex items-center gap-1 px-1.5 py-0.5 md:gap-2 md:px-2 lg:gap-2.5 lg:px-2.5 lg:py-1">
                        <span class="text-[10px] font-medium capitalize text-[#767676] md:text-[13px] lg:text-[15px]">1</span>
                        <span class="text-[10px] font-semibold tracking-[0.2px] text-[#1e1e1e] md:text-[13px] lg:text-[15px]"><?= htmlspecialchars($odds['1']) ?></span>
                    </div>
                    <div class="flex items-center gap-1 border-x border-[#e6e9ec] px-1.5 py-0.5 md:gap-2 md:px-2 lg:gap-2.5 lg:px-2.5 lg:py-1">
                        <span class="text-[10px] font-medium capitalize text-[#767676] md:text-[13px] lg:text-[15px]">X</span>
                        <span class="text-[10px] font-semibold tracking-[0.2px] text-[#1e1e1e] md:text-[13px] lg:text-[15px]"><?= htmlspecialchars($odds['X']) ?></span>
                    </div>
                    <div class="flex items-center gap-1 px-1.5 py-0.5 md:gap-2 md:px-2 lg:gap-2.5 lg:px-2.5 lg:py-1">
                        <span class="text-[10px] font-medium capitalize text-[#767676] md:text-[13px] lg:text-[15px]">2</span>
                        <span class="text-[10px] font-semibold tracking-[0.2px] text-[#1e1e1e] md:text-[13px] lg:text-[15px]"><?= htmlspecialchars($odds['2']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</a>
