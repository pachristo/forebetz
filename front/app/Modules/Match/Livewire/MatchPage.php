<?php

namespace App\Modules\Match\Livewire;

use App\APIprediction;
use App\Models\APIodds;
use App\Models\Fixture;
use App\Models\PlanCategory;
use App\Models\Prediction;
use App\Services\ApiFootball;
use App\Services\PredictionFeed;
use App\Support\MatchUrl;
use App\Support\TipLabel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class MatchPage extends Component
{
    #[Locked]
    public int $matchId = 0;

    public function mount(int $matchId, string $slug): void
    {
        $this->matchId = $matchId;
        $fixture = $this->fixture();

        abort_unless($this->matchId > 0 && $fixture, 404);

        if ($slug !== MatchUrl::slug($fixture->home_name, $fixture->away_name)) {
            abort(redirect(MatchUrl::make($this->matchId, $fixture->home_name, $fixture->away_name), 301));
        }
    }

    protected function fixture(): ?Fixture
    {
        return once(fn () => Fixture::query()->where('match_id', $this->matchId)->first());
    }

    /** @return array{free: list<array<string, mixed>>, vip: list<array<string, mixed>>, locked: list<string>} */
    protected function predictions(Fixture $fixture): array
    {
        $rows = Prediction::query()->where('match_id', $fixture->match_id)->get();
        $unlocked = auth()->user()?->activeCategoryIds() ?? [];

        $map = fn (Prediction $p) => [
            'market' => self::marketName($p->type),
            'pick' => TipLabel::for($p->type, $p->tips),
            'odds' => $p->odds && (float) $p->odds > 0 ? number_format((float) $p->odds, 2) : null,
            'prob' => $p->prob !== null && $p->prob !== '' ? (int) round((float) $p->prob) : null,
            'result' => match ((string) $p->winning_status) { '1' => 'won', '2' => 'lost', default => null },
            'priority' => array_search($p->type, PredictionFeed::TYPE_PRIORITY, true) === false ? 99 : array_search($p->type, PredictionFeed::TYPE_PRIORITY, true),
        ];

        $free = $rows->where('vip_type', 'regular')->map($map)->sortBy('priority')->values()->all();
        $vipRows = $rows->where('vip_type', '!=', 'regular');

        $vip = $vipRows->filter(fn (Prediction $p) => in_array((int) $p->vip_type, $unlocked, true))->map($map)->values()->all();

        $locked = $vipRows->reject(fn (Prediction $p) => in_array((int) $p->vip_type, $unlocked, true))
            ->pluck('vip_type')->unique()
            ->map(fn ($id) => PlanCategory::find((int) $id)?->name)
            ->filter()->values()->all();

        return compact('free', 'vip', 'locked');
    }

    /** @return list<array<string, mixed>> */
    protected function h2h(?array $pred): array
    {
        return collect($pred['h2h'] ?? [])
            ->sortByDesc(fn ($row) => $row['fixture']['timestamp'] ?? 0)
            ->take(6)
            ->map(fn ($row) => ApiFootball::fixtureRow($row))
            ->values()
            ->all();
    }

    /** @return list<array{label: string, home: int, away: int}> */
    protected function comparison(?array $pred): array
    {
        $labels = ['form' => 'Form', 'att' => 'Attack', 'def' => 'Defence', 'h2h' => 'Head to head', 'goals' => 'Goals', 'total' => 'Overall'];

        return collect($labels)
            ->filter(fn ($label, $key) => isset($pred['comparison'][$key]))
            ->map(fn ($label, $key) => [
                'label' => $label,
                'home' => (int) round((float) $pred['comparison'][$key]['home']),
                'away' => (int) round((float) $pred['comparison'][$key]['away']),
            ])
            ->values()
            ->all();
    }

    public function render()
    {
        $api = app(ApiFootball::class);
        $fixture = $this->fixture();
        $data = $fixture->match_data ?? [];
        $league = $data['league'] ?? [];
        $status = (string) $fixture->match_status;
        $started = in_array($status, PredictionFeed::FINISHED, true) || in_array($status, PredictionFeed::LIVE, true);
        $kickoff = $fixture->match_date?->copy()->setTimeFromTimeString((string) ($fixture->match_time ?: '00:00'));

        $pred = APIprediction::query()->where('match_id', $fixture->match_id)->first()?->pred_data[0] ?? null;
        $percent = $pred['predictions']['percent'] ?? null;
        $odds = APIodds::query()->where('match_id', $fixture->match_id)->first()?->odd_data ?? [];
        $predictions = $this->predictions($fixture);
        $top = $predictions['free'][0] ?? null;

        $leagueId = (int) ($league['id'] ?? $fixture->league_id);
        $season = (int) ($league['season'] ?? $fixture->season) ?: null;
        $standings = $leagueId && ($league['standings'] ?? true)
            ? $api->standings($leagueId, $season, (int) $fixture->home_id)
            : [];

        $title = "{$fixture->home_name} vs {$fixture->away_name}";

        return view('match::livewire.match-page', [
            'fixture' => $fixture,
            'title' => $title,
            'league' => $league,
            'venue' => trim(($data['fixture']['venue']['name'] ?? '').(isset($data['fixture']['venue']['city']) ? ', '.$data['fixture']['venue']['city'] : ''), ', '),
            'kickoff' => $kickoff,
            'status' => $status,
            'started' => $started,
            'isLive' => in_array($status, PredictionFeed::LIVE, true),
            'predictions' => $predictions,
            'top' => $top,
            'probs' => $percent ? [
                ['label' => 'Home', 'pct' => (int) $percent['home'], 'odds' => $odds['Home'] ?? null],
                ['label' => 'Draw', 'pct' => (int) $percent['draw'], 'odds' => $odds['Draw'] ?? null],
                ['label' => 'Away', 'pct' => (int) $percent['away'], 'odds' => $odds['Away'] ?? null],
            ] : [],
            'advice' => (string) ($pred['predictions']['advice'] ?? ''),
            'comparison' => $this->comparison($pred),
            'h2h' => $this->h2h($pred),
            'homeLast' => $fixture->home_id ? $api->lastFixtures((int) $fixture->home_id) : [],
            'awayLast' => $fixture->away_id ? $api->lastFixtures((int) $fixture->away_id) : [],
            'standings' => $standings,
            'teamIds' => [(int) $fixture->home_id, (int) $fixture->away_id],
        ])->layoutData([
            'title' => "{$title} Prediction, Odds & H2H — ".config('site.name'),
            'canonical' => url(MatchUrl::make($fixture->match_id, $fixture->home_name, $fixture->away_name)),
            'description' => "{$title} prediction".($top ? ": {$top['pick']}" : '').'. '.($league['name'] ?? 'Football')
                .' match preview with head to head, form, standings and expert betting tips.',
        ]);
    }

    public static function marketName(string $type): string
    {
        return match ($type) {
            'home_win', 'away_win', 'draw' => '1X2',
            'double_chance' => 'Double Chance',
            '1_5_goal' => 'Over/Under 1.5',
            '2_5_goal' => 'Over/Under 2.5',
            '3_5_goal' => 'Over/Under 3.5',
            'btts' => 'Both Teams To Score',
            'dnd' => 'Draw No Bet',
            'weh' => 'Win Either Half',
            'home_1_5_goals', 'away_1_5_goals' => 'Team Goals',
            'correct_score' => 'Correct Score',
            default => ucwords(str_replace('_', ' ', $type)),
        };
    }
}
