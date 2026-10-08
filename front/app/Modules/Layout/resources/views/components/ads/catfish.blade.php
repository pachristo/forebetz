{{-- Sticky strip ad: header_sticky(_d) / footer_sticky(_d), with code_ variants as fallback. --}}
@props(['position' => 'footer'])

@php
    $base = $position === 'header' ? 'header_sticky' : 'footer_sticky';
    $desktop = \App\Support\Ads::first($base.'_d', 'code_'.$base.'_d');
    $mobile = \App\Support\Ads::first($base, 'code_'.$base);
    $dismissKey = 'ad-catfish-'.$position.'-'.($desktop['id'] ?? 0).'-'.($mobile['id'] ?? 0);
@endphp

@if ($desktop || $mobile)
    <div
        x-data="{ open: sessionStorage.getItem(@js($dismissKey)) !== '1', close() { this.open = false; sessionStorage.setItem(@js($dismissKey), '1') } }"
        x-show="open"
        x-cloak
        @class([
            'z-[60] flex w-full justify-center px-2',
            'sticky top-0 bg-black/85 py-1.5 backdrop-blur' => $position === 'header',
            'pointer-events-none fixed inset-x-0 bottom-[96px] lg:bottom-2' => $position === 'footer',
            'max-lg:hidden' => ! $mobile,
            'lg:hidden' => ! $desktop,
        ])
        data-ad-slot="{{ $base }}"
        role="complementary"
        aria-label="Advertisement"
    >
        <div class="pointer-events-auto relative inline-flex max-w-full items-center justify-center">
            @if ($desktop)
                <x-layout::ads.creative :ad="$desktop" class="hidden max-h-[90px] lg:block" img-class="h-auto max-h-[90px] w-auto max-w-full rounded-[6px] object-contain" />
            @endif
            @if ($mobile)
                <x-layout::ads.creative :ad="$mobile" class="max-h-[90px] lg:hidden" img-class="h-auto max-h-[90px] w-auto max-w-full rounded-[6px] object-contain" />
            @endif

            <button
                type="button"
                @click="close()"
                @class([
                    'absolute -right-1.5 z-10 flex size-6 items-center justify-center rounded-full bg-[#ec221f] text-[15px] font-bold leading-none text-white shadow ring-2 ring-white/70 hover:bg-[#c81a17]',
                    '-bottom-1.5' => $position === 'header',
                    '-top-1.5' => $position === 'footer',
                ])
                aria-label="Close advertisement"
            >&times;</button>
        </div>
    </div>
@endif
