<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteTextFileResource\Pages;
use App\Models\SiteTextFile;
use App\Support\FrontUrl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SiteTextFileResource extends Resource
{
    protected static ?string $model = SiteTextFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'robots / ads / llms.txt';

    protected static ?string $modelLabel = 'SEO text file';

    protected static ?string $pluralModelLabel = 'SEO text files';

    protected static ?int $navigationSort = 25;

    protected static ?string $slug = 'seo-text-files';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Public file')
                    ->description('Served on the public front as raw text/plain (UTF-8) — no HTML — so Google, ads.txt validators, and LLM crawlers can read it. Sitemap is separate XML at /wp-sitemap.xml (XSL-styled for browsers).')
                    ->schema([
                        Forms\Components\Placeholder::make('public_url')
                            ->label('Public URL')
                            ->content(function (?SiteTextFile $record): string {
                                if (! $record) {
                                    return '—';
                                }

                                return FrontUrl::forTextFile((string) $record->key);
                            }),
                        Forms\Components\Placeholder::make('sitemap_url')
                            ->label('XML sitemap')
                            ->content(FrontUrl::forSitemap())
                            ->visible(fn (?SiteTextFile $record): bool => $record?->key === SiteTextFile::KEY_ROBOTS),
                        Forms\Components\TextInput::make('label')
                            ->label('Admin label')
                            ->required()
                            ->maxLength(120)
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\FileUpload::make('upload')
                            ->label('Upload .txt file')
                            ->disk('local')
                            ->directory('seo-text-uploads')
                            ->visibility('private')
                            ->acceptedFileTypes(['text/plain', 'text/*,.txt', '.txt'])
                            ->maxSize(512)
                            ->helperText('Optional. Upload a plain .txt file to replace the contents below. You can also paste/edit text directly.')
                            ->storeFiles(false)
                            ->afterStateUpdated(function (?TemporaryUploadedFile $state, Set $set): void {
                                if ($state === null) {
                                    return;
                                }
                                $raw = @file_get_contents($state->getRealPath());
                                if ($raw === false) {
                                    return;
                                }
                                // Strip UTF-8 BOM if present
                                if (str_starts_with($raw, "\xEF\xBB\xBF")) {
                                    $raw = substr($raw, 3);
                                }
                                $text = str_replace(["\r\n", "\r"], "\n", $raw);
                                $set('content', rtrim($text)."\n");
                            })
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('content')
                            ->label('File contents (plain text)')
                            ->rows(24)
                            ->required()
                            ->extraInputAttributes([
                                'class' => 'font-mono text-sm',
                                'spellcheck' => 'false',
                            ])
                            ->helperText('Plain text only. Saved to the database and served at the public URL with Content-Type: text/plain; charset=UTF-8.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('File')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->badge(),
                Tables\Columns\TextColumn::make('content')
                    ->label('Preview')
                    ->limit(48)
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('viewOnSite')
                    ->label('View')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (SiteTextFile $record): string => FrontUrl::forTextFile((string) $record->key))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([])
            ->defaultSort('key');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteTextFiles::route('/'),
            'edit' => Pages\EditSiteTextFile::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
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
