<section class="relative z-10 w-full px-2.5 pb-3 sm:px-8 lg:px-[100px]">
    <div class="mx-auto flex w-full max-w-site flex-col gap-4 py-3">
        <div class="flex w-full max-w-[836px] flex-col gap-3">
            <div class="flex flex-col gap-1 text-white">
                @if ($heroHighlight !== '' || $heroHeading !== '')
                    <h1 class="text-[24px] font-semibold leading-tight sm:text-[32px] lg:text-[36px]">
                        @if ($heroHighlight !== '')<span class="text-[#ff6900]">{{ $heroHighlight }} </span>@endif{{ $heroHeading }}
                    </h1>
                @endif
                @if ($heroIntro !== '')
                    <p class="text-[13px] font-normal leading-normal text-white/85 sm:text-[15px]">{{ $heroIntro }}</p>
                @endif
            </div>
            <div class="grid w-full grid-cols-2 gap-2.5 sm:max-w-[500px]">
                <a href="{{ $whatsappUrl }}" class="btn-fx inline-flex h-12 items-center justify-center gap-2 rounded-[12px] bg-gradient-to-b from-[#45c655] to-[#216029] px-4 text-[15px] font-semibold text-white sm:text-[16px]">
                    <x-icon name="whatsapp-logo" class="size-5" />
                    Join Whatsapp
                </a>
                <a href="{{ $bankerUrl }}" class="btn-fx inline-flex h-12 items-center justify-center gap-2 rounded-[12px] bg-gradient-to-b from-[#cb5140] to-[#8b392d] px-4 text-[15px] font-semibold text-white sm:text-[16px]">
                    <x-icon name="fire" class="size-5" />
                    Banker Tips
                </a>
            </div>
        </div>
    </div>
</section>
