<div>
    <x-page.title :title="$page?->head1 ?: 'Our Partners'" :subtitle="$page?->head2" />

    <x-page.panel>
        <div class="flex w-full flex-col gap-4">
            @if (trim((string) $page?->content) !== '')
                <div class="home-content rounded-[18px] bg-white px-4 py-4 sm:px-6">{!! $page->content !!}</div>
            @endif

            @if ($partners)
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($partners as $partner)
                        <a href="/partners/{{ $partner['id'] }}/visit" target="_blank" rel="noopener" wire:key="partner-{{ $partner['id'] }}" class="card-fx flex items-center gap-3 rounded-[16px] bg-white p-3">
                            @if ($partner['logo'])
                                <img src="{{ $partner['logo'] }}" alt="" loading="lazy" class="size-12 shrink-0 rounded-[10px] object-contain">
                            @else
                                <span class="flex size-12 shrink-0 items-center justify-center rounded-[10px] bg-[#fff0e6] text-[18px] font-bold text-[#cc5400]">{{ mb_substr($partner['name'], 0, 1) }}</span>
                            @endif
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-[15px] font-semibold">{{ $partner['name'] }}</span>
                                @if ($partner['description'] !== '')
                                    <span class="line-clamp-2 text-[13px] text-[#5a5a5a]">{{ $partner['description'] }}</span>
                                @endif
                            </span>
                            <x-icon name="chevron-right" class="size-5 shrink-0" />
                        </a>
                    @endforeach
                </div>
            @else
                <p class="rounded-[16px] bg-white p-6 text-center text-[14px] text-[#5a5a5a]">Partner listings are coming soon.</p>
            @endif
        </div>
    </x-page.panel>
</div>
