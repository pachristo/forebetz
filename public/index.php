<?php
require_once __DIR__ . '/../includes/config.php';
include __DIR__ . '/../includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/../includes/header.php'; ?>
        <?php include __DIR__ . '/../includes/components/hero.php'; ?>

        <main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
            <div class="mx-auto w-full max-w-site rounded-[20px] bg-[#f0f0f0] p-2.5 text-[#1e1e1e] sm:rounded-[30px] sm:p-8 lg:p-10">
                <div class="flex flex-col gap-6 sm:gap-8 lg:flex-row lg:items-start lg:gap-[30px]">
                    <div class="min-w-0 flex-1">
                        <?php include __DIR__ . '/../includes/components/predictions.php'; ?>
                        <?php include __DIR__ . '/../includes/components/packages.php'; ?>
                        <?php include __DIR__ . '/../includes/components/recent-winnings.php'; ?>
                        <?php include __DIR__ . '/../includes/components/articles.php'; ?>
                    </div>
                    <?php include __DIR__ . '/../includes/components/sidebar.php'; ?>
                </div>

                <?php include __DIR__ . '/../includes/components/seo-faq.php'; ?>
            </div>
        </main>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
