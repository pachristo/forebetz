<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VipRecentWinningResource\Pages;
use App\Models\PlanCategory;
use App\Models\VipRecentWinning;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VipRecentWinningResource extends Resource
{
    protected static ?string $model = VipRecentWinning::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Fixture and Predictions';

    protected static ?string $navigationLabel = 'Homepage VIP results';

    protected static ?string $modelLabel = 'VIP day result';

    protected static ?string $pluralModelLabel = 'Homepage VIP results';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('plan_category_id')
                ->label('Plan Category')
                ->options(
                    PlanCategory::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->searchable()
                ->required()
                ->helperText('Homepage strip uses the plan category marked “Show results on homepage” under Plan Categories.'),

            Forms\Components\DatePicker::make('winning_date')
                ->label('Date')
                ->required()
                ->native(false)
                ->displayFormat('d/m/Y')
                ->unique(
                    table: VipRecentWinning::class,
                    column: 'winning_date',
                    ignoreRecord: true,
                    modifyRuleUsing: function ($rule, Get $get) {
                        return $rule->where('plan_category_id', $get('plan_category_id'));
                    }
                )
                ->helperText('One result per plan category per day. Homepage shows the last 12 days (d/m).'),

            Forms\Components\Select::make('status')
                ->label('Result')
                ->options([
                    'won' => 'Won (green check)',
                    'lost' => 'Lost (red X)',
                    'pending' => 'Pending / Neutral (hollow circle)',
                ])
                ->required()
                ->default('won')
                ->helperText('Won = green check · Lost = red X · Pending = hollow circle on the homepage.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('planCategory.name')
                    ->label('Plan Category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('winning_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Result')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match (strtolower((string) $state)) {
                        'lost' => 'Lost',
                        'pending' => 'Pending',
                        default => 'Won',
                    })
                    ->color(fn (?string $state): string => match (strtolower((string) $state)) {
                        'lost' => 'danger',
                        'pending' => 'gray',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('winning_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('plan_category_id')
                    ->label('Plan')
                    ->options(PlanCategory::query()->orderBy('name')->pluck('name', 'id')->toArray()),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'won' => 'Won',
                        'lost' => 'Lost',
                        'pending' => 'Pending',
                    ]),
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
            'index' => Pages\ListVipRecentWinnings::route('/'),
            'create' => Pages\CreateVipRecentWinning::route('/create'),
            'edit' => Pages\EditVipRecentWinning::route('/{record}/edit'),
        ];
    }
}
