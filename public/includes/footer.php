<?php
/**
 * Site footer + optional mobile native nav.
 *
 * @var bool $hideSiteFooter  Skip full site footer (auth pages)
 * @var bool $hideMobileNav   Skip mobile bottom nav (auth pages)
 * @var bool $embedFooterInPageShell  Footer sits inside page patterned shell (transparent over hero-bg); closes those wrappers
 */
$hideSiteFooter = !empty($hideSiteFooter);
$hideMobileNav = !empty($hideMobileNav);
$embedFooterInPageShell = !empty($embedFooterInPageShell);
?>
<?php if (!$hideSiteFooter): ?>
<footer class="relative z-10 w-full overflow-hidden px-2.5 py-8 sm:px-8 sm:py-[50px] lg:px-[100px]">
    <div class="mx-auto flex w-full max-w-site flex-col gap-[18px]">
        <div class="flex flex-col gap-5 rounded-[20px] border-0 bg-transparent px-3 py-6 backdrop-blur-[7.45px] sm:px-5 sm:py-10">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between lg:gap-[30px] lg:px-[30px]">
                <div class="flex w-full max-w-[502px] flex-col gap-2.5">
                    <?php include __DIR__ . '/components/logo.php'; ?>
                    <p class="text-[15px] font-normal leading-normal text-white sm:text-[17px]">
                        Dailysuretips is your best surest prediction site for 100 football predictions, sure six straight win and daily expert tips.
                    </p>
                    <a
                        href="#telegram"
                        class="inline-flex w-full items-center justify-center gap-2.5 rounded-[15px] p-4 text-[16px] font-semibold text-[#f3f3f3] sm:w-fit sm:p-5 sm:text-[17px]"
                        style="background-image: linear-gradient(-81deg, #2481b2 0%, #2aabee 100%);"
                    >
                        Join Our Telegram
                        <span class="size-6 shrink-0 overflow-hidden">
                            <img src="<?= $asset ?>/icons/telegram.svg" alt="" class="h-full w-full object-contain">
                        </span>
                    </a>
                </div>

                <div class="grid w-full gap-6 sm:grid-cols-2 lg:max-w-[880px] lg:grid-cols-4 lg:justify-between lg:gap-8">
                    <div class="flex flex-col gap-4 sm:gap-5">
                        <h4 class="text-[18px] font-bold capitalize text-white sm:text-[21px]">Quick Links</h4>
                        <ul class="flex flex-col gap-1.5 text-[16px] text-white">
                            <li><a href="/" class="leading-5 hover:text-[#ff6900]">Home</a></li>
                            <li><a href="/about.php" class="leading-5 hover:text-[#ff6900]">About Us</a></li>
                            <li><a href="/pricing.php" class="leading-5 hover:text-[#ff6900]">Packages</a></li>
                            <li><a href="/contact.php" class="leading-5 hover:text-[#ff6900]">Contact Us</a></li>
                        </ul>
                    </div>

                    <div class="flex flex-col gap-5">
                        <h4 class="text-[18px] font-bold capitalize text-white sm:text-[21px]">Predictions</h4>
                        <ul class="flex flex-col gap-1.5 text-[16px] text-white">
                            <?php foreach (($tipCategories ?? []) as $cat): ?>
                                <li>
                                    <a href="/category.php?cat=<?= urlencode($cat['slug']) ?>" class="leading-5 hover:text-[#ff6900]">
                                        <?= htmlspecialchars($cat['label']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="flex flex-col gap-5">
                        <h4 class="text-[18px] font-bold capitalize text-white sm:text-[21px]">Legal Links</h4>
                        <ul class="flex flex-col gap-1.5 text-[16px] text-white">
                            <li><a href="/partners.php" class="leading-5 hover:text-[#ff6900]">Partners</a></li>
                            <li><a href="/disclaimer.php" class="leading-5 hover:text-[#ff6900]">Disclaimer</a></li>
                            <li><a href="/terms.php" class="leading-5 hover:text-[#ff6900]">Terms &amp; Conditions</a></li>
                            <li><a href="/privacy.php" class="leading-5 hover:text-[#ff6900]">Privacy Policy</a></li>
                            <li><a href="/refund.php" class="leading-5 hover:text-[#ff6900]">Refund Policy</a></li>
                        </ul>
                    </div>

                    <div class="flex flex-col gap-5">
                        <h4 class="text-[18px] font-bold capitalize text-white sm:text-[21px]">Reach Us</h4>
                        <ul class="flex flex-col gap-1.5 text-[16px] text-white">
                            <li class="flex items-center gap-1.5">
                                <span class="size-4 shrink-0 overflow-hidden">
                                    <img src="<?= $asset ?>/icons/whatsapp-logo.svg" alt="" class="h-full w-full object-contain">
                                </span>
                                <span>WhatsApp Only: <span class="font-semibold">+234 2345353453</span></span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="size-4 shrink-0 overflow-hidden">
                                    <img src="<?= $asset ?>/icons/mail.svg" alt="" class="h-full w-full object-contain">
                                </span>
                                <span>Email Us: <span class="font-semibold text-[#f3f3f3]">Forebetz@gmail.com</span></span>
                            </li>
                        </ul>
                        <div class="mt-1 flex items-center gap-5">
                            <a href="#x" class="size-[27px] overflow-hidden" aria-label="X">
                                <img src="<?= $asset ?>/icons/x-twitter.svg" alt="" class="h-full w-full object-contain">
                            </a>
                            <a href="#telegram" class="size-[27px] overflow-hidden" aria-label="Telegram">
                                <img src="<?= $asset ?>/icons/telegram-social.svg" alt="" class="h-full w-full object-contain">
                            </a>
                            <a href="#facebook" class="size-[27px] overflow-hidden" aria-label="Facebook">
                                <img src="<?= $asset ?>/icons/facebook.svg" alt="" class="h-full w-full object-contain">
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[10px] bg-white/20 px-[15px] py-2.5">
                <p class="text-[14px] capitalize leading-[23px] text-[#e8e8e8]">
                    Free football predictions | Bettingvoice | Free sure win prediction | Sure winning prediction website | Sure correct fixed tips | Sure tips | Sport Betting Tips | 100 prediction | Best Football Prediction | free football site | Free soccer Prediction Site | Sure football betting tips | Pitch Prediction | Betsassured | Tips180 Predictions | SoccerVista | PredictZ | Score808 Live | 100% Sure Win | Football Predictions | Surest Prediction Site | Soccer Predictions | Correct Score Predictions | 100% Sure Straight Wins | Sports411 | https://soccerbeep.com/ | 100 sure football predictions
                </p>
            </div>

            <div class="border-t border-[rgba(183,188,196,0.3)] pt-2.5">
                <p class="text-center text-[14px] leading-5 text-[#e6ecef] sm:text-[18px]">
                    Copyright © 2025 Dailysuretips All Rights Reserved.
                </p>
            </div>
        </div>
    </div>
</footer>
<?php endif; ?>

<?php if ($embedFooterInPageShell): ?>
    </div>
</div>
<?php endif; ?>

<?php if (!$hideMobileNav): ?>
    <?php include __DIR__ . '/components/mobile-nav.php'; ?>
    <?php include __DIR__ . '/components/tips-categories-drawer.php'; ?>
<?php endif; ?>
<?php include __DIR__ . '/components/mobile-menu.php'; ?>
</body>
</html>
