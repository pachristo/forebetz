<section class="w-full">
    <?php
    $sectionTitle = $sectionTitle ?? 'Free Football Predictions';
    $sectionDate = $sectionDate ?? 'Wed, Mar 19th 2025';
    $includeInvestment = $includeInvestment ?? true;
    $hideDateNav = $hideDateNav ?? false;
    ?>
    <div class="mb-3 flex flex-col gap-2 sm:mb-[15px] sm:gap-2.5">
        <div>
            <h2 class="text-[22px] font-medium text-[#1e1e1e] sm:text-[28px] lg:text-[32px]"><?= htmlspecialchars($sectionTitle) ?></h2>
            <p class="text-[13px] leading-5 text-[#303030] sm:text-[16px] sm:leading-[30px] lg:text-[18px]"><?= htmlspecialchars($sectionDate) ?></p>
        </div>
        <div class="flex flex-col gap-2 border-b border-[#d9d9d9] pb-2.5 sm:gap-3 sm:pb-[15px] <?= $hideDateNav ? '' : 'md:flex-row md:items-center md:justify-between' ?>">
            <div class="flex h-[40px] w-full max-w-full rounded-[26px] bg-[#fde8e7] p-1 sm:h-[56px] sm:max-w-[514px] sm:rounded-[40px] sm:p-[5px]">
                <button type="button" class="flex flex-1 items-center justify-center rounded-[44px] bg-white px-2 py-1 text-center text-[11px] font-medium capitalize tracking-[0.13px] text-[#303030] sm:px-[15px] sm:py-2 sm:text-[17px]">
                    Yesterday
                </button>
                <button type="button" class="flex flex-1 items-center justify-center rounded-[44px] bg-gradient-to-b from-[#ef1410] to-[#890b09] px-2 py-1 text-center text-[11px] font-bold capitalize tracking-[0.13px] text-white sm:px-[15px] sm:py-2 sm:text-[17px]">
                    Today
                </button>
                <button type="button" class="flex flex-1 items-center justify-center rounded-[44px] bg-white px-2 py-1 text-center text-[11px] font-medium capitalize tracking-[0.13px] text-[#303030] sm:px-[15px] sm:py-2 sm:text-[17px]">
                    Tomorrow
                </button>
            </div>
            <?php if (!$hideDateNav): ?>
            <div class="flex items-center justify-center gap-1.5 p-1 sm:gap-2 sm:justify-start">
                <button type="button" class="flex size-[34px] items-center justify-center rounded-full bg-white p-1 sm:size-[51px] sm:p-[7px]" aria-label="Previous day">
                    <span class="size-5 rotate-180 overflow-hidden sm:size-[30px]">
                        <img src="<?= $asset ?>/icons/chevron-right.svg" alt="" class="h-full w-full object-contain">
                    </span>
                </button>
                <div class="flex h-[34px] items-center gap-2 rounded-full bg-white px-3 text-[10px] font-semibold text-[#5a5a5a] sm:h-[51px] sm:gap-3 sm:px-4 sm:text-[15px]">
                    <span class="size-[18px] shrink-0 overflow-hidden sm:size-[27px]">
                        <img src="<?= $asset ?>/icons/calendar.svg" alt="" class="h-full w-full object-contain">
                    </span>
                    19/12
                </div>
                <button type="button" class="flex size-[34px] items-center justify-center rounded-full bg-white p-1 sm:size-[51px] sm:p-[7px]" aria-label="Next day">
                    <span class="size-5 overflow-hidden sm:size-[30px]">
                        <img src="<?= $asset ?>/icons/chevron-right.svg" alt="" class="h-full w-full object-contain">
                    </span>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex flex-col gap-2.5">
        <?php foreach ($matches as $match): ?>
            <?php include __DIR__ . '/match-card.php'; ?>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($includeInvestment)): ?>
        <?php include __DIR__ . '/investment.php'; ?>
    <?php endif; ?>
</section>
