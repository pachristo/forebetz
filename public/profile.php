<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Account Overview — Forebetz';
$pageDescription = 'Edit your Forebetz account profile, contact details, and password.';

$loggedInUser = [
    'name' => 'Michael Adalikwu',
    'short_name' => 'Michael A.',
    'email' => 'michaeladalikwu@gmail.com',
    'avatar' => 'avatar.jpg',
];

$profile = [
    'full_name' => 'Michael Adalikwu',
    'username' => 'mikie_techieboy',
    'email' => 'Forebetzge@gmail.com',
    'phone_code' => '+234',
    'phone' => '08162911484',
    'country' => 'Nigeria',
];

$dashboardActive = 'account';
$countries = ['Nigeria', 'United Kingdom', 'Ghana', 'Kenya', 'South Africa'];

include __DIR__ . '/../includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <?php /* Mobile 382:84376 / Desktop 387:91561 — Account Overview */ ?>
        <main class="w-full px-2.5 py-5 sm:px-5 sm:py-6 lg:px-5 lg:pb-10 lg:pt-0">
            <div class="mx-auto flex w-full max-w-site flex-col gap-2.5 rounded-[20px] bg-[#dedede] p-2.5 sm:gap-[26px] sm:p-5 lg:flex-row lg:items-stretch lg:p-5">
                <div class="hidden lg:block">
                    <?php include __DIR__ . '/../includes/components/dashboard-sidebar.php'; ?>
                </div>

                <div class="flex min-w-0 flex-1 flex-col items-stretch gap-2.5 rounded-[21px] bg-white p-2.5 sm:items-center sm:justify-center sm:gap-[26px] sm:rounded-[20px] sm:p-5 md:p-6 lg:p-8">
                    <div class="flex w-full max-w-[714px] flex-col gap-2.5 sm:gap-[26px]">
                        <div>
                            <a
                                href="/dashboard.php"
                                class="inline-flex items-center justify-center gap-[5px] rounded-[24px] bg-[#e6e6e6] px-[15px] py-2.5 text-[14px] font-normal tracking-[0.16px] text-[#1e1e1e] hover:bg-[#dcdcdc] sm:text-[17px]"
                            >
                                <span class="size-[19px] shrink-0 rotate-180 overflow-hidden">
                                    <img src="<?= $asset ?>/icons/dashboard/back.svg" alt="" class="h-full w-full object-contain">
                                </span>
                                Back
                            </a>
                        </div>

                        <div class="w-full overflow-hidden rounded-[20px] border border-[#d9d9d9] p-[15px] sm:px-8 sm:py-[34px] lg:px-10">
                            <div class="border-b border-[#e9f2ed] pb-2">
                                <h1 class="text-[17px] font-bold leading-[22px] text-[#303030] sm:text-[20px] sm:font-semibold">
                                    Account Overview
                                </h1>
                            </div>

                            <form class="mx-auto flex w-full max-w-[635px] flex-col items-center gap-5 py-0 pt-2.5 sm:gap-[30px] sm:py-[15px]" action="#" method="post">
                                <div class="flex w-full flex-col items-center gap-[26px]">
                                    <div class="size-[76px] overflow-hidden rounded-full sm:size-[104px]">
                                        <img
                                            src="<?= $asset ?>/icons/dashboard/<?= htmlspecialchars($loggedInUser['avatar']) ?>"
                                            alt=""
                                            class="h-full w-full object-cover"
                                        >
                                    </div>

                                    <div class="flex w-full flex-col gap-2">
                                        <?php /* Name + Username stay 2-col on mobile (382:84376) */ ?>
                                        <div class="grid grid-cols-2 gap-2">
                                            <label class="flex min-w-0 flex-col gap-0.5">
                                                <span class="text-[11px] font-normal leading-6 tracking-[0.2px] text-[#303030] sm:text-[13px]">Full Name*</span>
                                                <input
                                                    type="text"
                                                    name="full_name"
                                                    value="<?= htmlspecialchars($profile['full_name']) ?>"
                                                    class="h-10 w-full rounded-[8px] border border-[#d9d9d9] bg-white px-3 text-[12px] tracking-[0.2px] text-[#1e1e1e] outline-none focus:ring-2 focus:ring-[#fcbd02] sm:text-[14px]"
                                                >
                                            </label>
                                            <label class="flex min-w-0 flex-col gap-0.5">
                                                <span class="text-[11px] font-normal leading-6 tracking-[0.2px] text-[#303030] sm:text-[13px]">Username</span>
                                                <input
                                                    type="text"
                                                    name="username"
                                                    value="<?= htmlspecialchars($profile['username']) ?>"
                                                    class="h-10 w-full rounded-[8px] border border-[#d9d9d9] bg-white px-3 text-[12px] tracking-[0.2px] text-[#1e1e1e] outline-none focus:ring-2 focus:ring-[#fcbd02] sm:text-[14px]"
                                                >
                                            </label>
                                        </div>

                                        <label class="flex flex-col gap-0.5">
                                            <span class="text-[11px] font-normal leading-6 tracking-[0.2px] text-[#303030] sm:text-[13px]">Email Address</span>
                                            <input
                                                type="email"
                                                name="email"
                                                value="<?= htmlspecialchars($profile['email']) ?>"
                                                class="h-10 w-full rounded-[8px] border border-[#d9d9d9] bg-white px-3 text-[12px] tracking-[0.2px] text-[#1e1e1e] outline-none focus:ring-2 focus:ring-[#fcbd02] sm:text-[14px]"
                                            >
                                        </label>

                                        <?php /* Mobile stacks phone/country; tablet+ side-by-side */ ?>
                                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                            <label class="flex flex-col gap-0.5">
                                                <span class="text-[11px] font-normal leading-6 tracking-[0.2px] text-[#1e1e1e] sm:text-[13px]">Phone Number</span>
                                                <span class="flex h-10 w-full overflow-hidden rounded-[8px] border border-[#d9d9d9] bg-white">
                                                    <span class="flex h-full shrink-0 items-center gap-[5px] bg-[#f5f5f5] px-2">
                                                        <span class="size-[18px] shrink-0 overflow-hidden">
                                                            <img src="<?= $asset ?>/icons/dashboard/flag-nigeria.svg" alt="" class="h-full w-full object-contain">
                                                        </span>
                                                        <span class="text-[12px] tracking-[0.2px] text-[#828282] sm:text-[14px]"><?= htmlspecialchars($profile['phone_code']) ?></span>
                                                    </span>
                                                    <input
                                                        type="tel"
                                                        name="phone"
                                                        value="<?= htmlspecialchars($profile['phone']) ?>"
                                                        class="min-w-0 flex-1 bg-transparent px-3 text-[12px] tracking-[0.2px] text-[#1e1e1e] outline-none sm:text-[14px]"
                                                    >
                                                </span>
                                            </label>

                                            <label class="relative flex flex-col gap-0.5">
                                                <span class="text-[11px] font-normal leading-6 tracking-[0.2px] text-[#303030] sm:text-[13px]">Country</span>
                                                <span class="relative">
                                                    <select
                                                        name="country"
                                                        class="h-10 w-full appearance-none rounded-[8px] border border-[#d9d9d9] bg-white px-3 pr-10 text-[12px] tracking-[0.2px] text-[#1e1e1e] outline-none focus:ring-2 focus:ring-[#fcbd02] sm:text-[14px]"
                                                    >
                                                        <?php foreach ($countries as $c): ?>
                                                            <option value="<?= htmlspecialchars($c) ?>" <?= $c === $profile['country'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($c) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <span class="pointer-events-none absolute right-3 top-1/2 size-[18px] -translate-y-1/2 overflow-hidden">
                                                        <img src="<?= $asset ?>/icons/dashboard/sort-down.svg" alt="" class="h-full w-full object-contain">
                                                    </span>
                                                </span>
                                            </label>
                                        </div>

                                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                            <label class="flex flex-col gap-0.5">
                                                <span class="text-[11px] font-normal leading-6 tracking-[0.2px] text-[#303030] sm:text-[13px]">New Password</span>
                                                <span class="relative flex h-10 items-center gap-2.5 rounded-[8px] border border-[#d9d9d9] bg-white px-3">
                                                    <input
                                                        type="password"
                                                        name="password"
                                                        value="************"
                                                        data-password-input
                                                        class="min-w-0 flex-1 bg-transparent text-[12px] tracking-[0.2px] text-[#b3b3b3] outline-none sm:text-[14px]"
                                                    >
                                                    <button type="button" class="size-[18px] shrink-0 overflow-hidden" data-password-toggle aria-label="Toggle password visibility">
                                                        <img src="<?= $asset ?>/icons/dashboard/eye-off.svg" alt="" class="h-full w-full object-contain">
                                                    </button>
                                                </span>
                                            </label>
                                            <label class="flex flex-col gap-0.5">
                                                <span class="text-[11px] font-normal leading-6 tracking-[0.2px] text-[#303030] sm:text-[13px]">Confirm Password</span>
                                                <span class="relative flex h-10 items-center gap-2.5 rounded-[8px] border border-[#d9d9d9] bg-white px-3">
                                                    <input
                                                        type="password"
                                                        name="password_confirm"
                                                        value="************"
                                                        data-password-input
                                                        class="min-w-0 flex-1 bg-transparent text-[12px] tracking-[0.2px] text-[#b3b3b3] outline-none sm:text-[14px]"
                                                    >
                                                    <button type="button" class="size-[18px] shrink-0 overflow-hidden" data-password-toggle aria-label="Toggle password visibility">
                                                        <img src="<?= $asset ?>/icons/dashboard/eye-off.svg" alt="" class="h-full w-full object-contain">
                                                    </button>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    class="rounded-[35px] bg-[#fcbd02] px-10 py-[15px] text-[14px] font-medium leading-[22px] text-[#1e1e1e] hover:brightness-95"
                                >
                                    Save Updates
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

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

<?php include __DIR__ . '/../includes/footer.php'; ?>
