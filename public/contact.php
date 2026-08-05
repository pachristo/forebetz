<?php
/**
 * Contact Us — Figma 354:33003 (desktop); stacks for mobile (477:80794)
 */
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Contact Us — Forebetz';
$pageDescription = 'Reach Forebetz by email or phone, or send us a message and we will get back to you.';

include __DIR__ . '/../includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden pb-20 lg:pb-0">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <section class="w-full px-2.5 pt-2 sm:px-8 sm:pt-2.5 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site py-3 sm:py-5">
                <h1 class="text-[32px] font-medium leading-tight text-[#fcbd02] sm:text-[40px] lg:text-[48px]">
                    Contact Us
                </h1>
                <p class="mt-1 text-[14px] font-normal leading-normal text-white sm:mt-1.5 sm:text-[16px] sm:leading-7 lg:text-[18px] lg:leading-7">
                    How to reach out to us
                </p>
            </div>
        </section>

        <main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site rounded-[20px] bg-[#f0f0f0] px-2.5 py-5 sm:rounded-[30px] sm:px-10 sm:py-8 lg:px-[150px] lg:py-10">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:gap-9">
                    <?php /* Left: intro + contact cards */ ?>
                    <div class="flex min-w-0 flex-1 flex-col gap-5 lg:gap-9">
                        <p class="text-[15px] font-normal leading-normal text-[#303030] lg:max-w-[472px] lg:text-justify lg:text-[20px]">
                            You can reach to us by sending us a message and we will get in touch with you as soon as possible.
                        </p>

                        <div class="flex flex-col gap-3 sm:gap-5">
                            <a
                                href="mailto:forebetz@gmail.com"
                                class="flex w-full items-center rounded-[12px] border-l-[2.5px] border-[#162640] bg-[#e8e9ec] p-3 sm:rounded-[20px] sm:border-l-4 sm:p-5"
                            >
                                <span class="flex min-w-0 flex-1 items-start gap-[19px] sm:gap-[30px]">
                                    <img
                                        src="<?= $asset ?>/images/contact/email.svg"
                                        alt=""
                                        width="40"
                                        height="40"
                                        class="size-[25px] shrink-0 object-contain sm:size-10"
                                    >
                                    <span class="flex min-w-0 flex-1 flex-col justify-center font-medium">
                                        <span class="text-[14px] leading-[17px] text-[#5a5a5a] sm:text-[17px] sm:leading-7">Email Us</span>
                                        <span class="text-[16px] leading-[17px] text-[#1e1e1e] sm:text-[21px] sm:leading-7">forebetz@gmail.com</span>
                                    </span>
                                </span>
                            </a>

                            <a
                                href="tel:+234817126112612"
                                class="flex w-full items-center rounded-[12px] border-l-[2.5px] border-[#162640] bg-[#e8e9ec] p-3 sm:rounded-[20px] sm:border-l-4 sm:p-5"
                            >
                                <span class="flex min-w-0 flex-1 items-start gap-[19px] sm:gap-[30px]">
                                    <img
                                        src="<?= $asset ?>/images/contact/phone.svg"
                                        alt=""
                                        width="40"
                                        height="40"
                                        class="size-[25px] shrink-0 object-contain sm:size-10"
                                    >
                                    <span class="flex min-w-0 flex-1 flex-col justify-center font-medium">
                                        <span class="text-[14px] leading-[17px] text-[#5a5a5a] sm:text-[17px] sm:leading-7">Call Us</span>
                                        <span class="text-[16px] leading-[17px] text-[#1e1e1e] sm:text-[21px] sm:leading-7">+234 817126 112612</span>
                                    </span>
                                </span>
                            </a>
                        </div>
                    </div>

                    <?php /* Right: contact form */ ?>
                    <div class="flex min-w-0 flex-1 flex-col rounded-[15px] border border-[#d9d9d9] px-[15px] py-5 sm:rounded-[30px] sm:p-5">
                        <form class="flex w-full flex-col gap-5" action="#" method="post">
                            <h2 class="text-[22px] font-bold leading-normal text-[#1e1e1e] sm:text-[24px]">
                                Contact Us
                            </h2>

                            <div class="flex flex-col gap-[15px] sm:gap-5">
                                <label class="flex flex-col gap-0.5">
                                    <span class="text-[14px] font-normal leading-6 tracking-[0.2px] text-[#303030] sm:text-[17px]">Name</span>
                                    <input
                                        type="text"
                                        name="name"
                                        placeholder="Enter name"
                                        required
                                        class="w-full rounded-[10px] border border-[#d9d9d9] bg-white px-5 py-2.5 text-[15px] tracking-[0.2px] text-[#1e1e1e] outline-none placeholder:text-[#b3b3b3] focus:ring-2 focus:ring-[#fcbd02] sm:py-[15px] sm:text-[17px]"
                                    >
                                </label>

                                <label class="flex flex-col gap-0.5">
                                    <span class="text-[14px] font-normal leading-6 tracking-[0.2px] text-[#303030] sm:text-[17px]">Email</span>
                                    <input
                                        type="email"
                                        name="email"
                                        placeholder="e.g myemail@gmail.com"
                                        required
                                        class="w-full rounded-[10px] border border-[#d9d9d9] bg-white px-5 py-2.5 text-[15px] tracking-[0.2px] text-[#1e1e1e] outline-none placeholder:text-[#b3b3b3] focus:ring-2 focus:ring-[#fcbd02] sm:py-[15px] sm:text-[17px]"
                                    >
                                </label>

                                <label class="flex min-h-[139px] flex-col gap-0.5 sm:min-h-[217px]">
                                    <span class="text-[14px] font-normal leading-6 tracking-[0.2px] text-[#303030] sm:text-[17px]">Write your message</span>
                                    <textarea
                                        name="message"
                                        placeholder="Tell us about your needs"
                                        required
                                        rows="6"
                                        class="min-h-[100px] w-full flex-1 resize-y rounded-[10px] border border-[#d9d9d9] bg-white px-5 py-2.5 text-[15px] tracking-[0.2px] text-[#1e1e1e] outline-none placeholder:text-[#b3b3b3] focus:ring-2 focus:ring-[#fcbd02] sm:min-h-[160px] sm:py-[15px] sm:text-[17px]"
                                    ></textarea>
                                </label>
                            </div>

                            <button
                                type="submit"
                                class="flex w-full items-center justify-center rounded-[15px] bg-[#fcbd02] px-[42px] py-[15px] text-[15px] font-medium tracking-[0.2px] text-[#1e1e1e] hover:brightness-95 sm:text-[17px]"
                            >
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
<?php
$embedFooterInPageShell = true;
include __DIR__ . '/../includes/footer.php';
?>
