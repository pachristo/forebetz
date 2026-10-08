<?php

namespace App\Filament\Resources\SiteTextFileResource\Pages;

use App\Filament\Resources\SiteTextFileResource;
use App\Models\SiteTextFile;
use App\Support\FrontUrl;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSiteTextFile extends EditRecord
{
    protected static string $resource = SiteTextFileResource::class;

    public function getTitle(): string
    {
        /** @var SiteTextFile $record */
        $record = $this->getRecord();

        return $record->label ?: ($record->key.'.txt');
    }

    public function getSubheading(): ?string
    {
        /** @var SiteTextFile $record */
        $record = $this->getRecord();

        return 'Served at '.FrontUrl::forTextFile((string) $record->key);
    }

    protected function getHeaderActions(): array
    {
        /** @var SiteTextFile $record */
        $record = $this->getRecord();

        return [
            Actions\Action::make('viewOnSite')
                ->label('View on site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(FrontUrl::forTextFile((string) $record->key))
                ->openUrlInNewTab(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }

    protected function getSavedNotification(): ?Notification
    {
        /** @var SiteTextFile $record */
        $record = $this->getRecord();

        return Notification::make()
            ->success()
            ->title(($record->label ?: $record->key.'.txt').' saved')
            ->body('The public front will serve the updated file contents.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var SiteTextFile $record */
        $record = $this->getRecord();

        $data['key'] = $record->key;
        unset($data['upload']);

        $content = (string) ($data['content'] ?? '');
        // Normalize newlines for crawlers; keep UTF-8 plain text only
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }
        $data['content'] = rtrim($content)."\n";

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['upload'] = null;

        return $data;
    }
}
