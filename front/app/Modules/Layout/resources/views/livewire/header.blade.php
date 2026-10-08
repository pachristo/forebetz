<header class="relative z-30 w-full px-2.5 py-2.5 sm:px-8 sm:py-3 lg:px-[100px]">
    <div class="header-glass relative mx-auto flex max-w-site items-center justify-between gap-3 rounded-[16px] border-0 px-4 py-2.5 sm:rounded-[22px] sm:px-6 sm:py-3 lg:gap-5">
        <a href="/" class="min-w-0 shrink-0 origin-left scale-90 sm:scale-100">
            <x-logo />
        </a>

        <nav class="hidden items-center justify-end lg:flex">
            <div class="flex items-center gap-1 xl:gap-2">
                <a
                    href="/"
                    @class([
                        'nav-link',
                        'nav-link-active' => $this->isHome(),
                    ])
                >
                    <x-icon name="nav-home" class="hidden size-5 xl:block" />
                    Home
                </a>

                <x-layout::nav-dropdown label="Tips category" icon="nav-tips" :mega="true">
                    <div class="grid grid-cols-[1fr_270px] gap-5">
                        <div>
                            <div class="mb-3 flex items-center justify-between border-b border-white/10 pb-3">
                                <p class="text-[13px] font-semibold uppercase tracking-[1px] text-white/50">Free prediction categories</p>
                                <a href="/" class="text-[13px] font-medium text-[#ff8a3d] hover:text-[#ff6900]">Today&rsquo;s top picks &rarr;</a>
                            </div>
                            <div class="grid grid-cols-3 gap-x-2 gap-y-1">
                                @foreach ($categories as $cat)
                                    @php($active = $this->isActiveCategory($cat['slug']))
                                    <a
                                        href="{{ $this->categoryUrl($cat['slug']) }}"
                                        role="menuitem"
                                        @class([
                                            'group flex h-10 items-center gap-2.5 rounded-[10px] px-3 text-[14px] capitalize tracking-[0.2px] transition',
                                            'bg-[#ff6900] font-semibold text-[#1e1e1e]' => $active,
                                            'text-[#f3f3f3] hover:bg-white/[0.08] hover:text-[#ff8a3d]' => ! $active,
                                        ])
                                    >
                                        <span @class(['size-1.5 shrink-0 rounded-full transition', 'bg-[#1e1e1e]' => $active, 'bg-[#ff6900]/60 group-hover:bg-[#ff6900]' => ! $active])></span>
                                        <span class="truncate">{{ $cat['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex flex-col gap-2.5">
                            <a href="/pricing" role="menuitem" class="group relative flex flex-1 flex-col justify-between gap-3 overflow-hidden rounded-[14px] bg-gradient-to-br from-[#ff8a3d] to-[#b84800] p-4">
                                <div class="flex items-center gap-2">
                                    <x-icon name="crown" class="size-6" />
                                    <span class="text-[16px] font-bold text-white">VIP Predictions</span>
                                </div>
                                <p class="text-[13px] leading-snug text-white/90">Higher-confidence picks from our expert analysts, every day.</p>
                                <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-black/25 px-3 py-1.5 text-[12px] font-semibold uppercase text-white transition group-hover:bg-black/40">
                                    See plans <x-icon name="arrow-right" class="size-4" />
                                </span>
                            </a>
                            <a href="{{ $this->categoryUrl('sure-banker-of-the-day') }}" role="menuitem" class="flex items-center gap-3 rounded-[14px] border border-white/10 bg-white/[0.04] p-3 transition hover:border-[#ff6900]/60 hover:bg-white/[0.08]">
                                <span class="flex size-9 items-center justify-center rounded-full bg-[#cb5140]/25"><x-icon name="fire" class="size-5" /></span>
                                <span class="flex flex-col">
                                    <span class="text-[14px] font-semibold text-white">Banker of the Day</span>
                                    <span class="text-[12px] text-white/60">Our safest single pick</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </x-layout::nav-dropdown>

                <a href="/live" @class(['nav-link', 'nav-link-active' => request()->routeIs('live')])>
                    <x-icon name="nav-live" class="hidden size-5 xl:block" />
                    Livescores
                </a>

                <a href="/blog" @class(['nav-link', 'nav-link-active' => $this->onBlog()])>
                    <x-icon name="nav-blog" class="hidden size-5 xl:block" />
                    Blog
                </a>

                <x-layout::nav-dropdown label="Links" icon="nav-link" align="right" panel-class="w-[280px]">
                    @if ($textLinks)
                        <div class="flex max-h-[60vh] flex-col gap-0.5 overflow-y-auto">
                            @foreach ($textLinks as $link)
                                <a href="{{ $link['href'] }}" target="_blank" rel="noopener noreferrer" role="menuitem" class="group flex items-center justify-between gap-2 rounded-[10px] px-3.5 py-2.5 text-[14px] text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.12)] hover:text-[#ff8a3d]">
                                    <span class="truncate">{{ $link['label'] }}</span>
                                    <x-icon name="chevron-right" class="size-4 shrink-0 opacity-40 group-hover:opacity-100" />
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col gap-1">
                            @foreach ($moreLinks as $link)
                                <a href="{{ $link['href'] }}" role="menuitem" class="rounded-[10px] px-4 py-3 text-[15px] text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.12)]">{{ $link['label'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </x-layout::nav-dropdown>
            </div>
        </nav>

        <div class="hidden shrink-0 items-center gap-2 lg:flex xl:gap-2.5 xl:pl-5">
            @if ($user)
                <x-layout::nav-dropdown
                    :label="str($user->name)->before(' ')"
                    icon="dashboard/nav-user"
                    align="right"
                    trigger-class="flex h-11 items-center gap-3 rounded-full border border-[#ff6900]/60 px-5 text-[15px] transition duration-300 hover:border-[#ff6900] hover:bg-white/5 font-medium tracking-[0.2px] text-[#f3f3f3] backdrop-blur-[7.45px]"
                >
                    <div class="flex flex-col gap-1">
                        @foreach ($accountLinks as $link)
                            <a href="{{ $link['href'] }}" role="menuitem" class="rounded-[10px] px-4 py-3 text-[15px] text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.12)]">{{ $link['label'] }}</a>
                        @endforeach
                        <form method="POST" action="/logout">
                            @csrf
                            <button type="submit" role="menuitem" class="w-full rounded-[10px] px-4 py-3 text-left text-[15px] text-[#ec221f] hover:bg-[rgba(240,240,240,0.12)]">Logout</button>
                        </form>
                    </div>
                </x-layout::nav-dropdown>
            @else
                <a href="/register" class="btn-fx inline-flex h-11 items-center rounded-full border border-[#ff6900]/60 bg-white/[0.03] px-4 text-[14px] xl:px-6 xl:text-[15px] font-semibold tracking-[0.2px] text-[#ff8a3d] duration-300 ease-out hover:border-[#ff6900] hover:bg-[#ff6900] hover:text-white">Register</a>
                <a href="/login" class="btn-fx inline-flex h-11 items-center rounded-full bg-gradient-to-b from-[#ff8a3d] to-[#e85d00] px-5 text-[14px] xl:px-7 xl:text-[15px] font-semibold tracking-[0.2px] text-white shadow-[0_6px_18px_-6px_rgba(255,105,0,0.7)] duration-300 ease-out">Login</a>
            @endif
        </div>

        <button
            type="button"
            class="relative z-10 flex size-8 items-center justify-center lg:hidden"
            aria-label="Open menu"
            aria-controls="mobile-menu"
            x-data
            @click="$store.overlay.toggle('menu')"
            :aria-expanded="$store.overlay.is('menu')"
        >
            <x-icon name="menu" class="size-8" />
        </button>
    </div>
</header>
