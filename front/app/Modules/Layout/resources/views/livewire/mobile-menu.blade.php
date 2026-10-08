<div>
    <x-layout::drawer name="menu" id="mobile-menu" label="Site menu" panel-class="w-[min(340px,92vw)]">
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
            <a href="/" class="min-w-0 shrink-0"><x-logo /></a>
            <button
                type="button"
                class="flex size-10 items-center justify-center rounded-full bg-white/10 text-[28px] leading-none text-white"
                @click="$store.overlay.close('menu')"
                aria-label="Close menu"
            >&times;</button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 text-[16px] text-[#f3f3f3]">
            <div class="flex flex-col gap-1">
                <a
                    href="/"
                    @class([
                        'flex items-center gap-3 rounded-[12px] px-3 py-3.5',
                        'border-l-4 border-[#ff6900] bg-white/10' => $this->isHome(),
                        'hover:bg-white/5' => ! $this->isHome(),
                    ])
                >
                    <x-icon name="nav-home" class="size-5" />
                    Home
                </a>

                <div class="rounded-[12px]" x-data="{ open: false }">
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-[12px] px-3 py-3.5 text-left hover:bg-white/5"
                        @click="open = ! open"
                        :aria-expanded="open"
                    >
                        <span class="flex items-center gap-3">
                            <x-icon name="nav-tips" class="size-5" />
                            Tips category
                        </span>
                        <x-icon name="arrow-down" class="size-5 transition-transform duration-200" x-bind:class="open && 'rotate-180'" />
                    </button>
                    <div class="mb-2 ml-2 flex flex-col gap-1.5 rounded-[12px] bg-[#141414] p-2" x-cloak x-show="open" x-collapse>
                        @foreach ($categories as $cat)
                            <a
                                href="{{ $this->categoryUrl($cat['slug']) }}"
                                @class([
                                    'rounded-[10px] px-3 py-2.5 text-center text-[15px]',
                                    'bg-[#ff6900] font-semibold text-[#1e1e1e]' => $this->isActiveCategory($cat['slug']),
                                    'bg-[rgba(240,240,240,0.12)] text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.2)]' => ! $this->isActiveCategory($cat['slug']),
                                ])
                            >{{ $cat['label'] }}</a>
                        @endforeach
                    </div>
                </div>

                <a href="/live" class="flex items-center gap-3 rounded-[12px] px-3 py-3.5 hover:bg-white/5">
                    <x-icon name="nav-live" class="size-5" />
                    Livescores
                </a>
                <a href="/blog" class="flex items-center gap-3 rounded-[12px] px-3 py-3.5 hover:bg-white/5">
                    <x-icon name="nav-blog" class="size-5" />
                    Blog
                </a>
                @foreach ($moreLinks as $link)
                    <a href="{{ $link['href'] }}" class="flex items-center gap-3 rounded-[12px] px-3 py-3.5 hover:bg-white/5">
                        @if ($loop->first)
                            <x-icon name="nav-link" class="size-5" />
                        @endif
                        {{ $link['label'] }}
                    </a>
                @endforeach
                <a href="/pricing" class="flex items-center gap-3 rounded-[12px] px-3 py-3.5 hover:bg-white/5">VIP Packages</a>
                @if ($textLinks)
                    <div x-data="{ open: false }" class="rounded-[12px]">
                        <button type="button" @click="open = ! open" class="flex w-full items-center justify-between gap-3 rounded-[12px] px-3 py-3.5 hover:bg-white/5" :aria-expanded="open">
                            <span class="flex items-center gap-3"><x-icon name="nav-link" class="size-5" /> Links</span>
                            <x-icon name="arrow-down" class="size-5 opacity-70 transition-transform" x-bind:class="open && 'rotate-180'" />
                        </button>
                        <div x-cloak x-show="open" x-transition class="flex flex-col gap-0.5 pb-2 pl-11">
                            @foreach ($textLinks as $link)
                                <a href="{{ $link['href'] }}" target="_blank" rel="noopener noreferrer" class="truncate rounded-[10px] px-3 py-2 text-[14px] text-white/80 hover:bg-white/5 hover:text-[#ff8a3d]">{{ $link['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </nav>

        <div class="border-t border-white/10 px-4 py-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
            <div class="flex flex-col gap-3">
                @if ($user)
                    <a href="/dashboard" class="rounded-[15px] bg-[#ff6900] px-5 py-3.5 text-center text-[16px] font-medium text-[#1e1e1e]">Dashboard</a>
                    <a href="/profile" class="rounded-[15px] border border-[#ff6900] px-5 py-3.5 text-center text-[16px] font-medium text-[#ff6900]">My Account</a>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="w-full rounded-[15px] border border-[#ec221f] px-5 py-3.5 text-center text-[16px] font-medium text-[#ec221f]">Logout</button>
                    </form>
                @else
                    <a href="/register" class="rounded-[15px] border border-[#ff6900] px-5 py-3.5 text-center text-[16px] font-medium text-[#ff6900]">Register</a>
                    <a href="/login" class="rounded-[15px] bg-[#ff6900] px-5 py-3.5 text-center text-[16px] font-medium text-[#1e1e1e]">Login</a>
                @endif
            </div>
        </div>
    </x-layout::drawer>
</div>
