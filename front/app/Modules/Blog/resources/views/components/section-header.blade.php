@props(['title', 'href' => null, 'label' => 'More', 'tag' => 'h2'])

<div class="flex w-full items-center gap-3 border-b border-[#ff6900] pb-2.5">
    <{{ $tag }} class="min-w-0 flex-1 text-[16px] font-semibold tracking-[0.2px] text-[#303030] lg:text-[20px]">{{ $title }}</{{ $tag }}>
    @if ($href)
        <a href="{{ $href }}" class="link-fx inline-flex shrink-0 items-center text-[13px] tracking-[0.2px] text-[#ff6900] lg:text-[14px]">
            {{ $label }}
            <x-icon name="chevron-right-gold" class="size-5" />
        </a>
    @endif
</div>
