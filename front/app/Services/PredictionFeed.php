<?php

namespace App\Services;

use App\APIprediction;
use App\Models\APIodds;
use App\Models\Fixture;
use App\Models\Prediction;
use App\Support\TipLabel;
use Illuminate\Support\Collection;

/**
 * Builds match-card rows (fixture + odds + form + one featured tip) for a date.
 */
class PredictionFeed
{
    /** Order in which a fixture's tips are preferred for the single featured pick. */
    public const TYPE_PRIORITY = [
        'home_win', 'away_win', 'double_chance', '2_5_goal', '1_5_goal', 'btts', '3_5_goal',
        'draw', 'dnd', 'weh', 'home_1_5_goals', 'away_1_5_goals', 'correct_score',
    ];

    public const FINISHED = ['FT', 'AET', 'PEN'];

    public const LIVE = ['1H', 'HT', '2H', 'ET', 'BT', 'P', 'LIVE', 'INT'];

    /**
     * @param  list<string>|null  $types  restrict to these prediction types (null = all)
     * @param  string  $vipType  'regular' for free tips, or a plan category id for VIP tips
     * @return array{total: int, matches: list<array<string, mixed>>}
     */
    public function forDate(string $date, int $limit = 10, ?array $types = null, string $vipType = 'regular'): array
    {
        $predictions = Prediction::query()
            ->join('fixtures', 'fixtures.match_id', '=', 'predictions.match_id')
            ->where('fixtures.match_date', $date)
            ->where('predictions.vip_type', $vipType)
            ->when($types, fn ($q) => $q->whereIn('predictions.type', $types))
            ->get(['predictions.match_id', 'predictions.type', 'predictions.tips', 'predictions.odds', 'predictions.winning_status'])
            ->groupBy('match_id')
            ->map(fn (Collection $rows) => $rows->sortBy(fn ($p) => $this->priority($p->type))->first());

        if ($predictions->isEmpty()) {
            return ['total' => 0, 'matches' => []];
        }

        $fixtures = Fixture::query()
            ->whereIn('match_id', $predictions->keys())
            ->orderBy('match_time')
            ->orderBy('match_id')
            ->limit($limit)
            ->get();

        $ids = $fixtures->pluck('match_id')->all();
        $odds = APIodds::whereIn('match_id', $ids)->get()->keyBy('match_id');
        $forms = APIprediction::whereIn('match_id', $ids)->get(['match_id', 'home_form', 'away_form'])->keyBy('match_id');

        return [
            'total' => $predictions->count(),
            'matches' => $fixtures
                ->map(fn (Fixture $f) => $this->card($f, $predictions[$f->match_id], $odds[$f->match_id] ?? null, $forms[$f->match_id] ?? null))
                ->all(),
        ];
    }

    /**
     * Latest settled winning tips (winning_status = '1').
     *
     * @return list<array<string, mixed>>
     */
    public function recentWinnings(int $limit = 5): array
    {
        return Prediction::query()
            ->select('predictions.*')
            ->join('fixtures', 'fixtures.match_id', '=', 'predictions.match_id')
            ->with('fixture')
            ->where('predictions.vip_type', 'regular')
            ->where('predictions.winning_status', '1')
            ->orderByDesc('fixtures.match_date')
            ->orderByDesc('fixtures.match_time')
            ->orderByRaw('predictions.odds IS NULL OR predictions.odds = 0')
            ->limit($limit * 10)
            ->get()
            ->unique('match_id')
            ->take($limit)
            ->map(fn (Prediction $p) => [
                'home' => $p->fixture->home_name,
                'away' => $p->fixture->away_name,
                'home_logo' => $p->fixture->url_home_icon,
                'away_logo' => $p->fixture->url_away_icon,
                'score' => "{$p->fixture->home_goal} - {$p->fixture->away_goal}",
                'pick' => TipLabel::for($p->type, $p->tips),
                'odds' => $p->odds ?: '-',
                'date' => $p->fixture->match_date?->format('d/m/y'),
            ])
            ->values()
            ->all();
    }

    /** @return array<string, mixed> */
    private function card(Fixture $fixture, Prediction $prediction, ?APIodds $odds, ?APIprediction $form): array
    {
        $league = $fixture->match_data['league'] ?? [];
        $oddData = $odds?->odd_data ?? [];
        $status = (string) $fixture->match_status;
        $started = in_array($status, self::FINISHED, true) || in_array($status, self::LIVE, true);

        return [
            'id' => $fixture->match_id,
            'country' => $league['country'] ?? '',
            'league' => $league['name'] ?? '',
            'league_logo' => $league['logo'] ?? null,
            'home' => $fixture->home_name,
            'away' => $fixture->away_name,
            'home_logo' => $fixture->url_home_icon,
            'away_logo' => $fixture->url_away_icon,
            'time' => substr((string) $fixture->match_time, 0, 5),
            'status' => $status,
            'is_live' => in_array($status, self::LIVE, true),
            'home_goals' => $started ? $fixture->home_goal : null,
            'away_goals' => $started ? $fixture->away_goal : null,
            'home_form' => $this->form($form?->home_form),
            'away_form' => $this->form($form?->away_form),
            'odds' => [
                '1' => $this->odd($oddData['Home'] ?? null),
                'X' => $this->odd($oddData['Draw'] ?? null),
                '2' => $this->odd($oddData['Away'] ?? null),
            ],
            'prediction' => TipLabel::for($prediction->type, $prediction->tips),
            'prediction_odds' => $prediction->odds ? number_format((float) $prediction->odds, 2) : null,
            'result' => match ((string) $prediction->winning_status) {
                '1' => 'won',
                '2' => 'lost',
                default => null,
            },
        ];
    }

    private function priority(string $type): int
    {
        $index = array_search($type, self::TYPE_PRIORITY, true);

        return $index === false ? PHP_INT_MAX : $index;
    }

    /** @return list<string> */
    private function form(?string $form): array
    {
        $form = strtolower(substr(preg_replace('/[^WDL]/i', '', (string) $form), -5));

        return $form === '' ? [] : str_split($form);
    }

    private function odd(mixed $value): string
    {
        return is_numeric($value) && (float) $value > 1 ? number_format((float) $value, 2) : '-';
    }
}
