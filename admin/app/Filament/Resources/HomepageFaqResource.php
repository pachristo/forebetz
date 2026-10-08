<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomepageFaqResource\Pages;
use App\Models\HomepageFaq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomepageFaqResource extends Resource
{
    protected static ?string $model = HomepageFaq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $navigationLabel = 'Homepage FAQs';

    protected static ?string $modelLabel = 'Homepage FAQ';

    protected static ?string $pluralModelLabel = 'Homepage FAQs';

    protected static ?int $navigationSort = 36;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('question')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('answer')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Lower numbers appear first on the homepage.'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Visible on homepage'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('question')
                    ->searchable()
                    ->wrap()
                    ->limit(80),
                Tables\Columns\TextColumn::make('answer')
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
            'index' => Pages\ListHomepageFaqs::route('/'),
            'create' => Pages\CreateHomepageFaq::route('/create'),
            'edit' => Pages\EditHomepageFaq::route('/{record}/edit'),
        ];
    }
}
