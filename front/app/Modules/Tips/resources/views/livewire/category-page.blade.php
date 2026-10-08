<div>
    <livewire:home::hero :highlight="$name" heading="Predictions" :intro="$intro" />

    <x-page.panel>
        <x-layout::ads.banner place="ac" class="mb-4" />

        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-6">
            <div class="min-w-0 flex-1">
                <livewire:home::predictions :types="$types" :heading="$heading" :empty-label="strtolower($name).' predictions'" :active-category="$category->slug" />
                <x-layout::ads.banner place="uc" class="mt-5" />
            </div>

            <x-page.sidebar />
        </div>

        @if ($content !== '')
            <section class="mt-7 rounded-[16px] bg-white px-5 py-6 sm:rounded-[24px] sm:px-7 sm:py-7">
                <div class="home-content">{!! $content !!}</div>
            </section>
        @endif
    </x-page.panel>
</div>
