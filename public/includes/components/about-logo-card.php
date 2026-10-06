<?php
/**
 * About Us branded logo card — Figma 353:32994
 * Gold border on navy patterned panel with scaled site logo.
 *
 * @var string $asset
 */
?>
<div class="relative flex w-full max-w-[477px] flex-col items-start overflow-hidden rounded-[28px] border-[6px] border-[#ff6900] px-8 py-16 sm:rounded-[36px] sm:px-[55px] sm:py-[120px] lg:py-[184px]">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute inset-0 rounded-[22px] bg-[#0a0a0a] sm:rounded-[30px]"></div>
        <img src="<?= $asset ?>/images/hero-bg.png" alt="" class="absolute inset-0 h-full w-full rounded-[22px] object-cover opacity-20 sm:rounded-[30px]">
    </div>
    <div class="relative h-[49px] w-full max-w-[367px] shrink-0 overflow-hidden">
        <div class="origin-top-left scale-[1.69]">
            <?php include __DIR__ . '/logo.php'; ?>
        </div>
    </div>
</div>
