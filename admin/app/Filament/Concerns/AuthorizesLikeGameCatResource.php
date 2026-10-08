<?php

namespace App\Filament\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Reuse FilamentShield permissions for {@see \App\Models\GameCat} so new resources
 * appear for the same roles without running shield:generate again.
 */
trait AuthorizesLikeGameCatResource
{
    /** Whether the user is super admin or has the given Tip categories (GameCat) permission. */
    protected static function authorizesWithGameCatPermissions(string $permission): bool
    {
        $u = Auth::user();
        if (! $u instanceof User) {
            return false;
        }

        if ($u->hasRole((string) config('filament-shield.super_admin.name', 'super_admin'))) {
            return true;
        }

        return $u->can($permission);
    }

    public static function canViewAny(): bool
    {
        return static::authorizesWithGameCatPermissions('view_any_game::cat');
    }

    public static function canCreate(): bool
    {
        return static::authorizesWithGameCatPermissions('create_game::cat');
    }

    public static function canEdit(Model $record): bool
    {
        return static::authorizesWithGameCatPermissions('update_game::cat');
    }

    public static function canDelete(Model $record): bool
    {
        return static::authorizesWithGameCatPermissions('delete_game::cat');
    }

    public static function canDeleteAny(): bool
    {
        return static::authorizesWithGameCatPermissions('delete_any_game::cat');
    }
}
