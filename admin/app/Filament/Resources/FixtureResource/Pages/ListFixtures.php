<?php

namespace App\Filament\Resources\FixtureResource\Pages;

use App\Filament\Resources\FixtureResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Support\Facades\FilamentView;
use Filament\Forms;
use App\Models\Fixture;
use App\Models\PlanCategory;
use App\Models\Prediction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;

class ListFixtures extends ListRecords
{
    protected static string $resource = FixtureResource::class;

    public $search = '';

    /** @var string|null Snapshot of date filter before tableFilters updates (see updatingTableFilters). */
    private ?string $fixtureDateFilterBefore = null;

    public function updatingTableFilters(mixed $value): void
    {
        $this->fixtureDateFilterBefore = data_get($this->tableFilters, 'date.match_date');
    }

    public function updatedTableFilters(): void
    {
        $before = $this->fixtureDateFilterBefore;
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

        $url = $this->fixtureListReloadUrl($afterNormalized);
        $this->js('window.location.assign('.json_encode($url).')');
    }

    /**
     * Full page URL with the same Filament #[Url] query state + match_date (no SPA redirect).
     */
    private function fixtureListReloadUrl(string $matchDate): string
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

        $base = FixtureResource::getUrl('index');
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
                // Avoid landing on a prediction sub-tab (empty until tips exist) when opening a date deep link.
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

