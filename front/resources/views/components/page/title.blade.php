@props(['title', 'subtitle' => null, 'back' => null])

<section class="w-full px-2.5 pb-3 sm:px-8 lg:px-[100px]">
    <div class="mx-auto flex w-full max-w-site flex-col gap-1 py-2 text-white">
        @if ($back)
            <a href="{{ $back }}" class="link-fx mb-1 inline-flex w-fit items-center gap-1.5 text-[13px] text-white/70 hover:text-[#ff6900]">
                <x-icon name="chevron-right" class="size-4 rotate-180 invert" /> Back
            </a>
        @endif
        <h1 class="text-[24px] font-semibold leading-tight sm:text-[30px] lg:text-[34px]">{{ $title }}</h1>
        @if ($subtitle)
            <p class="max-w-[820px] text-[13px] leading-normal text-white/80 sm:text-[15px]">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
