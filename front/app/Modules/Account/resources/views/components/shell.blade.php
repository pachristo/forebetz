@props(['active' => 'dashboard', 'title'])

@php
    $member = auth()->user();
    $items = [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'dashboard'],
        ['id' => 'packages', 'label' => 'VIP Packages', 'href' => '/pricing', 'icon' => 'vip'],
        ['id' => 'history', 'label' => 'History', 'href' => '/dashboard#history', 'icon' => 'history'],
        ['id' => 'account', 'label' => 'My Account', 'href' => '/profile', 'icon' => 'user'],
    ];
@endphp

<x-page.panel>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start">
        <aside class="flex w-full flex-col gap-3 rounded-[18px] bg-white p-3 lg:sticky lg:top-5 lg:w-[240px] lg:shrink-0 lg:p-4">
            <div class="flex items-center gap-3 lg:flex-col lg:text-center">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-b from-[#ff8a3d] to-[#e85d00] text-[16px] font-bold text-white">{{ $member->initials() }}</span>
                <div class="min-w-0">
                    <p class="truncate text-[15px] font-semibold">{{ $member->name }}</p>
                    <p class="truncate text-[13px] text-[#5a5a5a]">{{ $member->email }}</p>
                </div>
            </div>

            <nav class="grid grid-cols-4 gap-1.5 border-t border-[#eee] pt-3 lg:grid-cols-1">
                @foreach ($items as $item)
                    <a href="{{ $item['href'] }}" @class([
                        'flex flex-col items-center gap-1 rounded-[10px] px-2 py-2 text-[11px] font-medium transition sm:text-[12px] lg:flex-row lg:gap-2.5 lg:px-3 lg:py-2.5 lg:text-[14px]',
                        'bg-[#ff6900] text-[#1e1e1e]' => $active === $item['id'],
                        'text-[#2c2c2c] hover:bg-[#fff0e6] hover:text-[#cc5400]' => $active !== $item['id'],
                    ])>
                        <x-icon :name="'dashboard/'.$item['icon']" class="size-5" />
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <form method="POST" action="/logout" class="hidden lg:block">
                @csrf
                <button type="submit" class="btn-fx flex h-10 w-full items-center justify-center gap-2 rounded-full bg-[#fee9e7] text-[14px] font-medium text-[#ec221f] hover:bg-[#ec221f] hover:text-white">
                    <x-icon name="dashboard/logout" class="size-5" />
                    Logout
                </button>
            </form>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col gap-4">
            <h1 class="sr-only">{{ $title }}</h1>
            {{ $slot }}
        </div>
    </div>
</x-page.panel>
