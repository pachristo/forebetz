<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesLikeGameCatResource;
use App\Filament\Resources\ManualSportFixtureResource\Pages;
use App\Models\ManualSportFixture;
use App\Support\TipSportOptions;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ManualSportFixtureResource extends Resource
{
    use AuthorizesLikeGameCatResource;

    protected static ?string $model = ManualSportFixture::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Fixture and Predictions';

    protected static ?string $navigationLabel = 'Other sports (manual fixtures)';

    protected static ?string $modelLabel = 'Other sport fixture (manual)';

    protected static ?string $pluralModelLabel = 'Other sports fixtures (manual)';

    protected static ?int $navigationSort = 52;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tip category & league')
                    ->description('Link this row to a non-football tip category URL; league name appears on the public table.')
                    ->schema([
                        Forms\Components\Select::make('game_cat_id')
                            ->label('Tip category page')
                            ->relationship(
                                'gameCat',
                                'title',
                                fn (Builder $query) => $query
                                    ->whereNotIn('slug', \App\Models\SeoPage::legalSitePageSlugs())
                                    ->where('sport', '!=', TipSportOptions::DEFAULT)
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('Only categories with Sport ≠ Football (the /{slug} page that lists these picks).'),
                        Forms\Components\TextInput::make('league_name')
                            ->label('League / competition name')
                            ->maxLength(191)
                            ->placeholder('e.g. NBA, MLB, NHL')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Event')
                    ->description('Sport, date & time on the first row; home team, market type, away team on the second.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('sport')
                                    ->options(ManualSportFixture::sportOptions())
                                    ->required()
                                    ->native(false),
                                Forms\Components\DatePicker::make('event_date')
                                    ->label('Date')
                                    ->required()
                                    ->native(false),
                                Forms\Components\TimePicker::make('event_time')
                                    ->label('Time')
                                    ->seconds(false)
                                    ->nullable(),
                            ]),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('home_team')
                                    ->label('Home team')
                                    ->required()
                                    ->maxLength(191)
                                    ->placeholder('Home'),
                                Forms\Components\TextInput::make('tip_type')
                                    ->label('Type / market')
                                    ->maxLength(120)
                                    ->placeholder('e.g. ML, spread, O/U'),
                                Forms\Components\TextInput::make('away_team')
                                    ->label('Away team')
                                    ->required()
                                    ->maxLength(191)
                                    ->placeholder('Away'),
                            ]),
                    ]),
                Forms\Components\Section::make('Pick & odds')
                    ->schema([
                        Forms\Components\Textarea::make('tips')
                            ->label('Tips / notes')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('odds')
                            ->label('Odds')
                            ->maxLength(64)
                            ->placeholder('e.g. 1.90')
                            ->prefixIcon('heroicon-o-calculator'),
                    ]),
                Forms\Components\Section::make('Result (optional)')
                    ->schema([
                        Forms\Components\TextInput::make('home_score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(999)
                            ->nullable(),
                        Forms\Components\TextInput::make('away_score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(999)
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('gameCat.title')
                    ->label('Tip category')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('league_name')
                    ->label('League')
                    ->placeholder('—')
                    ->searchable()
                    ->limit(28),
                Tables\Columns\TextColumn::make('sport')
                    ->formatStateUsing(fn (string $state): string => ManualSportFixture::sportOptions()[$state] ?? $state)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_time')
                    ->label('Time')
                    ->formatStateUsing(function ($state): string {
                        if ($state === null || $state === '') {
                            return '—';
                        }

                        return substr((string) $state, 0, 5);
                    }),
                Tables\Columns\TextColumn::make('home_team')
                    ->searchable()
                    ->limit(24),
                Tables\Columns\TextColumn::make('away_team')
                    ->searchable()
                    ->limit(24),
                Tables\Columns\TextColumn::make('tip_type')
                    ->label('Type')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('odds')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->getStateUsing(function (ManualSportFixture $record): string {
                        if ($record->home_score === null && $record->away_score === null) {
                            return '—';
                        }

                        return (string) ($record->home_score ?? '—').' - '.(string) ($record->away_score ?? '—');
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('event_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('sport')
                    ->options(ManualSportFixture::sportOptions()),
            ])
            ->actions([
                Tables\Actions\Action::make('setScores')
                    ->label('Set scores')
                    ->icon('heroicon-o-calculator')
                    ->modalSubmitActionLabel('Save scores')
                    ->fillForm(fn (ManualSportFixture $record): array => [
                        'home_score' => $record->home_score,
                        'away_score' => $record->away_score,
                    ])
                    ->form([
                        Forms\Components\TextInput::make('home_score')
                            ->label('Home score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(999),
                        Forms\Components\TextInput::make('away_score')
                            ->label('Away score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(999),
                    ])
                    ->action(function (ManualSportFixture $record, array $data): void {
                        $record->update([
                            'home_score' => $data['home_score'] === '' || $data['home_score'] === null ? null : (int) $data['home_score'],
                            'away_score' => $data['away_score'] === '' || $data['away_score'] === null ? null : (int) $data['away_score'],
                        ]);
                        Notification::make()
                            ->title('Scores updated')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManualSportFixtures::route('/'),
            'create' => Pages\CreateManualSportFixture::route('/create'),
            'edit' => Pages\EditManualSportFixture::route('/{record}/edit'),
        ];
    }
}
