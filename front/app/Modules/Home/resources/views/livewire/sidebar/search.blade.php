<div class="relative w-full" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
    <form action="/search" method="get" role="search" @submit="if (! $wire.q.trim()) $event.preventDefault()">
        <label class="sr-only" for="sidebar-search">Search</label>
        <input
            id="sidebar-search"
            type="search"
            name="q"
            autocomplete="off"
            placeholder="Search teams, leagues..."
            wire:model.live.debounce.300ms="q"
            @focus="open = true"
            @input="open = true"
            class="w-full rounded-full border-0 bg-white px-5 py-3 text-[14px] text-[#1e1e1e] outline-none ring-0 placeholder:text-[#9ca3af] focus:ring-2 focus:ring-[#ff6900]"
        >
        <span wire:loading wire:target="q" class="absolute right-5 top-1/2 size-4 -translate-y-1/2 animate-spin rounded-full border-2 border-[#ff6900] border-t-transparent"></span>
    </form>

    @if (mb_strlen(trim($q)) >= 2)
        <div x-show="open" x-transition class="absolute inset-x-0 top-full z-20 mt-2 max-h-[360px] overflow-y-auto rounded-[16px] bg-white py-2 text-[#1e1e1e] shadow-xl">
            @if ($leagues)
                <p class="px-4 pb-1 pt-2 text-[11px] font-semibold uppercase tracking-wide text-[#9ca3af]">Leagues</p>
                <ul>
                    @foreach ($leagues as $league)
                        <x-home::link-row :href="$league['href']" :image="$league['image']" :label="$league['label']" />
                    @endforeach
                </ul>
            @endif

            @if ($matches)
                <p class="px-4 pb-1 pt-2 text-[11px] font-semibold uppercase tracking-wide text-[#9ca3af]">Matches</p>
                <ul>
                    @foreach ($matches as $match)
                        <li>
                            <a href="{{ $match['href'] }}" class="flex items-center justify-between gap-3 px-5 py-3 text-[14px] hover:bg-[#f8f8f8]">
                                <span class="truncate font-medium">{{ $match['label'] }}</span>
                                <span class="shrink-0 text-[12px] text-[#767676]">{{ $match['meta'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if (! $leagues && ! $matches)
                <p class="px-4 py-3 text-[14px] text-[#767676]">No teams or leagues match “{{ $q }}”.</p>
            @endif
        </div>
    @endif
</div>
