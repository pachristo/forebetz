<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
use App\Models\PlanCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Payments';

    public static function form(Form $form): Form
    {
      return $form->schema([
            // Forms\Components\TextInput::make('name')->required()->label('Plan name'),
            // customizable variation text (free text)
            Forms\Components\TextInput::make('variation')->label('Variation')->placeholder('e.g. 1 month, 2 months, 1 week, 2 years')->required()->columnSpanFull(),
            Forms\Components\Select::make('plan_category_id')
                ->label('Category')
                ->options(fn () => PlanCategory::orderBy('name')->pluck('name', 'id')->toArray())
                ->searchable()
                ->placeholder('Select category')->columnSpanFull(),
            Forms\Components\Grid::make(4)->schema([
                Forms\Components\TextInput::make('price_ngn')->label('Nigerian NGN')->numeric(),
                Forms\Components\TextInput::make('price_ghs')->label('Ghanaian GHS')->numeric(),
                Forms\Components\TextInput::make('price_xaf')->label('Central African XAF')->numeric(),
                Forms\Components\TextInput::make('price_rwf')->label('Rwandan RWF')->numeric(),
                Forms\Components\TextInput::make('price_zar')->label('South African ZAR')->numeric(),
                Forms\Components\TextInput::make('price_kes')->label('Kenyan KES')->numeric(),
                Forms\Components\TextInput::make('price_tzs')->label('Tanzanian TZS')->numeric(),
                Forms\Components\TextInput::make('price_usd')->label('USD Dollar')->numeric(),
                Forms\Components\TextInput::make('price_mwk')->label('Malawian MWK')->numeric(),
                Forms\Components\TextInput::make('price_ugx')->label('Ugandan UGX')->numeric(),
                Forms\Components\TextInput::make('price_zmw')->label('Zambian ZMW')->numeric(),
            ]),
            Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
            Forms\Components\TextInput::make('selar_payment_link')
                ->label('Selar payment link')
                ->url()
                ->maxLength(500)
                ->placeholder('https://selar.com/…')
                ->helperText('Optional. When set, the VIP “Get started” button on the front opens this Selar checkout URL instead of the internal /pay page.')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
            Tables\Columns\TextColumn::make('category.name')->label('Name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('variation')->label('Variation')->sortable(),
            Tables\Columns\TextColumn::make('price_usd')->label('USD Price')->sortable(),
            Tables\Columns\IconColumn::make('selar_payment_link')
                ->label('Selar')
                ->boolean()
                ->getStateUsing(fn (Plan $record): bool => filled(trim((string) $record->selar_payment_link))),
        ])->filters([])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
