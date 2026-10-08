<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PredictionResource\Pages;
use App\Models\Fixture;
use App\Models\PlanCategory;
use App\Models\Prediction;
use App\Support\PredictionResourceFilterOptions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

class PredictionResource extends Resource
{
    protected static ?string $model = Fixture::class;

    protected static ?string $navigationLabel = 'Predictions';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Fixture and Predictions';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('prediction')
            ->with(['prediction', 'league.country']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('match_id')->nullable(),
                TextInput::make('type')->nullable(),
                TextInput::make('vip_type')->nullable(),
                Textarea::make('tips')->nullable(),
                TextInput::make('odds')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('fixture')
                    ->label('Fixture')
                    ->html()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('home_name', 'like', "%{$search}%")
                            ->orWhere('away_name', 'like', "%{$search}%")
                            ->orWhereHas('league', function (Builder $q) use ($search) {
                                $q->where('league_name', 'like', "%{$search}%")
                                    ->orWhereHas('country', function (Builder $q) use ($search) {
                                        $q->where('country_name', 'like', "%{$search}%");
                                    });
                            });
                    })
                    ->getStateUsing(function ($record) {
                        $homeName = htmlspecialchars($record->home_name, ENT_QUOTES, 'UTF-8');
                        $awayName = htmlspecialchars($record->away_name, ENT_QUOTES, 'UTF-8');

                        $countryName = $record->league && $record->league->country ? htmlspecialchars($record->league->country->country_name, ENT_QUOTES, 'UTF-8') : '';
                        $leagueName = $record->league ? htmlspecialchars($record->league->league_name, ENT_QUOTES, 'UTF-8') : '';
                        $leagueInfoHtml = '<div style="font-size: 0.75rem; margin-top: 4px;">'
                                        . '<span style="color: #374151; font-weight: 500;">' . $countryName . '</span>'
                                        . '<span style="color: #6b7280; margin-left: 6px;">' . $leagueName . '</span>'
                                        . '</div>';

                        $score = '';
                        if (isset($record->home_goal) && isset($record->away_goal)) {
                            $score = '<div style="font-weight: 700; font-size: 1.1rem; color: #16a34a;">'
                                   . htmlspecialchars($record->home_goal, ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($record->away_goal, ENT_QUOTES, 'UTF-8')
                                   . '</div>';
                        }

                        $date = Carbon::parse($record->match_date)->format('Y-m-d');
                        $gameDateTime = Carbon::parse($date . ' ' . $record->match_time)
                                        ->format('D, M j, Y @ g:i A');

                        $timeHtml = '<div style="font-size:0.85rem; color:#6b7280; margin-top:4px">' . $gameDateTime . '</div>';

                        return '<div style="display:flex; align-items:center; justify-content:space-between; width:100%;">'
                             . '<div style="display:flex; flex-direction:column;">'
                             . '<div style="display:flex; align-items:center; font-weight: 600;">'
                             . '<span>' . $homeName . '</span>'
                             . '<span style="margin:0 8px; color:#9ca3af;">vs</span>'
                             . '<span>' . $awayName . '</span>'
                             . '</div>'
                             . $leagueInfoHtml
                             . $timeHtml
                             . '</div>'
                             . $score
                             . '</div>';
                    }),
                TextColumn::make('predictions')
                    ->label('Predictions')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $prediction = $record->prediction; // Access the related prediction model
                        if (!$prediction) {
                            return collect();
                        }

                        $predictionFields = [
                            'free' => ['label' => 'Free', 'color' => 'gray'],
                            '0_5_goal' => ['label' => 'O/U 0.5', 'color' => 'info'],
                            '1_5_goal' => ['label' => 'O/U 1.5', 'color' => 'info'],
                            '2_5_goal' => ['label' => 'O/U 2.5', 'color' => 'info'],
                            '3_5_goal' => ['label' => 'O/U 3.5', 'color' => 'info'],
                            'super_single' => ['label' => 'Super Single', 'color' => 'success'],
                            'banker' => ['label' => 'Banker', 'color' => 'warning'],
                            'acca' => ['label' => 'ACCA', 'color' => 'danger'],
                            'double_chance' => ['label' => 'DC', 'color' => 'primary'],
                            'home_win' => ['label' => 'HW', 'color' => 'success'],
                            'away_win' => ['label' => 'AW', 'color' => 'success'],
                            'dnb' => ['label' => 'DNB', 'color' => 'info'],
                             'btts' => ['label' => 'BTTS', 'color' => 'warning'],
                              'draw' => ['label' => 'Draw', 'color' => 'primary'],
                            'weh' => ['label' => 'WEH', 'color' => 'success'],
                            'sure_2_odds' => ['label' => 'Sure 2 Odds', 'color' => 'warning'],
                            'sure_3_odds' => ['label' => 'Sure 3 Odds', 'color' => 'warning'],
                            'sure_5_odds' => ['label' => 'Sure 5 Odds', 'color' => 'info'],
                            'sure_10_odds' => ['label' => 'Sure 10 Odds', 'color' => 'danger'],
                            'home_1_5_goals' => ['label' => 'Home 1.5 Goals', 'color' => 'success'],
                            'away_1_5_goals' => ['label' => 'Away 1.5 Goals', 'color' => 'warning'],
                        ];

                        // Add dynamic VIP labels so v{ID} types render properly and don't break styling/layout.
                        foreach (PlanCategory::allOrderedCached() as $cat) {
                            $id = $cat->id ?? null;
                            if (! $id) {
                                continue;
                            }
                            $vipLabel = trim((string) ($cat->name ?? $cat->title ?? ('VIP ' . $id)));
                            $predictionFields['v' . $id] = [
                                'label' => $vipLabel,
                                'color' => 'primary',
                            ];
                        }

                        $html = '<div style="display: flex; flex-wrap: wrap; gap: 4px;">';

                        foreach ($prediction as $field => $e) {
                            $type = (string) ($e->type ?? '');
                            $details = $predictionFields[$type] ?? [
                                'label' => $type !== '' ? ucwords(str_replace(['_', '-'], ' ', $type)) : 'Prediction',
                                'color' => 'gray',
                            ];
                            $label = $details['label'];
                            $color = $details['color'];
                            $value = htmlspecialchars((string) ($e->tips ?? ''), ENT_QUOTES, 'UTF-8');

                            $bgColor = match($color) {
                                'gray' => '#6b7280',
                                'info' => '#3b82f6',
                                'success' => '#16a34a',
                                'warning' => '#f59e0b',
                                'danger' => '#ef4444',
                                'primary' => '#8f73f2',
                                default => '#6b7280',
                            };

                            $html .= '<span style="background-color: ' . $bgColor . '; color: white; font-size: 0.75rem; font-weight: 500; padding: 2px 8px; border-radius: 9999px;">'
                                  . '<strong>' . $label . ':</strong> ' . $value
                                  . '</span>';

                        }

                        $html .= '</div>';

                        return $html;
                    }),

            ])
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('match_date')->label('Date')->live()->extraAttributes(['style' => 'width: 200px;']),
                    ])
                    ->query(function ($query, $data = null) {
                        // allow pre-filtering via request querystring ?match_date=YYYY-MM-DD
                        $reqDate = Request::query('match_date');
                        $formDate = $data['match_date'] ?? null;

                        // resolve date precedence: request -> form -> default to today
                        $date = $reqDate ?? $formDate ?? Carbon::now()->format('Y-m-d');

                        return $query->whereDate('match_date', $date);
                    }),
                // Types that exist in `predictions` only; labels from tip categories, plan categories, then fallbacks.
                Filter::make('prediction_type')
                    ->label('Prediction type')
                    ->form([
                        Select::make('prediction_type')
                            ->label('Prediction type')
                            ->options(fn (): array => PredictionResourceFilterOptions::groupedBySource())
                            ->searchable()
                            ->placeholder('All types')
                            ->helperText('Options are built from distinct `predictions.type` values. Names come from Tip categories and Plan categories where they match.'),
                    ])
                    ->query(function (Builder $query, array $data = []): Builder {
                        $value = $data['prediction_type'] ?? null;

                        if ($value === null || $value === '') {
                            return $query;
                        }

                        return $query->whereHas('prediction', function (Builder $q) use ($value): void {
                            $q->where('type', $value);
                        });
                    }),

            ], FiltersLayout::AboveContent)
            ->actions([
                Action::make('quick_edit')
                ->label('Free prediction')
                ->icon('heroicon-o-pencil')
                ->color('primary')

                ->extraAttributes(function ($record) {
                    $prefill = [];
                    $oddsBackfill = app(\App\Services\PredictionOddsBackfillService::class);
                    $matchId = (string) ($record->match_id ?? '');
                    $predCollection = $record->relationLoaded('prediction')
                        ? $record->prediction
                        : $record->prediction()->get();
                    foreach ($predCollection as $p) {
                        $type = $p->type ?? null;
                        if ($type === null || str_starts_with((string) $type, 'v')) {
                            continue;
                        }
                        $tips = trim((string) ($p->tips ?? ''));
                        if ($tips !== '') {
                            $prefill[$type] = $tips;
                        }
                        $odds = trim((string) ($p->odds ?? ''));
                        if ($tips !== '' && $oddsBackfill->isMissingOdds($odds)) {
                            $odds = $oddsBackfill->resolveForTip($matchId, $tips, false);
                        }
                        if ($odds !== '') {
                            $prefill[\App\Support\QuickPickFields::oddsPayloadKey($type)] = $odds;
                        }
                    }
                    $payload = [];

                    $payload['predictions'] = $prefill;
                    $payload['countryName'] = $record->league->country->country_name ?? ($record->league->country->name ?? '');
                    $payload['leagueName'] = $record->league->league_name ?? ($record->league->name ?? '—');
                    $payload['leagueName'] = $payload['countryName'] . ' - ' . $payload['leagueName'];
                    $payload['id'] = $record->match_id;
                    $payload['homeIcon'] = $record->home_icon ? (preg_match('#^https?://#', $record->home_icon) ? $record->home_icon : asset('storage/' . ltrim($record->home_icon, '/'))) : ($record->url_home_icon ?? null);
                    $payload['awayIcon'] = $record->away_icon ? (preg_match('#^https?://#', $record->away_icon) ? $record->away_icon : asset('storage/' . ltrim($record->away_icon, '/'))) : ($record->url_away_icon ?? null);
                    $payload['homeName'] = $record->home_name ?? '—';
                    $payload['awayName'] = $record->away_name ?? '—';
                    $payload['matchTime'] = $record->match_time ?? '';
                    $payload['matchDate'] = $record->match_date ?? '';
                    $payload['leagueLogo'] = $record->league && $record->league->country && !empty($record->league->country->country_logo) ? (preg_match('#^https?://#', $record->league->country->country_logo) ? $record->league->country->country_logo : asset('storage/' . ltrim($record->league->country->country_logo, '/'))) : null;

                    return [
                        'x-on:click.stop' => '',
                        'data-record' => json_encode($payload),
                        'onclick' => 'openFastEditModal(this.dataset.record); return false;',
                    ];
                }),
                Action::make('quick_edit1')
                    ->label('VIP prediction')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->extraAttributes(function ($record) {
                        $predCollection = $record->relationLoaded('prediction')
                            ? $record->prediction
                            : $record->prediction()->get();
                        $vipPredictions = $predCollection
                            ->filter(fn ($p) => str_starts_with((string) ($p->type ?? ''), 'v'))
                            ->keyBy('type');

                        $oddsBackfill = app(\App\Services\PredictionOddsBackfillService::class);
                        $matchId = (string) ($record->match_id ?? '');
                        $prefill = [];
                        foreach (PlanCategory::allOrderedCached() as $cat) {
                            $cid = $cat->id ?? null;
                            if (! $cid) {
                                continue;
                            }
                            $typeKey = 'v' . $cid;
                            $pred = $vipPredictions->get($typeKey);
                            $tips = $pred ? trim((string) ($pred->tips ?? '')) : '';
                            $odds = $pred ? trim((string) ($pred->odds ?? '')) : '';
                            if ($tips !== '' && $oddsBackfill->isMissingOdds($odds)) {
                                $odds = $oddsBackfill->resolveForTip($matchId, $tips, false);
                            }
                            $prefill['v_' . $cid] = $pred ? $pred->type : '';
                            $prefill['v_' . $cid . '_tips'] = $tips;
                            $prefill['v_' . $cid . '_odds'] = $odds;
                        }

                        $payload = [];

                        $payload['predictions'] = $prefill;
                        $payload['countryName'] = $record->league->country->country_name ?? ($record->league->country->name ?? '');
                        $payload['leagueName'] = $record->league->league_name ?? ($record->league->name ?? '—');
                        $payload['leagueName'] = $payload['countryName'] . ' - ' . $payload['leagueName'];
                        $payload['id'] = $record->match_id;
                        $payload['homeIcon'] = $record->home_icon ? (preg_match('#^https?://#', $record->home_icon) ? $record->home_icon : asset('storage/' . ltrim($record->home_icon, '/'))) : ($record->url_home_icon ?? null);
                        $payload['awayIcon'] = $record->away_icon ? (preg_match('#^https?://#', $record->away_icon) ? $record->away_icon : asset('storage/' . ltrim($record->away_icon, '/'))) : ($record->url_away_icon ?? null);
                        $payload['homeName'] = $record->home_name ?? '—';
                        $payload['awayName'] = $record->away_name ?? '—';
                        $payload['matchTime'] = $record->match_time ?? '';
                        $payload['matchDate'] = $record->match_date ?? '';
                        $payload['leagueLogo'] = $record->league && $record->league->country && !empty($record->league->country->country_logo) ? (preg_match('#^https?://#', $record->league->country->country_logo) ? $record->league->country->country_logo : asset('storage/' . ltrim($record->league->country->country_logo, '/'))) : null;

                        return [
                            'x-on:click.stop' => '',
                            'data-record' => json_encode($payload),
                            'onclick' => 'openFastEditModal1(this.dataset.record); return false;',
                        ];
                    }),
                Action::make('delete_predictions')
                    ->label('Delete Predictions')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->prediction()->delete();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPredictions::route('/'),
            'create' => Pages\CreatePrediction::route('/create'),
            'edit' => Pages\EditPrediction::route('/{record}/edit'),
        ];
    }
}
