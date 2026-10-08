<div>
    <nav class="fixed inset-x-0 bottom-0 z-40 bg-[#1a1a1a] px-2 py-2 lg:hidden" aria-label="Mobile primary" x-data>
        <div class="mx-auto flex min-h-16 w-full max-w-site items-center justify-between gap-0.5 rounded-[20px] backdrop-blur-[7.45px]">
            @foreach ($items as $item)
                @php
                    $itemClass = 'flex min-w-0 flex-1 flex-col items-center justify-center gap-1 px-0.5 py-1.5 backdrop-blur-[7.25px] '
                        .($item['active'] ? 'rounded-[15px] border-b-4 border-[#ff6900] bg-[rgba(255,255,255,0.14)]' : 'rounded-[7px]');
                    $labelClass = 'w-full whitespace-pre-line text-center text-[11px] font-normal leading-[1.15] tracking-[0.1px] '
                        .($item['active'] ? 'text-white' : 'text-[#f3f3f3]');
                @endphp

                @if ($item['type'] === 'overlay')
                    <button
                        type="button"
                        class="{{ $itemClass }}"
                        @click="$store.overlay.toggle('{{ $item['overlay'] }}')"
                        :aria-expanded="$store.overlay.is('{{ $item['overlay'] }}')"
                        aria-label="Open {{ str_replace("\n", ' ', strtolower($item['label'])) }}"
                    >
                        <x-icon :name="$item['icon']" class="size-[18px]" />
                        <span class="{{ $labelClass }}">{{ $item['label'] }}</span>
                    </button>
                @else
                    <a href="{{ $item['href'] }}" class="{{ $itemClass }}" @if ($item['active']) aria-current="page" @endif>
                        <x-icon :name="$item['icon']" class="size-[18px]" />
                        <span class="{{ $labelClass }}">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </nav>
    <div class="h-[92px] lg:hidden" aria-hidden="true"></div>
</div>
