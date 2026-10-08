<div>
    @if ($winnings)
    <section class="mt-7 overflow-hidden rounded-[16px] bg-white md:rounded-[20px]">
        <div class="flex items-center justify-between border-b border-[#e6e9ec] px-3 py-3 md:px-5">
            <h2 class="text-[18px] font-semibold text-[#1e1e1e] md:text-[22px]">Recent Winnings</h2>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#14ae5c]/15 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.04px] text-[#108245] md:text-[12px]">
                <x-icon name="trophy-emoji" class="size-3.5 md:size-4" />
                Won tips
            </span>
        </div>

        <div class="flex flex-col gap-2 p-2 md:p-3 lg:gap-2.5">
            @foreach ($winnings as $row)
                <x-winning-card :row="$row" wire:key="win-{{ $loop->index }}" />
            @endforeach
        </div>

        <div class="flex justify-center border-t border-[#eee] px-4 py-3">
            <a href="/winnings" class="btn-fx inline-flex items-center gap-2 rounded-[10px] bg-[#ff6900] px-5 py-2 text-[14px] font-bold text-[#1a1a1a]">
                View all
                <x-icon name="chevron-right" class="size-5" />
            </a>
        </div>
    </section>
    @endif
</div>
