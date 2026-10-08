<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteConfigurationResource\Pages;
use App\Models\PlanCategory;
use App\Models\SiteConfiguration;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table as TablesTable;

class SiteConfigurationResource extends Resource
{
    protected static ?string $model = SiteConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Site Configuration';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Card::make()->heading('Branding')->schema([
                Forms\Components\FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->directory('site')
                    ->disk('public')
                    ->visibility('public')
                    ->nullable()
                    ->helperText('Site logo (used in header, etc.).'),
                Forms\Components\FileUpload::make('favicon')
                    ->label('Favicon')
                    ->image()
                    ->directory('site')
                    ->disk('public')
                    ->visibility('public')
                    ->nullable()
                    ->helperText('Browser tab icon (e.g. 32×32 or 64×64).'),
                Forms\Components\TextInput::make('logo_width')
                    ->label('Logo Width (px)')
                    ->numeric()
                    ->minValue(40)
                    ->maxValue(600)
                    ->nullable()
                    ->helperText('Used on frontend header/footer logo width.'),
                Forms\Components\TextInput::make('logo_height')
                    ->label('Logo Height (px, optional)')
                    ->numeric()
                    ->minValue(20)
                    ->maxValue(300)
                    ->nullable()
                    ->helperText('Leave empty to keep height auto.'),
            ])->columns(2),
            Card::make()->heading('Contact & phone')->schema([
                Forms\Components\TextInput::make('contact_phone')
                    ->label('Display phone (footer)')
                    ->tel()
                    ->maxLength(64)
                    ->nullable()
                    ->helperText('Shown in footer “Reach us”; optional tel: link.'),
                Forms\Components\TextInput::make('contact_email')
                    ->email()
                    ->nullable(),
                Forms\Components\TextInput::make('advert_email')
                    ->label('Advertising / business email')
                    ->email()
                    ->nullable()
                    ->helperText('Shown in footer for adverts & partnerships.'),
            ])->columns(2),
            Card::make()->heading('Social links (footer icons)')->schema([
                Forms\Components\TextInput::make('tiktok_link')->label('TikTok URL')->url()->maxLength(512)->nullable(),
                Forms\Components\TextInput::make('instagram_link')->label('Instagram URL')->url()->maxLength(512)->nullable(),
                Forms\Components\TextInput::make('facebook_link')->label('Facebook URL')->url()->maxLength(512)->nullable(),
                Forms\Components\TextInput::make('whatsapp_link')->label('WhatsApp chat URL')->url()->maxLength(512)->nullable(),
                Forms\Components\TextInput::make('linkedin_link')->label('LinkedIn URL')->url()->maxLength(512)->nullable(),
                Forms\Components\TextInput::make('twitter_link')->label('X (Twitter) URL')->url()->maxLength(512)->nullable(),
                Forms\Components\TextInput::make('skype_link')->label('Skype URL')->url()->maxLength(512)->nullable(),
            ])->columns(2),
            Card::make()->heading('Telegram')->schema([
                Forms\Components\TextInput::make('telegram_link')
                    ->label('Community / channel Telegram URL')
                    ->url()
                    ->maxLength(512)
                    ->nullable()
                    ->helperText('Optional: general Telegram link used elsewhere on the site.'),
                Forms\Components\TextInput::make('telegram_admin_link')
                    ->label('Admin / support Telegram URL')
                    ->url()
                    ->maxLength(512)
                    ->nullable()
                    ->helperText('Footer “Reach us on Telegram” uses this link.'),
                Forms\Components\TextInput::make('telegram_admin_username')
                    ->label('Telegram admin link label')
                    ->maxLength(255)
                    ->nullable()
                    ->helperText('e.g. Reach us on Telegram (shown as link text).'),
            ])->columns(1),
            Card::make()->heading('WhatsApp number (display)')->schema([
                Forms\Components\TextInput::make('whatsapp_no')
                    ->label('WhatsApp number (text)')
                    ->maxLength(255)
                    ->nullable()
                    ->helperText('Shown next to WhatsApp in footer; pair with WhatsApp chat URL above.'),
            ]),
            Card::make()->heading('Checkout payment proof')->schema([
                Forms\Components\Textarea::make('payment_proof_instruction')
                    ->label('Payment proof instruction')
                    ->rows(4)
                    ->columnSpanFull()
                    ->nullable()
                    ->helperText(
                        'Shown on every payment method card footer on checkout. '
                        .'Placeholders: {contact_email}, {whatsapp_no}, {contact_phone}. '
                        .'Leave empty to use the built-in default on the front.'
                    )
                    ->placeholder(
                        "After you're done with the payment, kindly send the payment proof to our email {contact_email} "
                        .'or WhatsApp phone number {whatsapp_no}. Your account will be activated instantly.'
                    ),
            ]),
            Card::make()->heading('Homepage & past VIP results')->schema([
                Forms\Components\Select::make('homepage_results_plan_category_id')
                    ->label('Plan category')
                    ->options(
                        PlanCategory::query()
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(static function (PlanCategory $category): array {
                                $label = trim((string) ($category->title ?? '')) !== ''
                                    ? (string) $category->title
                                    : (string) $category->name;

                                return [$category->id => $label];
                            })
                            ->toArray()
                    )
                    ->searchable()
                    ->nullable()
                    ->helperText(
                        'VIP plan category for the homepage strip and /past-vip-winnings game tables. '
                        .'Mark daily Won/Lost under Fixture and Predictions → VIP investment results. '
                        .'Games shown are VIP tips typed as v{category id} on fixtures for that date.'
                    ),
                Forms\Components\DatePicker::make('homepage_results_week_date')
                    ->label('Results week (any day in week)')
                    ->native(false)
                    ->nullable()
                    ->helperText(
                        'Pick any date in the week you want to display (Mon–Sun). '
                        .'Leave empty to show the current week.'
                    ),
            ])->columns(2),
        ]);
    }

    public static function table(TablesTable $table): TablesTable
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('logo')->disk('public')->label('Logo'),
            Tables\Columns\ImageColumn::make('favicon')->disk('public')->label('Favicon')->circular(),
            Tables\Columns\TextColumn::make('logo_width')->label('Logo W')->suffix('px'),
            Tables\Columns\TextColumn::make('logo_height')->label('Logo H')->suffix('px'),
            Tables\Columns\TextColumn::make('advert_email'),
            Tables\Columns\TextColumn::make('contact_email'),
            Tables\Columns\TextColumn::make('whatsapp_no'),
            Tables\Columns\TextColumn::make('homepageResultsPlanCategory.name')
                ->label('Homepage VIP results')
                ->placeholder('—'),
            Tables\Columns\TextColumn::make('homepage_results_week_date')
                ->label('Results week')
                ->date('M j, Y')
                ->placeholder('Current week'),
            Tables\Columns\TextColumn::make('created_at')->date()->label('Created'),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteConfigurations::route('/'),
            'create' => Pages\CreateSiteConfiguration::route('/create'),
            'edit' => Pages\EditSiteConfiguration::route('/{record}/edit'),
        ];
    }
}
