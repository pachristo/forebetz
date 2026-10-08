@props(['src' => null, 'alt' => '', 'eager' => false])

@if ($src)
    <span {{ $attributes->class('relative block overflow-hidden bg-[#1e1e1e]') }}>
        <img src="{{ $src }}" alt="{{ $alt }}" @unless ($eager) loading="lazy" @endunless class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]">
    </span>
@endif
