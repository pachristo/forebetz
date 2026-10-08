<?php

namespace App\Modules\Home\Livewire;

use App\Modules\Home\Support\HomeContent;
use App\Services\PredictionFeed;
use App\Support\SiteNavigation;
use Carbon\CarbonImmutable;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Predictions extends Component
{
    /** Selected fixture date (Y-m-d); empty means today. Component state only, never written to the URL. */
    public string $date = '';

    public int $limit = 0;

    /** Prediction types to show (empty = every type). */
    #[Locked]
    public array $types = [];

    /** 'regular' for free tips, or a plan category id for a subscriber's VIP tips. */
    #[Locked]
    public string $vip = 'regular';

    /** Section heading; null uses the homepage fixture heading. */
    #[Locked]
    public ?string $heading = null;

    #[Locked]
    public string $emptyLabel = 'free predictions';

    /** Date (Y-m-d) to open on when the URL has none, e.g. the next Saturday on a day page. */
    #[Locked]
    public string $defaultDate = '';

    /** Tip-category slug being viewed; null on the homepage ("Free Tips"). */
    #[Locked]
    public ?string $activeCategory = null;

    public function mount(): void
    {
        $this->limit = $this->perPage();
        $this->date = $this->normalise($this->date !== '' ? $this->date : $this->defaultDate);
    }

    public function setDay(int $offset): void
    {
        $this->goTo(CarbonImmutable::today()->addDays($offset));
    }

    public function shiftDay(int $days): void
    {
        $this->goTo($this->selectedDate()->addDays($days));
    }

    public function updatedDate(): void
    {
        $this->date = $this->normalise($this->date);
        $this->limit = $this->perPage();
    }

    public function loadMore(): void
    {
        $this->limit += $this->perPage();
    }

    public function render(PredictionFeed $feed)
    {
        $date = $this->selectedDate();
        $result = $this->canView()
            ? $feed->forDate($date->toDateString(), $this->limit, $this->types ?: null, $this->vip)
            : ['total' => 0, 'matches' => []];

        return view('home::livewire.predictions', [
            'title' => trim($this->heading ?? (string) HomeContent::page()?->fixture_heading),
            'emptyLabel' => $this->emptyLabel,
            'selected' => $date,
            'dayOffset' => (int) CarbonImmutable::today()->diffInDays($date, false),
            'matches' => $result['matches'],
            'total' => $result['total'],
            'categoryPills' => $this->vip === 'regular' ? SiteNavigation::tipCategories() : [],
        ]);
    }

    private function canView(): bool
    {
        if ($this->vip === 'regular') {
            return true;
        }

        return in_array((int) $this->vip, auth()->user()?->activeCategoryIds() ?? [], true);
    }

    private function goTo(CarbonImmutable $date): void
    {
        $this->date = $date->isToday() ? '' : $date->toDateString();
        $this->limit = $this->perPage();
    }

    private function selectedDate(): CarbonImmutable
    {
        return $this->date === '' ? CarbonImmutable::today() : CarbonImmutable::parse($this->date)->startOfDay();
    }

    private function normalise(string $date): string
    {
        try {
            $parsed = CarbonImmutable::createFromFormat('!Y-m-d', $date);
        } catch (\Throwable) {
            return '';
        }

        return $parsed && ! $parsed->isToday() ? $parsed->toDateString() : '';
    }

    private function perPage(): int
    {
        return (int) config('modules.home.predictions_per_page', 10);
    }
}
