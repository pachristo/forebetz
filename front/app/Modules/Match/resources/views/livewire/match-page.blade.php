<div>
    <x-page.title :title="$title" :subtitle="trim(($league['country'] ?? '').' '.($league['name'] ?? '').' prediction, odds, head to head and form guide')" />

    <x-page.panel>
        <div class="flex w-full flex-col gap-3 sm:gap-4">
            {{-- Overview --}}
            <x-match::card>
                <div class="flex flex-col gap-2 border-b border-[#eee] pb-2.5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2">
                        @if (! empty($league['logo']))
                            <img src="{{ $league['logo'] }}" alt="" class="size-6 object-contain">
                        @endif
                        <span class="text-[13px] text-[#5a5a5a] sm:text-[15px]">{{ $league['country'] ?? '' }}</span>
                        <a href="/league?id={{ $league['id'] ?? $fixture->league_id }}" class="link-fx text-[13px] font-semibold sm:text-[15px]">{{ $league['name'] ?? '' }}</a>
                        @if (! empty($league['round']))
                            <span class="hidden text-[12px] text-[#9a9a9a] md:inline">· {{ $league['round'] }}</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[12px] text-[#5a5a5a] sm:text-[14px]">
                        <span class="inline-flex items-center gap-1.5"><x-icon name="date" class="size-4" />{{ $kickoff?->format('D, M j Y') }}</span>
                        <span class="inline-flex items-center gap-1.5"><x-icon name="time" class="size-4" />{{ $kickoff?->format('H:i') }}</span>
                        @if ($venue !== '')
                            <span class="inline-flex items-center gap-1.5"><x-icon name="stadium" class="size-4" />{{ $venue }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between gap-2 py-3 sm:justify-center sm:gap-10">
                    <div class="flex min-w-0 flex-1 flex-col items-center gap-1.5 sm:max-w-[260px]">
                        <img src="{{ $fixture->url_home_icon }}" alt="{{ $fixture->home_name }}" class="size-16 object-contain sm:size-20 lg:size-24">
                        <p class="max-w-full truncate text-center text-[14px] font-semibold sm:text-[18px]">{{ $fixture->home_name }}</p>
                    </div>

                    <div class="flex shrink-0 flex-col items-center gap-1">
                        @if ($started)
                            <span class="rounded-[8px] bg-[#1a1a1a] px-3 py-1.5 text-[20px] font-bold text-white sm:text-[26px]">{{ $fixture->home_goal }} - {{ $fixture->away_goal }}</span>
                            <span @class(['text-[12px] font-semibold uppercase sm:text-[14px]', 'text-[#ef1410]' => $isLive, 'text-[#767676]' => ! $isLive])>{{ $isLive ? 'Live · '.$status : 'Full time' }}</span>
                        @else
                            <span class="rounded-[8px] bg-[#f5f5f5] px-3 py-1.5 text-[18px] font-semibold sm:text-[22px]">{{ $kickoff?->format('H:i') }}</span>
                            <span class="whitespace-nowrap text-[12px] font-medium text-[#767676] sm:text-[14px]">{{ $kickoff?->isFuture() ? 'Kick-off '.$kickoff->diffForHumans() : $status }}</span>
                        @endif
                    </div>

                    <div class="flex min-w-0 flex-1 flex-col items-center gap-1.5 sm:max-w-[260px]">
                        <img src="{{ $fixture->url_away_icon }}" alt="{{ $fixture->away_name }}" class="size-16 object-contain sm:size-20 lg:size-24">
                        <p class="max-w-full truncate text-center text-[14px] font-semibold sm:text-[18px]">{{ $fixture->away_name }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    @if ($top)
                        <div class="rounded-[14px] border border-[#ff6900]/50 p-3">
                            <h2 class="mb-2 text-[15px] font-semibold sm:text-[16px]">Expert Prediction</h2>
                            <div class="flex items-end justify-between gap-2">
                                <div class="flex min-w-0 flex-col">
                                    <span class="text-[11px] text-[#767676] sm:text-[12px]">{{ $top['market'] }}</span>
                                    <span class="inline-flex items-center gap-1.5 rounded-[10px] bg-gradient-to-r from-[#ff7a1a] to-[#e85d00] px-3 py-2 text-[14px] font-bold text-white sm:text-[15px]">
                                        {{ $top['pick'] }}
                                        @if ($top['odds'])<span class="font-medium opacity-90">({{ $top['odds'] }})</span>@endif
                                    </span>
                                </div>
                                @if ($top['prob'])
                                    <div class="flex flex-col items-center">
                                        <span class="text-[11px] text-[#767676] sm:text-[12px]">Probability</span>
                                        <span class="rounded-[10px] border border-[#dadde2] px-3 py-2 text-[14px] font-semibold">{{ $top['prob'] }}%</span>
                                    </div>
                                @endif
                                @if ($started)
                                    <div class="flex flex-col items-center">
                                        <span class="text-[11px] text-[#767676] sm:text-[12px]">Score</span>
                                        <span class="rounded-[10px] border border-[#dadde2] px-3 py-2 text-[14px] font-semibold">{{ $fixture->home_goal }}:{{ $fixture->away_goal }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($probs)
                        @php($best = max(array_column($probs, 'pct')))
                        <div class="rounded-[14px] border border-[#ff6900]/50 p-3">
                            <h2 class="mb-2 text-[15px] font-semibold sm:text-[16px]">Match Probability</h2>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach ($probs as $prob)
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-[12px] text-[#767676]">{{ $prob['label'] }}</span>
                                        <span class="flex items-center gap-1 text-[14px] font-semibold">
                                            {{ $prob['pct'] }}%
                                            <x-icon :name="$prob['pct'] === $best ? 'prob-green' : ($prob['pct'] >= 25 ? 'prob-yellow' : 'prob-red')" class="size-5" />
                                        </span>
                                        <span class="text-[12px] font-medium text-[#5a5a5a]">{{ is_numeric($prob['odds']) ? number_format((float) $prob['odds'], 2) : '-' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </x-match::card>

            {{-- All tips --}}
            @if ($predictions['free'] || $predictions['vip'] || $predictions['locked'])
                <x-match::card title="Tips for this match">
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($predictions['vip'] as $tip)
                            <div class="flex items-center justify-between gap-2 rounded-[12px] border border-[#ffcc4d] bg-[#fff8e1] px-3 py-2">
                                <div class="min-w-0">
                                    <p class="flex items-center gap-1 text-[11px] font-semibold uppercase text-[#b07d00]"><x-icon name="crown" class="size-3.5" /> VIP · {{ $tip['market'] }}</p>
                                    <p class="truncate text-[14px] font-bold">{{ $tip['pick'] }}</p>
                                </div>
                                <span class="shrink-0 text-[13px] font-semibold text-[#5a5a5a]">{{ $tip['odds'] ?? '' }}</span>
                            </div>
                        @endforeach
                        @foreach ($predictions['free'] as $tip)
                            <div @class([
                                'flex items-center justify-between gap-2 rounded-[12px] border px-3 py-2 transition hover:border-[#ff6900]',
                                'border-[#14ae5c]/60 bg-[#ecfdf3]' => $tip['result'] === 'won',
                                'border-[#ef1410]/50 bg-[#fff1f0]' => $tip['result'] === 'lost',
                                'border-[#e6e6e6] bg-[#fafafa]' => $tip['result'] === null,
                            ])>
                                <div class="min-w-0">
                                    <p class="text-[11px] font-medium uppercase text-[#767676]">{{ $tip['market'] }}</p>
                                    <p class="truncate text-[14px] font-bold">{{ $tip['pick'] }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2 text-[13px] font-semibold text-[#5a5a5a]">
                                    @if ($tip['prob'])<span class="text-[12px] font-medium text-[#9a9a9a]">{{ $tip['prob'] }}%</span>@endif
                                    <span>{{ $tip['odds'] ?? '-' }}</span>
                                    @if ($tip['result'])
                                        <x-icon :name="$tip['result'] === 'won' ? 'check-green' : 'close-bold'" class="size-4" />
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @foreach ($predictions['locked'] as $plan)
                            <a href="/pricing" class="btn-fx flex items-center justify-between gap-2 rounded-[12px] border border-dashed border-[#ff6900] bg-[#fff4ec] px-3 py-2">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-semibold uppercase text-[#cc5400]">{{ str_ends_with(strtolower($plan), 'tip') ? $plan : $plan.' tip' }}</p>
                                    <p class="text-[14px] font-bold text-[#1e1e1e]">Locked — subscribe to view</p>
                                </div>
                                <x-icon name="crown" class="size-5" />
                            </a>
                        @endforeach
                    </div>
                </x-match::card>
            @endif

            {{-- Team comparison --}}
            @if ($comparison)
                <x-match::card title="Team Comparison">
                    <div class="mb-2 flex items-center justify-between text-[13px] font-semibold">
                        <span class="flex items-center gap-1.5"><img src="{{ $fixture->url_home_icon }}" alt="" class="size-5 object-contain">{{ $fixture->home_name }}</span>
                        <span class="flex items-center gap-1.5">{{ $fixture->away_name }}<img src="{{ $fixture->url_away_icon }}" alt="" class="size-5 object-contain"></span>
                    </div>
                    <div class="grid grid-cols-1 gap-x-6 gap-y-2 md:grid-cols-2">
                        @foreach ($comparison as $row)
                            <div>
                                <div class="mb-1 flex justify-between text-[12px] text-[#5a5a5a]">
                                    <span class="font-semibold text-[#1e1e1e]">{{ $row['home'] }}%</span>
                                    <span>{{ $row['label'] }}</span>
                                    <span class="font-semibold text-[#1e1e1e]">{{ $row['away'] }}%</span>
                                </div>
                                <div class="flex h-2 overflow-hidden rounded-full bg-[#eee]">
                                    <span class="h-full bg-[#ff6900]" style="width: {{ $row['home'] }}%"></span>
                                    <span class="h-full bg-[#1a1a1a]" style="width: {{ $row['away'] }}%"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-match::card>
            @endif

            {{-- Head to head and form --}}
            @if ($h2h || $homeLast || $awayLast)
                <div class="grid grid-cols-1 gap-3 sm:gap-4 lg:grid-cols-3">
                    @if ($h2h)
                        <x-match::card title="Head to Head">
                            <ul class="flex flex-col gap-1.5">
                                @foreach ($h2h as $row)
                                    <x-match::fixture-row :row="$row" wire:key="h2h-{{ $row['id'] }}" />
                                @endforeach
                            </ul>
                        </x-match::card>
                    @endif
                    @foreach ([[$fixture->home_name, $homeLast], [$fixture->away_name, $awayLast]] as [$team, $rows])
                        @if ($rows)
                            <x-match::card :title="$team.' — Last '.count($rows)">
                                <ul class="flex flex-col gap-1.5">
                                    @foreach ($rows as $row)
                                        <x-match::fixture-row :row="$row" :team-name="$team" wire:key="last-{{ $loop->parent->index }}-{{ $row['id'] }}" />
                                    @endforeach
                                </ul>
                            </x-match::card>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Standings --}}
            @if ($standings)
                <x-match::card :title="($league['name'] ?? 'League').' Table'">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[560px] text-left text-[13px]">
                            <thead class="bg-[#f5f5f5] text-[11px] uppercase text-[#767676]">
                                <tr>
                                    <th class="px-2 py-2">#</th>
                                    <th class="px-2 py-2">Team</th>
                                    <th class="px-2 py-2 text-center">GP</th>
                                    <th class="px-2 py-2 text-center">W</th>
                                    <th class="px-2 py-2 text-center">D</th>
                                    <th class="px-2 py-2 text-center">L</th>
                                    <th class="px-2 py-2 text-center">G</th>
                                    <th class="px-2 py-2 text-center">GD</th>
                                    <th class="px-2 py-2 text-center">Pts</th>
                                    <th class="hidden px-2 py-2 text-center md:table-cell">Form</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($standings as $row)
                                    <tr wire:key="st-{{ $row['team_id'] }}" @class(['border-t border-[#f0f0f0]', 'bg-[#fff0e6] font-semibold' => in_array($row['team_id'], $teamIds, true)])>
                                        <td class="px-2 py-1.5">{{ $row['pos'] }}</td>
                                        <td class="px-2 py-1.5">
                                            <span class="flex items-center gap-2"><img src="{{ $row['logo'] }}" alt="" loading="lazy" class="size-4 object-contain"><span class="truncate">{{ $row['club'] }}</span></span>
                                        </td>
                                        <td class="px-2 py-1.5 text-center">{{ $row['p'] }}</td>
                                        <td class="px-2 py-1.5 text-center">{{ $row['w'] ?? '' }}</td>
                                        <td class="px-2 py-1.5 text-center">{{ $row['d'] ?? '' }}</td>
                                        <td class="px-2 py-1.5 text-center">{{ $row['l'] ?? '' }}</td>
                                        <td class="px-2 py-1.5 text-center">{{ ($row['gf'] ?? 0).':'.($row['ga'] ?? 0) }}</td>
                                        <td class="px-2 py-1.5 text-center">{{ $row['gd'] > 0 ? '+'.$row['gd'] : $row['gd'] }}</td>
                                        <td class="px-2 py-1.5 text-center font-bold">{{ $row['pts'] }}</td>
                                        <td class="hidden px-2 py-1.5 md:table-cell">
                                            <span class="flex justify-center gap-0.5">
                                                @foreach ($row['form'] ?? [] as $f)
                                                    <span @class(['flex size-4 items-center justify-center rounded-[3px] text-[9px] font-bold text-white', 'bg-[#14ae5c]' => $f === 'w', 'bg-[#767676]' => $f === 'd', 'bg-[#ef1410]' => $f === 'l'])>{{ strtoupper($f) }}</span>
                                                @endforeach
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-match::card>
            @endif

            {{-- Conclusion --}}
            @if ($top || $advice !== '')
                <x-match::card title="Conclusion">
                    <p class="text-[14px] leading-6 text-[#303030]">
                        {{ $fixture->home_name }} host {{ $fixture->away_name }} in the {{ $league['name'] ?? 'league' }} on {{ $kickoff?->format('l, F j') }}.
                        @if ($top)
                            Our pick for this match is <strong>{{ $top['pick'] }}</strong>{{ $top['odds'] ? ' at odds of '.$top['odds'] : '' }}{{ $top['prob'] ? ', with an estimated '.$top['prob'].'% probability' : '' }}.
                        @endif
                        @if ($advice !== '')
                            Data model advice: {{ $advice }}.
                        @endif
                        Always bet responsibly and only stake what you can afford to lose.
                    </p>
                </x-match::card>
            @endif
        </div>
    </x-page.panel>
</div>
