@props(['row'])

@php
    [$homeScore, $awayScore] = array_pad(array_map('trim', preg_split('/\s*[-:]\s*/', (string) $row['score'], 2)), 2, '-');
@endphp

<article {{ $attributes->class('match-row w-full min-w-0 overflow-hidden rounded-[14px] font-grotesk border-l-4 border-[#14ae5c] bg-[#dcdee2] p-1.5 md:rounded-[16px] lg:rounded-[22px] lg:p-2') }}>
    <div class="min-w-0 rounded-[10px] bg-white px-1.5 py-2 md:rounded-[12px] md:px-2.5 lg:rounded-[16px] lg:px-3 lg:py-2.5">
        <div class="flex w-full min-w-0 flex-col gap-2 lg:flex-row lg:items-center lg:justify-between lg:gap-4">
            <div class="flex w-full min-w-0 items-center justify-center gap-1.5 md:gap-2.5 lg:min-w-0 lg:flex-1 lg:gap-3">
                <div class="flex min-w-0 flex-1 items-center justify-end gap-1 md:gap-1.5">
                    <p class="max-w-full truncate text-right text-[12px] font-medium leading-tight text-[#1e1e1e] md:text-[15px] lg:text-[16px]">{{ $row['home'] }}</p>
                    <div class="match-logo size-7 shrink-0 overflow-hidden md:size-8 lg:size-10">
                        <img src="{{ $row['home_logo'] }}" alt="" loading="lazy" class="h-full w-full object-contain">
                    </div>
                </div>

                <div class="flex w-[52px] shrink-0 flex-col items-center md:w-[60px]">
                    @if (! empty($row['date']))
                        <p class="text-center text-[9px] leading-3 text-[#757575] md:text-[10px]">{{ $row['date'] }}</p>
                    @endif
                    <div class="flex w-full gap-px overflow-hidden rounded-[4px]">
                        @foreach ([$homeScore, $awayScore] as $goals)
                            <div class="min-w-0 flex-1 bg-[#f5f5f5] text-center text-[13px] font-semibold leading-6 text-[#1e1e1e] md:text-[15px]">{{ $goals }}</div>
                        @endforeach
                    </div>
                </div>

                <div class="flex min-w-0 flex-1 items-center gap-1 md:gap-1.5">
                    <div class="match-logo size-7 shrink-0 overflow-hidden md:size-8 lg:size-10">
                        <img src="{{ $row['away_logo'] }}" alt="" loading="lazy" class="h-full w-full object-contain">
                    </div>
                    <p class="max-w-full truncate text-[12px] font-medium leading-tight text-[#1e1e1e] md:text-[15px] lg:text-[16px]">{{ $row['away'] }}</p>
                </div>
            </div>

            <div class="grid w-full min-w-0 grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] items-end gap-1 md:gap-1.5 lg:w-auto lg:shrink-0 lg:grid-cols-[180px_180px_auto] lg:gap-2.5">
                <div class="flex min-w-0 flex-col items-center">
                    <p class="text-[11px] font-medium leading-5 text-[#767676] md:text-[12px] lg:text-[13px]">Odds</p>
                    <div class="match-box flex h-7 w-full min-w-0 items-center justify-center rounded-[8px] border border-[#dadde2] bg-white md:h-9 lg:h-10 lg:rounded-[10px]">
                        <span class="whitespace-nowrap text-[11px] font-semibold text-[#1e1e1e] md:text-[13px] lg:text-[14px]">{{ $row['odds'] }}</span>
                    </div>
                </div>

                <div class="flex min-w-0 flex-col items-center">
                    <p class="text-[11px] font-medium leading-5 text-[#767676] md:text-[12px] lg:text-[13px]">Prediction</p>
                    <span class="match-pick flex h-7 w-full max-w-full items-center justify-center gap-1.5 rounded-[8px] bg-gradient-to-b from-[#14ae5c] to-[#108245] px-1.5 md:h-9 lg:h-10 lg:rounded-[10px]">
                        <span class="truncate text-[11px] font-bold text-white md:text-[13px] lg:text-[14px]">{{ $row['pick'] }}</span>
                        <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-white px-1.5 text-[9px] font-bold uppercase leading-4 text-[#14ae5c] md:text-[10px]">
                            WON
                            <x-icon name="trophy-emoji" class="size-3" />
                        </span>
                    </span>
                </div>

                <div class="flex shrink-0 flex-col items-center">
                    <p class="text-[11px] font-medium leading-5 text-[#767676] md:text-[12px] lg:text-[13px]">Score</p>
                    <div class="match-box flex h-7 min-w-[44px] items-center justify-center rounded-[8px] border border-[#dadde2] bg-white px-1.5 md:h-9 lg:h-10 lg:min-w-[52px] lg:rounded-[10px]">
                        <span class="whitespace-nowrap text-[11px] font-semibold text-[#1e1e1e] md:text-[13px] lg:text-[14px]">{{ $row['score'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
