@props(['post', 'variant' => 'grid'])

@php($meta = trim($post['date'].' • '.$post['readTime'], ' •'))

@switch($variant)
    @case('large')
        <a href="{{ $post['url'] }}" class="card-fx group flex w-full flex-col overflow-hidden rounded-[20px] border-[3px] border-white bg-white p-[5px] shadow-[0_1px_7.7px_rgba(0,0,0,0.15)] lg:p-2.5">
            <x-blog::image :src="$post['image']" :alt="$post['title']" eager class="aspect-[16/8] w-full rounded-[12px] lg:aspect-[776/300]" />
            <span class="flex flex-col gap-2 p-[5px] lg:px-[15px] lg:py-3">
                @if ($post['category'])
                    <span class="w-fit rounded-full bg-[#ff6900]/10 px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-[0.5px] text-[#e85d00]">{{ $post['category'] }}</span>
                @endif
                <span class="text-[18px] font-semibold leading-snug text-[#1e1e1e] transition-colors group-hover:text-[#ff6900] lg:text-[24px]">{{ $post['title'] }}</span>
                <span class="line-clamp-2 text-[13px] leading-relaxed text-[#5a5a5a] lg:text-[15px]">{{ $post['excerpt'] }}</span>
                <span class="text-[12px] font-medium text-[#767676] lg:text-[14px]">{{ $meta }}</span>
            </span>
        </a>
        @break

    @case('medium')
        <a href="{{ $post['url'] }}" class="card-fx group flex w-full flex-col overflow-hidden rounded-[14px] border-[3px] border-white bg-white p-[5px] shadow-[0_1px_7.7px_rgba(0,0,0,0.15)] lg:rounded-[20px] lg:p-2.5">
            <x-blog::image :src="$post['image']" :alt="$post['title']" class="aspect-[16/11] w-full rounded-[10px] lg:aspect-[368/190]" />
            <span class="flex flex-col gap-1.5 py-2.5 lg:p-2.5">
                <span class="line-clamp-3 text-[14px] font-semibold leading-snug text-[#1e1e1e] transition-colors group-hover:text-[#ff6900] lg:line-clamp-2 lg:text-[19px]">{{ $post['title'] }}</span>
                <span class="text-[11px] font-medium text-[#767676] lg:text-[14px]">{{ $meta }}</span>
            </span>
        </a>
        @break

    @case('compact')
        <a href="{{ $post['url'] }}" class="group flex h-[86px] w-full min-w-0 items-center gap-2.5 overflow-hidden">
            <x-blog::image :src="$post['image']" :alt="$post['title']" class="h-full w-[86px] shrink-0 rounded-[10px]" />
            <span class="flex min-w-0 flex-1 flex-col gap-1.5">
                <span class="line-clamp-2 text-[14px] font-semibold leading-snug text-[#1e1e1e] transition-colors group-hover:text-[#ff6900] lg:text-[15px]">{{ $post['title'] }}</span>
                <span class="truncate text-[12px] text-[#767676]">{{ $meta }}</span>
            </span>
        </a>
        @break

    @case('list')
        <a href="{{ $post['url'] }}" class="group flex h-[82px] w-full items-center gap-2.5 overflow-hidden">
            <x-blog::image :src="$post['image']" :alt="$post['title']" class="h-full w-[110px] shrink-0 rounded-[10px]" />
            <span class="flex min-w-0 flex-1 flex-col gap-1.5">
                <span class="line-clamp-2 text-[14px] font-semibold leading-snug text-[#1e1e1e] transition-colors group-hover:text-[#ff6900]">{{ $post['title'] }}</span>
                <span class="truncate text-[12px] text-[#767676]">{{ $post['date'] }}</span>
            </span>
        </a>
        @break

    @default
        <a href="{{ $post['url'] }}" class="card-fx group flex w-full flex-col overflow-hidden rounded-[20px] border-4 border-white bg-white shadow-[0_1px_7.7px_rgba(0,0,0,0.15)]">
            <x-blog::image :src="$post['image']" :alt="$post['title']" class="aspect-[16/8] w-full lg:aspect-[242/170]" />
            <span class="flex flex-1 flex-col gap-2 p-3 lg:px-[15px] lg:py-4">
                <span class="line-clamp-2 text-[16px] font-semibold leading-snug text-[#303030] transition-colors group-hover:text-[#ff6900] lg:text-[18px]">{{ $post['title'] }}</span>
                <span class="mt-auto text-[12px] font-medium text-[#767676] lg:text-[14px]">{{ $meta }}</span>
            </span>
        </a>
@endswitch
