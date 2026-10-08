<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanCategoryResource\Pages;
use App\Models\PlanCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlanCategoryResource extends Resource
{
    protected static ?string $model = PlanCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationGroup = 'Payments';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('title')->nullable(),
            Forms\Components\Textarea::make('benefits')->rows(4)->columnSpanFull(),
            Forms\Components\Toggle::make('show_results_on_homepage')
                ->label('Show results on homepage')
                ->helperText(
                    'Powers the homepage Premium Plan Results strip (last 12 days). Manage day results under Fixture and Predictions → Homepage VIP results. Only one category can be enabled.'
                )
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('title')->sortable(),
            Tables\Columns\IconColumn::make('show_results_on_homepage')
                ->label('Homepage results')
                ->boolean()
                ->sortable(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanCategories::route('/'),
            'create' => Pages\CreatePlanCategory::route('/create'),
            'edit' => Pages\EditPlanCategory::route('/{record}/edit'),
        ];
    }
}
