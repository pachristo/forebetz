<?php
/**
 * Shared auth page shell — Figma Register 382:82559 / Forgot 382:88972
 * Mobile: form-only card (382:85116 pattern). Desktop: left hero + right form.
 *
 * @var string $authContent  Form card inner HTML (title + fields + footer link)
 */
$authContent = $authContent ?? '';
?>
<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex min-h-screen flex-col">
        <?php include __DIR__ . '/../header.php'; ?>

        <main class="flex w-full flex-1 items-stretch px-2.5 py-5 sm:px-5 sm:py-6 lg:px-[100px] lg:pb-[30px] lg:pt-2.5">
            <div class="mx-auto flex w-full max-w-site flex-col gap-[15px] lg:flex-row lg:items-stretch lg:justify-between lg:gap-0">

                <aside class="relative hidden min-h-[520px] flex-1 overflow-hidden rounded-[25px] lg:block">
                    <img
                        src="<?= $asset ?>/images/register-hero.png"
                        alt="Surest Prediction Site in the World. Forebetz is your best surest prediction site for 100 football predictions, sure six straight win and daily expert tips."
                        class="absolute inset-0 h-full w-full rounded-[25px] object-cover"
                    >
                </aside>

                <div class="flex w-full flex-1 flex-col items-center justify-center lg:px-[60px] lg:pb-5 lg:pt-10">
                    <div class="flex w-full max-w-[560px] flex-col items-center gap-5 rounded-[20px] bg-[rgba(0,0,0,0.42)] py-10 backdrop-blur-[7.45px] sm:px-[15px]">
                        <?= $authContent ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
