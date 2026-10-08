<?php

namespace App\Filament\Resources\ApiFootballBetResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BetValueNamesRelationManager extends RelationManager
{
    protected static string $relationship = 'valueNames';

    protected static ?string $title = 'Outcome value names';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Value name')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('label')
            ->paginated([10, 25, 50, 100]);
    }
}
