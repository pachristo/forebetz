<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SingleBetTextResource\Pages;
use App\Models\SingleBetText;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class SingleBetTextResource extends Resource
{
    protected static ?string $model = SingleBetText::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Single Bet Texts';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 4;

    protected static bool $shouldRegisterNavigation = false;

    public static function canCreate(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 Forms\Components\DatePicker::make('date')->label('Date')->required(),
                TextInput::make('title')->required()->maxLength(255),
                TinyEditor::make('text')->required()->label('Content') ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('title')->limit(50)->searchable()->sortable(),

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
            'index' => Pages\ListSingleBetTexts::route('/'),
            'create' => Pages\CreateSingleBetText::route('/create'),
            'edit' => Pages\EditSingleBetText::route('/{record}/edit'),
        ];
    }
}
