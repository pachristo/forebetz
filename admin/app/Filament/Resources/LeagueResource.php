<?php

namespace App\Filament\Resources;

// use App\Filament\Resources\LeagueResource\Pages; (imported below)
use App\Models\League;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions as TableActions;
use App\Filament\Resources\LeagueResource\Pages;

class LeagueResource extends Resource
{
    protected static ?string $model = League::class;

    // switched to a safe, commonly available icon to avoid SvgNotFound errors
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Leagues & Countries';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('league_id')->disabled(),
                Forms\Components\TextInput::make('league_name')->required(),
                Forms\Components\TextInput::make('league_logo')->disabled(),
                Forms\Components\Select::make('country_id')
                    ->label('Country')
                    ->options(fn () => Country::pluck('country_name', 'country_id')->toArray())
                    ->searchable()
                    ->nullable(),
                Forms\Components\Toggle::make('feature')->label('Featured'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('league_id')->label('Code')->sortable(),
                Tables\Columns\TextColumn::make('league_name')->label('Name')->searchable(),
                Tables\Columns\ImageColumn::make('league_logo')->disk('public'),
                Tables\Columns\TextColumn::make('country.country_name')->label('Country')->searchable(),
                Tables\Columns\ToggleColumn::make('feature')->label('Featured'),
            ])
            ->filters([])
            ->actions([
                TableActions\Action::make('toggleFeature')
                    ->icon('heroicon-o-star')
                    ->action(fn ($record) => $record->update(['feature' => ! $record->feature]))
                    ->requiresConfirmation()
                    ->tooltip('Toggle Featured'),
                TableActions\EditAction::make(),
                TableActions\DeleteAction::make(),
            ])
            ->bulkActions([
                TableActions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeagues::route('/'),
            'create' => Pages\CreateLeague::route('/create'),
            'edit' => Pages\EditLeague::route('/{record}/edit'),
        ];
    }
}
