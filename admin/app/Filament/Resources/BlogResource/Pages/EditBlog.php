<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use App\Models\Blog;
use App\Support\FrontUrl;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlog extends EditRecord
{
    protected static string $resource = BlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewOnSite')
                ->label('View on site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => FrontUrl::forBlog((string) ($this->record instanceof Blog ? $this->record->slug : '')))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}
