<div>
    <x-layout::drawer name="tips" id="tips-categories-drawer" side="left" label="Tips category" panel-class="w-[min(320px,88vw)]">
        <div class="flex items-center justify-between border-b border-white/10 px-4 py-4">
            <div class="flex items-center gap-2.5">
                <x-icon name="nav-tips" class="size-6" />
                <h2 class="text-[18px] font-semibold tracking-[0.2px] text-white">Tips category</h2>
            </div>
            <button
                type="button"
                class="flex size-10 items-center justify-center rounded-full bg-white/10 text-[28px] leading-none text-white"
                @click="$store.overlay.close('tips')"
                aria-label="Close tips category"
            >&times;</button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4" aria-label="Tip categories">
            <ul class="flex flex-col gap-2.5">
                @foreach ($categories as $cat)
                    @php($active = $onCategoryPage && $currentCategory === $cat['slug'])
                    <li>
                        <a
                            href="{{ $this->categoryUrl($cat['slug']) }}"
                            @class([
                                'flex h-[52px] items-center justify-center rounded-[10px] px-4 text-center text-[16px] capitalize tracking-[0.2px]',
                                'border border-[#ff6900] bg-[#ff6900] font-semibold text-[#1e1e1e]' => $active,
                                'bg-[rgba(240,240,240,0.12)] font-normal text-[#f3f3f3] hover:bg-[rgba(240,240,240,0.2)]' => ! $active,
                            ])
                        >{{ $cat['label'] }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </x-layout::drawer>
</div>
