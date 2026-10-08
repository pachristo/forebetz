{{-- Grid advert above free picks: g_i (image) / g_c (code), desktop and mobile. --}}
@php
    $ads = \App\Support\Ads::for('g_c')->concat(\App\Support\Ads::for('g_i'));
@endphp

@if ($ads->isNotEmpty())
    <div {{ $attributes->class('grid w-full grid-cols-2 gap-2 lg:grid-cols-4') }} data-ad-slot="g">
        @foreach ($ads as $ad)
            <x-layout::ads.creative :ad="$ad" class="flex justify-center" img-class="h-auto w-full max-w-full rounded-[8px] object-contain" />
        @endforeach
    </div>
@endif
