<?php
/**
 * Match detail body — Figma desktop 353:26060 / mobile 465:35847
 * Expects: $matchDetail, $h2hMatches, $homeLast5, $awayLast5, $standings, $conclusion, $asset
 */
$m = $matchDetail;

$formLetterClass = static function (string $f): string {
    return match ($f) {
        'w' => 'bg-[#14ae5c] text-white',
        'd' => 'bg-[#767676] text-white',
        'l' => 'bg-[#ef1410] text-white',
        default => 'bg-[#767676] text-white',
    };
};

$formLetter = static function (string $f): string {
    return strtoupper($f === 'd' ? 'D' : $f);
};

$resultDot = static function (string $f) use ($asset): string {
    return match ($f) {
        'w' => $asset . '/icons/form-w.svg',
        'd' => $asset . '/icons/form-draw.svg',
        'l' => $asset . '/icons/form-lose.svg',
        default => $asset . '/icons/form-draw.svg',
    };
};
?>

<!-- Match overview -->
<section class="rounded-[16px] bg-white p-2.5 sm:rounded-[24px] sm:p-5 md:rounded-[30px] md:p-[30px]">
    <div class="mb-3 flex flex-col gap-2 sm:mb-5 sm:gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="/category.php" class="inline-flex size-6 shrink-0 items-center justify-center overflow-hidden sm:size-8" aria-label="Back">
                <img src="<?= $asset ?>/icons/back.svg" alt="" class="size-6 -scale-x-100 object-contain sm:size-8">
            </a>
            <div class="flex items-center gap-2 sm:gap-2.5">
                <span class="h-[18px] w-[27px] shrink-0 overflow-hidden rounded-[2px] sm:h-6 sm:w-9">
                    <img src="<?= $asset ?>/flags/<?= htmlspecialchars($m['flag']) ?>" alt="" class="h-full w-full object-cover">
                </span>
                <span class="text-[13px] font-medium text-[#5a5a5a] sm:text-[16px]"><?= htmlspecialchars($m['country']) ?></span>
                <span class="text-[13px] font-medium text-[#1e1e1e] sm:text-[16px]"><?= htmlspecialchars($m['league']) ?></span>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-x-2 gap-y-1.5 text-[12px] text-[#5a5a5a] sm:justify-start sm:gap-x-3 sm:text-[15px] lg:justify-end">
            <span class="inline-flex items-center gap-1 sm:gap-1.5">
                <span class="size-3 shrink-0 overflow-hidden sm:size-4"><img src="<?= $asset ?>/icons/date.svg" alt="" class="h-full w-full object-contain"></span>
                <?= htmlspecialchars($m['date']) ?>
            </span>
            <span class="h-3 w-px bg-[#dadde2] sm:h-4" aria-hidden="true"></span>
            <span class="inline-flex items-center gap-1 sm:gap-1.5">
                <span class="size-3 shrink-0 overflow-hidden sm:size-4"><img src="<?= $asset ?>/icons/time.svg" alt="" class="h-full w-full object-contain"></span>
                <?= htmlspecialchars($m['kickoff_display']) ?>
            </span>
            <span class="h-3 w-px bg-[#dadde2] sm:h-4" aria-hidden="true"></span>
            <span class="inline-flex items-center gap-1 sm:gap-1.5">
                <span class="size-3 shrink-0 overflow-hidden sm:size-4"><img src="<?= $asset ?>/icons/stadium.svg" alt="" class="h-full w-full object-contain"></span>
                <?= htmlspecialchars($m['venue']) ?>
            </span>
        </div>
    </div>

    <!-- Always horizontal: home | kickoff | away (Figma 465:35847) -->
    <div class="flex items-center justify-between gap-1 py-1 sm:justify-center sm:gap-6 md:gap-10 lg:gap-12">
        <div class="flex min-w-0 flex-1 flex-col items-center gap-1.5 sm:max-w-[285px] sm:gap-3">
            <div class="size-[80px] overflow-hidden sm:size-[120px] lg:size-[172px]">
                <img src="<?= $asset ?>/teams/<?= htmlspecialchars($m['home_logo']) ?>" alt="" class="h-full w-full object-contain">
            </div>
            <p class="max-w-full truncate text-center text-[13px] font-semibold text-[#1e1e1e] sm:text-[18px] lg:text-[22px]"><?= htmlspecialchars($m['home']) ?></p>
        </div>

        <div class="flex shrink-0 flex-col items-center gap-1 sm:gap-3">
            <div class="rounded-[5px] bg-[#f5f5f5] px-2 py-1 sm:rounded-[6px] sm:px-3 sm:py-1.5">
                <span class="text-[14px] font-semibold tracking-[0.16px] text-[#1e1e1e] sm:text-[18px] lg:text-[20px]"><?= htmlspecialchars($m['time']) ?></span>
            </div>
            <p class="whitespace-nowrap text-[12px] font-medium tracking-wide text-[#767676] sm:text-[16px] lg:text-[18px]"><?= htmlspecialchars($m['countdown']) ?></p>
        </div>

        <div class="flex min-w-0 flex-1 flex-col items-center gap-1.5 sm:max-w-[285px] sm:gap-3">
            <div class="size-[80px] overflow-hidden sm:size-[120px] lg:size-[172px]">
                <img src="<?= $asset ?>/teams/<?= htmlspecialchars($m['away_logo']) ?>" alt="" class="h-full w-full object-contain">
            </div>
            <p class="max-w-full truncate text-center text-[13px] font-semibold text-[#1e1e1e] sm:text-[18px] lg:text-[22px]"><?= htmlspecialchars($m['away']) ?></p>
        </div>
    </div>

    <!-- Expert + Probability: stacked mobile, 2-col from md (Figma 353:26131) -->
    <div class="mt-4 grid grid-cols-1 gap-3 sm:mt-5 sm:gap-4 md:grid-cols-2 md:gap-5">
        <div class="rounded-[14px] border border-[#ef1410] bg-white p-2 sm:rounded-[20px] sm:p-2.5">
            <div class="px-2 py-2 sm:px-2.5 sm:py-2.5">
                <h2 class="text-[14px] font-semibold text-[#1e1e1e] sm:text-[18px]">Expert Prediction</h2>
            </div>
            <div class="flex items-end justify-between gap-1.5 px-1.5 pb-2.5 pt-0.5 sm:gap-4 sm:px-2.5 sm:pb-3 sm:pt-1">
                <div class="flex min-w-0 flex-1 flex-col items-center">
                    <p class="mb-1 text-[11px] font-medium text-[#1a1a1a] sm:text-[14px]">Predictions</p>
                    <span
                        class="inline-flex max-w-full items-center justify-center rounded-[9px] px-2 py-2 sm:rounded-[11px] sm:px-3 sm:py-2.5"
                        style="background-image: linear-gradient(107deg, #ff6900 0%, #f26300 23%, #ee6100 28%, #f56500 79%, #dd5b00 90%);"
                    >
                        <span class="truncate text-[12px] font-bold text-[#1a1a1a] sm:text-[15px]"><?= htmlspecialchars($m['prediction']) ?></span>
                        <span class="ml-1 shrink-0 text-[11px] font-medium text-[#1a1a1a] sm:ml-1.5 sm:text-[15px]">(<?= htmlspecialchars($m['prediction_odds']) ?> odds)</span>
                    </span>
                </div>
                <div class="flex flex-col items-center">
                    <p class="mb-1 text-[11px] font-medium text-[#1a1a1a] sm:text-[14px]">Scores</p>
                    <span class="inline-flex min-w-[48px] items-center justify-center rounded-[9px] border border-[#dadde2] bg-white px-2 py-2 text-[12px] font-semibold text-[#1e1e1e] sm:min-w-[69px] sm:rounded-[11px] sm:px-3 sm:py-2.5 sm:text-[15px]">
                        <?= htmlspecialchars($m['score']) ?>
                    </span>
                </div>
                <div class="flex flex-col items-center">
                    <p class="mb-1 text-[11px] font-medium text-[#1a1a1a] sm:text-[14px]">Probability</p>
                    <div class="flex items-center gap-1 rounded-[9px] border border-[#dadde2] bg-white px-1.5 py-1.5 sm:gap-1.5 sm:rounded-[11px] sm:px-2.5 sm:py-2">
                        <span class="text-[12px] font-semibold text-[#1e1e1e] sm:text-[14px]"><?= (int) $m['prediction_prob'] ?>%</span>
                        <span class="size-[21px] shrink-0 overflow-hidden sm:size-[26px]">
                            <img src="<?= $asset ?>/icons/prob-green.svg" alt="" class="h-full w-full object-contain">
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-[14px] border border-[#ef1410] bg-white p-2 sm:rounded-[20px] sm:p-2.5">
            <div class="px-2 py-2 sm:px-2.5 sm:py-2.5">
                <h2 class="text-[14px] font-semibold text-[#1e1e1e] sm:text-[18px]">Match Probability</h2>
            </div>
            <div class="grid grid-cols-3 gap-1 px-1 pb-2.5 pt-0.5 sm:gap-2 sm:px-2.5 sm:pb-3 sm:pt-1">
                <?php foreach ($m['probs'] as $prob): ?>
                    <div class="flex flex-col items-center gap-1 sm:gap-1.5">
                        <p class="text-[11px] font-medium text-[#1a1a1a] sm:text-[14px]"><?= htmlspecialchars($prob['label']) ?></p>
                        <div class="flex items-center gap-0.5 sm:gap-1">
                            <span class="text-[12px] font-semibold text-[#1e1e1e] sm:text-[14px]"><?= (int) $prob['pct'] ?>%</span>
                            <span class="size-[21px] shrink-0 overflow-hidden sm:size-[26px]">
                                <img src="<?= $asset ?>/icons/<?= htmlspecialchars($prob['ring']) ?>" alt="" class="h-full w-full object-contain">
                            </span>
                        </div>
                        <p class="text-[12px] font-medium text-[#1e1e1e] sm:text-[15px]"><?= htmlspecialchars($prob['odds']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Team statistics -->
<section class="rounded-[16px] bg-white p-2.5 sm:rounded-[24px] sm:p-5 md:rounded-[30px] md:p-[30px]">
    <div class="mb-4 grid grid-cols-3 items-end gap-1 sm:mb-6 sm:gap-3">
        <div class="flex flex-col items-start gap-1 sm:gap-1.5">
            <div class="size-9 overflow-hidden sm:size-[54px]">
                <img src="<?= $asset ?>/teams/<?= htmlspecialchars($m['home_logo']) ?>" alt="" class="h-full w-full object-contain">
            </div>
            <p class="text-[12px] font-semibold text-[#1e1e1e] sm:text-[16px]"><?= htmlspecialchars($m['home']) ?></p>
        </div>
        <h2 class="pb-4 text-center text-[14px] font-semibold text-[#1e1e1e] sm:pb-6 sm:text-[18px] lg:text-[22px]">Team statistics</h2>
        <div class="flex flex-col items-end gap-1 sm:gap-1.5">
            <div class="size-9 overflow-hidden sm:size-[54px]">
                <img src="<?= $asset ?>/teams/<?= htmlspecialchars($m['away_logo']) ?>" alt="" class="h-full w-full object-contain">
            </div>
            <p class="text-right text-[12px] font-semibold text-[#1e1e1e] sm:text-[16px]"><?= htmlspecialchars($m['away']) ?></p>
        </div>
    </div>

    <div class="flex flex-col gap-3 sm:gap-5">
        <?php foreach ($m['stats'] as $stat): ?>
            <div>
                <div class="mb-1.5 grid grid-cols-3 items-center text-[12px] sm:mb-2 sm:text-[15px]">
                    <p class="text-center font-semibold text-[#1e1e1e]"><?= htmlspecialchars($stat['home']) ?></p>
                    <p class="px-0.5 text-center text-[11px] font-medium leading-tight text-[#5a5a5a] sm:text-[15px]"><?= htmlspecialchars($stat['label']) ?></p>
                    <p class="text-center font-semibold text-[#1e1e1e]"><?= htmlspecialchars($stat['away']) ?></p>
                </div>
                <div class="flex items-center gap-0.5 sm:gap-1">
                    <div class="flex h-2 flex-1 justify-end overflow-hidden rounded-l-full bg-[#f0f0f0] sm:h-[11px]">
                        <div class="h-full rounded-l-full bg-[#ff6900]" style="width: <?= (int) $stat['home_w'] ?>%"></div>
                    </div>
                    <div class="flex h-2 flex-1 justify-start overflow-hidden rounded-r-full bg-[#f0f0f0] sm:h-[11px]">
                        <div class="h-full rounded-r-full bg-[#c4c4c4]" style="width: <?= (int) $stat['away_w'] ?>%"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Compare Teams -->
<section class="rounded-[16px] bg-white p-2.5 sm:rounded-[24px] sm:p-5 md:rounded-[30px] md:p-[30px]">
    <div class="mb-4 grid grid-cols-3 items-start gap-1 sm:mb-6 sm:gap-3">
        <div class="flex flex-col items-start gap-1.5">
            <div class="flex flex-col items-start gap-1 sm:flex-row sm:items-center sm:gap-2">
                <div class="size-[26px] overflow-hidden sm:size-[39px]">
                    <img src="<?= $asset ?>/teams/<?= htmlspecialchars($m['home_logo']) ?>" alt="" class="h-full w-full object-contain">
                </div>
                <p class="text-[12px] font-semibold text-[#1e1e1e] sm:text-[16px]"><?= htmlspecialchars($m['home']) ?></p>
            </div>
            <div class="flex flex-wrap items-center gap-1 sm:gap-1.5">
                <?php foreach ($m['home_form'] as $f): ?>
                    <span class="inline-flex size-[15px] items-center justify-center rounded-full text-[9px] font-bold sm:size-[22px] sm:text-[11px] <?= $formLetterClass($f) ?>">
                        <?= $formLetter($f) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <h2 class="pt-1 text-center text-[14px] font-semibold text-[#1e1e1e] sm:pt-2 sm:text-[18px] lg:text-[22px]">Compare Teams</h2>

        <div class="flex flex-col items-end gap-1.5">
            <div class="flex flex-col items-end gap-1 sm:flex-row-reverse sm:items-center sm:gap-2">
                <div class="size-[26px] overflow-hidden sm:size-[39px]">
                    <img src="<?= $asset ?>/teams/<?= htmlspecialchars($m['away_logo']) ?>" alt="" class="h-full w-full object-contain">
                </div>
                <p class="text-right text-[12px] font-semibold text-[#1e1e1e] sm:text-[16px]"><?= htmlspecialchars($m['away']) ?></p>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-1 sm:gap-1.5">
                <?php foreach ($m['away_form'] as $f): ?>
                    <span class="inline-flex size-[15px] items-center justify-center rounded-full text-[9px] font-bold sm:size-[22px] sm:text-[11px] <?= $formLetterClass($f) ?>">
                        <?= $formLetter($f) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-3 sm:gap-5">
        <?php foreach ($m['compare'] as $row): ?>
            <div>
                <div class="mb-1.5 grid grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)_minmax(0,1fr)] items-center gap-1 text-[12px] sm:mb-2 sm:grid-cols-[1fr_auto_1fr] sm:gap-2 sm:text-[15px]">
                    <p class="text-center font-semibold text-[#1e1e1e]"><?= htmlspecialchars($row['home']) ?></p>
                    <div class="flex min-w-0 flex-col items-center justify-center gap-0.5 sm:flex-row sm:gap-2">
                        <span class="inline-flex size-[14px] shrink-0 items-center justify-center overflow-hidden rounded-full sm:size-[17px]" style="background: <?= htmlspecialchars($row['icon_bg']) ?>">
                            <img src="<?= $asset ?>/icons/<?= htmlspecialchars($row['icon']) ?>" alt="" class="size-[9px] object-contain sm:size-[11px]">
                        </span>
                        <p class="text-center text-[10px] font-medium leading-tight text-[#5a5a5a] sm:whitespace-nowrap sm:text-[15px]"><?= htmlspecialchars($row['label']) ?></p>
                    </div>
                    <p class="text-center font-semibold text-[#1e1e1e]"><?= htmlspecialchars($row['away']) ?></p>
                </div>
                <div class="flex items-center gap-0.5 sm:gap-1">
                    <div class="flex h-2 flex-1 justify-end overflow-hidden rounded-l-full bg-[#f0f0f0] sm:h-[11px]">
                        <div class="h-full rounded-l-full" style="width: <?= (int) $row['home_w'] ?>%; background: <?= htmlspecialchars($row['bar']) ?>"></div>
                    </div>
                    <div class="flex h-2 flex-1 justify-start overflow-hidden rounded-r-full bg-[#f0f0f0] sm:h-[11px]">
                        <div class="h-full rounded-r-full bg-[#c4c4c4]" style="width: <?= (int) $row['away_w'] ?>%"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Head to Head History — single column on mobile/tablet, 2-col desktop -->
<section class="rounded-[16px] bg-white p-2.5 sm:rounded-[24px] sm:p-5 md:rounded-[30px] md:p-[30px]">
    <h2 class="mb-3 text-center text-[15px] font-semibold text-[#1e1e1e] sm:mb-5 sm:text-[18px] lg:text-[22px]">Head to Head History</h2>

    <div class="grid grid-cols-1 gap-3 md:gap-4 lg:grid-cols-2 lg:gap-2.5">
        <div class="overflow-hidden rounded-[14px] border border-[#e6e9ec] bg-[#fafafa] sm:rounded-[20px]">
            <div class="flex items-center justify-between gap-2 border-b border-[#e6e9ec] bg-white px-3 py-3 sm:px-4 sm:py-4">
                <p class="text-[14px] font-semibold text-[#1e1e1e] sm:text-[16px]">Head to Head Match</p>
            </div>
            <div class="flex flex-col divide-y divide-[#e6e9ec]">
                <?php foreach ($h2hMatches as $row): ?>
                    <?php include __DIR__ . '/match-h2h-row.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="flex flex-col gap-3 lg:gap-2.5">
            <?php foreach ([
                ['title' => $m['home'] . ' Last 5 Matches', 'logo' => $m['home_logo'], 'form' => $m['home_form'], 'matches' => $homeLast5],
                ['title' => $m['away'] . ' Last 5 Matches', 'logo' => $m['away_logo'], 'form' => $m['away_form'], 'matches' => $awayLast5],
            ] as $list): ?>
                <div class="overflow-hidden rounded-[14px] border border-[#e6e9ec] bg-[#fafafa] sm:rounded-[20px]">
                    <div class="flex items-center justify-between gap-2 border-b border-[#e6e9ec] bg-white px-3 py-3 sm:px-4 sm:py-3.5">
                        <div class="flex min-w-0 items-center gap-1.5 sm:gap-2">
                            <span class="size-7 shrink-0 overflow-hidden sm:size-[39px]">
                                <img src="<?= $asset ?>/teams/<?= htmlspecialchars($list['logo']) ?>" alt="" class="h-full w-full object-contain">
                            </span>
                            <p class="truncate text-[13px] font-semibold text-[#1e1e1e] sm:text-[16px]"><?= htmlspecialchars($list['title']) ?></p>
                        </div>
                        <div class="flex shrink-0 items-center gap-0.5 sm:gap-1">
                            <?php foreach ($list['form'] as $f): ?>
                                <span class="inline-flex size-[16px] items-center justify-center rounded-full text-[8px] font-bold sm:size-[20px] sm:text-[10px] <?= $formLetterClass($f) ?>">
                                    <?= $formLetter($f) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="flex flex-col divide-y divide-[#e6e9ec]">
                        <?php foreach ($list['matches'] as $row): ?>
                            <?php include __DIR__ . '/match-h2h-row.php'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- League table -->
<section class="rounded-[16px] bg-white p-2.5 sm:rounded-[24px] sm:p-5 md:rounded-[30px] md:p-[30px]">
    <h2 class="mb-3 text-center text-[15px] font-semibold text-[#1e1e1e] sm:mb-5 sm:text-[18px] lg:text-[22px]">English Premier League Table</h2>

    <div class="-mx-1 overflow-x-auto sm:mx-0">
        <table class="w-full min-w-[720px] border-collapse text-[12px] text-[#1e1e1e] sm:min-w-[900px] sm:text-[15px]">
            <thead>
                <tr class="border-b border-[#e6e9ec] text-[#5a5a5a]">
                    <th class="px-1.5 py-2 text-left font-medium sm:px-2 sm:py-3">#</th>
                    <th class="px-1.5 py-2 text-left font-medium sm:px-2 sm:py-3">Team</th>
                    <th class="px-1 py-2 text-center font-medium sm:py-3">GP</th>
                    <th class="px-1 py-2 text-center font-medium sm:py-3">W</th>
                    <th class="px-1 py-2 text-center font-medium sm:py-3">D</th>
                    <th class="px-1 py-2 text-center font-medium sm:py-3">L</th>
                    <th class="px-1 py-2 text-center font-medium sm:py-3">G</th>
                    <th class="px-1 py-2 text-center font-medium sm:py-3">GD</th>
                    <th class="px-1 py-2 text-center font-medium sm:py-3">P</th>
                    <th class="px-1.5 py-2 text-center font-medium sm:px-2 sm:py-3">FORM</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($standings as $team): ?>
                    <?php
                    $zoneBorder = match ($team['zone'] ?? '') {
                        'cl' => 'border-l-[3px] border-l-[#14ae5c]',
                        'cl2' => 'border-l-[3px] border-l-[#2b7fff]',
                        'cl3' => 'border-l-[3px] border-l-[#ff6900]',
                        'rel' => 'border-l-[3px] border-l-[#ef1410]',
                        default => 'border-l-[3px] border-l-transparent',
                    };
                    ?>
                    <tr class="border-b border-[#f0f0f0] <?= $zoneBorder ?>">
                        <td class="px-1.5 py-2.5 font-medium sm:px-2 sm:py-3"><?= (int) $team['pos'] ?></td>
                        <td class="px-1.5 py-2.5 sm:px-2 sm:py-3">
                            <div class="flex items-center gap-1.5 sm:gap-2">
                                <span class="size-7 shrink-0 overflow-hidden sm:size-8">
                                    <img src="<?= $asset ?>/teams/<?= htmlspecialchars($team['logo']) ?>" alt="" class="h-full w-full object-contain">
                                </span>
                                <span class="font-medium"><?= htmlspecialchars($team['club']) ?></span>
                            </div>
                        </td>
                        <td class="px-1 py-2.5 text-center sm:py-3"><?= (int) $team['gp'] ?></td>
                        <td class="px-1 py-2.5 text-center sm:py-3"><?= (int) $team['w'] ?></td>
                        <td class="px-1 py-2.5 text-center sm:py-3"><?= (int) $team['d'] ?></td>
                        <td class="px-1 py-2.5 text-center sm:py-3"><?= (int) $team['l'] ?></td>
                        <td class="px-1 py-2.5 text-center whitespace-nowrap sm:py-3"><?= htmlspecialchars($team['g']) ?></td>
                        <td class="px-1 py-2.5 text-center sm:py-3"><?= htmlspecialchars($team['gd']) ?></td>
                        <td class="px-1 py-2.5 text-center font-semibold sm:py-3"><?= (int) $team['pts'] ?></td>
                        <td class="px-1.5 py-2.5 sm:px-2 sm:py-3">
                            <div class="flex items-center justify-center gap-0.5 sm:gap-1">
                                <?php foreach ($team['form'] as $f): ?>
                                    <span class="inline-flex size-[18px] items-center justify-center rounded-full text-[8px] font-bold sm:size-[22px] sm:text-[10px] <?= $formLetterClass($f) ?>">
                                        <?= $formLetter($f) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3 flex flex-col gap-1.5 text-[11px] text-[#5a5a5a] sm:mt-5 sm:gap-2 sm:text-[14px]">
        <div class="flex items-center gap-2 sm:gap-3">
            <span class="size-3 shrink-0 rounded-[3px] bg-[#14ae5c] sm:size-[14px]"></span>
            <span>Promotion - Champions League (League phase: )</span>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <span class="size-3 shrink-0 rounded-[3px] bg-[#2b7fff] sm:size-[14px]"></span>
            <span>Promotion - Champions League (League phase: )</span>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <span class="size-3 shrink-0 rounded-[3px] bg-[#ff6900] sm:size-[14px]"></span>
            <span>Promotion - Champions League (League phase: )</span>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <span class="size-3 shrink-0 rounded-[3px] bg-[#ef1410] sm:size-[14px]"></span>
            <span>Relegation</span>
        </div>
    </div>
</section>

<!-- Conclusion -->
<section class="rounded-[16px] bg-white p-4 sm:rounded-[24px] sm:p-6 md:rounded-[30px] md:p-8">
    <h2 class="mb-3 text-center text-[18px] font-bold text-[#ef1410] sm:mb-5 sm:text-[22px] lg:text-[26px]">Conclusion</h2>
    <p class="text-[13px] leading-6 text-[#303030] sm:text-[15px] sm:leading-7 lg:text-[16px]">
        <?= htmlspecialchars($conclusion) ?>
    </p>
</section>
