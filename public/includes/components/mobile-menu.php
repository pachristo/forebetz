<?php
/**
 * Mobile menu drawer — full-screen overlay above everything (z-[100])
 * Opened by header #mobile-menu-btn and bottom-nav [data-open-mobile-menu]
 *
 * @var string $asset
 * @var array $tipCategories
 * @var array|null $loggedInUser
 */
$tipCategories = $tipCategories ?? [];
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isHome = $currentPath === '/' || $currentPath === '/index.php';
?>
<div
    id="mobile-menu"
    class="fixed inset-0 z-[100] hidden lg:hidden"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-label="Site menu"
>
    <button
        type="button"
        class="absolute inset-0 bg-[#0a0a0a]/70 backdrop-blur-[6px] transition-opacity"
        data-close-menu
        aria-label="Close menu overlay"
    ></button>

    <aside
        id="mobile-menu-panel"
        class="absolute inset-y-0 right-0 flex w-[min(340px,92vw)] translate-x-full flex-col bg-[#0a0a0a] shadow-[-8px_0_40px_rgba(0,0,0,0.45)] transition-transform duration-300 ease-out"
    >
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
            <a href="/" class="min-w-0 shrink-0" data-close-menu>
                <?php include __DIR__ . '/logo.php'; ?>
            </a>
            <button
                type="button"
                class="flex size-10 items-center justify-center rounded-full bg-white/10 text-[28px] leading-none text-white"
                data-close-menu
                aria-label="Close menu"
            >&times;</button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 text-[16px] text-[#f3f3f3]">
            <div class="flex flex-col gap-1">
                <a
                    href="/"
                    class="flex items-center gap-3 rounded-[12px] px-3 py-3.5 <?= $isHome ? 'border-l-4 border-[#ff6900] bg-white/10' : 'hover:bg-white/5' ?>"
                >
                    <span class="size-5 shrink-0 overflow-hidden">
                        <img src="<?= $asset ?>/icons/nav-home.svg" alt="" class="h-full w-full object-contain">
                    </span>
                    Home
                </a>

                <div class="rounded-[12px]" data-mobile-accordion>
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-[12px] px-3 py-3.5 text-left hover:bg-white/5"
                        data-mobile-accordion-trigger
                        aria-expanded="false"
                    >
                        <span class="flex items-center gap-3">
                            <span class="size-5 shrink-0 overflow-hidden">
                                <img src="<?= $asset ?>/icons/nav-tips.svg" alt="" class="h-full w-full object-contain">
                            </span>
                            Tips category
                        </span>
                        <span class="size-5 shrink-0 overflow-hidden transition-transform duration-200" data-mobile-accordion-chevron>
                            <img src="<?= $asset ?>/icons/arrow-down.svg" alt="" class="h-full w-full object-contain">
                        </span>
                    </button>
                    <div class="mb-2 ml-2 hidden flex-col gap-1.5 rounded-[12px] bg-[#141414] p-2" data-mobile-accordion-panel>
                        <?php foreach ($tipCategories as $i => $cat): ?>
                            <a
                                href="/category.php?cat=<?= urlencode($cat['slug']) ?>"
                                class="rounded-[10px] px-3 py-3 text-center text-[15px] <?= $i === 0 ? 'bg-[#ff6900] font-semibold text-[#1e1e1e]' : 'bg-[rgba(240,240,240,0.12)] text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.2)]' ?>"
                            >
                                <?= htmlspecialchars($cat['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <a href="/live.php" class="flex items-center gap-3 rounded-[12px] px-3 py-3.5 hover:bg-white/5">
                    <span class="size-5 shrink-0 overflow-hidden">
                        <img src="<?= $asset ?>/icons/nav-live.svg" alt="" class="h-full w-full object-contain">
                    </span>
                    Livescores
                </a>
                <a href="/blog.php" class="flex items-center gap-3 rounded-[12px] px-3 py-3.5 hover:bg-white/5">
                    <span class="size-5 shrink-0 overflow-hidden">
                        <img src="<?= $asset ?>/icons/nav-blog.svg" alt="" class="h-full w-full object-contain">
                    </span>
                    Blog
                </a>
                <a href="/partners.php" class="flex items-center gap-3 rounded-[12px] px-3 py-3.5 hover:bg-white/5">
                    <span class="size-5 shrink-0 overflow-hidden">
                        <img src="<?= $asset ?>/icons/nav-link.svg" alt="" class="h-full w-full object-contain">
                    </span>
                    Partners
                </a>
                <a href="/about.php" class="flex items-center gap-3 rounded-[12px] px-3 py-3.5 hover:bg-white/5">
                    About Us
                </a>
                <a href="/contact.php" class="flex items-center gap-3 rounded-[12px] px-3 py-3.5 hover:bg-white/5">
                    Contact Us
                </a>
                <a href="/pricing.php" class="flex items-center gap-3 rounded-[12px] px-3 py-3.5 hover:bg-white/5">
                    VIP Packages
                </a>
            </div>
        </nav>

        <div class="border-t border-white/10 px-4 py-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
            <div class="flex flex-col gap-3">
                <?php if (!empty($loggedInUser)): ?>
                    <a href="/dashboard.php" class="rounded-[15px] bg-[#ff6900] px-5 py-3.5 text-center text-[16px] font-medium text-[#1e1e1e]">Dashboard</a>
                    <a href="/profile.php" class="rounded-[15px] border border-[#ff6900] px-5 py-3.5 text-center text-[16px] font-medium text-[#ff6900]">My Account</a>
                    <a href="/" class="rounded-[15px] border border-[#ec221f] px-5 py-3.5 text-center text-[16px] font-medium text-[#ec221f]">Logout</a>
                <?php else: ?>
                    <a href="/register.php" class="rounded-[15px] border border-[#ff6900] px-5 py-3.5 text-center text-[16px] font-medium text-[#ff6900]">Register</a>
                    <a href="/login.php" class="rounded-[15px] bg-[#ff6900] px-5 py-3.5 text-center text-[16px] font-medium text-[#1e1e1e]">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </aside>
</div>
<script>
(() => {
  const menu = document.getElementById('mobile-menu');
  const panel = document.getElementById('mobile-menu-panel');
  const btn = document.getElementById('mobile-menu-btn');
  if (!menu || !panel) return;

  // Escape any parent stacking context / overflow so menu sits on top of everything
  if (menu.parentElement !== document.body) {
    document.body.appendChild(menu);
  }

  const openMenu = () => {
    menu.classList.remove('hidden');
    menu.setAttribute('aria-hidden', 'false');
    btn?.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => {
      panel.classList.remove('translate-x-full');
      panel.classList.add('translate-x-0');
    });
  };

  const closeMenu = () => {
    panel.classList.add('translate-x-full');
    panel.classList.remove('translate-x-0');
    btn?.setAttribute('aria-expanded', 'false');
    menu.setAttribute('aria-hidden', 'true');
    window.setTimeout(() => {
      if (menu.getAttribute('aria-hidden') === 'true') {
        menu.classList.add('hidden');
        document.body.style.overflow = '';
      }
    }, 280);
  };

  const toggleMenu = () => {
    if (menu.classList.contains('hidden') || menu.getAttribute('aria-hidden') === 'true') openMenu();
    else closeMenu();
  };

  btn?.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    toggleMenu();
  });

  document.querySelectorAll('[data-open-mobile-menu]').forEach((el) => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      openMenu();
    });
  });

  menu.querySelectorAll('[data-close-menu]').forEach((el) => {
    el.addEventListener('click', closeMenu);
  });
  menu.querySelectorAll('a').forEach((el) => {
    el.addEventListener('click', closeMenu);
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
  });

  document.querySelectorAll('[data-mobile-accordion]').forEach((wrap) => {
    const trigger = wrap.querySelector('[data-mobile-accordion-trigger]');
    const accordionPanel = wrap.querySelector('[data-mobile-accordion-panel]');
    const chevron = wrap.querySelector('[data-mobile-accordion-chevron]');
    if (!trigger || !accordionPanel) return;
    trigger.addEventListener('click', () => {
      const open = accordionPanel.classList.toggle('hidden') === false;
      accordionPanel.classList.toggle('flex', open);
      trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
      chevron?.classList.toggle('rotate-180', open);
    });
  });
})();
</script>
