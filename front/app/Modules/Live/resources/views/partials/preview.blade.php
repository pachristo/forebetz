<div wire:key="preview-{{ $p['id'] }}" x-data="{ tab: @js($p['events'] ? 'summary' : ($p['stats'] ? 'stats' : 'tips')) }">
    {{-- Scoreboard --}}
    <div class="relative bg-gradient-to-br from-[#1e1e1e] via-[#262626] to-[#3a1d08] px-4 pb-5 pt-4 text-white">
        <div class="mx-auto mb-3 h-1 w-10 rounded-full bg-white/30 lg:hidden"></div>

        <button type="button" @click="sheet = false" class="absolute right-3 top-3 flex size-8 items-center justify-center rounded-full bg-white/10 text-[20px] leading-none hover:bg-white/20 lg:hidden" aria-label="Close preview">&times;</button>

        <div class="flex items-center gap-2 pr-10 text-[12px] text-white/70 lg:pr-0">
            @if ($p['league_logo'])
                <img src="{{ $p['league_logo'] }}" alt="" class="size-5 shrink-0 rounded bg-white object-contain p-0.5">
            @endif
            <span class="truncate">{{ trim($p['country'].' · '.$p['league'], ' ·') }}</span>
        </div>
        @if ($p['round'] !== '')
            <p class="mt-0.5 text-[11px] text-white/45">{{ $p['round'] }}</p>
        @endif

        <div class="mt-4 grid grid-cols-[1fr_auto_1fr] items-start gap-2">
            <div class="flex min-w-0 flex-col items-center gap-2 text-center">
                <span class="flex size-14 items-center justify-center rounded-full bg-white p-2"><img src="{{ $p['home_logo'] }}" alt="" class="size-full object-contain"></span>
                <span class="line-clamp-2 text-[13px] font-semibold leading-tight">{{ $p['home'] }}</span>
            </div>

            <div class="flex min-w-[92px] flex-col items-center gap-1 pt-2">
                @if ($p['state'] === 'upcoming')
                    <span class="text-[26px] font-bold tabular-nums">{{ $p['time'] }}</span>
                    <span class="text-[11px] uppercase tracking-wide text-white/60">{{ $p['kickoff']?->isToday() ? 'Today' : $p['kickoff']?->format('D, M j') }}</span>
                @else
                    <span class="text-[32px] font-bold leading-none tabular-nums">{{ $p['home_goals'] ?? 0 }}<span class="mx-1.5 text-white/40">-</span>{{ $p['away_goals'] ?? 0 }}</span>
                    @if ($p['state'] === 'live')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-[#ef1410] px-2.5 py-0.5 text-[11px] font-bold uppercase">
                            <span class="size-1.5 animate-pulse rounded-full bg-white"></span>{{ $p['clock'] }}
                        </span>
                    @else
                        <span class="rounded-full bg-white/10 px-2.5 py-0.5 text-[11px] font-semibold uppercase text-white/80">{{ $p['state'] === 'finished' ? 'Full time' : $p['status'] }}</span>
                    @endif
                    @if ($p['ht'])
                        <span class="text-[11px] text-white/50">HT {{ $p['ht'] }}</span>
                    @endif
                @endif
            </div>

            <div class="flex min-w-0 flex-col items-center gap-2 text-center">
                <span class="flex size-14 items-center justify-center rounded-full bg-white p-2"><img src="{{ $p['away_logo'] }}" alt="" class="size-full object-contain"></span>
                <span class="line-clamp-2 text-[13px] font-semibold leading-tight">{{ $p['away'] }}</span>
            </div>
        </div>

        @if ($p['venue'] !== '')
            <p class="mt-4 flex items-center justify-center gap-1.5 text-[11px] text-white/55">
                <x-icon name="stadium" class="size-3.5 invert" />{{ $p['venue'] }}
            </p>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="sticky top-0 z-10 flex border-b border-[#eee] bg-white px-2">
        @foreach (['summary' => 'Summary', 'stats' => 'Stats', 'tips' => 'Prediction'] as $key => $label)
            <button
                type="button"
                @click="tab = '{{ $key }}'"
                class="relative flex-1 py-3 text-[13px] font-semibold transition"
                :class="tab === '{{ $key }}' ? 'text-[#ff6900]' : 'text-[#767676] hover:text-[#1e1e1e]'"
            >
                {{ $label }}
                <span class="absolute inset-x-4 bottom-0 h-0.5 rounded-full bg-[#ff6900]" x-show="tab === '{{ $key }}'"></span>
            </button>
        @endforeach
    </div>

    <div class="px-4 py-4 text-[#1e1e1e]">
        {{-- Summary: match events --}}
        <div x-show="tab === 'summary'">
            @if ($p['events'])
                <ol class="flex flex-col gap-2">
                    @foreach ($p['events'] as $e)
                        @php
                            $isGoal = $e['type'] === 'goal' && $e['detail'] !== 'Missed Penalty';
                            $isCard = $e['type'] === 'card';
                            $isSub = $e['type'] === 'subst';
                            $home = $e['side'] === 'home';
                        @endphp
                        <li @class(['flex items-center gap-2.5', 'flex-row-reverse text-right' => ! $home])>
                            <span class="w-9 shrink-0 text-center text-[12px] font-semibold tabular-nums text-[#767676]">{{ $e['minute'] }}{{ $e['extra'] ? '+'.$e['extra'] : '' }}'</span>
                            <span @class([
                                'flex size-7 shrink-0 items-center justify-center rounded-full',
                                'bg-[#e8f8ee]' => $isGoal,
                                'bg-[#f5f5f5]' => ! $isGoal,
                            ])>
                                @if ($isGoal)
                                    <svg viewBox="0 0 24 24" class="size-4 text-[#0f9d58]" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/><path d="M12 7l3.5 2.5-1.3 4h-4.4l-1.3-4z"/></svg>
                                @elseif ($isCard)
                                    <span @class(['h-3.5 w-2.5 rounded-[2px]', 'bg-[#f5c518]' => str_contains($e['detail'], 'Yellow'), 'bg-[#ef1410]' => ! str_contains($e['detail'], 'Yellow')])></span>
                                @elseif ($isSub)
                                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M7 4v14m0 0l-3-3m3 3l3-3" stroke="#0f9d58"/><path d="M17 20V6m0 0l-3 3m3-3l3 3" stroke="#ef1410"/></svg>
                                @else
                                    <span class="text-[10px] font-bold text-[#767676]">VAR</span>
                                @endif
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-[13px] font-semibold">{{ $e['player'] ?: $e['detail'] }}</span>
                                @if ($isSub && $e['assist'])
                                    <span class="block truncate text-[11px] text-[#767676]">Off: {{ $e['assist'] }}</span>
                                @elseif ($isGoal && $e['assist'])
                                    <span class="block truncate text-[11px] text-[#767676]">Assist: {{ $e['assist'] }}</span>
                                @elseif ($e['player'])
                                    <span class="block truncate text-[11px] text-[#767676]">{{ $e['detail'] }}</span>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ol>
            @else
                <p class="py-6 text-center text-[13px] text-[#767676]">
                    {{ $p['state'] === 'upcoming' ? 'Kick-off '.($p['kickoff']?->diffForHumans() ?? 'soon').'. Events will appear here once the match starts.' : 'No match events yet.' }}
                </p>
            @endif
        </div>

        {{-- Stats --}}
        <div x-show="tab === 'stats'" x-cloak>
            @if ($p['stats'])
                <div class="flex flex-col gap-3.5">
                    @foreach ($p['stats'] as $s)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-[13px]">
                                <span class="font-semibold tabular-nums">{{ $s['home'] }}</span>
                                <span class="text-[12px] text-[#767676]">{{ $s['label'] }}</span>
                                <span class="font-semibold tabular-nums">{{ $s['away'] }}</span>
                            </div>
                            <div class="flex h-1.5 gap-1">
                                <div class="flex flex-1 justify-end overflow-hidden rounded-full bg-[#f0f0f0]"><span class="h-full rounded-full bg-[#ff6900]" style="width: {{ $s['home_pct'] }}%"></span></div>
                                <div class="flex-1 overflow-hidden rounded-full bg-[#f0f0f0]"><span class="block h-full rounded-full bg-[#1e1e1e]" style="width: {{ 100 - $s['home_pct'] }}%"></span></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="py-6 text-center text-[13px] text-[#767676]">Match statistics are not available yet.</p>
            @endif
        </div>

        {{-- Prediction --}}
        <div x-show="tab === 'tips'" x-cloak class="flex flex-col gap-4">
            @if ($p['tip'])
                <div @class([
                    'flex items-center justify-between gap-3 rounded-[14px] border px-3.5 py-3',
                    'border-[#bfe8cf] bg-[#effaf3]' => $p['tip']['result'] === 'won',
                    'border-[#f6c9c8] bg-[#fdf0f0]' => $p['tip']['result'] === 'lost',
                    'border-[#ffd9bf] bg-[#fff6ef]' => $p['tip']['result'] === null,
                ])>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-[#cc5400]">Our free tip</p>
                        <p class="text-[16px] font-bold">{{ $p['tip']['pick'] }}</p>
                    </div>
                    <div class="text-right">
                        @if ($p['tip']['odds'])
                            <p class="text-[16px] font-bold tabular-nums">{{ $p['tip']['odds'] }}</p>
                        @endif
                        @if ($p['tip']['result'])
                            <p @class(['text-[11px] font-semibold uppercase', 'text-[#0f9d58]' => $p['tip']['result'] === 'won', 'text-[#ef1410]' => $p['tip']['result'] === 'lost'])>{{ $p['tip']['result'] }}</p>
                        @endif
                    </div>
                </div>
            @endif

            @if ($p['probs'])
                <div>
                    <p class="mb-2 text-[13px] font-semibold">Win probability</p>
                    <div class="flex h-2.5 overflow-hidden rounded-full">
                        @foreach ($p['probs'] as $i => $prob)
                            <span @class(['h-full', 'bg-[#ff6900]' => $i === 0, 'bg-[#bdbdbd]' => $i === 1, 'bg-[#1e1e1e]' => $i === 2]) style="width: {{ $prob['pct'] }}%"></span>
                        @endforeach
                    </div>
                    <div class="mt-1.5 flex justify-between text-[12px] text-[#5a5a5a]">
                        @foreach ($p['probs'] as $prob)
                            <span>{{ $prob['label'] }} <b class="text-[#1e1e1e]">{{ $prob['pct'] }}%</b></span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($p['odds'])
                <div class="grid grid-cols-3 gap-2">
                    @foreach ($p['odds'] as $label => $odd)
                        <div class="rounded-[12px] bg-[#f5f5f5] py-2 text-center">
                            <p class="text-[11px] text-[#767676]">{{ $label }}</p>
                            <p class="text-[15px] font-bold tabular-nums">{{ number_format((float) $odd, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            @if (! $p['tip'] && ! $p['probs'] && ! $p['odds'])
                <p class="py-6 text-center text-[13px] text-[#767676]">No prediction for this match yet.</p>
            @endif
        </div>

        <a href="{{ $p['href'] }}" class="mt-5 flex items-center justify-center gap-2 rounded-full bg-gradient-to-b from-[#ff7a1a] to-[#cc5400] px-4 py-3 text-[14px] font-semibold text-white hover:brightness-110">
            Full match preview
            <x-icon name="chevron-right" class="size-4 brightness-0 invert" />
        </a>
    </div>
</div>
