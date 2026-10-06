<?php
/**
 * League page hero — desktop 377:71910 / mobile 465:61788
 * Override: $leagueName, $leagueCountry, $leagueLogo, $leagueOptions
 */
$leagueName = $leagueName ?? 'Serie A';
$leagueCountry = $leagueCountry ?? 'Italy';
$leagueLogo = $leagueLogo ?? 'serie-a.png';
$leagueOptions = $leagueOptions ?? ['Serie A', 'Premier League', 'La Liga', 'Bundesliga', 'Ligue 1'];
?>
<section class="relative z-10 w-full px-2.5 pb-2 pt-2 sm:px-8 sm:pb-3 lg:px-[100px]">
    <div class="mx-auto flex w-full max-w-site flex-col items-start gap-[15px] rounded-[20px] py-3.5 sm:flex-row sm:items-center sm:justify-between sm:gap-5 sm:rounded-[30px] sm:py-5">
        <div class="flex min-w-0 w-full flex-1 items-center gap-[15px] sm:gap-5">
            <div class="size-[61px] shrink-0 overflow-hidden sm:size-[72px] lg:size-[84px]">
                <img src="<?= $asset ?>/leagues/<?= htmlspecialchars($leagueLogo) ?>" alt="" class="h-full w-full object-contain">
            </div>
            <div class="min-w-0 flex flex-1 flex-col gap-0 sm:gap-1.5">
                <h1 class="truncate text-[32px] font-semibold leading-tight text-[#ff6900] sm:text-[40px] lg:text-[48px]">
                    <?= htmlspecialchars($leagueName) ?>
                </h1>
                <p class="text-[15px] font-normal leading-5 text-white sm:text-[18px] lg:text-[20px]">
                    <?= htmlspecialchars($leagueCountry) ?>
                </p>
            </div>
        </div>

        <label class="relative inline-flex w-[176px] shrink-0 items-center sm:w-[200px] lg:w-[241px]">
            <span class="sr-only">Select league</span>
            <select
                class="w-full appearance-none rounded-[7px] bg-white py-1.5 pl-2 pr-8 text-[13px] text-[#303030] outline-none focus:ring-2 focus:ring-[#ff6900] sm:rounded-[10px] sm:py-2.5 sm:pl-2.5 sm:pr-10 sm:text-[16px] lg:text-[18px]"
                aria-label="Select league"
            >
                <?php foreach ($leagueOptions as $opt): ?>
                    <option value="<?= htmlspecialchars($opt) ?>" <?= $opt === $leagueName ? 'selected' : '' ?>>
                        <?= htmlspecialchars($opt) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="pointer-events-none absolute right-2 top-1/2 size-[13px] -translate-y-1/2 overflow-hidden sm:right-2.5 sm:size-[18px]">
                <img src="<?= $asset ?>/icons/dropdown-down.svg" alt="" class="h-full w-full object-contain">
            </span>
        </label>
    </div>
</section>
