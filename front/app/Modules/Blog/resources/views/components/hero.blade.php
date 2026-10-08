@props(['categories', 'active' => null, 'accent' => config('site.name'), 'title' => 'Blog', 'subtitle' => null, 'tag' => 'h1'])

<section class="w-full px-2.5 pb-3 sm:px-8 lg:px-[100px]">
    <div class="mx-auto flex w-full max-w-site flex-col gap-3 py-2">
        <div class="flex flex-col gap-1">
            <{{ $tag }} class="text-[28px] font-semibold leading-tight sm:text-[34px] lg:text-[42px]">
                <span class="text-[#ff6900]">{{ $accent }}</span>
                <span class="text-white">{{ $title }}</span>
            </{{ $tag }}>
            @if ($subtitle)
                <p class="max-w-[820px] text-[14px] text-white/85 lg:text-[16px]">{{ $subtitle }}</p>
            @endif
        </div>

        @if ($categories->isNotEmpty())
            <nav class="-mx-2.5 flex gap-2 overflow-x-auto px-2.5 pb-1 sm:mx-0 sm:flex-wrap sm:px-0" aria-label="Blog categories">
                <a href="/blog" @class([
                    'shrink-0 rounded-full px-4 py-1.5 text-[13px] font-medium transition',
                    'bg-[#ff6900] text-white' => $active === null,
                    'bg-white/10 text-white/85 hover:bg-white/20' => $active !== null,
                ])>All posts</a>
                @foreach ($categories as $cat)
                    <a href="{{ $cat['url'] }}" @class([
                        'shrink-0 rounded-full px-4 py-1.5 text-[13px] font-medium transition',
                        'bg-[#ff6900] text-white' => $active === $cat['slug'],
                        'bg-white/10 text-white/85 hover:bg-white/20' => $active !== $cat['slug'],
                    ])>{{ $cat['name'] }}</a>
                @endforeach
            </nav>
        @endif
    </div>
</section>
