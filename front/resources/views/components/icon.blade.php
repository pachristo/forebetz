@props(['name', 'alt' => ''])

<span {{ $attributes->class('shrink-0 overflow-hidden') }}>
    <img src="{{ config('site.asset_path') }}/icons/{{ $name }}.svg" alt="{{ $alt }}" class="h-full w-full object-contain">
</span>
