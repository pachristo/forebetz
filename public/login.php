<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Login — Forebetz';
$pageDescription = 'Log in to your Forebetz account to access predictions, VIP packages, and your dashboard.';

include __DIR__ . '/../includes/head.php';

ob_start();
?>
                        <div class="flex w-full flex-col gap-[30px] rounded-[8px] px-[22px] py-[30px]">
                            <h1 class="text-[24px] font-bold leading-7 text-[#f5f5f5] lg:text-[32px]">
                                Login
                            </h1>

                            <form class="flex w-full flex-col gap-4" action="/dashboard.php" method="get">
                                <div class="flex w-full flex-col gap-2">
                                    <label class="flex flex-col gap-0.5">
                                        <span class="text-[12px] font-semibold leading-6 tracking-[0.2px] text-[#f5f5f5] lg:text-[13px]">Email</span>
                                        <input
                                            type="email"
                                            name="email"
                                            placeholder="Enter email"
                                            required
                                            class="h-10 w-full rounded-[8px] border border-[#d9d9d9] bg-white px-3 text-[13px] tracking-[0.2px] text-[#1e1e1e] outline-none placeholder:text-[#828282] focus:ring-2 focus:ring-[#fcbd02] lg:text-[14px]"
                                        >
                                    </label>

                                    <label class="flex flex-col gap-0.5">
                                        <span class="text-[12px] font-semibold leading-6 tracking-[0.2px] text-[#f5f5f5] lg:text-[13px]">Password</span>
                                        <span class="relative flex h-10 items-center gap-2.5 rounded-[8px] border border-[#d9d9d9] bg-white px-3">
                                            <input
                                                type="password"
                                                name="password"
                                                placeholder="************"
                                                required
                                                data-password-input
                                                class="min-w-0 flex-1 bg-transparent text-[13px] tracking-[0.2px] text-[#1e1e1e] outline-none placeholder:text-[#828282] lg:text-[14px]"
                                            >
                                            <button type="button" class="size-[18px] shrink-0 overflow-hidden" data-password-toggle aria-label="Toggle password visibility">
                                                <img src="<?= $asset ?>/icons/dashboard/eye-off.svg" alt="" class="h-full w-full object-contain">
                                            </button>
                                        </span>
                                    </label>

                                    <div class="flex justify-end">
                                        <a href="/forgot-password.php" class="text-[13px] font-medium tracking-[0.2px] text-[#facb00] underline hover:brightness-110 lg:text-[14px]">
                                            Forgot Password?
                                        </a>
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    class="flex h-[52px] w-full items-center justify-center rounded-[10px] bg-[#fcbd02] px-[17px] text-[15px] font-bold tracking-[0.2px] text-[#1e1e1e] hover:brightness-95 lg:text-[16px]"
                                >
                                    Login
                                </button>
                            </form>
                        </div>

                        <p class="px-4 text-center text-[15px] leading-5 lg:text-[16px]">
                            <span class="font-normal text-[#f5f5f5]">Don&rsquo;t have an account?</span>
                            <a href="/register.php" class="font-bold text-[#facb00] underline">REGISTER</a>
                        </p>
<?php
$authContent = ob_get_clean();
include __DIR__ . '/../includes/components/auth-shell.php';
?>

<script>
(() => {
  document.querySelectorAll('[data-password-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const wrap = btn.closest('span');
      const input = wrap && wrap.querySelector('[data-password-input]');
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
    });
  });
})();
</script>

<?php
$hideSiteFooter = true;
$hideMobileNav = true;
include __DIR__ . '/../includes/footer.php';
?>
