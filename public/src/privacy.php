<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Privacy Policy — Forebetz Legal Terms';
$pageDescription = 'Forebetz privacy policy explaining how we collect, use, protect, and disclose your information.';
$lastUpdated = 'December 13, 2025';

include __DIR__ . '/../includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <section class="w-full px-2.5 pt-2 sm:px-8 sm:pt-2.5 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site py-3 sm:py-5">
                <h1 class="text-[32px] font-medium leading-tight text-[#fcbd02] sm:text-[40px] lg:text-[48px]">
                    Privacy Policy
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

                    <div class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                        <p class="mb-3">
                            What this contains is the bits of the Privacy Policy of Forebetz
                            (<a href="/" class="underline">https://www.Forebetz</a>).
                            This is for us to explain how we collect, make use, protect, and disclose the information which you give to us when you are making use of our website or try to interact with the services that we offer.
                        </p>
                        <p>
                            Kindly take a moment to read the document below carefully. If you do not agree with the terms in this policy, you may need to stop using Forebetz. By continuing to use our site, you are giving your consent to the way we collect and use your information as explained in this privacy policy.
                        </p>
                    </div>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            Information We Collect
                        </h2>
                        <p class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            At Forebetz, we collect and use two main types of information: personal information and non-personal information. Below, we've explained what each of these terms means.
                        </p>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            Personal Information
                        </h2>
                        <div class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            <p class="mb-3">At Forebetz, we only collect personal information when you choose to share it with us. This happens when you fill out our contact forms, send us emails, leave comments, submit guest posts, or sign up for our newsletter.</p>
                            <p class="mb-3">The personal information we may receive from you includes:</p>
                            <ul class="mb-3 list-disc space-y-0 pl-[21px] sm:pl-[27px]">
                                <li>Your name</li>
                                <li>Your email address</li>
                                <li>Your country or region</li>
                                <li>Any message or content you choose to submit</li>
                            </ul>
                            <p>We do not ask for or collect sensitive personal details like passwords, bank information, or biometric data.</p>
                        </div>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            Non-Personal Information
                        </h2>
                        <div class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            <p class="mb-3">We will sometimes collect other non-personal information if you visit our site and accept cookies. These non-personal information include your IP address, browser type, device type, pages visited, how long you stay on the site, and how you arrived at our platform.</p>
                            <p>The only reason we collect this information is to know how you interact with our site and what we can do to improve our offerings as a platform just to serve you better.</p>
                        </div>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            How We Use Your Information
                        </h2>
                        <div class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            <p class="mb-3">You can be rest assured that when we collect any piece of information from you, they are used for profitable use. The following are what you can expect that we use your details to do:</p>
                            <ul class="mb-3 list-disc space-y-0 pl-[21px] sm:pl-[27px]">
                                <li>Give you the best football betting tips</li>
                                <li>Reply your questions, requests, and inquiries</li>
                                <li>Forward our newsletters to you including any updates we make to our disclaimer policy</li>
                                <li>Learn about how we can make our site functional and perform better.</li>
                                <li>To know what our visitor behavior is like and what we can identify from their actions.</li>
                                <li>To keep you from fraud, abuse, or any form of illegal behavior.</li>
                            </ul>
                            <p>Forebetz is never going to sell your personal data or give them away to any third parties.</p>
                        </div>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            Cookies and Tracking Technologies
                        </h2>
                        <div class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            <p class="mb-3">We use cookies to determine the kind of content you will like and analyze the traffic to our site. There are three kinds of cookies that we accept and make use of here at Forebetz. They include:</p>
                            <ul class="list-disc space-y-0 pl-[21px] sm:pl-[27px]">
                                <li>Essential Cookies for basic website functions</li>
                                <li>Performance Cookies for tracking user behavior for analytics</li>
                                <li>Affiliate Cookies to help us to know when our affiliate link is clicked and we can earn commissions</li>
                            </ul>
                        </div>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            Google Analytics and Third-Party Tools
                        </h2>
                        <div class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            <p class="mb-3">At Forebetz, we use Google Analytics and a few other tools like it to help us see how our website is doing and understand what our visitors are looking for when they check out our content.</p>
                            <p class="mb-3">These tools collect things like your IP address, the kind of device and browser you're using, the pages you visit, and how long you stay on each page.</p>
                            <p class="mb-3">They don't collect anything that can personally tell us who you are. But Google may still use the data they collect based on their own Privacy Policy.</p>
                            <p>If you don't want Google Analytics to track your activity, you can stop it by using a browser add-on like the Google Analytics Opt-Out tool, especially if you use Chrome.</p>
                        </div>
                    </section>

                    <section class="flex flex-col gap-4 sm:gap-5">
                        <h2 class="text-[18px] font-bold text-[#1e1e1e] sm:text-[20px] lg:text-[24px]">
                            Affiliate and Advertising Partners
                        </h2>
                        <div class="text-[14px] leading-7 text-[#1e1e1e] sm:text-[16px] sm:leading-8 lg:text-[18px] lg:leading-8">
                            <p class="mb-3">Forebetz works with different partners and affiliate programs from around the world. This means you'll see some affiliate links and sponsored posts on our site. These partners may use cookies or other tools to track things like if you clicked a link, signed up, or bought something. That's how we earn commissions.</p>
                            <p>We don't control what these other websites do with your data. So, before you use their sites, it's a good idea to read their privacy policies first.</p>
                        </div>
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
                                <p class="font-medium text-[#1e1e1e]">Note:</p>
                                <p class="text-[#ef1410]">
                                    Forebetz is a football prediction platform and does not accept or place bets. Always bet responsibly. Betting involves risk, and it is possible to lose money.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
