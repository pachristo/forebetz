<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeaderFooterCodeResource\Pages;
use App\Models\HeaderFooterCode;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Resources\Table as ResourcesTable;
use Filament\Tables;
use Filament\Tables\Table as TablesTable;

class HeaderFooterCodeResource extends Resource
{
    protected static ?string $model = HeaderFooterCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Header/Footer Code';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Grid::make(2)->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'header' => 'Header',
                                'footer' => 'Footer',
                                'body' => 'Body',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('label')
                            ->maxLength(255)
                            ->nullable(),
                    ]),

                    Forms\Components\Textarea::make('code')
                        ->label('Code (HTML/JS)')
                        ->rows(8)
                        ->placeholder('Paste header/footer HTML or JS code here')
                        ->nullable(),
                ]),
            ]);
    }

    public static function table(TablesTable $table): TablesTable
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')->sortable(),
                Tables\Columns\TextColumn::make('label')->limit(30),
                Tables\Columns\TextColumn::make('created_at')->date()->label('Created'),
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
            'index' => Pages\ListHeaderFooterCodes::route('/'),
            'create' => Pages\CreateHeaderFooterCode::route('/create'),
            'edit' => Pages\EditHeaderFooterCode::route('/{record}/edit'),
        ];
    }
}
