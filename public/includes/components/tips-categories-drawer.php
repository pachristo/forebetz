<?php
/**
 * Tips category drawer — slides in from the left with all tip categories.
 * Opened by bottom-nav [data-open-tips-categories]
 *
 * @var string $asset
 * @var array $tipCategories
 */
$tipCategories = $tipCategories ?? [];
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$currentCat = $_GET['cat'] ?? '';
$isCategoryPage = $currentPath === '/category.php';
?>
<div
    id="tips-categories-drawer"
    class="fixed inset-0 z-[100] hidden lg:hidden"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-label="Tips category"
>
    <button
        type="button"
        class="absolute inset-0 bg-[#0a0a0a]/70 backdrop-blur-[6px]"
        data-close-tips-categories
        aria-label="Close tips categories overlay"
    ></button>

    <aside
        id="tips-categories-panel"
        class="absolute inset-y-0 left-0 flex w-[min(320px,88vw)] -translate-x-full flex-col bg-[#0a0a0a] shadow-[8px_0_40px_rgba(0,0,0,0.45)] transition-transform duration-300 ease-out"
    >
        <div class="flex items-center justify-between border-b border-white/10 px-4 py-4">
            <div class="flex items-center gap-2.5">
                <span class="size-6 shrink-0 overflow-hidden">
                    <img src="<?= $asset ?>/icons/nav-tips.svg" alt="" class="h-full w-full object-contain">
                </span>
                <h2 class="text-[18px] font-semibold tracking-[0.2px] text-white">Tips category</h2>
            </div>
            <button
                type="button"
                class="flex size-10 items-center justify-center rounded-full bg-white/10 text-[28px] leading-none text-white"
                data-close-tips-categories
                aria-label="Close tips category"
            >&times;</button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4" aria-label="Tip categories">
            <ul class="flex flex-col gap-2.5">
                <?php foreach ($tipCategories as $i => $cat): ?>
                    <?php
                    $isActive = $isCategoryPage && $currentCat === $cat['slug'];
                    $itemClass = $isActive
                        ? 'border border-[#ff6900] bg-[#ff6900] font-semibold text-[#1e1e1e]'
                        : 'bg-[rgba(240,240,240,0.12)] font-normal text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.2)]';
                    ?>
                    <li>
                        <a
                            href="/category.php?cat=<?= urlencode($cat['slug']) ?>"
                            class="flex h-[52px] items-center justify-center rounded-[10px] px-4 text-center text-[16px] capitalize tracking-[0.2px] <?= $itemClass ?>"
                        >
                            <?= htmlspecialchars($cat['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </aside>
</div>
<script>
(() => {
  const drawer = document.getElementById('tips-categories-drawer');
  const panel = document.getElementById('tips-categories-panel');
  if (!drawer || !panel) return;

  if (drawer.parentElement !== document.body) {
    document.body.appendChild(drawer);
  }

  const openDrawer = () => {
    document.getElementById('mobile-menu')?.setAttribute('aria-hidden', 'true');
    document.getElementById('mobile-menu')?.classList.add('hidden');
    drawer.classList.remove('hidden');
    drawer.setAttribute('aria-hidden', 'false');
    document.querySelectorAll('[data-open-tips-categories]').forEach((el) => {
      el.setAttribute('aria-expanded', 'true');
    });
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => {
      panel.classList.remove('-translate-x-full');
      panel.classList.add('translate-x-0');
    });
  };

  const closeDrawer = () => {
    panel.classList.add('-translate-x-full');
    panel.classList.remove('translate-x-0');
    drawer.setAttribute('aria-hidden', 'true');
    document.querySelectorAll('[data-open-tips-categories]').forEach((el) => {
      el.setAttribute('aria-expanded', 'false');
    });
    window.setTimeout(() => {
      if (drawer.getAttribute('aria-hidden') === 'true') {
        drawer.classList.add('hidden');
        const menuOpen = document.getElementById('mobile-menu')?.getAttribute('aria-hidden') === 'false';
        if (!menuOpen) document.body.style.overflow = '';
      }
    }, 280);
  };

  document.querySelectorAll('[data-open-tips-categories]').forEach((el) => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (drawer.classList.contains('hidden') || drawer.getAttribute('aria-hidden') === 'true') {
        openDrawer();
      } else {
        closeDrawer();
      }
    });
  });

  drawer.querySelectorAll('[data-close-tips-categories]').forEach((el) => {
    el.addEventListener('click', closeDrawer);
  });
  drawer.querySelectorAll('a').forEach((el) => {
    el.addEventListener('click', closeDrawer);
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDrawer();
  });
})();
</script>
