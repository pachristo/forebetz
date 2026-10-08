@props(['label', 'icon' => null, 'align' => 'left', 'panelClass' => 'min-w-[180px]', 'triggerClass' => '', 'mega' => false])

<div @class(['relative' => ! $mega]) x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button
        type="button"
        @class([
            'nav-link' => $triggerClass === '',
            $triggerClass,
        ])
        @click="open = ! open"
        :aria-expanded="open"
        aria-haspopup="true"
    >
        <span class="flex items-center gap-1.5">
            @if ($icon)
                <x-icon :name="$icon" @class(['hidden size-5 xl:block' => $triggerClass === '', 'size-6' => $triggerClass !== '']) />
            @endif
            {{ $label }}
        </span>
        <x-icon name="arrow-down" class="size-5 opacity-70 transition-transform duration-200" x-bind:class="open && 'rotate-180'" />
    </button>
    <div
        x-cloak
        x-show="open"
        x-transition.origin.top
        @class([
            'absolute z-50 overflow-hidden bg-[#141414] shadow-xl backdrop-blur-[36px]',
            $mega ? 'inset-x-0 top-[calc(100%+8px)] rounded-[18px] border border-white/10 p-5' : 'top-full mt-2 rounded-[10px] p-2.5',
            $mega ? '' : ($align === 'right' ? 'right-0' : 'left-0'),
            $panelClass,
        ])
        role="menu"
    >
        {{ $slot }}
    </div>
</div>
