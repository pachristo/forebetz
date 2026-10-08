<div>
    <x-page.title title="VIP Packages" subtitle="Premium daily predictions from our analysts. Pick a package, pay in your local currency and get access as soon as payment is confirmed." />

    <x-page.panel>
        <div class="flex w-full flex-col gap-4">
            <div class="flex flex-col gap-2 rounded-[16px] bg-white p-3 sm:flex-row sm:items-center sm:justify-between sm:p-4">
                <div>
                    <p class="text-[15px] font-semibold sm:text-[17px]">Choose your country</p>
                    <p class="text-[13px] text-[#5a5a5a]">Prices and payment methods update for the country you select.</p>
                </div>
                <label class="relative flex w-full items-center sm:max-w-[320px]">
                    <span class="sr-only">Country</span>
                    <img src="{{ $flag }}" alt="" class="pointer-events-none absolute left-3 size-5 rounded-sm object-cover">
                    <select wire:model.live="country" class="h-11 w-full appearance-none rounded-[12px] border border-[#d9d9d9] bg-[#f5f5f5] pl-11 pr-10 text-[14px] font-medium outline-none transition focus:border-[#ff6900] focus:ring-2 focus:ring-[#ff6900]/30">
                        @foreach ($countries as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-icon name="dropdown-down" class="pointer-events-none absolute right-3 size-5 opacity-70" />
                </label>
            </div>

            <section class="relative overflow-hidden rounded-[20px] bg-[#0a0a0a] p-3 sm:rounded-[24px] sm:p-5">
                <img src="{{ $asset }}/images/invest-bg.png" alt="" class="pointer-events-none absolute inset-0 size-full object-cover opacity-20" aria-hidden="true">

                <div class="relative z-10 flex flex-col gap-4" wire:loading.class="opacity-60" wire:target="country">
                    <h2 class="text-center text-[22px] font-bold text-white sm:text-[26px]">Go Premium</h2>

                    @if ($categories)
                        <div @class(['grid grid-cols-1 gap-4', 'lg:grid-cols-2' => count($categories) > 1])>
                            @foreach ($categories as $category)
                                @php($selected = $highlight === $category['id'])
                                <article
                                    id="plan-category-{{ $category['id'] }}"
                                    wire:key="category-{{ $category['id'] }}"
                                    @if ($selected) x-data x-init="$el.scrollIntoView({ block: 'center' })" @endif
                                    @class([
                                        'relative flex flex-col overflow-hidden rounded-[22px] border-2 bg-gradient-to-b from-[#1c1c1c] to-[#121212] transition duration-300',
                                        'border-[#ff6900] shadow-[0_20px_50px_-20px_rgba(255,105,0,0.8)]' => $selected,
                                        'border-white/10 hover:border-[#ff6900]/60' => ! $selected,
                                    ])
                                >
                                    <div class="pointer-events-none absolute -right-20 -top-20 size-56 rounded-full bg-[#ff6900]/20 blur-[80px]"></div>

                                    <div class="relative flex flex-col gap-4 p-5 sm:p-6">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <span class="grid size-12 shrink-0 place-items-center rounded-[14px] bg-[#ff6900]/15"><x-icon name="crown" class="size-7" /></span>
                                                <div>
                                                    <h3 class="text-[20px] font-bold leading-tight text-white sm:text-[24px]">{{ $category['title'] }}</h3>
                                                    @if ($category['from'])
                                                        <p class="text-[13px] text-white/60">From <span class="font-semibold text-[#ff8a3d]">{{ $category['from'] }}</span></p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if ($category['active'])
                                                <span class="shrink-0 rounded-full bg-[#14ae5c] px-3 py-1 text-[11px] font-bold uppercase text-white">Active</span>
                                            @elseif ($selected)
                                                <span class="shrink-0 rounded-full bg-[#ff6900] px-3 py-1 text-[11px] font-bold uppercase text-black">Selected</span>
                                            @endif
                                        </div>

                                        @if ($category['benefits'])
                                            <ul class="grid gap-2 rounded-[14px] border border-white/10 bg-white/[0.03] p-4 sm:grid-cols-2">
                                                @foreach ($category['benefits'] as $benefit)
                                                    <li class="flex items-start gap-2 text-[14px] leading-snug text-[#e9e9e9]">
                                                        <x-icon name="check-fill" class="mt-0.5 size-4" />
                                                        {{ $benefit }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        <div class="flex flex-col gap-2">
                                            <p class="text-[12px] font-semibold uppercase tracking-[1px] text-white/50">Choose a duration</p>
                                            <div @class(['grid gap-2.5', 'sm:grid-cols-2' => count($category['plans']) > 1])>
                                                @foreach ($category['plans'] as $plan)
                                                    <a
                                                        href="/payment?plan={{ $plan['id'] }}"
                                                        wire:key="plan-{{ $plan['id'] }}"
                                                        class="btn-fx group flex items-center justify-between gap-3 rounded-[14px] px-4 py-3 text-black"
                                                        style="background-image: linear-gradient(109deg, #ff7a1a 13%, #cc5400 101%);"
                                                    >
                                                        <span class="flex flex-col">
                                                            <span class="text-[12px] font-semibold uppercase tracking-[0.5px] text-black/70">{{ $plan['duration'] }}</span>
                                                            <span class="text-[22px] font-bold leading-tight">{{ $plan['price']['label'] }}</span>
                                                        </span>
                                                        <span class="flex items-center gap-1 rounded-full bg-black px-3 py-1.5 text-[12px] font-bold uppercase text-white transition group-hover:gap-2">
                                                            {{ $category['active'] ? 'Renew' : 'Subscribe' }}
                                                            <x-icon name="arrow-right" class="size-4" />
                                                        </span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <p class="py-8 text-center text-[15px] text-white/80">No packages are available right now. Please check back soon.</p>
                    @endif
                </div>
            </section>
        </div>
    </x-page.panel>
</div>
