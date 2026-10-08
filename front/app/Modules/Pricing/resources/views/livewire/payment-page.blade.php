<div>
    <x-page.title :title="$planName" subtitle="Pay with any method below, then tap “I Have Sent the Money” so we can activate your plan." back="/pricing" />

    <x-page.panel>
        <div class="flex w-full flex-col gap-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                <div class="flex items-center justify-between gap-3 rounded-[16px] bg-white p-3 sm:p-4">
                    <div>
                        <p class="text-[12px] font-medium uppercase text-[#767676]">Your plan</p>
                        <p class="text-[17px] font-bold sm:text-[19px]">{{ $planName }} · {{ $duration }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[12px] font-medium uppercase text-[#767676]">Amount</p>
                        <p class="text-[22px] font-bold text-[#cc5400] sm:text-[26px]">{{ $price['label'] ?? '—' }}</p>
                    </div>
                </div>

                <div class="flex flex-col justify-center gap-1.5 rounded-[16px] bg-white p-3 sm:p-4">
                    <p class="text-[13px] font-semibold">Methods available in</p>
                    <label class="relative flex w-full items-center">
                        <span class="sr-only">Country</span>
                        <img src="{{ $flag }}" alt="" class="pointer-events-none absolute left-3 size-5 rounded-sm object-cover">
                        <select wire:model.live="country" class="h-10 w-full appearance-none rounded-[10px] border border-[#d9d9d9] bg-[#f5f5f5] pl-11 pr-10 text-[14px] font-medium outline-none focus:border-[#ff6900] focus:ring-2 focus:ring-[#ff6900]/30">
                            @foreach ($countries as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-icon name="dropdown-down" class="pointer-events-none absolute right-3 size-5 opacity-70" />
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2" wire:loading.class="opacity-60" wire:target="country">
                @forelse ($methods as $method)
                    <article wire:key="method-{{ $method['id'] }}" class="flex flex-col gap-3 rounded-[16px] border border-[#e6e6e6] bg-white p-3 transition hover:border-[#ff6900] hover:shadow-[0_14px_30px_-20px_rgba(255,105,0,0.8)] sm:p-4" x-data="{ copied: false }">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5">
                                @if ($method['image'])
                                    <img src="{{ $method['image'] }}" alt="" class="size-9 rounded-[8px] object-contain">
                                @endif
                                <h2 class="text-[16px] font-bold">{{ $method['name'] }}</h2>
                            </div>
                            <span class="text-[15px] font-bold text-[#cc5400]">{{ $price['label'] ?? '' }}</span>
                        </div>

                        @if ($method['details'] !== '')
                            <div class="relative rounded-[12px] bg-[#f7f7f7] px-3.5 py-3">
                                <div class="home-content pr-16 text-[14px] [&_p]:my-0.5 [&_p]:text-[14px] [&_strong]:text-[#1e1e1e]" x-ref="details">{!! $method['details'] !!}</div>
                                <button type="button" class="btn-fx absolute right-2.5 top-2.5 rounded-[8px] bg-[#1a1a1a] px-3 py-1.5 text-[12px] font-semibold text-white hover:bg-[#ff6900]" @click="navigator.clipboard.writeText($refs.details.innerText.trim()); copied = true; setTimeout(() => copied = false, 1500)">
                                    <span x-text="copied ? 'Copied' : 'Copy'">Copy</span>
                                </button>
                            </div>
                        @endif

                        <p class="mt-auto flex items-start gap-2 border-t border-[#efefef] pt-3 text-[12px] leading-5 text-[#5a5a5a] sm:text-[13px]">
                            <x-icon name="info-circle" class="mt-0.5 size-4" />
                            <span>{{ $method['proof'] }}</span>
                        </p>
                    </article>
                @empty
                    <div class="rounded-[16px] bg-white p-6 text-center md:col-span-2">
                        <p class="text-[15px] font-semibold">No payment methods are set up for {{ $countryLabel }} yet.</p>
                        <p class="mt-1 text-[13px] text-[#5a5a5a]">Contact us on WhatsApp or Telegram and we&rsquo;ll help you pay.</p>
                    </div>
                @endforelse
            </div>

            <div class="flex flex-col items-center gap-2 rounded-[16px] bg-white p-3 text-center sm:flex-row sm:justify-between sm:p-4 sm:text-left">
                <p class="text-[13px] text-[#5a5a5a] sm:text-[14px]">After paying, send us your proof of payment. Your plan is activated once we confirm it.</p>
                <div class="flex w-full shrink-0 gap-2 sm:w-auto">
                    @if ($plan->selar_payment_link)
                        <a href="{{ $plan->selar_payment_link }}" target="_blank" rel="noopener" class="btn-fx flex h-11 flex-1 items-center justify-center rounded-[12px] bg-[#1a1a1a] px-5 text-[14px] font-bold text-white sm:flex-none">Pay online</a>
                    @endif
                    <a href="{{ $confirmUrl }}" target="_blank" rel="noopener" class="btn-fx flex h-11 flex-1 items-center justify-center gap-2 rounded-[12px] bg-gradient-to-b from-[#45c655] to-[#216029] px-5 text-[14px] font-bold text-white sm:flex-none">
                        <x-icon name="whatsapp-logo" class="size-5" />
                        I Have Sent the Money
                    </a>
                </div>
            </div>
        </div>
    </x-page.panel>
</div>
