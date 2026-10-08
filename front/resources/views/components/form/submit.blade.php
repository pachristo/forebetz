@props(['target' => null])

<button
    type="submit"
    wire:loading.attr="disabled"
    @if ($target) wire:target="{{ $target }}" @endif
    {{ $attributes->class('btn-fx flex h-11 w-full items-center justify-center gap-2 rounded-[12px] bg-gradient-to-b from-[#ff8a3d] to-[#e85d00] px-5 text-[15px] font-bold tracking-[0.2px] text-white shadow-[0_8px_20px_-10px_rgba(255,105,0,0.8)] disabled:opacity-60') }}
>
    <span wire:loading @if ($target) wire:target="{{ $target }}" @endif class="size-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
    {{ $slot }}
</button>
