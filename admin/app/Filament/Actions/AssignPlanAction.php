<?php

namespace App\Filament\Actions;

use App\Models\MemberSubscription;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\PlanCategory;
use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

/**
 * Shared “Assign Plan” table action for membership resources.
 */
final class AssignPlanAction
{
    public static function make(): Action
    {
        return Action::make('assignPlan')
            ->label('Assign Plan')
            ->icon('heroicon-o-shopping-cart')
            ->color('primary')
            ->form(self::formSchema())
            ->action(function (Membership $record, array $data): void {
                self::assign($record, $data);
            });
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    public static function formSchema(): array
    {
        return [
            Select::make('plan_category_id')
                ->label('Plan Category')
                ->options(fn () => PlanCategory::query()->orderBy('name')->pluck('name', 'id')->toArray())
                ->live()
                ->required()
                ->afterStateUpdated(function ($state, callable $set): void {
                    $set('plan_id', null);
                    $set('sub_date', null);
                    $set('next_due_date', null);
                }),

            Select::make('plan_id')
                ->label('Plan')
                ->options(fn (callable $get): array => $get('plan_category_id')
                    ? Plan::query()
                        ->where('plan_category_id', $get('plan_category_id'))
                        ->orderBy('variation')
                        ->pluck('variation', 'variation')
                        ->toArray()
                    : [])
                ->extraAttributes(['class' => 'text-black'])
                ->extraInputAttributes(['class' => 'text-black'])
                ->required()
                ->live()
                ->disabled(fn (callable $get): bool => ! $get('plan_category_id'))
                ->afterStateUpdated(function (?string $state, callable $set): void {
                    self::suggestDates($set, $state);
                }),

            DateTimePicker::make('sub_date')
                ->label('Subscription start')
                ->required()
                ->native(false)
                ->seconds(false)
                ->visible(fn (callable $get): bool => (bool) $get('plan_id')),

            DateTimePicker::make('next_due_date')
                ->label('Expiry date')
                ->required()
                ->native(false)
                ->seconds(false)
                ->after('sub_date')
                ->visible(fn (callable $get): bool => (bool) $get('plan_id')),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function assign(Membership $record, array $data): void
    {
        $planCategoryId = $data['plan_category_id'] ?? null;
        $duration = $data['plan_id'] ?? null;

        if (! $planCategoryId || ! $duration) {
            throw new \Exception('Plan category and plan are required.');
        }

        try {
            $subDate = self::parseDate($data['sub_date'] ?? null, Carbon::now());
            $expiry = self::parseDate(
                $data['next_due_date'] ?? null,
                Carbon::now()->add($duration),
            );
        } catch (Exception $e) {
            Notification::make()
                ->title('Invalid date or plan duration')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        }

        if ($expiry->lte($subDate)) {
            Notification::make()
                ->title('Expiry must be after subscription start')
                ->danger()
                ->send();

            return;
        }

        $subFormatted = $subDate->format('Y-m-d H:i:s');
        $expiryFormatted = $expiry->format('Y-m-d H:i:s');

        $category = PlanCategory::query()->find($planCategoryId);
        $record->subscription_id = $planCategoryId;
        $record->subscription_type = (string) ($category?->name ?: $category?->title ?: $record->subscription_type);
        $record->subscription_status = 1;
        $record->sub_date = $subFormatted;
        $record->next_due_date = $expiryFormatted;
        $record->save();

        MemberSubscription::create([
            'user_id' => $record->id,
            'category_id' => $planCategoryId,
            'sub_date' => $subFormatted,
            'next_due_date' => $expiryFormatted,
        ]);

        Notification::make()->title('Plan assigned')->success()->send();
    }

    private static function suggestDates(callable $set, ?string $duration): void
    {
        $now = Carbon::now();
        $set('sub_date', $now->toDateTimeString());

        if ($duration === null || $duration === '') {
            $set('next_due_date', null);

            return;
        }

        try {
            $set('next_due_date', $now->copy()->add($duration)->toDateTimeString());
        } catch (Exception) {
            $set('next_due_date', null);
        }
    }

    private static function parseDate(mixed $value, Carbon $fallback): Carbon
    {
        if ($value === null || $value === '') {
            return $fallback->copy();
        }

        return Carbon::parse($value);
    }
}
