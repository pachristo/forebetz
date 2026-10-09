<?php

namespace App\Modules\Pages\Livewire;

use App\Models\SeoPage;
use Carbon\CarbonImmutable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class DayPredictionsPage extends Component
{
    /** seo_pages slug => day of week (0 = Sunday … 6 = Saturday), or 'weekend'. */
    public const DAYS = [
        'football-predictions-for-monday' => CarbonImmutable::MONDAY,
        'football-predictions-for-tuesday' => CarbonImmutable::TUESDAY,
        'football-predictions-for-wednesday' => CarbonImmutable::WEDNESDAY,
        'football-predictions-for-thursday' => CarbonImmutable::THURSDAY,
        'football-predictions-for-friday' => CarbonImmutable::FRIDAY,
        'football-predictions-for-saturday' => CarbonImmutable::SATURDAY,
        'football-predictions-for-sunday' => CarbonImmutable::SUNDAY,
        'football-predictions-for-the-weekend' => 'weekend',
    ];

    #[Locked]
    public string $slug = '';

    public function mount(): void
    {
        $this->slug = request()->path();

        abort_unless(array_key_exists($this->slug, self::DAYS) && $this->page(), 404);
    }

    protected function page(): ?SeoPage
    {
        return once(fn () => SeoPage::query()->where('slug', $this->slug)->where('status', 'published')->first());
    }

    /** Days ahead of today a weekday page may point to; further days fall back to the past occurrence. */
    private const MAX_DAYS_AHEAD = 2;

    /** The weekday within the next two days, otherwise its most recent past date (the weekend starts on Saturday). */
    protected function targetDate(): CarbonImmutable
    {
        $today = CarbonImmutable::today();
        $day = self::DAYS[$this->slug];

        if ($day === 'weekend') {
            return $today->isWeekend() ? $today : $today->next(CarbonImmutable::SATURDAY);
        }

        $ahead = ($day - $today->dayOfWeek + 7) % 7;

        return $ahead <= self::MAX_DAYS_AHEAD
            ? $today->addDays($ahead)
            : $today->subDays(7 - $ahead);
    }

    public function render()
    {
        $page = $this->page();
        $date = $this->targetDate();
        [$accent, $rest] = array_pad(explode(' ', trim((string) $page->head1), 2), 2, '');

        return view('pages::livewire.day-predictions-page', [
            'page' => $page,
            'accent' => $accent,
            'rest' => $rest,
            'date' => $date->toDateString(),
        ])->layoutData([
            'title' => (string) ($page->title ?: $page->head1),
            'description' => (string) $page->meta_description,
            'keywords' => (string) $page->meta_keywords,
            'canonical' => url('/'.$page->slug),
        ]);
    }
}
