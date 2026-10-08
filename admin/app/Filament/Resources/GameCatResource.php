<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameCatResource\Pages;
use App\Models\GameCat;
use App\Models\SeoPage;
use App\Support\FrontUrl;
use App\Support\TipCategoryPredictionPresets;
use App\Support\TipSportOptions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class GameCatResource extends Resource
{
    protected static ?string $model = GameCat::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $navigationLabel = 'Tip categories';

    protected static ?string $modelLabel = 'Tip category';

    protected static ?string $pluralModelLabel = 'Tip categories';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sport')
                    ->description('Which sport this tip category is for. Football stays the default for classic soccer markets.')
                    ->schema([
                        Forms\Components\Select::make('sport')
                            ->label('Sport')
                            ->options(TipSportOptions::selectOptions())
                            ->default(TipSportOptions::DEFAULT)
                            ->required()
                            ->native(false)
                            ->rules([Rule::in(array_keys(TipSportOptions::selectOptions()))]),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('System — Free pick')
                    ->description('This category is required for free picks. Its URL and prediction key are locked; you can still edit title and page copy.')
                    ->schema([])
                    ->visible(fn (?Model $record): bool => $record instanceof GameCat && $record->is_system)
                    ->icon('heroicon-o-shield-check')
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('URL & prediction mapping')
                    ->description('Tip category pages power `/{slug}` together with Static pages. Choose the prediction type that matches `predictions.type` (odds pipeline). Legal URLs are reserved — manage those under Legal & site pages.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($set, $state, ?Model $record) {
                                if ($record instanceof GameCat && $record->is_system) {
                                    return;
                                }
                                $set('slug', Str::slug((string) $state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules([Rule::notIn(SeoPage::legalSitePageSlugs())])
                            ->disabled(fn (?Model $record): bool => $record instanceof GameCat
                                && (in_array((string) $record->slug, SeoPage::legalSitePageSlugs(), true) || $record->is_system))
                            ->helperText('Public URL segment. Reserved for legal/site pages: '.implode(', ', SeoPage::legalSitePageSlugs()).'.'),

                        Forms\Components\Select::make('prediction_slug')
                            ->label('Prediction type')
                            ->options(TipCategoryPredictionPresets::groupedSelectOptions())
                            ->searchable()
                            ->required()
                            ->live()
                            ->native(false)
                            ->disabled(fn (?Model $record): bool => $record instanceof GameCat && $record->is_system)
                            ->helperText('Pick the market whose odds/tips this page represents. `cat_type` is synced to this key on save.'),

                        Forms\Components\TextInput::make('prediction_slug_custom')
                            ->label('Custom prediction key')
                            ->maxLength(120)
                            ->regex('/^[a-z0-9_]+$/')
                            ->helperText('Lowercase letters, numbers, underscores only (e.g. corners, my_vip_pack).')
                            ->live(onBlur: true)
                            ->visible(fn (Get $get): bool => $get('prediction_slug') === TipCategoryPredictionPresets::CUSTOM_SENTINEL)
                            ->required(fn (Get $get): bool => $get('prediction_slug') === TipCategoryPredictionPresets::CUSTOM_SENTINEL)
                            ->disabled(fn (?Model $record): bool => $record instanceof GameCat && $record->is_system),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Home category — tips labels')
                    ->description('Shown when `cat_type` is `home` (custom prediction key `home`). Used in Quick Edit and on the site for the tips table and action button.')
                    ->schema([
                        Forms\Components\TextInput::make('tips_table_name')
                            ->label('Tips table name')
                            ->maxLength(255)
                            ->helperText('Legacy/alternate tips table label. Fixture list heading (On-page copy) is what the public panel uses.'),

                        Forms\Components\TextInput::make('tips_button_name')
                            ->label('Tips button name')
                            ->maxLength(255)
                            ->helperText('Label for the related tips button (e.g. CTA text).'),
                    ])
                    ->columns(1)
                    ->visible(fn (Get $get, ?Model $record): bool => self::formShowsHomeTipsLabels($get, $record))
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('Quick Edit modal (fixtures)')
                    ->description('Controls how staff enter the tip value when quick-editing a fixture. Does not change the prediction key above. Custom prediction keys use the same homepage tip dropdown in Quick Edit. Correct score always uses a text field.')
                    ->schema([
                        Forms\Components\ToggleButtons::make('prediction_entry_mode')
                            ->label('Tip value field')
                            ->options([
                                'preset' => 'Dropdown (predefined options)',
                                'manual' => 'Text (manual entry)',
                            ])
                            ->inline()
                            ->default('preset')
                            ->helperText('Catalog prediction types: choose dropdown vs text. Banker, single bet, custom keys, and homepage always use the homepage tip dropdown in Quick Edit.'),
                    ])
                    ->columns(1)
                    ->visible(fn (?Model $record): bool => ! ($record instanceof GameCat && ($record->is_system || $record->isHomeCatType() || strtolower(trim((string) $record->slug)) === 'homeslug')))
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('Free tips store (hub)')
                    ->description('Tiles on the public `/free-tips` page. Toggle on, set sort order (lower first), and optional short label — if empty, the category title is used.')
                    ->schema([
                        Forms\Components\Toggle::make('show_on_free_tips_store')
                            ->label('Show on free tips store')
                            ->default(false)
                            ->inline(false),

                        Forms\Components\TextInput::make('store_grid_sort_order')
                            ->label('Sort order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(9999)
                            ->helperText('Lower numbers appear first in the grid.'),

                        Forms\Components\TextInput::make('tips_button_name')
                            ->label('Tile / button label')
                            ->maxLength(255)
                            ->helperText('Short label on the store tile (e.g. Sure 2 Odds). Leave blank to use the category title. Same field as the home tips CTA label when editing the home category.'),
                    ])
                    ->columns(1)
                    ->visible(fn (Get $get, ?Model $record): bool => self::formShowsStoreGridSection($get, $record))
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('On-page copy')
                    ->schema([
                        Forms\Components\TextInput::make('fixture_heading')
                            ->label('Fixture list heading')
                            ->maxLength(255)
                            ->helperText('Shown above the fixtures on this tip page (and homepage for Free picks). Leave empty to hide. Examples: Free picks, 2.5 Goals, Both Team to score, Correct score.')
                            ->visible(fn (Get $get, ?Model $record): bool => self::formShowsFixtureHeading($get, $record)),

                        Forms\Components\Textarea::make('head1')
                            ->helperText('Homepage: highlighted (orange) start of the hero heading.')
                            ->maxLength(555),

                        Forms\Components\Textarea::make('head2')
                            ->helperText('Homepage: rest of the hero heading.')
                            ->maxLength(555),

                        Forms\Components\Textarea::make('descc')
                            ->label('Hero intro')
                            ->helperText('Homepage: paragraph under the hero heading.')
                            ->rows(3)
                            ->maxLength(900)
                            ->columnSpanFull(),

                        TinyEditor::make('content')
                            ->label('Content')->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\Textarea::make('meta_keywords')
                            ->maxLength(900)
                            ->rows(3)->columnSpanFull(),

                        Forms\Components\Textarea::make('meta_description')
                            ->maxLength(900)
                            ->rows(3)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->description(fn ($record) => $record->meta_description ? Str::limit($record->meta_description, 50) : null)
                    ->searchable()
                    ->limit(50)
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),

                Tables\Columns\TextColumn::make('slug')
                    ->label('URL slug')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('sport')
                    ->label('Sport')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => TipSportOptions::label((string) ($state ?: TipSportOptions::DEFAULT)))
                    ->sortable(),

                Tables\Columns\TextColumn::make('page_kind')
                    ->label('Kind')
                    ->badge()
                    ->getStateUsing(fn (GameCat $record): string => in_array($record->slug, SeoPage::legalSitePageSlugs(), true)
                        ? 'Legal / static'
                        : 'Tip category')
                    ->tooltip('Legal URLs: after `php artisan migrate`, manage them under Legal & site pages.'),

                Tables\Columns\ToggleColumn::make('show_on_free_tips_store')
                    ->label('Store')
                    ->sortable()
                    ->disabled(fn (GameCat $record): bool => in_array((string) $record->slug, SeoPage::legalSitePageSlugs(), true)
                        || $record->isHomeCatType()
                        || strtolower(trim((string) $record->slug)) === 'homeslug'),

                Tables\Columns\TextColumn::make('store_grid_sort_order')
                    ->label('Store sort')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('fixture_heading')
                    ->label('Fixture heading')
                    ->searchable()
                    ->toggleable()
                    ->limit(40)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('prediction_slug')
                    ->label('Prediction key')
                    ->placeholder('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('prediction_entry_mode')
                    ->label('Quick Edit tip field')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => ($state ?? 'preset') === 'manual' ? 'Text' : 'Dropdown')
                    ->color(fn (?string $state): string => ($state ?? 'preset') === 'manual' ? 'warning' : 'gray'),

                Tables\Columns\TextColumn::make('cat_type')
                    ->label('Cat type')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable()
                    ->alignEnd(),
            ])
            ->actions([
                Tables\Actions\Action::make('viewOnSite')
                    ->label('View')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (GameCat $record): string => FrontUrl::forTipCategory((string) $record->slug))
                    ->openUrlInNewTab()
                    ->hidden(fn (GameCat $record): bool => in_array((string) $record->slug, SeoPage::legalSitePageSlugs(), true)),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->hidden(fn (GameCat $record): bool => in_array((string) $record->slug, SeoPage::legalSitePageSlugs(), true)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sport')
                    ->label('Sport')
                    ->options(TipSportOptions::selectOptions()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGameCats::route('/'),
            'create' => Pages\CreateGameCat::route('/create'),
            'edit' => Pages\EditGameCat::route('/{record}/edit'),
        ];
    }

    /**
     * Free tips store fields apply to published tip URLs, not the system home row or legal static slugs.
     */
    protected static function formShowsStoreGridSection(Get $get, ?Model $record): bool
    {
        if ($record instanceof GameCat) {
            if ($record->isHomeCatType() || strtolower(trim((string) $record->slug)) === 'homeslug') {
                return false;
            }
            if (in_array((string) $record->slug, SeoPage::legalSitePageSlugs(), true)) {
                return false;
            }

            return true;
        }

        $slug = strtolower(trim((string) $get('slug')));
        if ($slug === 'homeslug' || in_array($slug, SeoPage::legalSitePageSlugs(), true)) {
            return false;
        }

        return true;
    }

    /**
     * Fixture list heading applies to tip category URLs (including home), not legal static slugs.
     */
    protected static function formShowsFixtureHeading(Get $get, ?Model $record): bool
    {
        if ($record instanceof GameCat) {
            return ! in_array((string) $record->slug, SeoPage::legalSitePageSlugs(), true);
        }

        $slug = strtolower(trim((string) $get('slug')));

        return $slug === '' || ! in_array($slug, SeoPage::legalSitePageSlugs(), true);
    }

    /**
     * Home tip labels apply when `cat_type` is `home` (usually custom key `home`).
     */
    protected static function formShowsHomeTipsLabels(Get $get, ?Model $record): bool
    {
        if ($record instanceof GameCat && $record->isHomeCatType()) {
            return true;
        }

        if ($get('prediction_slug') !== TipCategoryPredictionPresets::CUSTOM_SENTINEL) {
            return false;
        }

        return strtolower(trim((string) $get('prediction_slug_custom'))) === 'home';
    }
}
