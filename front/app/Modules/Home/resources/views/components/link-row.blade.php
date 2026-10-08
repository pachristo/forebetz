@props(['href', 'image', 'label', 'imageClass' => ''])

<li>
    <a href="{{ $href }}" class="group flex items-center gap-3 px-4 py-2.5 text-[14px] text-[#1e1e1e] hover:bg-[#fff0e6] hover:pl-5 hover:text-[#ff6900]">
        <span class="size-5 shrink-0 overflow-hidden {{ $imageClass }}">
            <img src="{{ $image }}" alt="" loading="lazy" class="h-full w-full object-contain">
        </span>
        <span class="flex-1 truncate">{{ $label }}</span>
        <x-icon name="chevron-right" class="size-4 opacity-50 transition group-hover:translate-x-1 group-hover:opacity-100" />
    </a>
</li>
