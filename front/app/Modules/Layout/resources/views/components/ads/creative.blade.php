@props(['ad', 'imgClass' => 'h-auto w-auto max-w-full object-contain'])

@if ($ad['image'])
    <a href="{{ $ad['href'] }}" target="_blank" rel="nofollow sponsored noopener noreferrer" {{ $attributes->class('block max-w-full') }}>
        <img src="{{ $ad['image'] }}" alt="{{ $ad['label'] }}" class="{{ $imgClass }}" loading="lazy" decoding="async">
    </a>
@else
    <div {{ $attributes->class('max-w-full overflow-x-auto [&_iframe]:max-w-full [&_img]:mx-auto [&_img]:max-w-full') }}>{!! $ad['code'] !!}</div>
@endif
