<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoPageResource\Pages;
use App\Models\SeoPage;
use App\Support\FrontUrl;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class SeoPageResource extends Resource
{
    protected static ?string $model = SeoPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content Management';

    /** Legal & fixed URLs are managed under "Legal & site pages". */
    protected static ?string $navigationLabel = 'Other SEO pages';

    protected static ?string $modelLabel = 'SEO page';

    protected static ?string $pluralModelLabel = 'Other SEO pages';

    protected static ?int $navigationSort = 22;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->afterStateUpdated(function ($set, $state) {
                                $set('slug', Str::slug($state));
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('category')->maxLength(100),

                        Forms\Components\TextInput::make('head1')->label('Head 1')->maxLength(255),
                        Forms\Components\TextInput::make('head2')->label('Head 2')->maxLength(255),

                        TinyEditor::make('content')
                            ->columnSpanFull(),

                        // Forms\Components\FileUpload::make('display_image')
                        //     ->image()
                        //     ->directory('seo-images')
                        //     ->maxSize(2048),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Publishing')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->required()
                            ->default('draft'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\Textarea::make('meta_keywords')->rows(3),
                        Forms\Components\Textarea::make('meta_description')->rows(3),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->limit(50)->searchable(),
                Tables\Columns\TextColumn::make('head1')->label('Head 1')->limit(30),
                Tables\Columns\TextColumn::make('head2')->label('Head 2')->limit(30),
                Tables\Columns\TextColumn::make('category')->sortable(),
                Tables\Columns\BadgeColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('viewOnSite')
                    ->label('View')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (SeoPage $record): string => FrontUrl::forSeoPage((string) $record->slug))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListSeoPages::route('/'),
            'create' => Pages\CreateSeoPage::route('/create'),
            'edit' => Pages\EditSeoPage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->notLegalSitePages();
    }
}
