@props(['name', 'side' => 'right', 'label' => '', 'panelClass' => ''])

@php
    $hidden = $side === 'right' ? 'translate-x-full' : '-translate-x-full';
@endphp

<div
    id="{{ $attributes->get('id', $name) }}"
    x-data
    x-cloak
    x-show="$store.overlay.is('{{ $name }}')"
    @keydown.escape.window="$store.overlay.close('{{ $name }}')"
    class="fixed inset-0 z-[100] lg:hidden"
    role="dialog"
    aria-modal="true"
    aria-label="{{ $label }}"
>
    <button
        type="button"
        class="absolute inset-0 bg-[#0a0a0a]/70 backdrop-blur-[6px]"
        x-show="$store.overlay.is('{{ $name }}')"
        x-transition.opacity.duration.300ms
        @click="$store.overlay.close('{{ $name }}')"
        aria-label="Close {{ strtolower($label) }}"
    ></button>

    <aside
        x-show="$store.overlay.is('{{ $name }}')"
        x-transition:enter="transition-transform duration-300 ease-out"
        x-transition:enter-start="{{ $hidden }}"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition-transform duration-300 ease-in"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="{{ $hidden }}"
        @click="if ($event.target.closest('a')) $store.overlay.close('{{ $name }}')"
        @class([
            'absolute inset-y-0 flex flex-col bg-[#0a0a0a]',
            'right-0 shadow-[-8px_0_40px_rgba(0,0,0,0.45)]' => $side === 'right',
            'left-0 shadow-[8px_0_40px_rgba(0,0,0,0.45)]' => $side !== 'right',
            $panelClass,
        ])
    >
        {{ $slot }}
    </aside>
</div>
