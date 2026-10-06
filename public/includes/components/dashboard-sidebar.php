<?php
/**
 * Dashboard sidebar — Figma 382:80587
 *
 * @var array $loggedInUser
 * @var string $dashboardActive  dashboard|packages|history|account
 */
$dashboardActive = $dashboardActive ?? 'dashboard';
$navItems = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'href' => '/dashboard.php', 'icon' => 'dashboard.svg'],
    ['id' => 'packages', 'label' => 'VIP Packages', 'href' => '/pricing.php', 'icon' => 'vip.svg'],
    ['id' => 'history', 'label' => 'History', 'href' => '#history', 'icon' => 'history.svg'],
];
?>
<aside class="flex w-full flex-col rounded-[20px] bg-white pt-8 lg:w-[240px] lg:shrink-0 lg:self-stretch">
    <?php /* Desktop sidebar 382:80587 — hidden below lg (mobile 477:96830 has no sidebar) */ ?>
    <div class="flex flex-col items-center gap-[5px] px-4">
        <div class="size-11 overflow-hidden rounded-full">
            <img
                src="<?= $asset ?>/icons/dashboard/<?= htmlspecialchars($loggedInUser['avatar'] ?? 'avatar.jpg') ?>"
                alt=""
                class="h-full w-full object-cover"
            >
        </div>
        <div class="text-center tracking-[0.2px]">
            <p class="text-[16px] font-medium text-[#2c2c2c]">
                <?= htmlspecialchars($loggedInUser['name'] ?? '') ?>
            </p>
            <p class="text-[14px] font-normal text-[#00372f]">
                <?= htmlspecialchars($loggedInUser['email'] ?? '') ?>
            </p>
        </div>
    </div>

    <div class="mt-[30px] flex w-full flex-col gap-5 px-2.5">
        <nav class="flex flex-col gap-2.5">
            <?php foreach ($navItems as $item): ?>
                <?php $isActive = $dashboardActive === $item['id']; ?>
                <a
                    href="<?= htmlspecialchars($item['href']) ?>"
                    class="flex items-center gap-[11px] p-[15px] text-[16px] font-medium tracking-[0.2px] <?= $isActive ? 'rounded-[12px] bg-[#ff6900] text-[#1e1e1e]' : 'rounded-[8px] text-[#2c2c2c] hover:bg-[#f5f5f5]' ?>"
                >
                    <span class="size-6 shrink-0 overflow-hidden">
                        <img src="<?= $asset ?>/icons/dashboard/<?= htmlspecialchars($item['icon']) ?>" alt="" class="h-full w-full object-contain">
                    </span>
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="h-px w-full bg-[#e5e5e5]"></div>

        <div class="flex flex-col gap-[5px]">
            <p class="px-5 text-[14px] font-normal tracking-[0.2px] text-[#767676]">Profile</p>
            <a
                href="/profile.php"
                class="flex items-center gap-[11px] p-[15px] text-[16px] font-medium tracking-[0.2px] <?= $dashboardActive === 'account' ? 'rounded-[12px] bg-[#ff6900] text-[#2c2c2c]' : 'rounded-[8px] text-[#2c2c2c] hover:bg-[#f5f5f5]' ?>"
            >
                <span class="size-6 shrink-0 overflow-hidden">
                    <img src="<?= $asset ?>/icons/dashboard/user.svg" alt="" class="h-full w-full object-contain">
                </span>
                My Account
            </a>
        </div>
    </div>

    <div class="mt-auto flex w-full items-center justify-center p-[25px]">
        <a
            href="/"
            class="flex w-full items-center justify-center gap-[7px] rounded-[27px] bg-[#fee9e7] py-[15px] text-[16px] font-medium tracking-[0.2px] text-[#ec221f]"
        >
            <span class="size-[22px] shrink-0 overflow-hidden">
                <img src="<?= $asset ?>/icons/dashboard/logout.svg" alt="" class="h-full w-full object-contain">
            </span>
            Logout
        </a>
    </div>
</aside>
