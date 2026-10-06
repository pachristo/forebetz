<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Forgot Password — Dailysuretips';
$pageDescription = 'Reset your Dailysuretips account password. Enter your email to receive a reset link.';

include __DIR__ . '/../includes/head.php';

ob_start();
?>
                        <div class="flex w-full flex-col gap-[30px] rounded-[8px] px-[22px] py-[30px]">
                            <h1 class="text-[24px] font-bold leading-7 text-[#f5f5f5] lg:text-[32px]">
                                Forgot Password?
                            </h1>

                            <form class="flex w-full flex-col gap-4" action="/login.php" method="get">
                                <label class="flex flex-col gap-0.5">
                                    <span class="text-[12px] font-semibold leading-6 tracking-[0.2px] text-[#f5f5f5] lg:text-[13px]">Email</span>
                                    <input
                                        type="email"
                                        name="email"
                                        placeholder="Enter email"
                                        required
                                        class="h-10 w-full rounded-[8px] border border-[#d9d9d9] bg-white px-3 text-[13px] tracking-[0.2px] text-[#1e1e1e] outline-none placeholder:text-[#828282] focus:ring-2 focus:ring-[#ff6900] lg:text-[14px]"
                                    >
                                </label>

                                <button
                                    type="submit"
                                    class="flex h-[52px] w-full items-center justify-center rounded-[10px] bg-[#ff6900] px-[17px] text-[15px] font-bold tracking-[0.2px] text-[#1e1e1e] hover:brightness-95 lg:text-[16px]"
                                >
                                    Send Reset Link
                                </button>
                            </form>
                        </div>

                        <p class="px-4 text-center text-[15px] leading-5 lg:text-[16px]">
                            <span class="font-normal text-[#f5f5f5]">Remember your password?</span>
                            <a href="/login.php" class="font-bold text-[#ff6900] underline">LOGIN</a>
                        </p>
<?php
$authContent = ob_get_clean();
include __DIR__ . '/../includes/components/auth-shell.php';
$hideSiteFooter = true;
$hideMobileNav = true;
include __DIR__ . '/../includes/footer.php';
?>
