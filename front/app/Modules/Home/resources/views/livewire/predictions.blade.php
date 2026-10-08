@use('App\Support\SiteNavigation')
<section class="w-full" id="predictions">
    <div class="mb-3 flex flex-col gap-2.5">
        <div class="flex items-baseline justify-between gap-2">
            @if ($title !== '')
                <h2 class="text-[20px] font-semibold leading-tight text-[#1e1e1e] sm:text-[26px]">{{ $title }}</h2>
            @endif
            <p class="shrink-0 text-[12px] text-[#5a5a5a] sm:text-[14px]">{{ $selected->format('D, M jS Y') }}</p>
        </div>

        <div class="flex flex-col gap-2 border-b border-[#d9d9d9] pb-2.5 md:flex-row md:items-center md:justify-between">
            <div class="flex h-10 w-full max-w-full rounded-full bg-[#ffe9d9] p-1 sm:h-11 sm:max-w-[460px]">
                @foreach ([-1 => 'Yesterday', 0 => 'Today', 1 => 'Tomorrow'] as $offset => $label)
                    <button
                        type="button"
                        wire:click="setDay({{ $offset }})"
                        @class([
                            'flex flex-1 items-center justify-center rounded-[44px] px-2 text-center text-[12px] capitalize transition sm:px-3 sm:text-[15px]',
                            'bg-gradient-to-b from-[#ff7a1a] to-[#cc5400] font-bold text-white' => $dayOffset === $offset,
                            'bg-white font-medium text-[#303030] hover:bg-[#ff6900] hover:text-white' => $dayOffset !== $offset,
                        ])
                        @if ($dayOffset === $offset) aria-current="date" @endif
                    >{{ $label }}</button>
                @endforeach
            </div>

            <div class="flex items-center justify-center gap-1.5 sm:justify-start">
                <button type="button" wire:click="shiftDay(-1)" class="flex size-9 items-center justify-center rounded-full bg-white hover:-translate-x-0.5 hover:bg-[#ff6900] hover:text-white hover:shadow-md active:scale-95 sm:size-11 [&:hover_img]:brightness-0 [&:hover_img]:invert" aria-label="Previous day">
                    <x-icon name="chevron-right" class="size-5 rotate-180" />
                </button>
                <label class="relative flex h-9 cursor-pointer items-center gap-2 rounded-full bg-white px-3 transition hover:bg-[#fff0e6] hover:text-[#ff6900] hover:shadow-md text-[12px] font-semibold text-[#5a5a5a] sm:h-11 sm:text-[14px]" x-data>
                    <x-icon name="calendar" class="size-[18px] sm:size-5" />
                    {{ $selected->format('d/m') }}
                    <input
                        type="date"
                        @click="'showPicker' in $el && $el.showPicker()"
                        wire:model.live="date"
                        class="absolute inset-0 cursor-pointer opacity-0"
                        aria-label="Pick a date"
                    >
                </label>
                <button type="button" wire:click="shiftDay(1)" class="flex size-9 items-center justify-center rounded-full bg-white hover:translate-x-0.5 hover:bg-[#ff6900] hover:shadow-md active:scale-95 sm:size-11 [&:hover_img]:brightness-0 [&:hover_img]:invert" aria-label="Next day">
                    <x-icon name="chevron-right" class="size-5" />
                </button>
            </div>
        </div>

        @if ($categoryPills)
            <nav
                class="no-scrollbar relative -mx-2.5 flex items-center gap-2 overflow-x-auto px-2.5 sm:mx-0 sm:px-0"
                aria-label="Tip categories"
                x-data
                x-init="const a = $el.querySelector('[aria-current]'); if (a) $el.scrollLeft = a.offsetLeft - ($el.clientWidth - a.offsetWidth) / 2"
            >
                <a
                    href="/"
                    @class([
                        'shrink-0 whitespace-nowrap rounded-2xl px-4 py-2.5 text-[13px] font-semibold transition-colors duration-200',
                        'bg-[#1e1e1e] text-white' => $activeCategory === null,
                        'bg-white text-[#5a5a5a] hover:bg-[#ffe9d9] hover:text-[#cc5400]' => $activeCategory !== null,
                    ])
                    @if ($activeCategory === null) aria-current="page" @endif
                >Free Tips</a>
                @foreach ($categoryPills as $pill)
                    <a
                        href="{{ SiteNavigation::categoryUrl($pill['slug']) }}"
                        wire:key="pill-{{ $pill['slug'] }}"
                        @class([
                            'shrink-0 whitespace-nowrap rounded-2xl px-4 py-2.5 text-[13px] font-semibold transition-colors duration-200',
                            'bg-[#1e1e1e] text-white' => $activeCategory === $pill['slug'],
                            'bg-white text-[#5a5a5a] hover:bg-[#ffe9d9] hover:text-[#cc5400]' => $activeCategory !== $pill['slug'],
                        ])
                        @if ($activeCategory === $pill['slug']) aria-current="page" @endif
                    >{{ $pill['label'] }}</a>
                @endforeach
            </nav>
        @endif
    </div>

    <div class="flex flex-col gap-2 transition-opacity lg:gap-2.5" wire:loading.class="pointer-events-none opacity-50" wire:target="setDay, shiftDay, date">
        @forelse ($matches as $match)
            <x-match-card :match="$match" wire:key="match-{{ $match['id'] }}" />
        @empty
            <div class="rounded-[20px] bg-white px-5 py-10 text-center">
                <p class="text-[16px] font-semibold text-[#1e1e1e]">No {{ $emptyLabel }} for {{ $selected->format('D, M jS') }} yet.</p>
                <p class="mt-1 text-[14px] text-[#767676]">Check another day or come back later — new tips are published daily.</p>
            </div>
        @endforelse
    </div>

    @if (count($matches) < $total)
        <div class="mt-3 flex justify-center">
            <button
                type="button"
                wire:click="loadMore"
                wire:loading.attr="disabled"
                class="btn-fx inline-flex items-center gap-2 rounded-[12px] bg-[#1a1a1a] hover:bg-[#ff6900] hover:text-[#1a1a1a] px-6 py-2.5 text-[14px] font-bold text-white disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="loadMore">Load more ({{ $total - count($matches) }} left)</span>
                <span wire:loading wire:target="loadMore">Loading…</span>
            </button>
        </div>
    @endif
</section>
