@props(['title', 'footer' => null])

<main class="w-full px-2.5 pb-6 sm:px-8 lg:px-[100px]">
    <div class="mx-auto grid min-h-[calc(100svh-176px)] w-full max-w-site overflow-hidden rounded-[22px] border border-white/10 bg-black/45 backdrop-blur-[7.45px] lg:min-h-[calc(100svh-110px)] lg:grid-cols-2">
        <aside class="relative hidden min-h-[520px] overflow-hidden lg:block">
            <img
                src="{{ $asset }}/images/auth-hero.jpg"
                alt="{{ config('site.name') }} — surest football prediction site with daily expert tips."
                class="absolute inset-0 h-full w-full object-cover object-[70%_center]"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent"></div>

            <div class="relative flex h-full flex-col justify-end gap-4 p-8 xl:p-10">
                <span class="w-fit rounded-full border border-[#ff6900]/60 bg-[#ff6900]/15 px-3 py-1 text-[12px] font-semibold uppercase tracking-[1px] text-[#ff8a3d]">
                    {{ config('site.name') }}
                </span>
                <h2 class="max-w-[420px] text-[34px] font-bold leading-[1.1] text-white xl:text-[40px]">
                    Surest Prediction <span class="text-[#ff6900]">Site in the World</span>
                </h2>
                <ul class="flex flex-col gap-2.5">
                    @foreach (['Free expert football tips every day', 'VIP plans with higher-confidence picks', 'Track results and your subscriptions'] as $point)
                        <li class="flex items-center gap-2.5 text-[15px] text-white/85">
                            <x-icon name="check-fill" class="size-5 shrink-0" />
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <div class="relative flex items-center justify-center px-4 py-6 sm:px-8 sm:py-8 lg:px-12 lg:py-10">
            <img src="{{ $asset }}/images/auth-hero.jpg" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full object-cover object-top opacity-35 lg:hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/70 to-black lg:hidden"></div>

            <div class="relative flex w-full max-w-[460px] flex-col gap-4">
                <h1 class="text-[24px] font-bold leading-tight text-[#f5f5f5] lg:text-[28px]">{{ $title }}</h1>

                {{ $slot }}

                @if ($footer)
                    <p class="text-center text-[14px] text-[#f5f5f5] sm:text-[15px]">{{ $footer }}</p>
                @endif
            </div>
        </div>
    </div>
</main>