    public function getTabs(): array
    {
        $selectedDate = $this->resolveAppliedDate();
        $baseFixtureQuery = Fixture::query();
        if (! empty($selectedDate)) {
            $baseFixtureQuery->whereDate('match_date', $selectedDate);
        }

        // "All" = every fixture for the date (API import does not create predictions — old whereHas('prediction') hid them).
        $tabs = [
            'all' => Tab::make('All')
                ->badge((clone $baseFixtureQuery)->count())
                ->modifyQueryUsing(
                    fn (Builder $query): Builder => $query->when(
                        ! empty($selectedDate),
                        fn (Builder $q): Builder => $q->whereDate('match_date', $selectedDate)
                    )
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
        foreach (PlanCategory::allOrderedCached() as $category) {
            $id = $category->id ?? null;
            if (! $id) {
                continue;
            }
            $label = trim((string) ($category->name ?? $category->title ?? ('Plan ' . $id)));
            $vipLabelByType['v' . $id] = 'VIP: ' . $label;
        }

        try {
            $types = Prediction::query()
                ->select('type')
                ->whereIn('match_id', function ($q) use ($selectedDate) {
                    $q->from('fixtures')
                        ->select('match_id')
                        ->when(! empty($selectedDate), fn ($sub) => $sub->whereDate('match_date', $selectedDate));
                })
                ->distinct()
                ->orderBy('type')
                ->limit(60)
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

    /**
     * Run fixture import in the background (no queue job). Equivalent shell:
     * cd {base_path} && nohup php artisan fetch:fixtures {Y-m-d} > storage/logs/fixtures-fetch-{date}-{ts}.log 2>&1 &
     */

    protected function startFixtureFetchInBackground(string $dateString): bool
    {
        $backendPath = base_path();
        $logFilename = 'fixtures-fetch-'.$dateString.'-'.time().'.log';
        $cmd = sprintf(
            'cd %s && nohup php artisan fetch:fixtures %s > storage/logs/%s 2>&1 & echo $!',
            escapeshellarg($backendPath),
            escapeshellarg($dateString),
            escapeshellarg($logFilename)
        );
        exec($cmd, $output, $ret);
        $pid = trim($output[0] ?? '');
        if ($pid === '' || ! ctype_digit($pid)) {
            return false;
        }

        $statusDir = storage_path('app/fixture_import');
        if (! is_dir($statusDir)) {
            mkdir($statusDir, 0755, true);
        }
        file_put_contents(
            $statusDir.'/status_'.$dateString.'.json',
            json_encode([
                'pid' => $pid,
                'log' => $logFilename,
                'started_at' => now()->toDateTimeString(),
                'date' => $dateString,
            ])
        );

        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('fetchFixturesBackground')
                ->label('Fetch fixtures (background)')
                ->form([
                    Forms\Components\DatePicker::make('date')->label('Date')->required(),
                ])
                ->action(function (array $data) {
                    $date = $data['date'];
                    $dateString = $date instanceof Carbon ? $date->format('Y-m-d') : Carbon::parse($date)->format('Y-m-d');
                    if ($this->startFixtureFetchInBackground($dateString)) {
                        $this->notify('success', 'Background fetch started for '.$dateString.' (log under storage/logs; status in app/fixture_import)');
                    } else {
                        $this->notify('danger', 'Failed to start background fetch (shell returned no PID)');
                    }
                })
                ->requiresConfirmation(),

            Actions\Action::make('viewFetchStatus')
                ->label('View Fetch Status')
                ->form([
                    Forms\Components\TextInput::make('date')->label('Date (Y-m-d)')->required()->default(\Carbon\Carbon::now()->format('Y-m-d')),
                ])
                ->action(function (array $data) {
                    $dateString = $data['date'];
                    $statusFile = storage_path('app/fixture_import/status_'.$dateString.'.json');
                    if (! file_exists($statusFile)) {
                        $this->notify('warning', 'No status found for ' . $dateString);
                        return;
                    }
                    $data = json_decode(file_get_contents($statusFile), true) ?: [];
                    $total = $data['total'] ?? 0; $processed = $data['processed'] ?? 0; $created = $data['created'] ?? 0; $skipped = $data['skipped'] ?? 0; $status = $data['status'] ?? 'unknown';
                    $this->notify('info', "Date: $dateString\nStatus: $status\nTotal: $total\nProcessed: $processed\nCreated: $created\nSkipped: $skipped");
                }),
        ];
    }

    protected function notify($type, $message)
    {
        // simple wrapper for Filament notifications
        \Filament\Notifications\Notification::make()->{$type}()->title($message)->send();
    }

    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        $query = Fixture::query()->select(Fixture::filamentIndexColumns());
        $query->with([
            'league.country',
            'prediction' => function ($q): void {
                $q->select([
                    'id',
                    'match_id',
                    'type',
                    'vip_type',
                    'tips',
                    'odds',
                ]);
            },
            // Same constrained load as FixtureResource::getEloquentQuery — avoid hydrating multi‑MB JSON per row.
            'odds' => function ($q): void {
                $q->select([
                    'id',
                    'match_id',
                    'odds1',
                    'odds2',
                    'oddsx',
                    'api_bets_count',
                    'api_odd_values_count',
                    'is_predicted',
                    'created_at',
                    'updated_at',
                ]);
            },
        ]);

        $q = $this->getTableSearch();
        if (! empty($q)) {
            $term = '%'.str_replace('%', '\\%', $q).'%';
            $query->where(function ($builder) use ($term) {
                $builder->where('match_id', 'like', $term)
                    ->orWhere('home_name', 'like', $term)
                    ->orWhere('away_name', 'like', $term)
                    ->orWhereHas('league', function ($lq) use ($term) {
                        $lq->where('league_name', 'like', $term)
                          ->orWhereHas('country', function ($cq) use ($term) {
                              $cq->where('country_name', 'like', $term);
                          });
                    });
            });
        }

        // Apply the exact same date source used by tab counts.
        $selectedDate = $this->resolveAppliedDate();
        if (! empty($selectedDate)) {
            $query->whereDate('match_date', $selectedDate);
        }

        // Auto-fetch if no fixtures for the selected date (use resolve only; do not override URL date with session form state)
        if ($selectedDate && !Cache::has('fetched_' . $selectedDate)) {
            $count = Fixture::whereDate('match_date', $selectedDate)->count();
            if ($count == 0) {
                if ($this->startFixtureFetchInBackground($selectedDate)) {
                    Cache::put('fetched_'.$selectedDate, true, 3600); // prevent multiple fetches for 1 hour
                }
            }
        }

        return $query;
    }

    public function getTitle(): \Illuminate\Contracts\Support\Htmlable|string
    {
        $q = $this->getTableSearch();
        if (! empty($q)) {
            return 'Fixtures — search: ' . $q;
        }
        $d = $this->resolveAppliedDate();
        if (! empty($d)) {
            return 'Fixtures for ' . $d;
        }
        return 'Fixtures';
    }

    private function resolveAppliedDate(): ?string
    {
        // 1) Live filter state first (user’s date pick). Must beat a stale ?match_date= left in the URL
        //    after we stopped stripping params with redirect().
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
