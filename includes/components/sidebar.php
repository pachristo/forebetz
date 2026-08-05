<aside class="flex w-full flex-col gap-4 lg:max-w-[360px] lg:shrink-0">
    <!-- Search -->
    <form action="#" method="get" class="w-full">
        <label class="sr-only" for="sidebar-search">Search</label>
        <input
            id="sidebar-search"
            type="search"
            name="q"
            placeholder="Search teams, leagues..."
            class="w-full rounded-full border-0 bg-white px-5 py-3.5 text-[15px] text-[#1e1e1e] placeholder:text-[#9ca3af] outline-none ring-0 focus:ring-2 focus:ring-[#fcbd02]"
        >
    </form>

    <!-- Top Leagues -->
    <div class="overflow-hidden rounded-[16px] bg-white">
        <div class="border-b border-[#eee] px-4 py-3.5 text-center">
            <h3 class="text-[17px] font-bold text-[#1e1e1e]">Top Leagues</h3>
        </div>
        <ul class="divide-y divide-[#f0f0f0]">
            <?php foreach ($topLeagues as $league): ?>
                <li>
                    <a href="/league.php" class="flex items-center gap-3 px-4 py-3 text-[15px] text-[#1e1e1e] hover:bg-[#f8f8f8]">
                        <span class="size-6 shrink-0 overflow-hidden">
                            <img src="<?= $asset ?>/icons/premier.svg" alt="" class="h-full w-full object-contain">
                        </span>
                        <span class="flex-1"><?= htmlspecialchars($league) ?></span>
                        <span class="size-5 shrink-0 overflow-hidden opacity-50">
                            <img src="<?= $asset ?>/icons/chevron-right.svg" alt="" class="h-full w-full object-contain">
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Countries -->
    <div class="overflow-hidden rounded-[16px] bg-white">
        <div class="border-b border-[#eee] px-4 py-3.5 text-center">
            <h3 class="text-[17px] font-bold text-[#1e1e1e]">Countries</h3>
        </div>
        <ul class="countries-scroll max-h-[220px] overflow-y-auto divide-y divide-[#f0f0f0]">
            <?php foreach ($countries as $country): ?>
                <li>
                    <a href="#country" class="flex items-center gap-3 px-4 py-3 text-[15px] text-[#1e1e1e] hover:bg-[#f8f8f8]">
                        <span class="size-6 shrink-0 overflow-hidden rounded-sm">
                            <img src="<?= $asset ?>/flags/<?= htmlspecialchars($country['flag']) ?>" alt="" class="h-full w-full object-contain">
                        </span>
                        <span class="flex-1"><?= htmlspecialchars($country['name']) ?></span>
                        <span class="size-5 shrink-0 overflow-hidden opacity-50">
                            <img src="<?= $asset ?>/icons/chevron-right.svg" alt="" class="h-full w-full object-contain">
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- League Table -->
    <div class="overflow-hidden rounded-[16px] bg-white">
        <div class="border-b border-[#eee] px-4 py-3.5 text-center">
            <h3 class="text-[17px] font-bold text-[#1e1e1e]">League Table</h3>
        </div>
        <div class="flex gap-1 border-b border-[#eee] px-2 py-2">
            <?php
            $tabs = ['ENG', 'SPA', 'GER', 'ITA', 'FRA'];
            foreach ($tabs as $i => $tab):
            ?>
                <button type="button" class="flex-1 rounded-lg px-1 py-2 text-[12px] font-semibold sm:text-[13px] <?= $i === 0 ? 'bg-[#162640] text-white' : 'bg-[#f5f5f5] text-[#5a5a5a]' ?>">
                    <?= $tab ?>
                </button>
            <?php endforeach; ?>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[13px] text-[#1e1e1e]">
                <thead class="bg-[#f5f5f5] text-[11px] uppercase text-[#767676]">
                    <tr>
                        <th class="px-3 py-2">#</th>
                        <th class="px-2 py-2">Club</th>
                        <th class="px-2 py-2 text-center">P</th>
                        <th class="px-2 py-2 text-center">GD</th>
                        <th class="px-3 py-2 text-center">Pts</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leagueTable as $i => $row): ?>
                        <tr class="border-t border-[#f0f0f0] <?= $i === 0 ? 'bg-[#e8f0fe]' : '' ?>">
                            <td class="px-3 py-2 font-semibold"><?= (int) $row['pos'] ?></td>
                            <td class="px-2 py-2">
                                <div class="flex items-center gap-2">
                                    <span class="size-5 shrink-0 overflow-hidden">
                                        <img src="<?= $asset ?>/teams/<?= htmlspecialchars($row['logo']) ?>" alt="" class="h-full w-full object-contain">
                                    </span>
                                    <span class="truncate font-medium"><?= htmlspecialchars($row['club']) ?></span>
                                </div>
                            </td>
                            <td class="px-2 py-2 text-center"><?= (int) $row['p'] ?></td>
                            <td class="px-2 py-2 text-center"><?= (int) $row['gd'] ?></td>
                            <td class="px-3 py-2 text-center font-bold"><?= (int) $row['pts'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Scorers -->
    <div class="overflow-hidden rounded-[16px] bg-white">
        <div class="border-b border-[#eee] px-4 py-3.5 text-center">
            <h3 class="text-[17px] font-bold text-[#1e1e1e]">Top Scorers</h3>
        </div>
        <div class="flex gap-1 border-b border-[#eee] px-2 py-2">
            <?php foreach ($tabs as $i => $tab): ?>
                <button type="button" class="flex-1 rounded-lg px-1 py-2 text-[12px] font-semibold sm:text-[13px] <?= $i === 0 ? 'bg-[#162640] text-white' : 'bg-[#f5f5f5] text-[#5a5a5a]' ?>">
                    <?= $tab ?>
                </button>
            <?php endforeach; ?>
        </div>
        <table class="w-full text-left text-[13px] text-[#1e1e1e]">
            <thead class="bg-[#f5f5f5] text-[11px] uppercase text-[#767676]">
                <tr>
                    <th class="px-4 py-2">Player</th>
                    <th class="px-2 py-2 text-center">Club</th>
                    <th class="px-4 py-2 text-center">G</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topScorers as $scorer): ?>
                    <tr class="border-t border-[#f0f0f0]">
                        <td class="px-4 py-2.5 font-medium"><?= htmlspecialchars($scorer['player']) ?></td>
                        <td class="px-2 py-2.5">
                            <span class="mx-auto flex size-6 overflow-hidden">
                                <img src="<?= $asset ?>/teams/<?= htmlspecialchars($scorer['club']) ?>" alt="" class="h-full w-full object-contain">
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-center font-bold"><?= (int) $scorer['goals'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</aside>
