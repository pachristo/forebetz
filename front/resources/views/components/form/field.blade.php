@props(['label', 'name', 'type' => 'text', 'placeholder' => '', 'model' => null, 'options' => null, 'tone' => 'dark'])

@php
    $model ??= $name;
    $input = 'h-10 w-full rounded-[10px] border bg-white px-3 text-[14px] text-[#1e1e1e] outline-none transition placeholder:text-[#9a9a9a] focus:border-[#ff6900] focus:ring-2 focus:ring-[#ff6900]/30';
    $border = $errors->has($model) ? 'border-[#ec221f]' : 'border-[#d9d9d9]';
@endphp

<label {{ $attributes->class('flex min-w-0 flex-col gap-1') }}>
    <span @class([
        'text-[12px] font-semibold tracking-[0.2px] sm:text-[13px]',
        'text-[#f5f5f5]' => $tone === 'dark',
        'text-[#1e1e1e]' => $tone === 'light',
    ])>{{ $label }}</span>

    @if ($options !== null)
        <span class="relative">
            <select wire:model="{{ $model }}" name="{{ $name }}" class="{{ $input }} {{ $border }} appearance-none pr-9">
                <option value="">{{ $placeholder ?: 'Select' }}</option>
                @foreach ($options as $value => $text)
                    <option value="{{ $value }}">{{ $text }}</option>
                @endforeach
            </select>
            <x-icon name="dashboard/sort-down" class="pointer-events-none absolute right-3 top-1/2 size-[18px] -translate-y-1/2" />
        </span>
    @elseif ($type === 'password')
        <span class="relative" x-data="{ show: false }">
            <input :type="show ? 'text' : 'password'" wire:model="{{ $model }}" name="{{ $name }}" placeholder="{{ $placeholder ?: '••••••••' }}" autocomplete="{{ str_contains($name, 'current') ? 'current-password' : 'new-password' }}" class="{{ $input }} {{ $border }} pr-10">
            <button type="button" class="absolute right-3 top-1/2 size-[18px] -translate-y-1/2 opacity-70 transition hover:opacity-100" @click="show = ! show" aria-label="Toggle password visibility">
                <x-icon name="dashboard/eye-off" class="size-[18px]" />
            </button>
        </span>
    @else
        <input type="{{ $type }}" wire:model="{{ $model }}" name="{{ $name }}" placeholder="{{ $placeholder }}" class="{{ $input }} {{ $border }}">
    @endif

    @error($model)
        <span class="text-[12px] font-medium text-[#ff5a52]">{{ $message }}</span>
    @enderror
</label>
