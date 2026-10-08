@props(['tabs', 'active', 'action' => 'selectTab'])

<div class="flex gap-1 border-b border-[#eee] px-2 py-1.5" role="tablist">
    @foreach ($tabs as $tab)
        <button
            type="button"
            role="tab"
            aria-selected="{{ $tab === $active ? 'true' : 'false' }}"
            wire:click="{{ $action }}('{{ $tab }}')"
            @class([
                'flex-1 rounded-lg px-1 py-1.5 text-[12px] font-semibold transition sm:text-[13px]',
                'bg-[#1a1a1a] text-white' => $tab === $active,
                'bg-[#f5f5f5] text-[#5a5a5a] hover:bg-[#ff6900] hover:text-white' => $tab !== $active,
            ])
        >{{ $tab }}</button>
    @endforeach
</div>
