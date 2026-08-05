<?php
$planResults = $planResults ?? [
    ['day' => 'tues', 'date' => '12/24'],
    ['day' => 'weds', 'date' => '12/24'],
    ['day' => 'thurs', 'date' => '12/24'],
    ['day' => 'thurs', 'date' => '12/24'],
    ['day' => 'thurs', 'date' => '12/24'],
    ['day' => 'fri', 'date' => '12/24'],
    ['day' => 'fri', 'date' => '12/24'],
];
?>
<section id="invest" class="relative mt-8 overflow-hidden rounded-[20px] p-4 sm:mt-12 sm:rounded-[30px] sm:p-[25px]">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute inset-0 rounded-[20px] bg-[#0a111d] sm:rounded-[30px]"></div>
        <img
            src="<?= $asset ?>/images/invest-bg.png"
            alt=""
            class="absolute inset-0 size-full rounded-[20px] object-cover opacity-20 backdrop-blur-[6px] sm:rounded-[30px]"
        >
    </div>

    <div class="relative z-10 flex flex-col gap-4 sm:gap-5">
        <div class="flex flex-col items-center gap-2 text-center text-white sm:gap-2.5">
            <h3 class="w-full text-[22px] font-semibold leading-normal sm:text-[26px] lg:text-[32px]">
                Grow more With our Investment Plan
            </h3>
            <p class="w-full text-[14px] font-normal leading-normal sm:text-[16px] lg:text-[20px]">
                Join Investment Scheme, Stand a Chance To Make Huge Profit
            </p>
            <a
                href="#subscribe"
                class="mt-1 inline-flex w-full max-w-[308px] items-center justify-center gap-2 rounded-[16px] bg-gradient-to-b from-[#b48701] to-[#967000] p-4 backdrop-blur-[7.25px] sm:gap-2.5 sm:rounded-[20px] sm:p-5"
            >
                <span class="size-5 shrink-0 overflow-hidden sm:size-[22px]">
                    <img src="<?= $asset ?>/icons/trophy-emoji.svg" alt="" class="h-full w-full object-contain">
                </span>
                <span class="text-[15px] font-semibold text-white sm:text-[16px]">Get Access now</span>
            </a>
        </div>

        <div class="rounded-[16px] bg-black/40 p-3 sm:rounded-[20px] sm:p-[15px]">
            <p class="mb-2 text-left text-[16px] font-semibold capitalize text-white sm:mb-2.5 sm:text-[20px]">
                Premium plan results
            </p>
            <div class="flex gap-2 overflow-x-auto pb-1 sm:gap-2.5">
                <?php foreach ($planResults as $result): ?>
                    <div class="flex h-[72px] min-w-[64px] flex-1 flex-col items-center justify-center gap-1 rounded-[12px] border border-[#14ae5c] bg-black/30 px-2 py-2 backdrop-blur-[7.45px] sm:h-[85px] sm:min-w-[72px] sm:rounded-[15px] sm:px-[15px] sm:py-3">
                        <p class="w-full text-center text-[11px] font-bold uppercase text-[#f5f5f5] sm:text-[12px]">
                            <?= htmlspecialchars($result['day']) ?>
                        </p>
                        <span class="size-5 shrink-0 overflow-hidden sm:size-6">
                            <img src="<?= $asset ?>/icons/check-green.svg" alt="" class="h-full w-full object-contain">
                        </span>
                        <p class="w-full text-center text-[11px] font-bold uppercase text-[#f5f5f5] sm:text-[12px]">
                            <?= htmlspecialchars($result['date']) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
