<section class="mt-8" id="blog">
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-[22px] font-semibold text-[#1e1e1e] sm:text-[28px]">Sports Articles</h2>
        <a href="/blog.php" class="text-[15px] font-semibold text-[#cb5140]">View more</a>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($articles as $article): ?>
            <article class="overflow-hidden rounded-[18px] bg-white">
                <div class="aspect-[16/10] overflow-hidden bg-[#e8e8e8]">
                    <img src="<?= $asset ?>/images/<?= htmlspecialchars($article['image']) ?>" alt="" class="h-full w-full object-cover">
                </div>
                <div class="px-4 py-4">
                    <h3 class="text-[16px] font-bold leading-snug text-[#1e1e1e] sm:text-[17px]">
                        <?= htmlspecialchars($article['title']) ?>
                    </h3>
                    <p class="mt-2 text-[13px] text-[#767676]"><?= htmlspecialchars($article['date']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
