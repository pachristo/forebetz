<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdResource\Pages;
use App\Models\Ad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Ads Management';

    protected static ?string $navigationLabel = 'Ads';

    protected static ?string $modelLabel = 'Ad';

    protected static ?string $pluralModelLabel = 'Ads';

    protected static ?int $navigationSort = 5;

    /**
     * Grouped options aligned with legacy admin `nice_front/admin/resources/views/part/ads.blade.php`
     * and text links from `nice_front/admin/resources/views/part/textlink.blade.php`.
     *
     * @return array<string, array<string, string>>
     */
    public static function adSlotGroups(): array
    {
        return [
            'Homepage Top Banner' => [
                'tb_i' => 'Homepage Top Banner (IMAGE): 300 x 250px (image) (Desktop & MOBILE)',
                'tb_c' => 'Homepage Top Banner (CODE): 300 x 250px (code) (Desktop & MOBILE)',
            ],
            'Above Free pick Homepage' => [
                'afp_i' => 'Above Free pick Homepage: 940 x 90px (image) (Desktop Only)',
                'mafp_i' => 'Above Free pick Homepage: 300 x 250px (image) (Mobile Only)',
                'afp_c' => 'Above Free pick Homepage: 940 x 90px (code) (Desktop Only)',
                'mafp_c' => 'Above Free pick Homepage: 300 x 250px (code) (Mobile Only)',
            ],
            'Grid Advert Above free pick' => [
                'g_i' => 'Grid Advert Above free pick (IMAGE): 280 x 91px (image) (Desktop & MOBILE)',
                'g_c' => 'Grid Advert Above free pick (CODE): 280 x 91px (code) (Desktop & MOBILE)',
            ],
            'Under Free pick Homepage' => [
                'ufp_i' => 'Under Free pick Homepage: 940 x 90px (image) (Desktop Only)',
                'mufp_i' => 'Under Free pick Homepage: 300 x 250px (image) (Mobile Only)',
                'ufp_c' => 'Under Free pick Homepage: 940 x 90px (code) (Desktop Only)',
                'mufp_c' => 'Under Free pick Homepage: 300 x 250px (code) (Mobile Only)',
            ],
            'Under Investment Homepage' => [
                'uin_i' => 'Under Investment Homepage: 940 x 90px (image) (Desktop Only)',
                'muin_i' => 'Under Investment Homepage: 300 x 250px (image) (Mobile Only)',
                'uin_c' => 'Under Investment Homepage: 940 x 90px (code) (Desktop Only)',
                'muin_c' => 'Under Investment Homepage: 300 x 250px (code) (Mobile Only)',
            ],
            'Under VIP packages Homepage' => [
                'uvi_i' => 'Under VIP packages Homepage: 940 x 90px (image) (Desktop Only)',
                'muvi_i' => 'Under VIP packages Homepage: 300 x 250px (image) (Mobile Only)',
                'uvi_c' => 'Under VIP packages Homepage: 940 x 90px (code) (Desktop Only)',
                'muvi_c' => 'Under VIP packages Homepage: 300 x 250px (code) (Mobile Only)',
            ],
            'Under Recent Winnings Homepage' => [
                'urw_i' => 'Under Recent Winnings Homepage: 940 x 90px (image) (Desktop Only)',
                'murw_i' => 'Under Recent Winnings Homepage: 300 x 250px (image) (Mobile Only)',
                'urw_c' => 'Under Recent Winnings Homepage: 940 x 90px (code) (Desktop Only)',
                'murw_c' => 'Under Recent Winnings Homepage: 300 x 250px (code) (Mobile Only)',
            ],
            'Above Category Page Advert' => [
                'ac_i' => 'Above Category Page Advert: 940 x 90px (image) (Desktop Only)',
                'mac_i' => 'Above Category Page Advert: 300 x 250px (image) (Mobile Only)',
                'ac_c' => 'Above Category Page Advert: 940 x 90px (code) (Desktop Only)',
                'mac_c' => 'Above Category Page Advert: 300 x 250px (code) (Mobile Only)',
            ],
            'Under Category Page Advert' => [
                'uc_i' => 'Under Category Page Advert: 940 x 90px (image) (Desktop Only)',
                'muc_i' => 'Under Category Page Advert: 300 x 250px (image) (Mobile Only)',
                'uc_c' => 'Under Category Page Advert: 940 x 90px (code) (Desktop Only)',
                'muc_c' => 'Under Category Page Advert: 300 x 250px (code) (Mobile Only)',
            ],
            'Header sticky' => [
                'header_sticky_d' => 'Header Sticky: 940 x 90px (image) (Desktop Only)',
                'header_sticky' => 'Header Sticky: 300 x 25px (image) (Mobile Only)',
                'code_header_sticky_d' => 'Header Sticky: 940 x 90px (code) (Desktop Only)',
                'code_header_sticky' => 'Header Sticky: 300 x 25px (code) (Mobile Only)',
            ],
            'Footer sticky' => [
                'footer_sticky_d' => 'Footer Sticky: 940 x 90px (image) (Desktop Only)',
                'footer_sticky' => 'Footer sticky: 300 x 25px (image) (Mobile Only)',
                'code_footer_sticky_d' => 'Footer Sticky: 940 x 90px (code) (Desktop Only)',
                'code_footer_sticky' => 'Footer Sticky: 300 x 25px (code) (Mobile Only)',
            ],
            'Other placements' => [
                'pop_up' => 'Pop Up Advert: 300 x 250px (strictly IMAGE)',
                'button' => 'Button Advert: 275 x 135 (strictly IMAGE)',
                'side' => 'Side pop up (IMAGE): 280 x 221px (Desktop & MOBILE)',
            ],
            'Text links' => [
                'header' => 'Header Textlink: (Strictly Text) (Desktop & Mobile)',
                'footer' => 'Footer Textlink: (Strictly Text) (Desktop & Mobile)',
                'body' => 'Body Textlink: (Strictly Text) (Desktop & Mobile)',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function adSlotLabelsFlat(): array
    {
        $flat = [];
        foreach (self::adSlotGroups() as $options) {
            foreach ($options as $value => $label) {
                $flat[$value] = $label;
            }
        }

        return $flat;
    }

    /**
     * Previous Filament slot keys (for list/edit display of existing rows).
     *
     * @return array<string, string>
     */
    public static function legacyAdSlotLabels(): array
    {
        return [
            'iad' => 'Above Free pick Homepage: 940 x 90px (image) (Desktop Only) [legacy slot]',
            'iam' => 'Above Free pick Homepage: 300 x 250px (image) (Mobile Only) [legacy slot]',
            'cad' => 'Above Free pick Homepage: 940 x 90px (code) (Desktop Only) [legacy slot]',
            'cam' => 'Above Free pick Homepage: 300 x 250px (code) (Mobile Only) [legacy slot]',
            'ibd' => 'Under Free pick Homepage: 940 x 90px (image) (Desktop Only) [legacy slot]',
            'ibm' => 'Under Free pick Homepage: 300 x 250px (image) (Mobile Only) [legacy slot]',
            'cbd' => 'Under Free pick Homepage: 940 x 90px (code) (Desktop Only) [legacy slot]',
            'cbm' => 'Under Free pick Homepage: 300 x 250px (code) (Mobile Only) [legacy slot]',
            'ifd' => 'Above Category Tips: 940 x 90px (image) (Desktop Only) [legacy slot]',
            'ifm' => 'Above Category Tips: 300 x 250px (image) (Mobile Only) [legacy slot]',
            'cfd' => 'Above Category Tips: 940 x 90px (code) (Desktop Only) [legacy slot]',
            'cfm' => 'Above Category: 300 x 250px (code) (Mobile Only) [legacy slot]',
            'ied' => 'Under Category Tips: 940 x 90px (image) (Desktop Only) [legacy slot]',
            'iem' => 'Under Category Tips: 300 x 250px (image) (Mobile Only) [legacy slot]',
            'ced' => 'Under Category Tips: 940 x 90px (code) (Desktop Only) [legacy slot]',
            'cem' => 'Under Category Tips: 300 x 250px (code) (Mobile Only) [legacy slot]',
            'icd' => 'Above Sport News Homepage: 940 x 90px (image) (Desktop Only) [legacy slot]',
            'icm' => 'Above Sport News Homepage: 300 x 250px (image) (Mobile Only) [legacy slot]',
            'ccd' => 'Above Sport News Homepage: 940 x 90px (code) (Desktop Only) [legacy slot]',
            'ccm' => 'Above Sport News Homepage: 300 x 250px (code) (Mobile Only) [legacy slot]',
            'idd' => 'Under Sport News Homepage: 940 x 90px (image) (Desktop Only) [legacy → use usn_i]',
            'idm' => 'Under Sport News Homepage: 300 x 250px (image) (Mobile Only) [legacy → use musn_i]',
            'cdd' => 'Under Sport News Homepage: 940 x 90px (code) (Desktop Only) [legacy → use usn_c]',
            'cdm' => 'Under Sport News Homepage: 300 x 250px (code) (Mobile Only) [legacy → use musn_c]',
            'usn_i' => 'Under sport News: 940 x 90px (image) (Desktop Only) [alias]',
            'musn_i' => 'Under sport News: 300 x 250px (image) (Mobile Only) [alias]',
            'usn_c' => 'Under sport News: 940 x 90px (code) (Desktop Only) [alias]',
            'musn_c' => 'Under sport News: 300 x 250px (code) (Mobile Only) [alias]',
        ];
    }

    public static function adSlotLabel(?string $state): string
    {
        if ($state === null || $state === '') {
            return '';
        }

        $map = array_merge(self::legacyAdSlotLabels(), self::adSlotLabelsFlat());

        return $map[$state] ?? $state;
    }

    public static function inferTypeFromSlot(?string $slot): ?string
    {
        if ($slot === null || $slot === '') {
            return null;
        }

        if (in_array($slot, ['header', 'footer', 'body'], true)) {
            return 'text';
        }

        if (str_ends_with($slot, '_c') || str_starts_with($slot, 'code_')) {
            return 'code';
        }

        if (
            str_ends_with($slot, '_i')
            || in_array($slot, [
                'pop_up',
                'button',
                'side',
                'footer_sticky',
                'footer_sticky_d',
                'header_sticky',
                'header_sticky_d',
            ], true)
        ) {
            return 'image';
        }

        return null;
    }

    public static function shouldShowImageUpload(Get $get): bool
    {
        if ($get('type') === 'image') {
            return true;
        }

        return static::inferTypeFromSlot($get('name')) === 'image';
    }

    public static function shouldShowCodeField(Get $get): bool
    {
        if ($get('type') === 'code') {
            return true;
        }

        return static::inferTypeFromSlot($get('name')) === 'code';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Placement & scheduling')
                    ->description('Slot key must match what the front-end queries (`name` / `location`).')
                    ->schema([
                        Forms\Components\Select::make('name')
                            ->label('Ad slot')
                            ->options(static::adSlotGroups())
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $inferred = static::inferTypeFromSlot($state);
                                if ($inferred !== null) {
                                    $set('type', $inferred);
                                }
                            })
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->label('Ad type')
                            ->options([
                                'image' => 'Image',
                                'code' => 'Code / HTML',
                                'text' => 'Text link',
                            ])
                            ->required()
                            ->live()
                            ->native(false),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('expiry')
                            ->label('Expiry')
                            ->nullable(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first within the same slot.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Creative')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Banner image')
                            ->disk('public')
                            ->directory('ads')
                            ->visibility('public')
                            ->image()
                            ->maxSize(2048)
                            ->nullable()
                            ->openable()
                            ->downloadable()
                            ->helperText('Upload or replace the banner. Shown when Ad type is Image (or an image slot is selected).')
                            ->visible(fn (Get $get): bool => static::shouldShowImageUpload($get))
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('link')
                            ->label('Destination URL')
                           
                            ->placeholder('https://example.com/…')
                            ->maxLength(2048)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('description')
                            ->label('Anchor / label text')
                            ->placeholder('e.g. Click here for latest offers')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('code')
                            ->label('Ad code (HTML / script)')
                            ->placeholder('Paste third-party or custom HTML when type is Code.')
                            ->rows(8)
                            ->nullable()
                            ->visible(fn (Get $get): bool => static::shouldShowCodeField($get))
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Advertiser & internal notes')
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('company')
                            ->label('Company / advertiser')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('contact')
                            ->label('Contact')
                            ->rows(3)
                            ->nullable(),
                        Forms\Components\Textarea::make('comment')
                            ->label('Admin comment')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),
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
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Preview')
                    ->disk('public')
                    ->height(44)
                    ->width(72)
                    ->defaultImageUrl(fn (): string => 'data:image/svg+xml,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="72" height="44" viewBox="0 0 72 44"><rect fill="#f3f4f6" width="72" height="44"/><text x="36" y="24" text-anchor="middle" fill="#9ca3af" font-size="9" font-family="sans-serif">No img</text></svg>'))
                    ->visible(fn ($livewire): bool => in_array((string) ($livewire->activeTab ?? 'all'), ['all', 'image'], true))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Code preview')
                    ->placeholder('—')
                    ->limit(40)
                    ->wrap()
                    ->visible(fn ($livewire): bool => in_array((string) ($livewire->activeTab ?? 'all'), ['all', 'code'], true))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Slot')
                    ->formatStateUsing(fn (?string $state): string => static::adSlotLabel($state) ?: '—')
                    ->description(fn (Ad $record): string => (string) $record->name)
                    ->searchable()
                    ->wrap()
                    ->limit(80),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'image' => 'Image',
                        'code' => 'Code',
                        'text' => 'Text',
                        default => $state ? ucfirst($state) : '—',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'image' => 'info',
                        'code' => 'warning',
                        'text' => 'gray',
                        default => 'gray',
                    })
                    ->visible(fn ($livewire): bool => ($livewire->activeTab ?? 'all') === 'all'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'active' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Text')
                    ->placeholder('—')
                    ->limit(36)
                    ->tooltip(fn (Ad $record): ?string => $record->description)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('link')
                    ->label('URL')
                    ->placeholder('—')
                    ->limit(32)
                    ->tooltip(fn (Ad $record): ?string => $record->link)
                    ->url(function (Ad $record): ?string {
                        $href = $record->link;
                        if ($href === null || $href === '') {
                            return null;
                        }
                        if (! str_starts_with($href, 'http://') && ! str_starts_with($href, 'https://')) {
                            return 'https://'.ltrim($href, '/');
                        }

                        return $href;
                    })
                    ->openUrlInNewTab()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('company')
                    ->label('Company')
                    ->placeholder('—')
                    ->limit(24)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('expiry')
                    ->label('Expires')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
                Tables\Filters\SelectFilter::make('name')
                    ->label('Slot')
                    ->options(static::adSlotLabelsFlat())
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }
}
