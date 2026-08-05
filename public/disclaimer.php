<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Disclaimer — Forebetz Legal Terms';
$pageDescription = 'Forebetz disclaimer and legal terms. Informational sports predictions only — not a bookmaker.';
$lastUpdated = 'December 13, 2025';

include __DIR__ . '/../includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <section class="w-full px-2.5 pt-2 sm:px-8 sm:pt-2.5 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site py-3 sm:py-5">
                <h1 class="text-[32px] font-medium leading-tight text-[#fcbd02] sm:text-[40px] lg:text-[48px]">
                    Disclaimer
                </h1>
                <p class="mt-1 text-[14px] font-normal text-white sm:mt-1.5 sm:text-[16px] sm:leading-7 lg:text-[18px]">
                    Legal Terms
                </p>
            </div>
        </section>

        <main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site rounded-[20px] bg-[#f0f0f0] px-[15px] py-5 text-[#1e1e1e] sm:rounded-[30px] sm:px-10 sm:py-8 lg:px-[150px] lg:py-10">
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

                <div class="mt-5 text-[14px] leading-7 text-[#1e1e1e] sm:mt-5 sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                    <p class="mb-3">
                        Forebetz is an informational platform that provides sports predictions and analysis. We are not a bookmaker and do not accept or place bets on behalf of users. While we strive for accuracy, all predictions and analyses provided on our platform are for informational purposes only and should not be interpreted as financial or betting advice.
                    </p>
                    <p class="mb-3">
                        All predictions, strategies, and recommendations on Forebetz are based on statistical analysis, historical data, and expert opinion. However, they are subject to errors and should be considered as guidance rather than guarantees of outcomes. The final betting decisions rest solely with the user, and Forebetz cannot be held responsible for any losses incurred as a result of using our information.
                    </p>
                    <p class="mb-3">
                        Sports betting and gambling may be illegal in certain jurisdictions. It is the sole responsibility of users to ensure that their activities comply with local laws and regulations. Forebetz makes no representations regarding the legality of online gambling or sports betting in any jurisdiction.
                    </p>
                    <p class="mb-3">
                        By using Forebetz, you acknowledge and agree that:
                    </p>
                    <ul class="mb-3 list-disc space-y-0 pl-[21px] sm:pl-[27px]">
                        <li>You are at least 18 years of age or the legal age for gambling in your jurisdiction</li>
                        <li>You use the information provided at your own risk</li>
                        <li>Forebetz and its affiliates are not responsible for any financial losses or damages</li>
                        <li>You are solely responsible for verifying the accuracy of information before making any decisions</li>
                    </ul>
                    <p class="mb-3">
                        We reserve the right to modify this disclaimer at any time without prior notice. All content, including images and text, is either owned by Forebetz or used in accordance with fair use principles. If you believe any content infringes on your copyright, please contact us immediately for resolution.
                    </p>
                    <p>
                        Remember: Gambling should be approached responsibly and only with money you can afford to lose. If you believe you may have a gambling problem, please seek help from professional organizations in your area.
                    </p>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
