<section class="mt-8 overflow-hidden rounded-[24px] bg-white">
    <div class="flex items-center justify-between border-b border-[#e6e9ec] px-4 py-4 sm:px-6">
        <h2 class="text-[22px] font-semibold text-[#1e1e1e] sm:text-[28px]">Recent Winnings</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-[14px] text-[#1e1e1e]">
            <thead class="bg-[#f5f5f5] text-[13px] font-semibold uppercase tracking-wide text-[#5a5a5a]">
                <tr>
                    <th class="px-4 py-3 sm:px-6">Match</th>
                    <th class="px-4 py-3">Score</th>
                    <th class="px-4 py-3">Finals / Pick</th>
                    <th class="px-4 py-3">Odds</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($winnings as $row): ?>
                    <tr class="border-t border-[#eee]">
                        <td class="px-4 py-3 sm:px-6">
                            <div class="flex items-center gap-2">
                                <span class="size-7 shrink-0 overflow-hidden">
                                    <img src="<?= $asset ?>/teams/<?= htmlspecialchars($row['home_logo']) ?>" alt="" class="h-full w-full object-contain">
                                </span>
                                <span class="font-medium"><?= htmlspecialchars($row['home']) ?></span>
                                <span class="text-[#767676]">vs</span>
                                <span class="size-7 shrink-0 overflow-hidden">
                                    <img src="<?= $asset ?>/teams/<?= htmlspecialchars($row['away_logo']) ?>" alt="" class="h-full w-full object-contain">
                                </span>
                                <span class="font-medium"><?= htmlspecialchars($row['away']) ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-semibold"><?= htmlspecialchars($row['score']) ?></td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col items-start gap-1">
                                <span><?= htmlspecialchars($row['pick']) ?></span>
                                <span class="inline-flex rounded-md bg-[#22c55e] px-2.5 py-0.5 text-[12px] font-bold uppercase text-white">WIN</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-semibold"><?= htmlspecialchars($row['odds']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="flex justify-center border-t border-[#eee] px-4 py-5">
        <a href="#winnings" class="inline-flex items-center gap-2 rounded-[12px] bg-[#fcbd02] px-6 py-3 text-[15px] font-bold text-[#162640]">
            View all
            <span class="size-5 shrink-0 overflow-hidden">
                <img src="<?= $asset ?>/icons/chevron-right.svg" alt="" class="h-full w-full object-contain">
            </span>
        </a>
    </div>
</section>
