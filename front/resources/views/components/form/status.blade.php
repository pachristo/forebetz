@props(['type' => 'success'])

<div {{ $attributes->class([
    'w-full rounded-[10px] border px-3 py-2 text-[13px] font-medium',
    'border-[#14ae5c]/40 bg-[#14ae5c]/15 text-[#7ee2a8]' => $type === 'success',
    'border-[#ec221f]/40 bg-[#ec221f]/15 text-[#ff8f8a]' => $type === 'error',
]) }} role="status">{{ $slot }}</div>
