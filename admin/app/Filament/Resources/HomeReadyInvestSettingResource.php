<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesLikeGameCatResource;
use App\Filament\Resources\HomeReadyInvestSettingResource\Pages;
use App\Models\HomeReadyInvestSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class HomeReadyInvestSettingResource extends Resource
{
    use AuthorizesLikeGameCatResource;

    protected static ?string $model = HomeReadyInvestSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $navigationLabel = 'Home: Ready to invest (settings)';

    protected static ?string $modelLabel = 'Ready to invest setting';

    protected static ?string $pluralModelLabel = 'Ready to invest settings';

    protected static ?int $navigationSort = 40;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Left column — Ready to invest')
                    ->schema([
                        Forms\Components\TextInput::make('heading')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('body')
                            ->label('Body text')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Right panel — VIP strip')
                    ->schema([
                        Forms\Components\TextInput::make('vip_strip_title')
                            ->label('Strip title')
                            ->required()
                            ->maxLength(120),
                        Forms\Components\TextInput::make('telegram_cta')
                            ->label('Telegram button label')
                            ->required()
                            ->maxLength(120),
                        Forms\Components\TextInput::make('telegram_url')
                            ->label('Telegram button URL')
                            ->maxLength(2048)
                            ->helperText('Optional full https URL. If empty, the front uses the Telegram link from Site configuration.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('heading')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('vip_strip_title')
                    ->label('VIP strip title')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('id')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeReadyInvestSettings::route('/'),
            'create' => Pages\CreateHomeReadyInvestSetting::route('/create'),
            'edit' => Pages\EditHomeReadyInvestSetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        if (HomeReadyInvestSetting::query()->exists()) {
            return false;
        }

        return static::authorizesWithGameCatPermissions('create_game::cat');
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
