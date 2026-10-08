<div>
    <livewire:home::hero :highlight="$accent" :heading="$rest" :intro="(string) $page->head2" />

    <x-page.panel>
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-6">
            <div class="min-w-0 flex-1">
                <livewire:home::predictions :default-date="$date" heading="Free Predictions" empty-label="free predictions" />
                <livewire:home::recent-winnings />
            </div>

            <x-page.sidebar />
        </div>

        @if (trim((string) $page->content) !== '')
            <section class="mt-7 rounded-[16px] bg-white px-5 py-6 sm:rounded-[24px] sm:px-7 sm:py-7">
                <div class="home-content">{!! $page->content !!}</div>
            </section>
        @endif
    </x-page.panel>
</div>
