<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesLikeGameCatResource;
use App\Filament\Resources\HomeReadyInvestTicketResource\Pages;
use App\Models\HomeReadyInvestTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomeReadyInvestTicketResource extends Resource
{
    use AuthorizesLikeGameCatResource;

    protected static ?string $model = HomeReadyInvestTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $navigationLabel = 'Home: VIP result strip';

    protected static ?string $modelLabel = 'VIP result ticket';

    protected static ?string $pluralModelLabel = 'VIP result tickets';

    protected static ?int $navigationSort = 43;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\DatePicker::make('result_date')
                            ->label('Result date')
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('outcome')
                            ->label('Outcome')
                            ->options([
                                'won' => 'Won',
                                'lost' => 'Lost',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('day_label')
                            ->label('Day label (optional)')
                            ->maxLength(64)
                            ->placeholder('e.g. MONDAY — leave blank to use weekday from date'),
                        Forms\Components\TextInput::make('display_date_override')
                            ->label('Display date (optional)')
                            ->maxLength(64)
                            ->placeholder('e.g. 11/12/24 — leave blank to format from result date'),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Toggle::make('is_visible')
                            ->label('Visible on homepage')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('result_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('day_label')
                    ->label('Day label')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('outcome')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'won' => 'Won',
                        'lost' => 'Lost',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'won' => 'success',
                        'lost' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean()
                    ->label('Visible'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Visible'),
            ])
            ->actions([
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
            'index' => Pages\ListHomeReadyInvestTickets::route('/'),
            'create' => Pages\CreateHomeReadyInvestTicket::route('/create'),
            'edit' => Pages\EditHomeReadyInvestTicket::route('/{record}/edit'),
        ];
    }
}
