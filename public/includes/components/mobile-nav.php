<?php
/**
 * Mobile native bottom nav — Figma 570:38958
 * Visible below lg only.
 * Items: Home, Tips category (left drawer), Blog, Menu, Login|Dashboard
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
        'id' => 'tips',
        'type' => 'tips',
        'label' => "Tips\ncategory",
        'icon' => 'nav-tips.svg',
        'active' => $currentPath === '/category.php',
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
<nav class="fixed inset-x-0 bottom-0 z-40 bg-[#1a1a1a] px-2 py-2 lg:hidden" aria-label="Mobile primary" data-mobile-bottom-nav>
    <div class="mx-auto flex min-h-16 w-full max-w-site items-center justify-between gap-0.5 rounded-[20px] backdrop-blur-[7.45px]">
        <?php foreach ($mobileNavItems as $item): ?>
            <?php
            $itemClass = 'flex min-w-0 flex-1 flex-col items-center justify-center gap-1 px-0.5 py-1.5 backdrop-blur-[7.25px] '
                . ($item['active'] ? 'rounded-[15px] border-b-4 border-[#ff6900] bg-[rgba(255,255,255,0.14)]' : 'rounded-[7px]');
            $labelClass = 'w-full whitespace-pre-line text-center text-[11px] font-normal leading-[1.15] tracking-[0.1px] '
                . ($item['active'] ? 'text-white' : 'text-[#f3f3f3]');
            $type = $item['type'] ?? 'link';
            $labelHtml = nl2br(htmlspecialchars($item['label']), false);
            ?>
            <?php if ($type === 'menu'): ?>
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
                    <span class="<?= $labelClass ?>"><?= $labelHtml ?></span>
                </button>
            <?php elseif ($type === 'tips'): ?>
                <button
                    type="button"
                    class="<?= $itemClass ?>"
                    data-open-tips-categories
                    aria-label="Open tips category"
                    aria-expanded="false"
                    aria-controls="tips-categories-drawer"
                >
                    <span class="size-[18px] shrink-0 overflow-hidden">
                        <img src="<?= $asset ?>/icons/<?= htmlspecialchars($item['icon']) ?>" alt="" class="h-full w-full object-contain">
                    </span>
                    <span class="<?= $labelClass ?>"><?= $labelHtml ?></span>
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
                    <span class="<?= $labelClass ?>"><?= $labelHtml ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</nav>
<div class="h-[92px] lg:hidden" aria-hidden="true"></div>
