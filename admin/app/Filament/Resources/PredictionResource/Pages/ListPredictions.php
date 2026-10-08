<?php

namespace App\Filament\Resources\PredictionResource\Pages;

use App\Filament\Resources\PredictionResource;
use App\Models\Fixture;
use App\Models\PlanCategory;
use App\Models\Prediction;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Facades\FilamentView;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;

class ListPredictions extends ListRecords
{
    protected static string $resource = PredictionResource::class;

    /** @var string|null Snapshot of date filter before tableFilters updates (see updatingTableFilters). */
    private ?string $predictionDateFilterBefore = null;

    public function updatingTableFilters(mixed $value): void
    {
        $this->predictionDateFilterBefore = data_get($this->tableFilters, 'date.match_date');
    }

    public function updatedTableFilters(): void
    {
        $before = $this->predictionDateFilterBefore;
        $after = data_get($this->tableFilters, 'date.match_date');
        if ($after === null || $after === '') {
            return;
        }
        try {
            $afterNormalized = Carbon::parse((string) $after)->format('Y-m-d');
        } catch (\Throwable) {
            return;
        }
        $beforeNormalized = null;
        if ($before !== null && $before !== '') {
            try {
                $beforeNormalized = Carbon::parse((string) $before)->format('Y-m-d');
            } catch (\Throwable) {
                $beforeNormalized = (string) $before;
            }
        }
        if ($beforeNormalized === $afterNormalized) {
            return;
        }

        $url = $this->predictionListReloadUrl($afterNormalized);
        $this->js('window.location.assign('.json_encode($url).')');
    }

    /**
     * Full page URL with the same Filament #[Url] query state + match_date (no SPA redirect).
     */
    private function predictionListReloadUrl(string $matchDate): string
    {
        $query = [];

        $query['match_date'] = $matchDate;

        if (($this->isTableReordering ?? false) === true) {
            $query['isTableReordering'] = '1';
        }
        if ($this->tableFilters !== null) {
            $query['tableFilters'] = $this->tableFilters;
        }
        if ($this->tableGrouping !== null && $this->tableGrouping !== '') {
            $query['tableGrouping'] = $this->tableGrouping;
        }
        if ($this->tableGroupingDirection !== null && $this->tableGroupingDirection !== '') {
            $query['tableGroupingDirection'] = $this->tableGroupingDirection;
        }
        if (($this->tableSearch ?? '') !== '') {
            $query['tableSearch'] = (string) $this->tableSearch;
        }
        if ($this->tableSortColumn !== null && $this->tableSortColumn !== '') {
            $query['tableSortColumn'] = $this->tableSortColumn;
        }
        if ($this->tableSortDirection !== null && $this->tableSortDirection !== '') {
            $query['tableSortDirection'] = $this->tableSortDirection;
        }
        if ($this->activeTab !== null && $this->activeTab !== '') {
            $query['activeTab'] = $this->activeTab;
        }

        $base = PredictionResource::getUrl('index');
        $qs = http_build_query($query);

        return $qs === '' ? $base : $base.(str_contains($base, '?') ? '&' : '?').$qs;
    }

    public function mount(): void
    {
        parent::mount();

        // Deep links like ?match_date=YYYY-MM-DD must win over session-stored filters.
        $urlDate = request()->query('match_date');
        if (is_string($urlDate) && $urlDate !== '') {
            try {
                $normalized = Carbon::parse($urlDate)->format('Y-m-d');
                $this->tableFilters = array_replace_recursive($this->tableFilters ?? [], [
                    'date' => [
                        'match_date' => $normalized,
                        'isActive' => true,
                    ],
                ]);
                $this->activeTab = 'all';
            } catch (\Throwable $_) {
                // ignore invalid date
            }
        }

        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): HtmlString => new HtmlString(
                '<style>
                    .fi-ta-tabs, .fi-tabs {
                        flex-wrap: wrap !important;
                        row-gap: .5rem !important;
                        overflow: visible !important;
                    }
                </style>'
            )
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $selectedDate = $this->resolveAppliedDate();
        $baseFixtureQuery = Fixture::query();
        if (! empty($selectedDate)) {
            $baseFixtureQuery->whereDate('match_date', $selectedDate);
        }

