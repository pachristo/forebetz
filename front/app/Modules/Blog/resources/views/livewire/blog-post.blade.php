<div>
    @push('head')
        <script type="application/ld+json">{!! json_encode(array_filter($schema), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endpush

    <section class="w-full px-2.5 pb-3 sm:px-8 lg:px-[100px]">
        <div class="mx-auto flex w-full max-w-site flex-col gap-2 py-2">
            <nav class="flex flex-wrap items-center gap-1.5 text-[13px] lg:text-[14px]" aria-label="Breadcrumb">
                <a href="/" class="text-white/70 hover:text-[#ff6900]">Home</a>
                <span class="text-white/40" aria-hidden="true">/</span>
                <a href="/blog" class="text-white/70 hover:text-[#ff6900]">Blog</a>
                @if ($post['category'])
                    <span class="text-white/40" aria-hidden="true">/</span>
                    @if ($post['categoryUrl'])
                        <a href="{{ $post['categoryUrl'] }}" class="text-[#ff6900] hover:text-[#ff8a3d]">{{ $post['category'] }}</a>
                    @else
                        <span class="text-[#ff6900]">{{ $post['category'] }}</span>
                    @endif
                @endif
            </nav>
            <h1 class="max-w-[980px] text-[24px] font-semibold leading-tight text-white sm:text-[32px] lg:text-[40px]">{{ $blog->title }}</h1>
            @if ($lead !== '')
                <p class="max-w-[860px] text-[14px] leading-normal text-white/85 lg:text-[16px]">{{ $lead }}</p>
            @endif
        </div>
    </section>

    <x-page.panel>
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:gap-6">
            <div class="flex min-w-0 flex-1 flex-col gap-5">
                <article class="overflow-hidden rounded-[20px] bg-white">
                    <x-blog::image :src="$post['image']" :alt="$blog->title" eager class="aspect-[16/9] w-full lg:aspect-[16/7]" />

                    <div class="flex flex-col gap-5 px-4 py-5 sm:px-7 sm:py-6 lg:px-10 lg:py-8">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#ececec] pb-4">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[14px] text-[#757575]">
                                <span class="flex items-center gap-2">
                                    <x-icon name="calendar-duotone" class="size-5" />
                                    <time datetime="{{ $post['iso'] }}">{{ $post['date'] }}</time>
                                </span>
                                <span class="flex items-center gap-2">
                                    <x-icon name="time" class="size-5" />
                                    {{ $post['readTime'] }}
                                </span>
                                @if ($post['category'])
                                    <a href="{{ $post['categoryUrl'] ?? '/blog' }}" class="rounded-full bg-[#ff6900]/10 px-3 py-0.5 text-[12px] font-semibold uppercase tracking-[0.5px] text-[#e85d00] hover:bg-[#ff6900] hover:text-white">{{ $post['category'] }}</a>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="mr-1 text-[13px] text-[#757575]">Share</span>
                                @foreach ($shareLinks as $share)
                                    <a href="{{ $share['href'] }}" target="_blank" rel="noopener" aria-label="{{ $share['label'] }}" class="flex size-9 items-center justify-center rounded-full {{ $share['bg'] }} transition hover:-translate-y-0.5 hover:brightness-110">
                                        <x-icon :name="$share['icon']" class="size-[18px]" />
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="blog-content">{!! $blog->content !!}</div>
                    </div>
                </article>

                @if ($related->isNotEmpty())
                    <section class="rounded-[20px] bg-white p-[15px]">
                        <x-blog::section-header title="You may also like" href="/blog" label="All posts" />
                        <div class="mt-3 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($related as $item)
                                <x-blog::card :post="$item" wire:key="rel-{{ $item['id'] }}" />
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <x-blog::sidebar :latest="$latest" :categories="$categories" :active-category="$blog->blogCategory?->slug" class="hidden lg:flex" />
        </div>
    </x-page.panel>
</div>
