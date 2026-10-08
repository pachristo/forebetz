<?php

namespace App\Filament\Resources\LeagueResource\Pages;

use App\Filament\Resources\LeagueResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Notifications\Notification;

class ListLeagues extends ListRecords
{
    protected static string $resource = LeagueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importLeagues')
                ->label('Import Leagues')
                ->action(function () {
                    $backendPath = base_path();
                    $logFilename = 'leagues-import-'.time().'.log';
                    $cmd = sprintf('cd %s && nohup php artisan leagues:import --log=%s > /dev/null 2>&1 & echo $!', escapeshellarg($backendPath), escapeshellarg($logFilename));
                    exec($cmd, $output, $ret);
                    $pid = trim($output[0] ?? '');
                    if ($pid) {
                        $status = ['pid' => $pid, 'log' => $logFilename, 'started_at' => now()->toDateTimeString()];
                        $dir = storage_path('app/league_import');
                        if (! file_exists($dir)) mkdir($dir, 0755, true);
                        file_put_contents($dir.'/status.json', json_encode($status));
                        Notification::make()->success()->title('Import started')->body('PID: '.$pid)->send();
                    } else {
                        Notification::make()->danger()->title('Failed')->body('Could not start import')->send();
                    }
                })
                ->requiresConfirmation(),
            Actions\Action::make('viewImportStatus')
                ->label('View Import Status')
                ->action(function () {
                    $statusFile = storage_path('app/league_import/status.json');
                    if (! file_exists($statusFile)) {
                        Notification::make()->warning()->title('No Status')->body('No import status found')->send();
                        return;
                    }
                    $data = json_decode(file_get_contents($statusFile), true) ?: [];
                    $total = $data['total'] ?? 0;
                    $processed = $data['processed'] ?? 0;
                    $created = $data['created'] ?? 0;
                    $skipped = $data['skipped'] ?? 0;

                    Notification::make()->info()->title('Import Status')
                        ->body("Total: $total\nProcessed: $processed\nCreated: $created\nSkipped: $skipped")
                        ->send();
                }),
        ];
    }
}
