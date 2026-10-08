<div>
    <x-blog::hero :categories="$categories" subtitle="Stay on top of every moment with the latest football news, match previews and betting guides." />

    <x-page.panel>
        @if ($featured)
            <div class="flex flex-col gap-4 lg:gap-5">
                <section class="rounded-[20px] border border-[#d9d9d9] bg-white p-2.5 lg:p-[15px]">
                    <x-blog::section-header title="Top Stories" />
                    <div class="-mx-0.5 mt-3 flex gap-3 overflow-x-auto pb-1 lg:mx-0 lg:grid lg:grid-cols-4 lg:overflow-visible lg:pb-0">
                        @foreach ($topStories as $post)
                            <div class="w-[260px] shrink-0 lg:w-auto lg:min-w-0" wire:key="top-{{ $post['id'] }}">
                                <x-blog::card :post="$post" variant="compact" />
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:gap-6">
                    <div class="flex min-w-0 flex-1 flex-col gap-5">
                        <x-blog::card :post="$featured" variant="large" />

                        @if ($pair->isNotEmpty())
                            <div class="grid grid-cols-2 gap-2.5 lg:gap-5">
                                @foreach ($pair as $post)
                                    <x-blog::card :post="$post" variant="medium" wire:key="pair-{{ $post['id'] }}" />
                                @endforeach
                            </div>
                        @endif

                        @foreach ($sections as $section)
                            <section class="rounded-[20px] bg-white p-[15px]" wire:key="section-{{ $loop->index }}">
                                <x-blog::section-header :title="$section['name']" :href="$section['url']" />
                                <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($section['posts'] as $post)
                                        <x-blog::card :post="$post" wire:key="sec-{{ $loop->parent->index }}-{{ $post['id'] }}" />
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>

                    <x-blog::sidebar :latest="$latest" :categories="$categories" class="hidden lg:flex" />
                </div>
            </div>
        @else
            <div class="rounded-[20px] bg-white px-6 py-14 text-center">
                <p class="text-[18px] font-semibold text-[#1e1e1e]">No articles yet</p>
                <p class="mt-1 text-[14px] text-[#5a5a5a]">New football news and betting guides are on the way — check back soon.</p>
            </div>
        @endif
    </x-page.panel>
</div>
