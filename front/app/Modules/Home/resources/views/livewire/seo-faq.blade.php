<div>
    @if ($content !== '' || $faqs)
        <section class="mt-7 rounded-[16px] bg-white px-5 py-6 text-[#1e1e1e] sm:rounded-[24px] sm:px-7 sm:py-8">
            @if ($content !== '')
                <div class="home-content">{!! $content !!}</div>
            @endif

            @if ($faqs)
                <h3 @class(['mb-2.5 text-[17px] font-bold text-[#ef1410] sm:text-[19px]', 'mt-5' => $content !== ''])>Frequently Asked Questions</h3>
                <div class="flex flex-col gap-2.5" x-data="{ open: 0 }">
                    @foreach ($faqs as $faq)
                        <div wire:key="faq-{{ $loop->index }}" class="rounded-[12px] border border-[#e6e9ec] px-4 py-3" :class="open === {{ $loop->index }} ? 'bg-white' : 'bg-[#fafafa]'">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-3 text-left text-[14px] font-semibold text-[#1e1e1e] sm:text-[15px]"
                                @click="open = open === {{ $loop->index }} ? null : {{ $loop->index }}"
                                :aria-expanded="open === {{ $loop->index }}"
                            >
                                {{ $faq['q'] }}
                                <x-icon name="arrow-down" class="size-5 invert transition-transform" x-bind:class="open === {{ $loop->index }} && 'rotate-180'" />
                            </button>
                            <div x-show="open === {{ $loop->index }}" x-collapse @if (! $loop->first) x-cloak @endif>
                                <p class="mt-1.5 text-[14px] leading-6 text-[#303030]">{{ $faq['a'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    @if ($faqs)
        @push('head')
            <script type="application/ld+json">{!! json_encode([
                '@'.'context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array_map(fn ($faq) => [
                    '@type' => 'Question',
                    'name' => $faq['q'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
                ], $faqs),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endpush
    @endif
</div>
