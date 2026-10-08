<?php

namespace App\Filament\Resources\ExpiredUsersResource\Pages;

use App\Filament\Resources\ExpiredUsersResource;
use Filament\Resources\Pages\ListRecords;

class ListExpiredUsers extends ListRecords
{
    protected static string $resource = ExpiredUsersResource::class;

    protected static function getEloquentQuery()
    {
        return parent::getEloquentQuery()
            ->where('subscription_status', 0)
            ->whereHas('subscriptions');
    }
}
