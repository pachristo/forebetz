@props(['match'])

@php
    $formIcon = fn (string $f) => match ($f) { 'w' => 'form-w', 'l' => 'form-lose', default => 'form-draw' };
    $hasScore = $match['home_goals'] !== null;
    $href = \App\Support\MatchUrl::make($match['id'], $match['home'], $match['away']);
@endphp

<article {{ $attributes->class('match-row w-full min-w-0 overflow-hidden rounded-[14px] font-grotesk bg-[#dcdee2] p-1.5 md:rounded-[16px] lg:rounded-[22px] lg:p-2') }}>
    <a href="{{ $href }}" class="flex min-w-0 items-center justify-between gap-2 px-1.5 pb-1.5 pt-0.5 md:px-2">
        <div class="flex min-w-0 items-center gap-1 md:gap-1.5">
            <span class="size-[18px] shrink-0 overflow-hidden md:size-5">
                <img src="{{ $match['league_logo'] ?: config('site.asset_path').'/icons/premier.svg' }}" alt="" loading="lazy" class="h-full w-full object-contain">
            </span>
            <span class="truncate text-[12px] font-medium capitalize leading-5 text-[#5a5a5a] md:text-[14px]">{{ $match['country'] }}</span>
            <span class="truncate text-[12px] font-medium capitalize leading-5 text-[#1e1e1e] md:text-[14px] match-league">{{ $match['league'] }}</span>
        </div>
        <x-icon name="chevron-right" class="match-chevron size-[18px] opacity-70" />
    </a>

    <div class="min-w-0 rounded-[10px] bg-white md:rounded-[12px] lg:rounded-[14px]">
        <a href="{{ $href }}" class="flex min-w-0 flex-col px-1.5 py-2 md:px-2.5 lg:px-3 lg:py-2.5">
            <div class="flex w-full min-w-0 flex-col gap-2 lg:flex-row lg:items-center lg:justify-between lg:gap-4">
                {{-- Teams + kickoff --}}
                <div class="flex w-full min-w-0 items-center justify-center gap-1.5 md:gap-2.5 lg:min-w-0 lg:flex-1 lg:gap-3">
                    <div class="flex min-w-0 flex-1 items-center justify-end gap-1 md:gap-1.5">
                        <div class="flex min-w-0 flex-col items-end gap-0.5">
                            <p class="max-w-full truncate text-right text-[12px] font-medium leading-tight text-[#1e1e1e] md:text-[15px] lg:text-[16px]">{{ $match['home'] }}</p>
                            <div class="flex items-center gap-0.5">
                                @foreach ($match['home_form'] as $f)
                                    <x-icon :name="$formIcon($f)" class="size-3 md:size-[13px] lg:size-[15px]" />
                                @endforeach
                            </div>
                        </div>
                        <div class="match-logo size-7 shrink-0 overflow-hidden md:size-8 lg:size-10">
                            <img src="{{ $match['home_logo'] }}" alt="" loading="lazy" class="h-full w-full object-contain">
                        </div>
                    </div>

                    <div class="flex w-[44px] shrink-0 flex-col items-center overflow-hidden rounded-[5px] md:w-[52px]">
                        <div class="flex w-full gap-px">
                            @foreach ([$match['home_goals'], $match['away_goals']] as $goals)
                                <div @class([
                                    'min-w-0 flex-1 bg-[#f5f5f5] text-center text-[13px] font-semibold leading-6 md:text-[15px]',
                                    'text-[#1e1e1e]' => $hasScore,
                                    'text-[#757575]' => ! $hasScore,
                                ])>{{ $goals ?? '-' }}</div>
                            @endforeach
                        </div>
                        <div @class([
                            'w-full text-center text-[10px] font-semibold leading-5 md:text-[12px]',
                            'bg-[#ff6900] text-[#1a1a1a]' => $match['is_live'],
                            'bg-[#f5f5f5] text-[#1e1e1e]' => ! $match['is_live'],
                        ])>{{ $match['is_live'] || $hasScore ? $match['status'] : $match['time'] }}</div>
                    </div>

                    <div class="flex min-w-0 flex-1 items-center gap-1 md:gap-1.5">
                        <div class="match-logo size-7 shrink-0 overflow-hidden md:size-8 lg:size-10">
                            <img src="{{ $match['away_logo'] }}" alt="" loading="lazy" class="h-full w-full object-contain">
                        </div>
                        <div class="flex min-w-0 flex-col items-start gap-0.5">
                            <p class="max-w-full truncate text-[12px] font-medium leading-tight text-[#1e1e1e] md:text-[15px] lg:text-[16px]">{{ $match['away'] }}</p>
                            <div class="flex items-center gap-0.5">
                                @foreach ($match['away_form'] as $f)
                                    <x-icon :name="$formIcon($f)" class="size-3 md:size-[13px] lg:size-[15px]" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Odds | Prediction | Score --}}
                <div class="grid w-full min-w-0 grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] items-end gap-1 md:gap-1.5 lg:w-auto lg:shrink-0 lg:grid-cols-[180px_180px_auto] lg:gap-2.5">
                    <div class="flex min-w-0 flex-col items-center">
                        <p class="text-[11px] font-medium leading-5 text-[#767676] md:text-[12px] lg:text-[13px]">Odds</p>
                        <div class="match-box flex h-7 w-full min-w-0 items-center rounded-[8px] border border-[#dadde2] bg-white md:h-9 lg:h-10 lg:rounded-[10px]">
                            @foreach ($match['odds'] as $key => $odd)
                                <div @class([
                                    'flex min-w-0 flex-1 items-center justify-center gap-0.5 md:gap-1 lg:gap-1.5',
                                    'border-x border-[#e6e9ec]' => $loop->index === 1,
                                ])>
                                    <span class="text-[10px] font-medium capitalize text-[#767676] md:text-[12px] lg:text-[13px]">{{ $key }}</span>
                                    <span class="text-[11px] font-semibold text-[#1e1e1e] md:text-[13px] lg:text-[14px]">{{ $odd }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex min-w-0 flex-col items-center">
                        <p class="text-[11px] font-medium leading-5 text-[#767676] md:text-[12px] lg:text-[13px]">Prediction</p>
                        <span
                            @class([
                                'match-pick flex h-7 w-full max-w-full items-center justify-center gap-1 rounded-[8px] px-1.5 md:h-9 lg:h-10 lg:rounded-[10px]',
                                'bg-gradient-to-b from-[#14ae5c] to-[#108245]' => $match['result'] === 'won',
                                'bg-[#d9d9d9]' => $match['result'] === 'lost',
                            ])
                            @unless ($match['result']) style="background-image: linear-gradient(107deg, #ff6900 0%, #f26300 23%, #ee6100 28%, #f56500 79%, #dd5b00 90%);" @endunless
                        >
                            <span @class([
                                'truncate text-[11px] font-bold md:text-[13px] lg:text-[14px]',
                                'text-white' => $match['result'] === 'won',
                                'text-[#1a1a1a] line-through' => $match['result'] === 'lost',
                                'text-[#1a1a1a]' => ! $match['result'],
                            ])>{{ $match['prediction'] }}</span>
                            @if ($match['prediction_odds'])
                                <span class="shrink-0 text-[10px] font-medium text-[#1a1a1a] md:text-[12px] lg:text-[13px]">({{ $match['prediction_odds'] }})</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex shrink-0 flex-col items-center">
                        <p class="text-[11px] font-medium leading-5 text-[#767676] md:text-[12px] lg:text-[13px]">Score</p>
                        <div class="match-box flex h-7 min-w-[44px] items-center justify-center rounded-[8px] border border-[#dadde2] bg-white px-1.5 md:h-9 lg:h-10 lg:min-w-[52px] lg:rounded-[10px]">
                            <span class="whitespace-nowrap text-[11px] font-semibold text-[#1e1e1e] md:text-[13px] lg:text-[14px]">
                                {{ $hasScore ? $match['home_goals'].' : '.$match['away_goals'] : '- : -' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</article>
