<?php

namespace App\Modules\Home\Livewire;

use App\Models\HomepageFaq;
use App\Modules\Home\Support\HomeContent;
use Livewire\Component;

class SeoFaq extends Component
{
    /** @return list<array{q: string, a: string}> */
    protected function faqs(): array
    {
        return HomepageFaq::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['question', 'answer'])
            ->map(fn (HomepageFaq $faq) => ['q' => $faq->question, 'a' => $faq->answer])
            ->all();
    }

    public function render()
    {
        return view('home::livewire.seo-faq', [
            'content' => trim((string) HomeContent::page()?->content),
            'faqs' => $this->faqs(),
        ]);
    }
}
