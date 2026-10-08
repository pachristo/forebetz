{{-- Pop-up advert (pop_up slot); shown once per browser session after a short delay. --}}
@php
    $ad = \App\Support\Ads::first('pop_up');
    $dismissKey = 'ad-popup-'.($ad['id'] ?? 0);
@endphp

@if ($ad)
    <div
        x-data="{ open: false, close() { this.open = false; sessionStorage.setItem(@js($dismissKey), '1') } }"
        x-init="if (sessionStorage.getItem(@js($dismissKey)) !== '1') setTimeout(() => open = true, 1500)"
        x-show="open"
        x-cloak
        x-transition.opacity.duration.300ms
        @keydown.escape.window="close()"
        class="fixed inset-0 z-[100] flex items-center justify-center px-4"
        role="dialog"
        aria-modal="true"
        aria-label="Advertisement"
        data-ad-slot="pop_up"
    >
        <div class="absolute inset-0 bg-black/75" @click="close()" aria-hidden="true"></div>

        <div class="relative z-10 max-w-[min(92vw,420px)]">
            <button
                type="button"
                @click="close()"
                class="absolute -right-3 -top-3 z-20 flex size-8 items-center justify-center rounded-full bg-white text-[20px] font-bold leading-none text-[#1e1e1e] shadow-lg hover:bg-[#ff6900] hover:text-white"
                aria-label="Close advertisement"
            >&times;</button>

            <x-layout::ads.creative :ad="$ad" img-class="mx-auto h-auto max-h-[80vh] w-full rounded-[14px] object-contain shadow-2xl" />
        </div>
    </div>
@endif
