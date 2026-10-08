<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiFootballBetResource\Pages;
use App\Filament\Resources\ApiFootballBetResource\RelationManagers\BetValueNamesRelationManager;
use App\Models\ApiFootballBet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApiFootballBetResource extends Resource
{
    protected static ?string $model = ApiFootballBet::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Fixture and Predictions';

    protected static ?string $navigationLabel = 'API bet types';

    protected static ?string $modelLabel = 'API bet type';

    protected static ?string $pluralModelLabel = 'API bet types';

    protected static ?int $navigationSort = 45;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [
            BetValueNamesRelationManager::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('bet_id')->label('Bet ID')->disabled(),
            Forms\Components\TextInput::make('name')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bet_id')
                    ->label('Bet ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('bet_id')
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiFootballBets::route('/'),
            'view' => Pages\ViewApiFootballBet::route('/{record}'),
        ];
    }
}
