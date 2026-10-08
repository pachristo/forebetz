<div>
    <x-home::widget title="Top Scorers">
        <x-home::tab-bar :tabs="$tabs" :active="$tab" />

        <div class="transition-opacity" wire:loading.class="opacity-50">
            @if ($scorers)
                <table class="w-full text-left text-[13px] text-[#1e1e1e]">
                    <thead class="bg-[#f5f5f5] text-[11px] uppercase text-[#767676]">
                        <tr>
                            <th class="px-3 py-2">Player</th>
                            <th class="px-2 py-2 text-center">Club</th>
                            <th class="px-3 py-2 text-center">G</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($scorers as $scorer)
                            <tr wire:key="{{ $tab }}-{{ $loop->index }}" class="border-t border-[#f0f0f0]">
                                <td class="px-3 py-2 font-medium">{{ $scorer['player'] }}</td>
                                <td class="px-2 py-2">
                                    <span class="mx-auto flex size-5 overflow-hidden" title="{{ $scorer['club'] }}"><img src="{{ $scorer['logo'] }}" alt="{{ $scorer['club'] }}" loading="lazy" class="h-full w-full object-contain"></span>
                                </td>
                                <td class="px-3 py-2 text-center font-bold">{{ $scorer['goals'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="px-4 py-6 text-center text-[14px] text-[#767676]">Top scorers for {{ $tab }} are not available right now.</p>
            @endif
        </div>
    </x-home::widget>
</div>
