@props(['title' => null])

<section {{ $attributes->class('rounded-[16px] bg-white p-3 sm:rounded-[20px] sm:p-4') }}>
    @if ($title)
        <h2 class="mb-2.5 text-[16px] font-semibold text-[#1e1e1e] sm:text-[18px]">{{ $title }}</h2>
    @endif
    {{ $slot }}
</section>
