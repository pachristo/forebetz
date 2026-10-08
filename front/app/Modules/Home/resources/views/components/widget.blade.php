@props(['title'])

<div {{ $attributes->class('overflow-hidden rounded-[14px] bg-white text-[#1e1e1e]') }}>
    <div class="border-b border-[#eee] px-4 py-3 text-center">
        <h3 class="text-[16px] font-bold text-[#1e1e1e]">{{ $title }}</h3>
    </div>
    {{ $slot }}
</div>
