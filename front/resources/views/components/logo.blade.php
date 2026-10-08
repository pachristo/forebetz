<picture {{ $attributes->class('block shrink-0') }}>
    <source srcset="{{ config('site.asset_path') }}/images/brand/logo.webp" type="image/webp">
    <img src="{{ config('site.asset_path') }}/images/brand/logo.png" alt="{{ config('site.name') }}" width="181" height="32" class="block h-[32px] w-auto max-w-none">
</picture>
