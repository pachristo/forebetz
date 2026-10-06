<section id="subscribe" class="relative mt-6 overflow-hidden rounded-[20px] sm:mt-8 sm:rounded-[30px]">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute inset-0 rounded-[20px] bg-[#1a1a1a] sm:rounded-[30px]"></div>
        <img
            src="<?= $asset ?>/images/packages-bg.png"
            alt=""
            class="absolute inset-0 size-full rounded-[20px] object-cover opacity-20 backdrop-blur-[6px] sm:rounded-[30px]"
        >
    </div>

    <div class="relative z-10 overflow-hidden rounded-[20px] p-3 sm:rounded-[30px] sm:p-[15px]">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute inset-0 rounded-[20px] bg-[#0a0a0a] sm:rounded-[30px]"></div>
            <img
                src="<?= $asset ?>/images/invest-bg.png"
                alt=""
                class="absolute inset-0 size-full rounded-[20px] object-cover opacity-20 sm:rounded-[30px]"
            >
        </div>

        <div class="relative z-10 flex flex-col gap-4 sm:gap-5">
            <h2 class="text-center text-[24px] font-bold text-white sm:text-[28px] lg:text-[32px]">Packages</h2>

            <div class="grid gap-3 sm:gap-[15px] md:grid-cols-2">
                <?php
                $packages = [
                    [
                        'badge' => 'Weekly | 2 Weeks | Monthly',
                        'title' => 'Premium Plan',
                        'features' => [
                            'Sure 2 Odds daily',
                            '24/7 Support',
                            'Access to Risk Management Guide.',
                        ],
                    ],
                    [
                        'badge' => '2 Weeks | Monthly',
                        'title' => 'Correct Score',
                        'features' => [
                            'Sure 1.50 - 1.90 Odds daily',
                            '24/7 Support',
                            'Access to Risk Management Guide.',
                        ],
                    ],
                ];
                foreach ($packages as $pkg):
                ?>
                <div class="flex flex-col rounded-[20px] border-2 border-[#ff312d] bg-gradient-to-b from-[#1a1a1a] to-[#262626] p-1 backdrop-blur-[7.45px] sm:rounded-[25px] sm:p-[5px]">
                    <div class="flex min-h-0 flex-col gap-4 p-4 sm:min-h-[244px] sm:gap-[17px] sm:p-5">
                        <div class="flex flex-col gap-2 pb-2 sm:gap-2.5 sm:pb-2.5">
                            <span class="inline-flex w-fit rounded-[5px] bg-[#ff6900] px-2.5 py-1 text-[13px] font-bold text-black sm:text-[15px]">
                                <?= htmlspecialchars($pkg['badge']) ?>
                            </span>
                            <h3 class="text-[22px] font-bold text-[#f0f0f0] sm:text-[26px]">
                                <?= htmlspecialchars($pkg['title']) ?>
                            </h3>
                        </div>
                        <ul class="flex flex-col gap-2 border-t border-[#b7bcc4] py-2.5 sm:gap-[9px]">
                            <?php foreach ($pkg['features'] as $feature): ?>
                                <li class="flex items-center gap-2.5 text-[13px] text-[#e9e9e9] sm:text-[14px]">
                                    <span class="size-5 shrink-0 overflow-hidden sm:size-[22px]">
                                        <img src="<?= $asset ?>/icons/check-fill.svg" alt="" class="h-full w-full object-contain">
                                    </span>
                                    <?= htmlspecialchars($feature) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <a
                        href="#subscribe"
                        class="mb-1 mx-1 flex h-[48px] items-center justify-center gap-2 rounded-[16px] px-6 text-[14px] font-bold uppercase text-black sm:mb-[5px] sm:mx-[5px] sm:h-[54px] sm:rounded-[20px] sm:px-8 sm:text-[16px]"
                        style="background-image: linear-gradient(109deg, #ff7a1a 13%, #cc5400 101%);"
                    >
                        subscribe
                        <span class="size-5 shrink-0 overflow-hidden sm:size-6">
                            <img src="<?= $asset ?>/icons/arrow-right.svg" alt="" class="h-full w-full object-contain">
                        </span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
