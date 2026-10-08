<section id="invest" class="relative mt-7 overflow-hidden rounded-[16px] p-4 sm:rounded-[24px] sm:p-5">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute inset-0 rounded-[16px] bg-[#0a0a0a] sm:rounded-[20px]"></div>
        <img src="{{ $asset }}/images/invest-bg.png" alt="" loading="lazy" class="absolute inset-0 size-full rounded-[16px] object-cover opacity-20 sm:rounded-[20px]">
    </div>

    <div class="relative z-10 flex flex-col gap-4">
        <div class="flex flex-col items-center gap-1.5 text-center text-white">
            <h3 class="w-full text-[20px] font-semibold leading-tight sm:text-[24px]">Grow more With our Investment Plan</h3>
            <p class="w-full text-[13px] font-normal leading-normal sm:text-[15px]">Join Investment Scheme, Stand a Chance To Make Huge Profit</p>
            <a href="{{ $url }}" class="btn-fx mt-1 inline-flex w-full max-w-[260px] items-center justify-center gap-2 rounded-[12px] bg-gradient-to-b from-[#cc5400] to-[#a34300] px-4 py-3 backdrop-blur-[7.25px]">
                <x-icon name="trophy-emoji" class="size-5 sm:size-[22px]" />
                <span class="text-[15px] font-semibold text-white sm:text-[16px]">Get Access now</span>
            </a>
        </div>

        <div class="rounded-[12px] bg-black/40 p-2.5 sm:rounded-[14px]">
            <p class="mb-2 text-left text-[14px] font-semibold capitalize text-white sm:text-[16px]">{{ $title }}</p>
            <div class="grid grid-flow-col auto-cols-[minmax(58px,1fr)] gap-2 overflow-x-auto pb-1">
                @foreach ($results as $result)
                    <div wire:key="result-{{ $result['date'] }}" @class([
                        'flex h-[68px] flex-col items-center justify-center gap-0.5 rounded-[10px] border bg-black/30 px-1.5 py-1.5 backdrop-blur-[7.45px]',
                        'border-[#14ae5c]' => $result['status'] === 'won',
                        'border-[#ec221f]' => $result['status'] === 'lost',
                        'border-white/20' => $result['status'] === 'pending',
                    ])>
                        <p class="w-full text-center text-[11px] font-bold uppercase text-[#f5f5f5] sm:text-[12px]">{{ $result['day'] }}</p>
                        @if ($result['status'] === 'pending')
                            <span class="block size-4 shrink-0 rounded-full border-2 border-white/50 sm:size-5" role="img" aria-label="No result"></span>
                        @else
                            <x-icon :name="$result['status'] === 'won' ? 'check-green' : 'close-bold'" class="size-4 sm:size-5" :alt="$result['status'] === 'won' ? 'Won' : 'Lost'" />
                        @endif
                        <p class="w-full text-center text-[11px] font-bold uppercase text-[#f5f5f5] sm:text-[12px]">{{ $result['date'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
