<div>
    <x-page.title :title="$page->head1 ?: $page->title" :subtitle="$page->head2" />

    <x-page.panel>
        <article class="w-full rounded-[18px] bg-white px-4 py-5 sm:px-7 sm:py-6 lg:px-10 lg:py-8">
            @if ($isLegal)
                <p class="mb-3 flex items-center gap-2 text-[13px] text-[#5a5a5a]">
                    <x-icon name="calendar" class="size-4" />
                    Last updated: {{ $page->updated_at?->format('F j, Y') }}
                </p>
            @endif
            <div class="home-content">{!! $page->content !!}</div>
        </article>
    </x-page.panel>
</div>
