<div>
@if ($packages)
<section id="subscribe" class="relative mt-7 overflow-hidden rounded-[16px] sm:rounded-[20px]">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute inset-0 rounded-[20px] bg-[#1a1a1a] sm:rounded-[30px]"></div>
        <img src="{{ $asset }}/images/packages-bg.png" alt="" loading="lazy" class="absolute inset-0 size-full rounded-[20px] object-cover opacity-20 sm:rounded-[30px]">
    </div>

    <div class="relative z-10 overflow-hidden rounded-[16px] p-3 sm:rounded-[20px]">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute inset-0 rounded-[20px] bg-[#0a0a0a] sm:rounded-[30px]"></div>
            <img src="{{ $asset }}/images/invest-bg.png" alt="" loading="lazy" class="absolute inset-0 size-full rounded-[20px] object-cover opacity-20 sm:rounded-[30px]">
        </div>

        <div class="relative z-10 flex flex-col gap-4">
            <h2 class="text-center text-[20px] font-bold text-white sm:text-[24px]">Packages</h2>

            <div class="grid gap-3 md:grid-cols-2">
                @foreach ($packages as $package)
                    <div wire:key="package-{{ $loop->index }}" class="flex flex-col rounded-[16px] border-2 border-[#ff312d] transition duration-300 hover:-translate-y-1 hover:border-[#ff6900] hover:shadow-[0_18px_40px_-18px_rgba(255,105,0,0.7)] bg-gradient-to-b from-[#1a1a1a] to-[#262626] p-1 backdrop-blur-[7.45px]">
                        <div class="flex min-h-0 flex-1 flex-col gap-3 p-4 sm:p-5">
                            <div class="flex flex-col gap-1.5">
                                <span class="inline-flex w-fit rounded-[5px] bg-[#ff6900] px-2 py-0.5 text-[12px] font-bold text-black sm:text-[13px]">{{ $package['badge'] }}</span>
                                <h3 class="text-[19px] font-bold leading-tight text-[#f0f0f0] sm:text-[22px]">{{ $package['title'] }}</h3>
                            </div>
                            <ul class="flex flex-col gap-2 border-t border-[#b7bcc4]/50 pt-2.5">
                                @foreach ([...$package['features'], ...$perks] as $feature)
                                    <li class="flex items-center gap-2 text-[13px] text-[#e9e9e9]">
                                        <x-icon name="check-fill" class="size-4 sm:size-[18px]" />
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <a
                            href="{{ $package['url'] }}"
                            class="btn-fx m-1 flex h-11 items-center justify-center gap-2 rounded-[12px] px-6 text-[14px] font-bold uppercase text-black"
                            style="background-image: linear-gradient(109deg, #ff7a1a 13%, #cc5400 101%);"
                        >
                            subscribe
                            <x-icon name="arrow-right" class="size-5" />
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
</div>
