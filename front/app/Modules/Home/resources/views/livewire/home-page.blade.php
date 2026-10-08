<div>
    <livewire:home::hero />

    <main class="w-full px-2.5 pb-6 sm:px-8 sm:pb-8 lg:px-[100px]">
        <div class="mx-auto w-full max-w-site rounded-[16px] bg-[#f0f0f0] p-2.5 text-[#1e1e1e] sm:rounded-[26px] sm:p-6 lg:p-8">
            <x-layout::ads.banner place="afp" class="mb-4" />
            <x-layout::ads.grid class="mb-4" />

            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:gap-6">
                <div class="min-w-0 flex-1">
                    <livewire:home::predictions />
                    <x-layout::ads.banner place="ufp" class="mt-5" />
                    <livewire:home::investment />
                    <x-layout::ads.banner place="uin" class="mt-5" />
                    <livewire:home::packages />
                    <x-layout::ads.banner place="uvi" class="mt-5" />
                    <livewire:home::recent-winnings />
                    <x-layout::ads.banner place="urw" class="mt-5" />
                    <livewire:home::articles />
                </div>

                <x-page.sidebar />
            </div>

            <livewire:home::seo-faq />
        </div>
    </main>
</div>
