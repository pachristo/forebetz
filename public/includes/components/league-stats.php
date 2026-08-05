<?php
/**
 * League home/draw/away stats — desktop 380:75748 / mobile 465:63356
 * Override: $leagueStats
 * Mobile/tablet: stacked rows. Desktop lg+: 3 columns.
 */
$leagueStats = $leagueStats ?? [
    [
        'title' => 'Home team wins',
        'pct' => '70%',
        'note' => 'In 38.28% of the game played at home.',
    ],
    [
        'title' => 'Number of draws',
        'pct' => '70%',
        'note' => 'In 38.28% of the game played at home.',
    ],
    [
        'title' => 'Away team wins',
        'pct' => '70%',
        'note' => 'In 38.28% of the game played at home.',
    ],
];
?>
<section class="w-full rounded-[20px] border-[3px] border-[#ef1410] bg-white py-2.5">
    <div class="flex flex-col items-center gap-2.5 overflow-hidden rounded-[20px] p-2.5 lg:flex-row lg:items-stretch lg:justify-center lg:gap-[22px]">
        <?php foreach ($leagueStats as $i => $stat): ?>
            <?php if ($i > 0): ?>
                <div class="hidden w-px shrink-0 self-stretch bg-[#dadde2] lg:block" aria-hidden="true"></div>
                <div class="h-px w-full bg-[#dadde2] lg:hidden" aria-hidden="true"></div>
            <?php endif; ?>
            <div class="flex w-full min-w-0 flex-1 flex-col items-center">
                <p class="py-0.5 text-center text-[16px] font-semibold capitalize tracking-[0.032px] text-[#162640] lg:text-[18px] lg:tracking-[0.036px]">
                    <?= htmlspecialchars($stat['title']) ?>
                </p>
                <div class="flex w-full items-center justify-center gap-5 px-2.5 lg:p-2.5">
                    <div class="relative size-[56px] shrink-0 lg:size-[76px]">
                        <img src="<?= $asset ?>/icons/league-stat-ring.svg" alt="" class="absolute inset-0 h-full w-full object-contain">
                        <span class="absolute inset-0 flex items-center justify-center text-[15px] font-bold text-[#1e1e1e] lg:text-[20px]">
                            <?= htmlspecialchars($stat['pct']) ?>
                        </span>
                    </div>
                    <p class="min-w-0 flex-1 text-[14px] leading-snug text-[#1e1e1e] lg:text-[16px]">
                        <?= htmlspecialchars($stat['note']) ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
