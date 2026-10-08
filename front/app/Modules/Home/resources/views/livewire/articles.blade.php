<div>
    @if ($articles)
        <section class="mt-7" id="blog">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-[18px] font-semibold text-[#1e1e1e] sm:text-[22px]">Sports Articles</h2>
                <a href="/blog" class="link-fx text-[14px] font-semibold text-[#cb5140]">View more</a>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <a href="{{ $article['url'] }}" wire:key="article-{{ $loop->index }}" class="card-fx group overflow-hidden rounded-[14px] bg-white">
                        @if ($article['image'])
                            <div class="aspect-[16/9] overflow-hidden bg-[#e8e8e8]">
                                <img src="{{ $article['image'] }}" alt="{{ $article['title'] }}" loading="lazy" class="h-full w-full object-cover">
                            </div>
                        @endif
                        <div class="px-4 py-3">
                            <h3 class="line-clamp-2 text-[14px] font-bold leading-snug text-[#1e1e1e] transition-colors group-hover:text-[#ff6900] sm:text-[15px]">{{ $article['title'] }}</h3>
                            @if ($article['date'] !== '')
                                <p class="mt-1 text-[12px] text-[#767676]">{{ $article['date'] }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>
