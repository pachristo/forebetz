<div
    x-data="{ sheet: false }"
    x-effect="const open = sheet && window.innerWidth < 1024; document.body.style.overflow = open ? 'hidden' : ''; document.body.classList.toggle('live-sheet-open', open)"
    @keydown.escape.window="sheet = false"
    @if ($dayOffset === 0) wire:poll.60s @endif
>
    <x-page.title title="Live Football Scores" subtitle="Real-time scores, goal scorers, cards and match stats from every league. Tap a match for the live preview." />

    <x-page.panel>
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:gap-6">
            {{-- Score list --}}
            <section class="min-w-0 flex-1">
                <div class="mb-3 flex flex-col gap-2.5">
                    <div class="flex items-center gap-1.5">
                        <button type="button" wire:click="setDay({{ $dayOffset - 1 }})" class="flex size-9 shrink-0 items-center justify-center rounded-full bg-white hover:bg-[#ff6900] sm:size-10 [&:hover_img]:brightness-0 [&:hover_img]:invert" aria-label="Previous day">
                            <x-icon name="chevron-right" class="size-5 rotate-180" />
                        </button>
                        <div class="flex h-10 min-w-0 flex-1 rounded-full bg-[#ffe9d9] p-1 sm:h-11">
                            @foreach ([-1 => 'Yesterday', 0 => 'Today', 1 => 'Tomorrow'] as $offset => $label)
                                <button type="button" wire:click="setDay({{ $offset }})" @class([
                                    'flex flex-1 items-center justify-center rounded-full px-2 text-[12px] transition sm:text-[14px]',
                                    'bg-gradient-to-b from-[#ff7a1a] to-[#cc5400] font-bold text-white' => $dayOffset === $offset,
                                    'bg-white font-medium text-[#303030] hover:bg-[#ff6900] hover:text-white' => $dayOffset !== $offset,
                                ])>{{ $label }}</button>
                            @endforeach
                        </div>
                        <button type="button" wire:click="setDay({{ $dayOffset + 1 }})" class="flex size-9 shrink-0 items-center justify-center rounded-full bg-white hover:bg-[#ff6900] sm:size-10 [&:hover_img]:brightness-0 [&:hover_img]:invert" aria-label="Next day">
                            <x-icon name="chevron-right" class="size-5" />
                        </button>
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <div class="no-scrollbar flex min-w-0 gap-1.5 overflow-x-auto">
                            @foreach (\App\Modules\Live\Livewire\LivePage::FILTERS as $key => $label)
                                <button type="button" wire:click="setFilter('{{ $key }}')" @class([
                                    'inline-flex shrink-0 items-center gap-1.5 rounded-full px-3.5 py-1.5 text-[13px] font-medium transition',
                                    'bg-[#1e1e1e] text-white' => $filter === $key,
                                    'bg-white text-[#303030] hover:bg-[#ffe9d9]' => $filter !== $key,
                                ])>
                                    @if ($key === 'live')
                                        <span class="relative flex size-2"><span class="absolute inline-flex size-full animate-ping rounded-full bg-[#ef1410] opacity-70"></span><span class="relative inline-flex size-2 rounded-full bg-[#ef1410]"></span></span>
                                    @endif
                                    {{ $label }}
                                    <span @class(['text-[11px]', 'text-white/60' => $filter === $key, 'text-[#9a9a9a]' => $filter !== $key])>{{ $counts[$key] }}</span>
                                </button>
                            @endforeach
                        </div>
                        <p class="hidden shrink-0 text-[13px] text-[#5a5a5a] sm:block">{{ $date->format('D, M jS Y') }}</p>
                    </div>
                </div>

                <div class="relative flex flex-col gap-3" wire:loading.class="opacity-60" wire:target="setDay,setFilter">
                    @forelse ($leagues as $league)
                        <div class="overflow-hidden rounded-[16px] bg-white" wire:key="league-{{ $league['id'] }}">
                            <div class="flex items-center gap-2 border-b border-[#f0f0f0] bg-[#fafafa] px-3 py-2.5">
                                @if ($league['flag'] || $league['logo'])
                                    <img src="{{ $league['flag'] ?: $league['logo'] }}" alt="" class="size-5 shrink-0 rounded-full object-cover" loading="lazy">
                                @endif
                                <p class="min-w-0 truncate text-[13px] sm:text-[14px]">
                                    <span class="text-[#767676]">{{ $league['country'] }}</span>
                                    <span class="font-semibold text-[#1e1e1e]">{{ $league['name'] }}</span>
                                </p>
                                @if ($league['live'])
                                    <span class="ml-auto shrink-0 rounded-full bg-[#ef1410]/10 px-2 py-0.5 text-[11px] font-semibold text-[#ef1410]">{{ $league['live'] }} live</span>
                                @endif
                            </div>

                            <div class="divide-y divide-[#f0f0f0]">
                                @foreach ($league['matches'] as $m)
                                    <button
                                        type="button"
                                        wire:key="m-{{ $m['id'] }}"
                                        wire:click="select({{ $m['id'] }})"
                                        @click="sheet = true"
                                        @class([
                                            'group flex w-full items-center gap-3 px-3 py-2.5 text-left transition hover:bg-[#fff6ef]',
                                            'bg-[#fff1e6] shadow-[inset_3px_0_0_#ff6900]' => $selectedId === $m['id'],
                                        ])
                                    >
                                        <span @class([
                                            'w-11 shrink-0 text-center text-[12px] font-semibold tabular-nums sm:w-12 sm:text-[13px]',
                                            'text-[#ef1410]' => $m['state'] === 'live',
                                            'text-[#767676]' => $m['state'] !== 'live',
                                        ])>
                                            {{ $m['clock'] }}
                                            @if ($m['state'] === 'live')
                                                <span class="mx-auto mt-0.5 block h-0.5 w-5 animate-pulse rounded bg-[#ef1410]"></span>
                                            @endif
                                        </span>

                                        <span class="flex min-w-0 flex-1 flex-col gap-1.5">
                                            @foreach (['home', 'away'] as $side)
                                                @php($other = $side === 'home' ? 'away' : 'home')
                                                <span class="flex items-center gap-2">
                                                    <img src="{{ $m[$side.'_logo'] }}" alt="" class="size-5 shrink-0 object-contain" loading="lazy">
                                                    <span @class([
                                                        'min-w-0 flex-1 truncate text-[13px] sm:text-[14px]',
                                                        'font-semibold text-[#1e1e1e]' => $m['state'] === 'finished' && $m[$side.'_goals'] > $m[$other.'_goals'],
                                                        'text-[#303030]' => ! ($m['state'] === 'finished' && $m[$side.'_goals'] > $m[$other.'_goals']),
                                                    ])>{{ $m[$side] }}</span>
                                                    <span @class([
                                                        'w-6 shrink-0 text-right text-[14px] font-bold tabular-nums',
                                                        'text-[#ef1410]' => $m['state'] === 'live',
                                                        'text-[#1e1e1e]' => $m['state'] !== 'live',
                                                    ])>{{ $m[$side.'_goals'] ?? '' }}</span>
                                                </span>
                                            @endforeach
                                        </span>

                                        <x-icon name="chevron-right" class="size-4 shrink-0 opacity-40 transition group-hover:opacity-80 lg:hidden" />
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[16px] bg-white px-5 py-12 text-center text-[14px] text-[#767676]">
                            No {{ $filter === 'all' ? '' : strtolower(\App\Modules\Live\Livewire\LivePage::FILTERS[$filter]).' ' }}matches for {{ $date->format('D, M jS') }}.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Match preview: sticky sidebar on desktop, bottom-sheet pop-up on mobile --}}
            <aside
                class="z-[95] shrink-0 max-lg:fixed max-lg:inset-0 max-lg:items-end max-lg:bg-black/60 lg:sticky lg:top-4 lg:block lg:w-[380px] xl:w-[400px]"
                :class="sheet ? 'max-lg:flex' : 'max-lg:hidden'"
                @click.self="sheet = false"
                x-cloak
            >
                <div class="live-sheet relative w-full overflow-hidden bg-white max-lg:max-h-[88svh] max-lg:overflow-y-auto max-lg:rounded-t-[24px] lg:rounded-[20px]">
                    <div wire:loading.flex wire:target="select" class="absolute inset-0 z-20 items-center justify-center bg-white/70">
                        <span class="size-8 animate-spin rounded-full border-[3px] border-[#ff6900] border-t-transparent"></span>
                    </div>

                    @if ($preview)
                        @include('live::partials.preview', ['p' => $preview])
                    @else
                        <div class="px-6 py-16 text-center text-[14px] text-[#767676]">Select a match to see the live preview.</div>
                    @endif
                </div>
            </aside>
        </div>
    </x-page.panel>
</div>
