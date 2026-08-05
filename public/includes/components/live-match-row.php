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
    class="flex w-full flex-col items-center overflow-hidden rounded-[18px] p-2 sm:rounded-[25px] sm:p-2.5 <?= $active ? 'border-2 border-[#ef1410] bg-[#f5f5f5]' : 'bg-white' ?>"
>
    <div class="flex w-full flex-col gap-2.5 sm:flex-row sm:items-center sm:gap-[20px] lg:gap-[30px]">
        <div class="flex min-w-0 flex-1 items-center justify-center">
            <div class="shrink-0 px-1.5 sm:px-[7px]">
                <p class="text-center text-[12px] font-semibold tracking-[0.2px] text-[#ef1410] sm:text-[14px]">
                    <?= htmlspecialchars($liveMatch['minute']) ?>
                </p>
            </div>

            <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-[15px]">
                <div class="flex min-w-0 flex-1 items-center justify-end">
                    <p class="max-w-[72px] truncate text-right text-[12px] font-medium tracking-[0.2px] text-[#1e1e1e] sm:max-w-none sm:text-[17px] sm:leading-[18px]">
                        <?= htmlspecialchars($liveMatch['home']) ?>
                    </p>
                    <div class="size-[33px] shrink-0 overflow-hidden sm:size-[47px]">
                        <img src="<?= $asset ?>/teams/<?= htmlspecialchars($liveMatch['home_logo']) ?>" alt="" class="h-full w-full object-contain">
                    </div>
                </div>

                <div class="flex shrink-0 overflow-hidden rounded-[4px] sm:rounded-[5px]">
                    <div class="flex w-[52px] gap-px sm:w-[64px]">
                        <div class="flex flex-1 items-center justify-center bg-[#f5f5f5] px-1 py-0.5 sm:px-1.5">
                            <span class="text-[14px] font-medium tracking-[0.2px] text-[#ef1410] sm:text-[17px]">
                                <?= htmlspecialchars((string) $liveMatch['home_score']) ?>
                            </span>
                        </div>
                        <div class="flex flex-1 items-center justify-center bg-[#f5f5f5] px-1 py-0.5 sm:px-1.5">
                            <span class="text-[14px] font-medium tracking-[0.2px] text-[#ef1410] sm:text-[17px]">
                                <?= htmlspecialchars((string) $liveMatch['away_score']) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex min-w-0 flex-1 items-center">
                    <div class="size-[33px] shrink-0 overflow-hidden sm:size-[47px]">
                        <img src="<?= $asset ?>/teams/<?= htmlspecialchars($liveMatch['away_logo']) ?>" alt="" class="h-full w-full object-contain">
                    </div>
                    <p class="max-w-[72px] truncate text-[12px] font-medium tracking-[0.2px] text-[#1e1e1e] sm:max-w-none sm:text-[17px] sm:leading-[18px]">
                        <?= htmlspecialchars($liveMatch['away']) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="flex shrink-0 items-center justify-center sm:justify-end">
            <div class="flex flex-col items-center">
                <p class="py-0.5 text-center text-[12px] font-medium tracking-[0.03px] text-[#162640] sm:text-[15px]">Odds</p>
                <div class="flex items-center justify-center rounded-[9px] border border-[#dadde2] bg-white p-2 sm:rounded-[11px] sm:p-2.5">
                    <div class="flex items-center gap-2 px-2 py-1 sm:gap-2.5 sm:px-2.5">
                        <span class="text-[12px] font-medium capitalize text-[#767676] sm:text-[15px]">1</span>
                        <span class="text-[12px] font-semibold tracking-[0.2px] text-[#1e1e1e] sm:text-[15px]"><?= htmlspecialchars($odds['1']) ?></span>
                    </div>
                    <div class="flex items-center gap-2 border-x border-[#e6e9ec] px-2 py-1 sm:gap-2.5 sm:px-2.5">
                        <span class="text-[12px] font-medium capitalize text-[#767676] sm:text-[15px]">X</span>
                        <span class="text-[12px] font-semibold tracking-[0.2px] text-[#1e1e1e] sm:text-[15px]"><?= htmlspecialchars($odds['X']) ?></span>
                    </div>
                    <div class="flex items-center gap-2 px-2 py-1 sm:gap-2.5 sm:px-2.5">
                        <span class="text-[12px] font-medium capitalize text-[#767676] sm:text-[15px]">2</span>
                        <span class="text-[12px] font-semibold tracking-[0.2px] text-[#1e1e1e] sm:text-[15px]"><?= htmlspecialchars($odds['2']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</a>
