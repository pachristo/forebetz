<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Create Account — Forebetz';
$pageDescription = 'Register for a Forebetz account to access sure football predictions, daily expert tips, and VIP packages.';

$countries = ['Nigeria', 'United Kingdom', 'Ghana', 'Kenya', 'South Africa', 'United States'];

include __DIR__ . '/includes/head.php';

ob_start();
?>
                        <div class="flex w-full flex-col gap-[30px] rounded-[8px] px-[22px] py-[30px]">
                            <h1 class="text-[24px] font-bold leading-7 text-[#f5f5f5] lg:text-[32px]">
                                Create Account
                            </h1>

                            <form class="flex w-full flex-col gap-4" action="/dashboard.php" method="get">
                                <div class="flex w-full flex-col gap-2">
                                    <label class="flex flex-col gap-0.5">
                                        <span class="text-[12px] font-semibold leading-6 tracking-[0.2px] text-[#f5f5f5] lg:text-[13px]">Full Name*</span>
                                        <input
                                            type="text"
                                            name="full_name"
                                            placeholder="Enter fullname"
                                            required
                                            class="h-10 w-full rounded-[8px] border border-[#d9d9d9] bg-white px-3 text-[13px] tracking-[0.2px] text-[#1e1e1e] outline-none placeholder:text-[#828282] focus:ring-2 focus:ring-[#fcbd02] lg:text-[14px]"
                                        >
                                    </label>

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

                                    <div class="grid grid-cols-2 gap-2">
                                        <label class="flex min-w-0 flex-col gap-0.5">
                                            <span class="text-[12px] font-semibold leading-6 tracking-[0.2px] text-[#f5f5f5] lg:text-[13px]">Phone Number</span>
                                            <span class="flex h-10 w-full overflow-hidden rounded-[8px] border border-[#d9d9d9] bg-white">
                                                <span class="flex h-full shrink-0 items-center gap-[5px] bg-[#f5f5f5] px-1.5 sm:px-2">
                                                    <span class="size-[18px] shrink-0 overflow-hidden">
                                                        <img src="<?= $asset ?>/icons/dashboard/flag-nigeria.svg" alt="" class="h-full w-full object-contain">
                                                    </span>
                                                    <span class="text-[12px] tracking-[0.2px] text-[#828282] sm:text-[13px] lg:text-[14px]">+234</span>
                                                </span>
                                                <input
                                                    type="tel"
                                                    name="phone"
                                                    placeholder=" "
                                                    class="min-w-0 flex-1 bg-transparent px-2 text-[13px] tracking-[0.2px] text-[#1e1e1e] outline-none placeholder:text-[#828282] sm:px-3 lg:text-[14px]"
                                                >
                                            </span>
                                        </label>

                                        <label class="relative flex min-w-0 flex-col gap-0.5">
                                            <span class="text-[12px] font-semibold leading-6 tracking-[0.2px] text-[#f5f5f5] lg:text-[13px]">Country</span>
                                            <span class="relative">
                                                <select
                                                    name="country"
                                                    class="h-10 w-full appearance-none rounded-[8px] border border-[#d9d9d9] bg-white px-2 pr-8 text-[13px] tracking-[0.2px] text-[#828282] outline-none focus:ring-2 focus:ring-[#fcbd02] focus:text-[#1e1e1e] sm:px-3 sm:pr-10 lg:text-[14px]"
                                                >
                                                    <option value="" selected disabled>Select country</option>
                                                    <?php foreach ($countries as $c): ?>
                                                        <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="pointer-events-none absolute right-2 top-1/2 size-[18px] -translate-y-1/2 overflow-hidden sm:right-3">
                                                    <img src="<?= $asset ?>/icons/dashboard/sort-down.svg" alt="" class="h-full w-full object-contain">
                                                </span>
                                            </span>
                                        </label>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <label class="flex min-w-0 flex-col gap-0.5">
                                            <span class="text-[12px] font-semibold leading-6 tracking-[0.2px] text-[#f5f5f5] lg:text-[13px]">New Password</span>
                                            <span class="relative flex h-10 items-center gap-2 rounded-[8px] border border-[#d9d9d9] bg-white px-2 sm:gap-2.5 sm:px-3">
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
                                        <label class="flex min-w-0 flex-col gap-0.5">
                                            <span class="text-[12px] font-semibold leading-6 tracking-[0.2px] text-[#f5f5f5] lg:text-[13px]">Confirm Password</span>
                                            <span class="relative flex h-10 items-center gap-2 rounded-[8px] border border-[#d9d9d9] bg-white px-2 sm:gap-2.5 sm:px-3">
                                                <input
                                                    type="password"
                                                    name="password_confirm"
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
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    class="flex h-[52px] w-full items-center justify-center rounded-[10px] bg-[#fcbd02] px-[17px] text-[15px] font-bold tracking-[0.2px] text-[#1e1e1e] hover:brightness-95 lg:text-[16px]"
                                >
                                    Create Account
                                </button>

                                <label class="flex cursor-pointer items-start justify-center gap-2.5 sm:items-center">
                                    <span class="relative mt-0.5 size-6 shrink-0 sm:mt-0">
                                        <input type="checkbox" name="terms" required class="peer absolute inset-0 z-10 cursor-pointer opacity-0">
                                        <span class="pointer-events-none absolute inset-0 overflow-hidden peer-checked:opacity-0">
                                            <img src="<?= $asset ?>/icons/auth/checkbox.svg" alt="" class="h-full w-full object-contain">
                                        </span>
                                        <span class="pointer-events-none absolute inset-0 hidden items-center justify-center rounded-[5px] border-[1.5px] border-white bg-[#fcbd02] peer-checked:flex">
                                            <span class="text-[12px] font-bold leading-none text-[#1e1e1e]">✓</span>
                                        </span>
                                    </span>
                                    <span class="text-[15px] font-normal leading-5 text-[#f5f5f5] lg:text-[16px]">
                                        Click here to accept the <a href="/terms.php" class="underline hover:text-[#fcbd02]">Terms &amp; Conditions</a>.
                                    </span>
                                </label>
                            </form>
                        </div>

                        <p class="px-4 text-center text-[15px] leading-5 lg:text-[16px]">
                            <span class="font-normal text-[#f5f5f5]">I have an account?</span>
                            <a href="/login.php" class="font-bold text-[#facb00] underline">LOGIN</a>
                        </p>
<?php
$authContent = ob_get_clean();
include __DIR__ . '/includes/components/auth-shell.php';
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
include __DIR__ . '/includes/footer.php';
?>
