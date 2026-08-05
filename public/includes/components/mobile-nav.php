<?php
/**
 * Mobile native bottom nav — Figma 570:38958
 * Visible below lg only.
 * Items: Home, Livescores, Blog, Menu (opens drawer), Login|Dashboard
 *
 * @var string $asset
 * @var array|null $loggedInUser
 */
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isLoggedIn = !empty($loggedInUser);
$accountHref = $isLoggedIn ? '/dashboard.php' : '/login.php';
$accountLabel = $isLoggedIn ? 'Dashboard' : 'Login';
$accountIcon = $isLoggedIn ? 'dashboard/dashboard.svg' : 'dashboard/nav-user.svg';
$accountActive = $isLoggedIn
    ? in_array($currentPath, ['/dashboard.php', '/profile.php', '/payment.php'], true)
    : in_array($currentPath, ['/login.php', '/register.php', '/forgot-password.php'], true);

$mobileNavItems = [
    [
        'id' => 'home',
        'type' => 'link',
        'label' => 'Home',
        'href' => '/',
        'icon' => 'mobile-nav/home.svg',
        'active' => $currentPath === '/' || $currentPath === '/index.php',
    ],
    [
        'id' => 'live',
        'type' => 'link',
        'label' => 'Livescores',
        'href' => '/live.php',
        'icon' => 'mobile-nav/live.svg',
        'active' => $currentPath === '/live.php',
    ],
    [
        'id' => 'blog',
        'type' => 'link',
        'label' => 'Blog',
        'href' => '/blog.php',
        'icon' => 'mobile-nav/blog.svg',
        'active' => $currentPath === '/blog.php' || $currentPath === '/blog-detail.php',
    ],
    [
        'id' => 'menu',
        'type' => 'menu',
        'label' => 'Menu',
        'icon' => 'menu.svg',
        'active' => false,
    ],
    [
        'id' => 'account',
        'type' => 'link',
        'label' => $accountLabel,
        'href' => $accountHref,
        'icon' => $accountIcon,
        'active' => $accountActive,
    ],
];
?>
<nav class="fixed inset-x-0 bottom-0 z-40 bg-[#162640] px-[15px] py-2.5 lg:hidden" aria-label="Mobile primary">
    <div class="mx-auto flex h-16 w-full max-w-site items-center justify-between rounded-[20px] backdrop-blur-[7.45px]">
        <?php foreach ($mobileNavItems as $item): ?>
            <?php
            $itemClass = 'flex flex-col items-center justify-center gap-[5px] p-2.5 backdrop-blur-[7.25px] '
                . ($item['active'] ? 'rounded-[15px] border-b-4 border-[#fcbd02] bg-[rgba(255,255,255,0.14)]' : 'rounded-[7px]');
            $labelClass = 'text-[14px] font-normal tracking-[0.2px] ' . ($item['active'] ? 'text-white' : 'text-[#f3f3f3]');
            ?>
            <?php if (($item['type'] ?? 'link') === 'menu'): ?>
                <button
                    type="button"
                    class="<?= $itemClass ?>"
                    data-open-mobile-menu
                    aria-label="Open menu"
                    aria-controls="mobile-menu"
                >
                    <span class="size-[18px] shrink-0 overflow-hidden">
                        <img src="<?= $asset ?>/icons/<?= htmlspecialchars($item['icon']) ?>" alt="" class="h-full w-full object-contain">
                    </span>
                    <span class="<?= $labelClass ?>"><?= htmlspecialchars($item['label']) ?></span>
                </button>
            <?php else: ?>
                <a
                    href="<?= htmlspecialchars($item['href']) ?>"
                    class="<?= $itemClass ?>"
                    <?= $item['active'] ? 'aria-current="page"' : '' ?>
                >
                    <span class="size-[18px] shrink-0 overflow-hidden">
                        <img src="<?= $asset ?>/icons/<?= htmlspecialchars($item['icon']) ?>" alt="" class="h-full w-full object-contain">
                    </span>
                    <span class="<?= $labelClass ?>"><?= htmlspecialchars($item['label']) ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</nav>
<div class="h-[84px] lg:hidden" aria-hidden="true"></div>
<script>
(() => {
  document.querySelectorAll('[data-open-mobile-menu]').forEach((el) => {
    el.addEventListener('click', () => {
      document.getElementById('mobile-menu-btn')?.click();
    });
  });
})();
</script>
