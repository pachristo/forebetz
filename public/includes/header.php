<?php
$tipCategories = $tipCategories ?? [];
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isHome = $currentPath === '/' || $currentPath === '/index.php';
?>
<header class="relative z-30 w-full px-2.5 py-3 sm:px-4 sm:py-5 lg:px-[100px]">
    <div class="header-glass mx-auto flex max-w-[1519px] items-center justify-between gap-3 rounded-[20px] border-0 px-4 py-3 sm:rounded-[30px] sm:px-[30px] sm:py-[15px] lg:gap-5">
        <a href="/" class="min-w-0 shrink-0 scale-90 origin-left sm:scale-100">
            <?php include __DIR__ . '/components/logo.php'; ?>
        </a>

        <nav class="hidden items-center justify-end lg:flex">
            <div class="flex h-16 items-center gap-2.5 px-[15px] py-5">
                <a
                    href="/"
                    class="flex items-center gap-1.5 p-2.5 text-[16px] tracking-[0.2px] text-white backdrop-blur-[7.25px] <?= $isHome ? 'border-b-4 border-[#fcbd02]' : 'rounded-[7px]' ?>"
                >
                    <span class="size-6 shrink-0 overflow-hidden">
                        <img src="<?= $asset ?>/icons/nav-home.svg" alt="" class="h-full w-full object-contain">
                    </span>
                    Home
                </a>

                <div class="relative" data-nav-dropdown>
                    <button
                        type="button"
                        class="flex items-center gap-2.5 rounded-[7px] p-2.5 text-[16px] tracking-[0.2px] text-[#f3f3f3] backdrop-blur-[7.25px]"
                        data-dropdown-trigger
                        aria-expanded="false"
                        aria-haspopup="true"
                    >
                        <span class="flex items-center gap-1.5">
                            <span class="size-6 shrink-0 overflow-hidden">
                                <img src="<?= $asset ?>/icons/nav-tips.svg" alt="" class="h-full w-full object-contain">
                            </span>
                            Tips Scores
                        </span>
                        <span class="size-6 shrink-0 overflow-hidden transition-transform duration-200" data-dropdown-chevron>
                            <img src="<?= $asset ?>/icons/arrow-down.svg" alt="" class="h-full w-full object-contain">
                        </span>
                    </button>
                    <div
                        class="absolute left-0 top-full z-50 mt-2 hidden w-[237px] overflow-hidden rounded-[10px] bg-[#121e33] p-2.5 shadow-xl backdrop-blur-[36px]"
                        data-dropdown-panel
                        role="menu"
                    >
                        <div class="flex flex-col gap-2.5">
                            <?php foreach ($tipCategories as $i => $cat): ?>
                                <a
                                    href="/category.php?cat=<?= urlencode($cat['slug']) ?>"
                                    role="menuitem"
                                    class="flex h-[52px] items-center justify-center rounded-[10px] px-[25px] py-2 text-center text-[17px] capitalize tracking-[0.2px] <?= $i === 0 ? 'border border-[#fcbd02] bg-[#fcbd02] font-semibold text-[#1e1e1e]' : 'bg-[rgba(240,240,240,0.12)] font-normal text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.2)]' ?>"
                                >
                                    <?= htmlspecialchars($cat['label']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <a href="/live.php" class="flex items-center gap-2.5 rounded-[7px] p-2.5 text-[16px] tracking-[0.2px] text-[#f3f3f3] backdrop-blur-[7.25px]">
                    <span class="flex items-center gap-1.5">
                        <span class="size-6 shrink-0 overflow-hidden">
                            <img src="<?= $asset ?>/icons/nav-live.svg" alt="" class="h-full w-full object-contain">
                        </span>
                        Livescores
                    </span>
                </a>

                <a href="/blog.php" class="flex items-center gap-1.5 rounded-[7px] p-2.5 text-[16px] tracking-[0.2px] text-[#f3f3f3] backdrop-blur-[7.25px]">
                    <span class="size-[22px] shrink-0 overflow-hidden">
                        <img src="<?= $asset ?>/icons/nav-blog.svg" alt="" class="h-full w-full object-contain">
                    </span>
                    Blog
                </a>

                <div class="relative" data-nav-dropdown>
                    <button
                        type="button"
                        class="flex items-center gap-2.5 rounded-[7px] p-2.5 text-[16px] tracking-[0.2px] text-[#f3f3f3] backdrop-blur-[7.25px]"
                        data-dropdown-trigger
                        aria-expanded="false"
                        aria-haspopup="true"
                    >
                        <span class="flex items-center gap-1.5">
                            <span class="size-6 shrink-0 overflow-hidden">
                                <img src="<?= $asset ?>/icons/nav-link.svg" alt="" class="h-full w-full object-contain">
                            </span>
                            Link 1
                        </span>
                        <span class="size-6 shrink-0 overflow-hidden transition-transform duration-200" data-dropdown-chevron>
                            <img src="<?= $asset ?>/icons/arrow-down.svg" alt="" class="h-full w-full object-contain">
                        </span>
                    </button>
                    <div
                        class="absolute right-0 top-full z-50 mt-2 hidden min-w-[180px] overflow-hidden rounded-[10px] bg-[#121e33] p-2.5 shadow-xl backdrop-blur-[36px]"
                        data-dropdown-panel
                        role="menu"
                    >
                        <div class="flex flex-col gap-1">
                            <a href="/partners.php" role="menuitem" class="rounded-[10px] px-4 py-3 text-[15px] text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.12)]">Partners</a>
                            <a href="/about.php" role="menuitem" class="rounded-[10px] px-4 py-3 text-[15px] text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.12)]">About Us</a>
                            <a href="/contact.php" role="menuitem" class="rounded-[10px] px-4 py-3 text-[15px] text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.12)]">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="hidden shrink-0 items-center gap-2.5 pl-5 lg:flex">
            <?php if (!empty($loggedInUser)): ?>
                <div class="relative" data-nav-dropdown>
                    <button
                        type="button"
                        class="flex items-center gap-5 rounded-[15px] border border-[#fcbd02] px-4 py-[15px] text-[17px] font-medium tracking-[0.2px] text-[#f3f3f3] backdrop-blur-[7.45px]"
                        data-dropdown-trigger
                        aria-expanded="false"
                        aria-haspopup="true"
                    >
                        <span class="size-6 shrink-0 overflow-hidden">
                            <img src="<?= $asset ?>/icons/dashboard/nav-user.svg" alt="" class="h-full w-full object-contain">
                        </span>
                        <?= htmlspecialchars($loggedInUser['short_name'] ?? 'Account') ?>
                        <span class="size-6 shrink-0 overflow-hidden transition-transform duration-200" data-dropdown-chevron>
                            <img src="<?= $asset ?>/icons/arrow-down.svg" alt="" class="h-full w-full object-contain">
                        </span>
                    </button>
                    <div
                        class="absolute right-0 top-full z-50 mt-2 hidden min-w-[180px] overflow-hidden rounded-[10px] bg-[#121e33] p-2.5 shadow-xl backdrop-blur-[36px]"
                        data-dropdown-panel
                        role="menu"
                    >
                        <div class="flex flex-col gap-1">
                            <a href="/dashboard.php" role="menuitem" class="rounded-[10px] px-4 py-3 text-[15px] text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.12)]">Dashboard</a>
                            <a href="/profile.php" role="menuitem" class="rounded-[10px] px-4 py-3 text-[15px] text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.12)]">My Account</a>
                            <a href="/pricing.php" role="menuitem" class="rounded-[10px] px-4 py-3 text-[15px] text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.12)]">VIP Packages</a>
                            <a href="/" role="menuitem" class="rounded-[10px] px-4 py-3 text-[15px] text-[#ec221f] hover:bg-[rgba(240,240,240,0.12)]">Logout</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="/register.php" class="rounded-[15px] border border-[#fcbd02] px-5 py-[15px] text-[17px] font-medium tracking-[0.2px] text-[#fcbd02] backdrop-blur-[7.45px]">
                    Register
                </a>
                <a href="/login.php" class="rounded-[15px] bg-[#fcbd02] px-5 py-[15px] text-[17px] font-medium tracking-[0.2px] text-[#1e1e1e]">
                    Login
                </a>
            <?php endif; ?>
        </div>

        <button
            type="button"
            id="mobile-menu-btn"
            class="flex size-8 items-center justify-center lg:hidden"
            aria-label="Open menu"
            aria-expanded="false"
            aria-controls="mobile-menu"
        >
            <span class="size-8 overflow-hidden">
                <img src="<?= $asset ?>/icons/menu.svg" alt="" class="h-full w-full object-contain">
            </span>
        </button>
    </div>

    <div id="mobile-menu" class="fixed inset-0 z-40 hidden lg:hidden" aria-hidden="true">
        <button type="button" class="absolute inset-0 bg-black/60" data-close-menu aria-label="Close menu overlay"></button>
        <div class="absolute right-0 top-0 flex h-full w-[min(320px,86vw)] flex-col gap-4 overflow-y-auto bg-[#0a111d] px-5 py-6 shadow-xl">
            <div class="flex items-center justify-between">
                <?php include __DIR__ . '/components/logo.php'; ?>
                <button type="button" class="text-[28px] leading-none text-white" data-close-menu aria-label="Close menu">&times;</button>
            </div>
            <nav class="mt-4 flex flex-col gap-1 text-[16px] text-[#f3f3f3]">
                <a href="/" class="rounded-lg <?= $isHome ? 'border-l-4 border-[#fcbd02] bg-white/5' : '' ?> px-3 py-3">Home</a>

                <div class="rounded-lg" data-mobile-accordion>
                    <button type="button" class="flex w-full items-center justify-between px-3 py-3 text-left hover:bg-white/5" data-mobile-accordion-trigger aria-expanded="false">
                        <span>Tips Scores</span>
                        <span class="size-5 shrink-0 overflow-hidden transition-transform" data-mobile-accordion-chevron>
                            <img src="<?= $asset ?>/icons/arrow-down.svg" alt="" class="h-full w-full object-contain">
                        </span>
                    </button>
                    <div class="mb-2 ml-2 hidden flex-col gap-1.5 rounded-[10px] bg-[#121e33] p-2" data-mobile-accordion-panel>
                        <?php foreach ($tipCategories as $i => $cat): ?>
                            <a
                                href="/category.php?cat=<?= urlencode($cat['slug']) ?>"
                                class="rounded-[10px] px-3 py-3 text-center text-[15px] <?= $i === 0 ? 'bg-[#fcbd02] font-semibold text-[#1e1e1e]' : 'bg-[rgba(240,240,240,0.12)] text-[#f3f3f3]' ?>"
                            >
                                <?= htmlspecialchars($cat['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <a href="/live.php" class="rounded-lg px-3 py-3 hover:bg-white/5">Livescores</a>
                <a href="/blog.php" class="rounded-lg px-3 py-3 hover:bg-white/5">Blog</a>
                <a href="/partners.php" class="rounded-lg px-3 py-3 hover:bg-white/5">Partners</a>
            </nav>
            <div class="mt-auto flex flex-col gap-3 pb-4">
                <?php if (!empty($loggedInUser)): ?>
                    <a href="/dashboard.php" class="rounded-[15px] bg-[#fcbd02] px-5 py-3.5 text-center text-[16px] font-medium text-[#1e1e1e]">Dashboard</a>
                    <a href="/" class="rounded-[15px] border border-[#ec221f] px-5 py-3.5 text-center text-[16px] font-medium text-[#ec221f]">Logout</a>
                <?php else: ?>
                    <a href="/register.php" class="rounded-[15px] border border-[#fcbd02] px-5 py-3.5 text-center text-[16px] font-medium text-[#fcbd02]">Register</a>
                    <a href="/login.php" class="rounded-[15px] bg-[#fcbd02] px-5 py-3.5 text-center text-[16px] font-medium text-[#1e1e1e]">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<script>
(() => {
  const btn = document.getElementById('mobile-menu-btn');
  const menu = document.getElementById('mobile-menu');

  document.querySelectorAll('[data-nav-dropdown]').forEach((wrap) => {
    const trigger = wrap.querySelector('[data-dropdown-trigger]');
    const panel = wrap.querySelector('[data-dropdown-panel]');
    const chevron = wrap.querySelector('[data-dropdown-chevron]');
    if (!trigger || !panel) return;

    const close = () => {
      panel.classList.add('hidden');
      trigger.setAttribute('aria-expanded', 'false');
      chevron?.classList.remove('rotate-180');
    };
    const open = () => {
      document.querySelectorAll('[data-nav-dropdown]').forEach((other) => {
        if (other !== wrap) {
          other.querySelector('[data-dropdown-panel]')?.classList.add('hidden');
          other.querySelector('[data-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
          other.querySelector('[data-dropdown-chevron]')?.classList.remove('rotate-180');
        }
      });
      panel.classList.remove('hidden');
      trigger.setAttribute('aria-expanded', 'true');
      chevron?.classList.add('rotate-180');
    };

    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      if (panel.classList.contains('hidden')) open();
      else close();
    });
    panel.addEventListener('click', (e) => e.stopPropagation());
    document.addEventListener('click', close);
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') close();
    });
  });

  document.querySelectorAll('[data-mobile-accordion]').forEach((wrap) => {
    const trigger = wrap.querySelector('[data-mobile-accordion-trigger]');
    const panel = wrap.querySelector('[data-mobile-accordion-panel]');
    const chevron = wrap.querySelector('[data-mobile-accordion-chevron]');
    if (!trigger || !panel) return;
    trigger.addEventListener('click', () => {
      const open = panel.classList.toggle('hidden') === false;
      panel.classList.toggle('flex', open);
      trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
      chevron?.classList.toggle('rotate-180', open);
    });
  });

  if (!btn || !menu) return;
  const openMenu = () => {
    menu.classList.remove('hidden');
    menu.setAttribute('aria-hidden', 'false');
    btn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  };
  const closeMenu = () => {
    menu.classList.add('hidden');
    menu.setAttribute('aria-hidden', 'true');
    btn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  };
  btn.addEventListener('click', openMenu);
  menu.querySelectorAll('[data-close-menu]').forEach((el) => el.addEventListener('click', closeMenu));
  menu.querySelectorAll('a').forEach((el) => el.addEventListener('click', closeMenu));
})();
</script>
