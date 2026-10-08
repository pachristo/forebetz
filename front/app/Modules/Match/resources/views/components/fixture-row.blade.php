@props(['row', 'teamId' => null, 'teamName' => null])

@php
    $isHome = $teamName !== null && $row['home'] === $teamName;
    $won = $teamName === null ? null : ($isHome ? $row['home_won'] : $row['away_won']);
    $result = $teamName === null ? null : ($won === true ? 'w' : ($won === false ? 'l' : 'd'));
@endphp

<li class="flex items-center gap-2 rounded-[10px] bg-[#f7f7f7] px-2.5 py-2 transition hover:bg-[#fff0e6]">
    <div class="flex w-[74px] shrink-0 flex-col text-[11px] leading-tight text-[#767676]">
        <span>{{ $row['date'] }}</span>
        <span class="truncate">{{ $row['league'] }}</span>
    </div>
    <div class="flex min-w-0 flex-1 flex-col gap-1 text-[13px]">
        <span class="flex items-center gap-1.5">
            <img src="{{ $row['home_logo'] }}" alt="" loading="lazy" class="size-4 shrink-0 object-contain">
            <span @class(['truncate', 'font-semibold' => $row['home_won'] === true])>{{ $row['home'] }}</span>
        </span>
        <span class="flex items-center gap-1.5">
            <img src="{{ $row['away_logo'] }}" alt="" loading="lazy" class="size-4 shrink-0 object-contain">
            <span @class(['truncate', 'font-semibold' => $row['away_won'] === true])>{{ $row['away'] }}</span>
        </span>
    </div>
    <div class="flex shrink-0 flex-col items-end gap-1 text-[13px] font-bold">
        <span>{{ $row['home_goals'] ?? '-' }}</span>
        <span>{{ $row['away_goals'] ?? '-' }}</span>
    </div>
    @if ($result)
        <span @class([
            'flex size-6 shrink-0 items-center justify-center rounded-[6px] text-[11px] font-bold text-white',
            'bg-[#14ae5c]' => $result === 'w',
            'bg-[#767676]' => $result === 'd',
            'bg-[#ef1410]' => $result === 'l',
        ])>{{ strtoupper($result) }}</span>
    @endif
</li>
