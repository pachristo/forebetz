<div>
    <x-blog::hero :categories="$categories" :active="$category->slug" :accent="$category->name" title="" :subtitle="$description" />

    <x-page.panel>
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:gap-6">
            <section class="min-w-0 flex-1 rounded-[20px] bg-white p-[15px]">
                <x-blog::section-header :title="$posts->total().' '.str('article')->plural($posts->total())" tag="h2" />

                @if ($cards->isNotEmpty())
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($cards as $post)
                            <x-blog::card :post="$post" wire:key="cat-{{ $post['id'] }}" />
                        @endforeach
                    </div>

                    @if ($posts->hasPages())
                        <div class="mt-5">{{ $posts->links(data: ['scrollTo' => false]) }}</div>
                    @endif
                @else
                    <p class="py-10 text-center text-[14px] text-[#5a5a5a]">No articles in this category yet.</p>
                @endif
            </section>

            <x-blog::sidebar :latest="$latest" :categories="$categories" :active-category="$category->slug" class="hidden lg:flex" />
        </div>
    </x-page.panel>
</div>
