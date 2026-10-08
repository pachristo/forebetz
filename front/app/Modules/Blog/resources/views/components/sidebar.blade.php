@props(['latest', 'categories', 'activeCategory' => null])

<aside {{ $attributes->class('flex w-full shrink-0 flex-col gap-4 lg:sticky lg:top-4 lg:max-w-[360px]') }}>
    @if ($latest->isNotEmpty())
        <section class="rounded-[20px] bg-white p-[15px]">
            <x-blog::section-header title="Latest Posts" tag="h3" href="/blog" label="All" />
            <div class="mt-3 flex flex-col gap-3">
                @foreach ($latest as $item)
                    <x-blog::card :post="$item" variant="list" wire:key="side-{{ $item['id'] }}" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($categories->isNotEmpty())
        <section class="rounded-[20px] bg-white p-[15px]">
            <x-blog::section-header title="Categories" tag="h3" />
            <ul class="mt-2 flex flex-col">
                @foreach ($categories as $cat)
                    <li>
                        <a href="{{ $cat['url'] }}" @class([
                            'flex items-center justify-between rounded-[10px] px-2.5 py-2.5 text-[14px] transition',
                            'bg-[#ff6900] font-semibold text-white' => $activeCategory === $cat['slug'],
                            'text-[#303030] hover:bg-[#f5f5f5] hover:text-[#ff6900]' => $activeCategory !== $cat['slug'],
                        ])>
                            {{ $cat['name'] }}
                            <span @class(['rounded-full px-2 text-[12px]', 'bg-white/25' => $activeCategory === $cat['slug'], 'bg-[#f0f0f0] text-[#767676]' => $activeCategory !== $cat['slug']])>{{ $cat['count'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</aside>
