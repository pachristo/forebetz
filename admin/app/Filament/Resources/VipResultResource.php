<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VipResultResource\Pages;
use App\Models\VipResult;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;

class VipResultResource extends Resource
{
    protected static ?string $model = VipResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationLabel = 'VIP Results';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('match_id'),
                Forms\Components\Hidden::make('type'),

              Forms\Components\DatePicker::make('date')
    ->label('Date')
    ->displayFormat('Y-m-d')
    ->placeholder('YYYY-MM-DD')
    ->required()
    ->dehydrateStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('Y-m-d'))
                   ,

                Forms\Components\TextInput::make('odds')
                    ->label('Odds')
                    ->placeholder('e.g. 1.75')
                    ->required(false),

                Forms\Components\TextInput::make('time')
                    ->label('Time')
                    ->type('time')
                    ->placeholder('HH:MM')
                    ->required(false),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        '' => 'Select status',
                        'win' => 'Win',
                        'lost' => 'Lost',
                        'postponed' => 'Postponed',
                    ])
                    ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('match_id')->label('Match'),
                // Tables\Columns\TextColumn::make('type')->label('Type'),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('time')->label('Time')->sortable(),
                Tables\Columns\TextColumn::make('odds')->label('Odds')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'win' => 'Win',
                            'lost' => 'Lost',
                            'postponed' => 'Postponed',
                            default => ucfirst((string) $state),
                        };
                    })
                    ->colors([
                        'success' => fn ($state): bool => $state === 'win',
                        'danger' => fn ($state): bool => $state === 'lost',
                        'secondary' => fn ($state): bool => $state === 'postponed',
                    ])->sortable(),
            ])
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
     
                Tables\Actions\EditAction::make(),
                           Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVipResults::route('/'),
            'create' => Pages\CreateVipResult::route('/create'),
            'edit' => Pages\EditVipResult::route('/{record}/edit'),
        ];
    }
}
