<?php

namespace App\Filament\Resources\ActiveUsersResource\Pages;

use App\Filament\Resources\ActiveUsersResource;
use App\Models\Membership;
use App\Models\PlanCategory;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListActiveUsers extends ListRecords
{
    protected static string $resource = ActiveUsersResource::class;

    public function getTabs(): array
    {
        $base = Membership::query()->whereSubscriptionCurrent();

        $tabs = [
            'all' => Tab::make('All')
                ->badge((clone $base)->count()),
        ];

        $categories = PlanCategory::query()->orderBy('name')->get();

        foreach ($categories as $category) {
            $id = $category->id;
            $label = $category->title ?: $category->name;

            $tabs['category_' . $id] = Tab::make($label)
                ->badge((clone $base)->where('subscription_id', $id)->count())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('subscription_id', $id));
        }

        $unassignedQuery = (clone $base)->where(function (Builder $q): void {
            $q->whereNull('subscription_id')->orWhere('subscription_id', 0);
        });

        $tabs['unassigned'] = Tab::make('Unassigned')
            ->badge($unassignedQuery->count())
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->where(function (Builder $q): void {
                    $q->whereNull('subscription_id')->orWhere('subscription_id', 0);
                });
            });

        return $tabs;
    }
}
