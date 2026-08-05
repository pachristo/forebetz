<?php
/**
 * Category / market page hero. Override via $categoryTitle, $categoryDesc.
 */
$categoryTitle = $categoryTitle ?? 'Forebetz Acca tips';
$categoryDesc = $categoryDesc ?? 'Forebetz Acca tips tips predictions today.';
?>
<section class="relative z-10 w-full px-2.5 pb-4 pt-2 sm:px-8 lg:px-[100px]">
    <div class="mx-auto flex w-full max-w-site flex-col gap-4 rounded-[30px] py-4 sm:gap-5 sm:py-5">
        <div class="flex flex-col items-start justify-between gap-5 lg:flex-row lg:gap-6">
            <div class="flex w-full max-w-[836px] flex-col gap-4 sm:gap-5">
                <div class="flex flex-col gap-1 text-white sm:gap-1.5">
                    <h1 class="text-[28px] font-medium leading-tight sm:text-[40px] lg:text-[48px]">
                        <?php
                        if (stripos($categoryTitle, 'Forebetz') === 0) {
                            $rest = trim(substr($categoryTitle, strlen('Forebetz')));
                            echo '<span class="text-[#fcbd02]">Forebetz</span> ' . htmlspecialchars($rest);
                        } else {
                            echo htmlspecialchars($categoryTitle);
                        }
                        ?>
                    </h1>
                    <p class="text-[14px] font-normal leading-normal text-white sm:text-[16px] sm:leading-7 lg:text-[18px]">
                        <?= htmlspecialchars($categoryDesc) ?>
                    </p>
                </div>
                <div class="flex w-full flex-col gap-2 sm:flex-row sm:flex-wrap sm:gap-2.5">
                    <a href="#whatsapp" class="inline-flex w-full flex-1 items-center justify-center gap-2 rounded-[13px] bg-gradient-to-b from-[#45c655] to-[#216029] px-6 py-4 text-[17px] font-semibold text-white sm:w-auto sm:rounded-[15px] sm:px-[29px] sm:py-5 sm:text-[20px]">
                        <span class="size-[19px] shrink-0 overflow-hidden sm:size-[22px]">
                            <img src="<?= $asset ?>/icons/whatsapp-logo.svg" alt="" class="h-full w-full object-contain">
                        </span>
                        Join Whatsapp
                    </a>
                    <a href="#banker" class="inline-flex w-full flex-1 items-center justify-center gap-2 rounded-[17px] bg-gradient-to-b from-[#cb5140] to-[#8b392d] px-6 py-4 text-[17px] font-bold capitalize text-[#f3f3f3] sm:max-w-[260px] sm:rounded-[20px] sm:px-8 sm:py-5 sm:text-[20px]">
                        Banker Tips
                        <span class="size-5 shrink-0 overflow-hidden sm:size-6">
                            <img src="<?= $asset ?>/icons/fire.svg" alt="" class="h-full w-full object-contain">
                        </span>
                    </a>
                </div>
            </div>
            <div class="hidden min-h-[180px] w-full items-center justify-center self-stretch bg-white px-8 py-16 lg:flex lg:w-[530px] lg:shrink-0">
                <p class="text-center text-[24px] text-[#162640]">Ads Section</p>
            </div>
        </div>
        <div class="flex h-[95px] w-full items-center justify-center bg-white px-6 sm:h-auto sm:py-8">
            <p class="text-center text-[17px] text-[#162640] sm:text-[24px]">Ads Section</p>
        </div>
    </div>
</section>
