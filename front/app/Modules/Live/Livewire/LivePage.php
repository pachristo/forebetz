<?php

namespace App\Modules\Live\Livewire;

use App\APIprediction;
use App\Models\APIodds;
use App\Models\Fixture;
use App\Models\Prediction;
use App\Services\ApiFootball;
use App\Services\PredictionFeed;
use App\Support\MatchUrl;
use App\Support\TipLabel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class LivePage extends Component
{
    public const FILTERS = ['all' => 'All', 'live' => 'Live', 'finished' => 'Finished', 'upcoming' => 'Upcoming'];

    /** Leagues listed first, in this order; everything else follows alphabetically by country. */
    private const TOP_LEAGUES = [2, 3, 848, 39, 140, 135, 78, 61, 1, 4, 9, 94, 88, 71, 253, 307];

    private const UPCOMING = ['NS', 'TBD'];

    #[Locked]
    public int $dayOffset = 0;

    #[Locked]
    public string $filter = 'all';

    #[Locked]
    public ?int $selected = null;

    public function setDay(int $offset): void
    {
        $this->dayOffset = max(-7, min(7, $offset));
        $this->selected = null;
    }

    public function setFilter(string $filter): void
    {
        $this->filter = array_key_exists($filter, self::FILTERS) ? $filter : 'all';
    }

    public function select(int $matchId): void
    {
        $this->selected = $matchId;
    }

    public function render()
    {
        $date = today()->addDays($this->dayOffset);
        $live = $this->dayOffset === 0 ? app(ApiFootball::class)->liveFixtures() : [];

        $rows = Fixture::query()
            ->whereDate('match_date', $date)
            ->orderBy('match_time')
            ->orderBy('match_id')
            ->get(['match_id', 'match_date', 'match_time', 'league_id', 'home_name', 'away_name', 'url_home_icon', 'url_away_icon', 'home_goal', 'away_goal', 'ht_home_goals', 'ht_away_goals', 'match_status', 'match_data'])
            ->map(fn (Fixture $f) => $this->row($f, $live[$f->match_id] ?? null));

        $counts = collect(self::FILTERS)->map(fn ($label, $key) => $key === 'all' ? $rows->count() : $rows->where('state', $key)->count());
        $visible = $this->filter === 'all' ? $rows : $rows->where('state', $this->filter);

        $selectedId = $this->selected
            ?? $rows->firstWhere('state', 'live')['id']
            ?? $visible->first()['id']
            ?? null;

        return view('live::livewire.live-page', [
            'date' => $date,
            'counts' => $counts,
            'leagues' => $this->groupByLeague($visible),
            'selectedId' => $selectedId,
            'preview' => $selectedId ? $this->preview($selectedId, $live[$selectedId] ?? null) : null,
        ])->layoutData([
            'title' => 'Live Football Scores Today, Results & Fixtures — '.config('site.name'),
            'description' => 'Live football scores, real-time results, goal scorers, cards and match stats from every league today, with free predictions for each fixture.',
            'canonical' => url('/live'),
        ]);
    }

    /** @return array<string, mixed> */
    private function row(Fixture $f, ?array $live): array
    {
        $status = (string) ($live['status'] ?? $f->match_status ?: 'NS');
        $elapsed = $live['elapsed'] ?? ($f->match_data['fixture']['status']['elapsed'] ?? null);
        $state = match (true) {
            in_array($status, PredictionFeed::LIVE, true) => 'live',
            in_array($status, PredictionFeed::FINISHED, true) || in_array($status, ['AWD', 'WO'], true) => 'finished',
            in_array($status, self::UPCOMING, true) => 'upcoming',
            default => 'other',
        };
        $league = $f->match_data['league'] ?? [];

        return [
            'id' => (int) $f->match_id,
            'time' => substr((string) $f->match_time, 0, 5),
            'status' => $status,
            'state' => $state,
            'clock' => $this->clock($status, $elapsed, $live['extra'] ?? null, $f),
            'home' => $f->home_name,
            'away' => $f->away_name,
            'home_logo' => $f->url_home_icon,
            'away_logo' => $f->url_away_icon,
            'home_goals' => $state === 'upcoming' ? null : ($live['home_goals'] ?? $f->home_goal),
            'away_goals' => $state === 'upcoming' ? null : ($live['away_goals'] ?? $f->away_goal),
            'ht' => $f->ht_home_goals !== null && $state !== 'upcoming' ? "{$f->ht_home_goals}-{$f->ht_away_goals}" : null,
            'league_id' => (int) ($league['id'] ?? $f->league_id),
            'league' => (string) ($league['name'] ?? 'Other'),
            'country' => (string) ($league['country'] ?? ''),
            'league_logo' => $league['logo'] ?? null,
            'flag' => $league['flag'] ?? null,
            'round' => (string) ($league['round'] ?? ''),
            'venue' => trim(($f->match_data['fixture']['venue']['name'] ?? '').', '.($f->match_data['fixture']['venue']['city'] ?? ''), ', '),
            'href' => MatchUrl::make($f->match_id, $f->home_name, $f->away_name),
        ];
    }

    private function clock(string $status, ?int $elapsed, ?int $extra, Fixture $f): string
    {
        return match (true) {
            $status === 'HT' => 'HT',
            in_array($status, ['1H', '2H', 'ET', 'LIVE'], true) && $elapsed => $elapsed.($extra ? '+'.$extra : '')."'",
            in_array($status, self::UPCOMING, true) => substr((string) $f->match_time, 0, 5),
            default => $status,
        };
    }

    /** @return list<array{id: int, name: string, country: string, logo: ?string, flag: ?string, live: int, matches: list<array<string, mixed>>}> */
    private function groupByLeague(Collection $rows): array
    {
        return $rows->groupBy('league_id')
            ->map(fn (Collection $matches, $id) => [
                'id' => (int) $id,
                'name' => $matches[0]['league'],
                'country' => $matches[0]['country'],
                'logo' => $matches[0]['league_logo'],
                'flag' => $matches[0]['flag'],
                'live' => $matches->where('state', 'live')->count(),
                'matches' => $matches->values()->all(),
            ])
            ->sortBy(fn (array $l) => [
                ($pos = array_search($l['id'], self::TOP_LEAGUES, true)) === false ? 999 : $pos,
                $l['country'],
                $l['name'],
            ])
            ->values()
            ->all();
    }

    /** @return array<string, mixed>|null */
    private function preview(int $matchId, ?array $live): ?array
    {
        $fixture = Fixture::query()->where('match_id', $matchId)->first();

        if (! $fixture) {
            return null;
        }

        $row = $this->row($fixture, $live);
        $detail = $row['state'] === 'upcoming'
            ? ['events' => [], 'stats' => []]
            : app(ApiFootball::class)->fixtureDetail($matchId, $row['state'] === 'live');

        $tip = Prediction::query()
            ->where('match_id', $matchId)
            ->where('vip_type', 'regular')
            ->get()
            ->sortBy(fn (Prediction $p) => array_search($p->type, PredictionFeed::TYPE_PRIORITY, true) === false ? 99 : array_search($p->type, PredictionFeed::TYPE_PRIORITY, true))
            ->first();

        $percent = APIprediction::query()->where('match_id', $matchId)->first()?->pred_data[0]['predictions']['percent'] ?? null;
        $odds = APIodds::query()->where('match_id', $matchId)->first()?->odd_data ?? [];

        return $row + [
            'kickoff' => $fixture->match_date ? Carbon::parse($fixture->match_date->toDateString().' '.($fixture->match_time ?: '00:00')) : null,
            'events' => $detail['events'],
            'stats' => $detail['stats'],
            'tip' => $tip ? [
                'pick' => TipLabel::for($tip->type, $tip->tips),
                'odds' => $tip->odds && (float) $tip->odds > 0 ? number_format((float) $tip->odds, 2) : null,
                'result' => match ((string) $tip->winning_status) { '1' => 'won', '2' => 'lost', default => null },
            ] : null,
            'probs' => $percent ? [
                ['label' => 'Home', 'pct' => (int) $percent['home']],
                ['label' => 'Draw', 'pct' => (int) $percent['draw']],
                ['label' => 'Away', 'pct' => (int) $percent['away']],
            ] : [],
            'odds' => array_filter([
                '1' => $odds['Home'] ?? null,
                'X' => $odds['Draw'] ?? null,
                '2' => $odds['Away'] ?? null,
            ]),
        ];
    }
}
