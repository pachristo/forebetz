{{-- In-page slot: {place}_i / {place}_c on desktop, m{place}_i / m{place}_c on mobile. --}}
@props(['place'])

@php
    $desktop = \App\Support\Ads::for($place.'_i')->concat(\App\Support\Ads::for($place.'_c'));
    $mobile = \App\Support\Ads::for('m'.$place.'_i')->concat(\App\Support\Ads::for('m'.$place.'_c'));
@endphp

@if ($desktop->isNotEmpty() || $mobile->isNotEmpty())
    <div {{ $attributes->class([
        'w-full',
        'max-lg:hidden' => $mobile->isEmpty(),
        'lg:hidden' => $desktop->isEmpty(),
    ]) }} data-ad-slot="{{ $place }}">
        @foreach (['hidden lg:flex' => $desktop, 'flex lg:hidden' => $mobile] as $visibility => $ads)
            @if ($ads->isNotEmpty())
                <div class="{{ $visibility }} flex-col items-center gap-3">
                    @foreach ($ads as $ad)
                        <x-layout::ads.creative :ad="$ad" img-class="mx-auto h-auto max-h-[250px] w-auto max-w-full rounded-[10px] object-contain lg:max-h-[120px]" />
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
@endif
