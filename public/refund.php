<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Refund Policy — Forebetz Legal Terms';
$pageDescription = 'Forebetz refund policy for prediction packages, service credits, and exceptional circumstances.';
$lastUpdated = 'December 13, 2025';

include __DIR__ . '/includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/includes/header.php'; ?>

        <section class="w-full px-2.5 pt-2 sm:px-8 sm:pt-2.5 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site py-3 sm:py-5">
                <h1 class="text-[32px] font-medium leading-tight text-[#fcbd02] sm:text-[40px] lg:text-[48px]">
                    Refund Policy
                </h1>
                <p class="mt-1 text-[14px] font-normal text-white sm:mt-1.5 sm:text-[16px] sm:leading-7 lg:text-[18px]">
                    Legal Terms
                </p>
            </div>
        </section>

        <main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
            <div class="mx-auto flex w-full max-w-site flex-col gap-6 rounded-[20px] bg-[#f0f0f0] px-[15px] py-5 text-[#1e1e1e] sm:gap-8 sm:rounded-[30px] sm:px-10 sm:py-8 lg:gap-[30px] lg:px-[150px] lg:py-10">
                <div class="flex flex-col gap-5 sm:gap-6 lg:gap-[30px]">
                    <div class="flex w-full items-center border-l-4 border-[#ef1410] bg-[#fff8e6] px-4 py-2.5 shadow-[0_0_0.8px_rgba(0,0,0,0.25)] sm:px-5 sm:py-4 lg:p-5">
                        <div class="flex items-center gap-2 pt-0.5 pr-2">
                            <span class="size-6 shrink-0 overflow-hidden">
                                <img src="<?= $asset ?>/icons/calendar-duotone.svg" alt="" class="h-full w-full object-contain">
                            </span>
                            <p class="text-[16px] font-semibold text-[#1e1e1e] sm:text-[18px] lg:text-[20px]">
                                Last Updated: <?= htmlspecialchars($lastUpdated) ?>
                            </p>
                        </div>
                    </div>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            1. Our Commitment
                        </h2>
                        <p class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            At Forebetz, we're dedicated to providing accurate football predictions and expert betting tips. Due to the digital nature of our services and the inherent unpredictability of sports outcomes, we maintain a strict no-refund policy for our prediction services.
                        </p>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            2. No Refund Policy
                        </h2>
                        <div class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            <p class="mb-3">All purchases of our prediction packages are final. We do not provide refunds for:</p>
                            <ul class="list-disc space-y-0 pl-[21px] sm:pl-[27px]">
                                <li>Prediction accuracy or performance</li>
                                <li>Changes in betting odds or match outcomes</li>
                                <li>Personal circumstances or change of mind</li>
                                <li>Inability to place bets with bookmakers</li>
                                <li>Service suspension due to policy violations</li>
                            </ul>
                        </div>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            3. Service Guarantee
                        </h2>
                        <p class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            While we strive for accuracy, we cannot guarantee specific betting results. Our predictions are based on thorough analysis but remain subject to the unpredictable nature of sports.
                        </p>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            4. Exceptional Circumstances
                        </h2>
                        <div class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            <p class="mb-3">Refund requests will only be considered in these specific cases:</p>
                            <ul class="list-disc space-y-0 pl-[21px] sm:pl-[27px]">
                                <li>Duplicate charges for the same subscription</li>
                                <li>Technical issues preventing access to our services for more than 48 hours</li>
                                <li>Unauthorized transactions (verification required)</li>
                            </ul>
                        </div>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            5. Service Credits
                        </h2>
                        <p class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            In cases of verified service disruptions, we may offer subscription extensions at our discretion.
                        </p>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            6. How to Contact Us
                        </h2>
                        <div class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            <p class="mb-3">
                                For any questions or concerns about this policy, please contact our support team:
                                <a href="mailto:infoForebetz@gmail.com" class="font-bold">infoForebetz@gmail.com</a>
                            </p>
                            <p>Our support team is available Monday to Friday, 9:00 AM to 5:00 PM GMT.</p>
                        </div>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            7. Policy Updates
                        </h2>
                        <p class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            Forebetz reserves the right to modify this refund policy at any time. Any changes will be effective immediately upon posting on our website. Your continued use of our services constitutes acceptance of the updated policy.
                        </p>
                    </section>
                </div>

                <section class="w-full overflow-hidden rounded-[20px] bg-[#dcdee2] p-2 sm:rounded-[30px] sm:p-2.5">
                    <div class="flex h-[56px] items-center justify-center border-b border-black/8 px-2.5 sm:h-[72px]">
                        <h2 class="text-center text-[20px] font-semibold capitalize tracking-[0.2px] text-[#1e1e1e] sm:text-[24px] lg:text-[28px]">
                            Contact Us
                        </h2>
                    </div>
                    <div class="flex flex-col gap-5 px-4 pb-8 pt-5 sm:gap-6 sm:px-[30px] sm:pb-10 sm:pt-5">
                        <div class="flex flex-col gap-2.5">
                            <p class="text-[15px] leading-7 text-[#1e1e1e] sm:text-[18px] sm:leading-7 lg:text-[20px]">
                                If you have any questions about what you've read here, feel free to email us at:
                            </p>
                            <a href="mailto:forebetzesupport@gmail.com" class="inline-flex items-center gap-2.5">
                                <span class="size-7 shrink-0 overflow-hidden sm:size-8">
                                    <img src="<?= $asset ?>/icons/email-noto.svg" alt="" class="h-full w-full object-contain">
                                </span>
                                <span class="text-[15px] font-medium underline sm:text-[18px] lg:text-[20px] lg:leading-[22px]">
                                    forebetzesupport@gmail.com
                                </span>
                            </a>
                            <a href="/" class="inline-flex items-center gap-2.5">
                                <span class="size-7 shrink-0 overflow-hidden sm:size-8">
                                    <img src="<?= $asset ?>/icons/web-flat.svg" alt="" class="h-full w-full object-contain">
                                </span>
                                <span class="text-[15px] font-medium sm:text-[18px] lg:text-[20px] lg:leading-[22px]">
                                    https://www.forebetz.com
                                </span>
                            </a>
                        </div>

                        <div class="flex w-full items-start gap-4 rounded-[16px] border-l-4 border-[#ef1410] bg-white p-4 sm:gap-[30px] sm:rounded-[20px] sm:p-[25px]">
                            <span class="size-7 shrink-0 overflow-hidden sm:size-8">
                                <img src="<?= $asset ?>/icons/info-circle.svg" alt="" class="h-full w-full object-contain">
                            </span>
                            <div class="min-w-0 flex-1 text-[14px] leading-7 sm:text-[18px] sm:leading-7 lg:text-[20px]">
                                <p class="font-medium text-[#1e1e1e]">Responsible Gambling:</p>
                                <p class="text-[#ef1410]">
                                    Forebetz encourages responsible gambling. Please remember that sports betting should be done for entertainment purposes only. Never wager more than you can afford to lose. If you feel you may have a gambling problem, please seek help from professional organizations in your area.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
