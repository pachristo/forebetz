<div>
    <x-home::widget title="League Table">
        <x-home::tab-bar :tabs="$tabs" :active="$tab" />

        <div class="overflow-x-auto transition-opacity" wire:loading.class="opacity-50">
            @if ($rows)
                <table class="w-full text-left text-[13px] text-[#1e1e1e]">
                    <thead class="bg-[#f5f5f5] text-[11px] uppercase text-[#767676]">
                        <tr>
                            <th class="px-3 py-2">#</th>
                            <th class="px-2 py-2">Club</th>
                            <th class="px-2 py-2 text-center">P</th>
                            <th class="px-2 py-2 text-center">GD</th>
                            <th class="px-3 py-2 text-center">Pts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr wire:key="{{ $tab }}-{{ $row['pos'] }}" @class(['border-t border-[#f0f0f0]', 'bg-[#fff0e6]' => $loop->first])>
                                <td class="px-3 py-2 font-semibold">{{ $row['pos'] }}</td>
                                <td class="px-2 py-2">
                                    <div class="flex items-center gap-2">
                                        <span class="size-4 shrink-0 overflow-hidden"><img src="{{ $row['logo'] }}" alt="{{ $row['club'] }}" loading="lazy" class="h-full w-full object-contain"></span>
                                        <span class="truncate font-medium">{{ $row['club'] }}</span>
                                    </div>
                                </td>
                                <td class="px-2 py-2 text-center">{{ $row['p'] }}</td>
                                <td class="px-2 py-2 text-center">{{ $row['gd'] }}</td>
                                <td class="px-3 py-2 text-center font-bold">{{ $row['pts'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="px-4 py-6 text-center text-[14px] text-[#767676]">Standings for {{ $tab }} are not available right now.</p>
            @endif
        </div>
    </x-home::widget>
</div>
