<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteLegalPageResource\Pages;
use App\Models\SeoPage;
use App\Policies\SeoPagePolicy;
use App\Support\FrontUrl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class SiteLegalPageResource extends Resource
{
    protected static ?string $model = SeoPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $navigationLabel = 'Legal & site pages';

    protected static ?string $modelLabel = 'Legal / site page';

    protected static ?string $pluralModelLabel = 'Legal & site pages';

    protected static ?int $navigationSort = 21;

    /**
     * Same permissions as "Other SEO pages" (SeoPage model) so roles that can manage SEO pages see this menu.
     */
    public static function canViewAny(): bool
    {
        return Auth::check() && app(SeoPagePolicy::class)->viewAny(Auth::user());
    }

    public static function canCreate(): bool
    {
        return Auth::check() && app(SeoPagePolicy::class)->create(Auth::user());
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof SeoPage
            && Auth::check()
            && app(SeoPagePolicy::class)->update(Auth::user(), $record);
    }

    public static function canView(Model $record): bool
    {
        return $record instanceof SeoPage
            && Auth::check()
            && app(SeoPagePolicy::class)->view(Auth::user(), $record);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page')
                    ->schema([
                        Forms\Components\Select::make('slug')
                            ->label('Page (URL)')
                            ->options(function (string $operation): array {
                                if ($operation !== 'create') {
                                    return SeoPage::legalSlugOptions();
                                }
                                $existing = SeoPage::query()->legalSitePages()->pluck('slug')->all();
                                $missing = array_values(array_diff(SeoPage::legalSitePageSlugs(), $existing));

                                return collect(SeoPage::legalSlugOptions())
                                    ->only($missing)
                                    ->all();
                            })
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->disabled(fn (string $operation): bool => $operation !== 'create')
                            ->dehydrated(fn (string $operation): bool => $operation === 'create')
                            ->helperText(fn (string $operation, $get): string => ($get('slug') ?? '') === SeoPage::BLOG_INDEX_SLUG
                                ? 'Blog index — edits title, meta, and hero for /blog.'
                                : ($operation !== 'create'
                                    ? 'This URL is fixed for this page type.'
                                    : 'Only pages not yet created are listed.')),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('category')
                            ->maxLength(100)
                            ->default('Legal & site')
                            ->helperText('Shown internally; front-end may ignore.'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\TextInput::make('head1')->label('Headline 1')->maxLength(255),
                        Forms\Components\TextInput::make('head2')->label('Headline 2')->maxLength(255),
                        TinyEditor::make('content')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Publishing')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->required()
                            ->default('published'),
                    ]),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\Textarea::make('meta_keywords')->rows(3),
                        Forms\Components\Textarea::make('meta_description')->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL slug')
                    ->formatStateUsing(fn (string $state): string => SeoPage::legalSlugLabel($state).' ('.$state.')')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->limit(45)
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('slug')
            ->actions([
                Tables\Actions\Action::make('viewOnSite')
                    ->label('View')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (SeoPage $record): string => FrontUrl::forSeoPage((string) $record->slug))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No legal / site pages in the database yet')
            ->emptyStateDescription('Click Sync from Nice JSON in the toolbar or below to import About, Privacy, Terms, Disclaimer, Refund, and Partners from nice_front/admin/public, or create Blog index (blogslug) for /blog SEO. Requires create or update permission on SEO pages (Filament Shield).');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteLegalPages::route('/'),
            'create' => Pages\CreateSiteLegalPage::route('/create'),
            'edit' => Pages\EditSiteLegalPage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->legalSitePages();
    }
}
