<?php
/**
 * League SEO / FAQ blocks — desktop 377:66627+ / mobile 465:59011
 * Override via $leagueSeoBlocks, $leagueFaqIntro, $leagueConclusion
 */
$leagueSeoBlocks = $leagueSeoBlocks ?? [];
$leagueFaqIntro = $leagueFaqIntro ?? '';
$leagueConclusion = $leagueConclusion ?? '';
?>
<div class="mt-5 flex flex-col gap-3 sm:mt-8 sm:gap-5">
    <?php foreach ($leagueSeoBlocks as $block): ?>
        <section class="rounded-[16px] bg-white px-2.5 py-4 text-[#1e1e1e] sm:rounded-[30px] sm:px-6 sm:py-8">
            <h2 class="mb-2 px-2.5 text-center text-[18px] font-bold leading-snug text-[#ef1410] sm:mb-4 sm:text-[22px] lg:text-[26px]">
                <?= htmlspecialchars($block['title']) ?>
            </h2>
            <p class="px-2.5 text-[14px] leading-6 text-[#303030] sm:text-[15px] sm:leading-7 lg:text-[16px]">
                <?= htmlspecialchars($block['body']) ?>
            </p>
        </section>
    <?php endforeach; ?>

    <?php if ($leagueFaqIntro !== ''): ?>
        <section class="rounded-[16px] bg-white px-2.5 py-4 text-[#1e1e1e] sm:rounded-[30px] sm:px-6 sm:py-8">
            <h2 class="mb-2 px-2.5 text-center text-[18px] font-bold leading-snug text-[#ef1410] sm:mb-4 sm:text-[22px] lg:text-[26px]">
                Frequently Asked Questions FAQs on Sure Football Prediction Site in the World
            </h2>
            <p class="px-2.5 text-[14px] leading-6 text-[#303030] sm:text-[15px] sm:leading-7 lg:text-[16px]">
                <?= htmlspecialchars($leagueFaqIntro) ?>
            </p>
        </section>
    <?php endif; ?>

    <?php if ($leagueConclusion !== ''): ?>
        <section class="rounded-[16px] bg-white px-2.5 py-4 text-[#1e1e1e] sm:rounded-[30px] sm:px-6 sm:py-8">
            <h2 class="mb-2 px-2.5 text-center text-[18px] font-bold leading-snug text-[#ef1410] sm:mb-4 sm:text-[22px] lg:text-[26px]">
                Conclusion
            </h2>
            <p class="px-2.5 text-[14px] leading-6 text-[#303030] sm:text-[15px] sm:leading-7 lg:text-[16px]">
                <?= htmlspecialchars($leagueConclusion) ?>
            </p>
        </section>
    <?php endif; ?>
</div>