        $tabs = [
            'all' => Tab::make('All')
                ->badge((clone $baseFixtureQuery)->whereHas('prediction')->count())
                ->modifyQueryUsing(
                    fn (Builder $query): Builder => $query
                        ->when(! empty($selectedDate), fn (Builder $q): Builder => $q->whereDate('match_date', $selectedDate))
                        ->whereHas('prediction')
                ),
        ];

        $knownLabels = [
            'free' => 'Free',
            '0_5_goal' => 'O/U 0.5',
            '1_5_goal' => 'O/U 1.5',
            '2_5_goal' => 'O/U 2.5',
            '3_5_goal' => 'O/U 3.5',
            'super_single' => 'Super Single',
            'banker' => 'Banker',
            'acca' => 'ACCA',
            'double_chance' => 'Double Chance',
            'home_win' => 'Home Win',
            'away_win' => 'Away Win',
            'dnb' => 'DNB',
            'btts' => 'BTTS',
            'draw' => 'Draw',
            'weh' => 'WEH',
            'sure_2_odds' => 'Sure 2 Odds',
            'sure_3_odds' => 'Sure 3 Odds',
            'sure_5_odds' => 'Sure 5 Odds',
            'sure_10_odds' => 'Sure 10 Odds',
            'home_1_5_goals' => 'Home 1.5 Goals',
            'away_1_5_goals' => 'Away 1.5 Goals',
        ];

        $vipLabelByType = [];
        try {
            $categories = PlanCategory::query()->select(['id', 'name', 'title'])->orderBy('id')->get();
            foreach ($categories as $category) {
                $id = $category->id ?? null;
                if (! $id) {
                    continue;
                }
                $label = trim((string) ($category->name ?? $category->title ?? ('Plan ' . $id)));
                $vipLabelByType['v' . $id] = 'VIP: ' . $label;
            }
        } catch (\Throwable $_) {
            // ignore and continue without VIP labels
        }

        try {
            $types = Prediction::query()
                ->select('type')
                ->distinct()
                ->orderBy('type')
                ->pluck('type')
                ->filter()
                ->values();
        } catch (\Throwable $_) {
            $types = collect();
        }

        foreach ($types as $type) {
            $label = $vipLabelByType[$type] ?? $knownLabels[$type] ?? ucwords(str_replace(['_', '-'], ' ', (string) $type));

            $tabs['type_' . $type] = Tab::make($label)
                ->badge(
                    (clone $baseFixtureQuery)
                        ->whereHas('prediction', fn (Builder $query): Builder => $query->where('type', $type))
                        ->count()
                )
                ->modifyQueryUsing(
                    fn (Builder $query): Builder => $query
                        ->when(! empty($selectedDate), fn (Builder $q): Builder => $q->whereDate('match_date', $selectedDate))
                        ->whereHas('prediction', fn (Builder $sub): Builder => $sub->where('type', $type))
                );
        }

        return $tabs;
    }

    private function resolveAppliedDate(): ?string
    {
        // 1) Live filter state first (user's date pick). Must beat a stale ?match_date= left in the URL.
        $filterDate = data_get($this->tableFilters ?? [], 'date.match_date');
        if (empty($filterDate)) {
            $filterDate = data_get($this->getTableFilters(), 'date.match_date');
        }
        if (! empty($filterDate)) {
            try {
                return Carbon::parse((string) $filterDate)->format('Y-m-d');
            } catch (\Throwable $_) {
                // fall through
            }
        }

        // 2) Standalone ?match_date= (bookmarks / shared links)
        $requestDate = Request::query('match_date');
        if (is_string($requestDate) && $requestDate !== '') {
            try {
                return Carbon::parse($requestDate)->format('Y-m-d');
            } catch (\Throwable $_) {
                // fall through
            }
        }

        // 3) tableFilters in query string only (first paint before mount hydrates)
        $requestFilterDate = data_get(Request::query('tableFilters', []), 'date.match_date')
            ?? Request::input('tableFilters.date.match_date');

        $resolved = $requestFilterDate ?? now()->format('Y-m-d');
        if (empty($resolved)) {
            return now()->format('Y-m-d');
        }

        try {
            return Carbon::parse((string) $resolved)->format('Y-m-d');
        } catch (\Throwable $_) {
            return now()->format('Y-m-d');
        }
    }
}
