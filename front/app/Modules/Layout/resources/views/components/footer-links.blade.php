@props(['title', 'links' => [], 'columns' => 1])

<div class="flex flex-col gap-3">
    <h4 class="relative pb-2 text-[15px] font-bold capitalize text-white sm:text-[16px]">
        {{ $title }}
        <span class="absolute bottom-0 left-0 h-[3px] w-8 rounded-full bg-[#ff6900]"></span>
    </h4>
    <ul @class(['grid gap-x-6 gap-y-1.5 text-[14px] text-white/70', 'grid-cols-2' => $columns > 1])>
        @foreach ($links as $link)
            <li>
                <a href="{{ $link['href'] }}" class="group inline-flex items-center gap-1.5 leading-5 hover:translate-x-1 hover:text-[#ff6900]">
                    <span class="size-1 rounded-full bg-[#ff6900]/70 transition group-hover:scale-150 group-hover:bg-[#ff6900]"></span>
                    {{ $link['label'] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
