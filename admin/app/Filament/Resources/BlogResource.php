<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Resources\BlogResource\RelationManagers;
use App\Models\Blog;
use App\Support\FrontUrl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
   use FilamentTiptapEditor\TiptapEditor;
use FilamentTiptapEditor\Enums\TiptapOutput;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Blog Content')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($set, $state) {
                                $set('slug', Str::slug($state));
                            }),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ,
                        
                        Forms\Components\Select::make('creator')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(auth()->id()),
                        
                        // Forms\Components\TextInput::make('category')
                        //     ->required()
                        //     ->maxLength(100),
                                Forms\Components\Select::make('category')
                            ->options([
                                'Sport News' => 'Sport News',
                                'esports news' => 'esports news',
                                'Preview' => 'Preview',
                                 'Updates' => 'Updates',
                            ])
                            ->required()
                            ->default('Sport News'),

                        Forms\Components\Select::make('blog_category_id')
                            ->label('News category (URL /news/category/...)')
                            ->relationship('blogCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                            // Sport News   Preview   esports news   Updates

                             TinyEditor::make('content')
                    ->label('Footer Content ')
                       ,
                         

                        
                        Forms\Components\FileUpload::make('display_image')
                            ->disk('public')
                            ->image()
                            ->directory('blog-images')
                            ->maxSize(2048)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('450'),
                    ])
                    ->columns(1),
                
                Forms\Components\Section::make('Publishing')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'Draft' => 'Draft',
                                'Publish' => 'Published',
                                'Archived' => 'Archived',
                            ])
                            ->required()
                            ->default('draft'),
                        
                 
                    ])
                    ->columns(1),
                
                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\Textarea::make('meta_keywords')
                            ->maxLength(500)
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('meta_description')
                            ->maxLength(300)
                            ->rows(3),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('display_image')
                    ->disk('public')
                    ->checkFileExistence(false)
                    ->circular()
                    ->defaultImageUrl('/default-blog.jpg'),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'Draft',
                        'success' => 'Publish',
                        'danger' => 'Archived',
                    ]),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('likes')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'publish' => 'Published',
                        'archived' => 'Archived',
                    ]),
                
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                Tables\Actions\Action::make('viewOnSite')
                    ->label('View on site')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Blog $record): string => FrontUrl::forBlog((string) $record->slug))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
                ]) ->label('Actions')
                    ->icon('heroicon-o-bars-3')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('publish')
                        ->action(fn ($records) => $records->each->update(['status' => 'Publish']))
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check'),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}