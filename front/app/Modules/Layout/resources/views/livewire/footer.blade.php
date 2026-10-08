<footer class="relative z-10 w-full px-2.5 pb-24 sm:px-8 sm:pb-5 lg:px-[100px]">
    <div class="relative mx-auto w-full max-w-site overflow-hidden rounded-[16px] border border-white/10 bg-[#141414] sm:rounded-[24px]">
        <div class="pointer-events-none absolute -right-32 -top-32 size-80 rounded-full bg-[#ff6900]/20 blur-[120px]"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-24 size-80 rounded-full bg-[#ef1410]/10 blur-[120px]"></div>

        <div class="relative grid gap-8 px-5 py-8 sm:px-8 lg:grid-cols-12 lg:gap-8 lg:py-10">
            <div class="flex flex-col gap-3 lg:col-span-4">
                <x-logo />
                <p class="max-w-[420px] text-[14px] leading-relaxed text-white/70">
                    {{ config('site.name') }} is your best surest prediction site for 100 football predictions, sure six straight win and daily expert tips.
                </p>
                <div class="flex items-center gap-3">
                    <a href="{{ $socials['x'] }}" aria-label="X" class="grid size-9 place-items-center rounded-full border border-white/15 bg-white/5 hover:-translate-y-1 hover:border-[#ff6900] hover:bg-[#ff6900] hover:shadow-[0_8px_18px_-6px_rgba(255,105,0,0.8)]"><x-icon name="x-twitter" class="size-5" /></a>
                    <a href="{{ $socials['telegram'] }}" aria-label="Telegram" class="grid size-9 place-items-center rounded-full border border-white/15 bg-white/5 hover:-translate-y-1 hover:border-[#ff6900] hover:bg-[#ff6900] hover:shadow-[0_8px_18px_-6px_rgba(255,105,0,0.8)]"><x-icon name="telegram-social" class="size-5" /></a>
                    <a href="{{ $socials['facebook'] }}" aria-label="Facebook" class="grid size-9 place-items-center rounded-full border border-white/15 bg-white/5 hover:-translate-y-1 hover:border-[#ff6900] hover:bg-[#ff6900] hover:shadow-[0_8px_18px_-6px_rgba(255,105,0,0.8)]"><x-icon name="facebook" class="size-5" /></a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:col-span-8 lg:grid-cols-4">
                <x-layout::footer-links title="Quick Links" :links="$quickLinks" />

                @if ($dailyLinks)
                    <x-layout::footer-links title="Daily Predictions" :links="$dailyLinks" />
                @endif

                <x-layout::footer-links title="Legal Links" :links="$legalLinks" />

                <div class="flex flex-col gap-4">
                    <h4 class="relative pb-2 text-[15px] font-bold capitalize text-white sm:text-[16px]">
                        Reach Us
                        <span class="absolute bottom-0 left-0 h-[3px] w-8 rounded-full bg-[#ff6900]"></span>
                    </h4>
                    <ul class="flex flex-col gap-2 text-[14px] text-white/70">
                        <li class="flex items-start gap-2.5">
                            <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-[#25d366]/15"><x-icon name="whatsapp-logo" class="size-4" /></span>
                            <span class="leading-tight">WhatsApp Only<br><span class="font-semibold text-white">{{ $contact['whatsapp'] }}</span></span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-[#ff6900]/15"><x-icon name="mail" class="size-4" /></span>
                            <span class="min-w-0 leading-tight">Email Us<br><span class="break-all font-semibold text-white">{{ $contact['email'] }}</span></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @if ($textLinks)
            @php($foldAt = 12)
            <div class="relative mx-5 sm:mx-8" x-data="{ open: false }">
                <div class="flex w-full flex-wrap items-center rounded-2xl bg-white/[0.07] px-4 py-3 lg:rounded-3xl lg:px-6 lg:py-5">
                    @foreach ($textLinks as $link)
                        <a
                            href="{{ $link['href'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="block px-1 text-[10px] font-light leading-4 text-[#BCD0EE] hover:text-gray-400 hover:underline sm:text-[11px]"
                            @if ($loop->index >= $foldAt) x-cloak x-show="open" @endif
                        >{{ $link['label'] }}</a>
                        @unless ($loop->last)
                            <span class="text-[10px] text-[#BCD0EE]" @if ($loop->index >= $foldAt - 1 && count($textLinks) > $foldAt) x-cloak x-show="open" @endif>|</span>
                        @endunless
                    @endforeach

                    @if (count($textLinks) > $foldAt)
                        <span class="text-[10px] text-[#BCD0EE]">|</span>
                        <button type="button" @click="open = ! open" class="ml-1 inline-flex items-center gap-1 rounded-full bg-[#B0C1BF] px-2 py-0.5 text-[10px] leading-none text-[#222222] hover:bg-white" :aria-expanded="open">
                            <x-icon name="arrow-down" class="size-2.5 invert transition-transform" x-bind:class="open && 'rotate-180'" />
                            <span x-text="open ? 'Fold' : 'Unfold'">Unfold</span>
                        </button>
                    @endif
                </div>
            </div>
        @else
            <div class="relative mx-5 rounded-[12px] border border-white/10 bg-white/[0.03] p-3 sm:mx-8">
                <p class="mb-2 text-[11px] font-semibold uppercase tracking-[0.12em] text-white/40">Popular searches</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($keywords as $keyword)
                        <span class="rounded-full bg-white/[0.06] px-2.5 py-0.5 text-[12px] capitalize text-white/60">{{ $keyword }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="relative mt-6 flex flex-col items-center gap-2 border-t border-white/10 px-5 py-4 text-center text-[13px] text-white/55 sm:flex-row sm:justify-between sm:px-10 sm:px-8 sm:text-left sm:text-[14px]">
            <p>Copyright © {{ now()->year }} {{ config('site.name') }}. All Rights Reserved.</p>
            <p class="flex items-center gap-2">
                <span class="grid size-6 place-items-center rounded-full border border-[#ef1410] text-[10px] font-bold text-[#ef1410]">18+</span>
                Please gamble responsibly.
            </p>
        </div>
    </div>
</footer>
