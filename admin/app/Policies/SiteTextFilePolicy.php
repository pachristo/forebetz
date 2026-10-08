<?php

namespace App\Policies;

use App\Models\SiteTextFile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Same capability gate as site configuration — admins who can edit branding can edit SEO text files.
 */
class SiteTextFilePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_site::configuration');
    }

    public function view(User $user, SiteTextFile $siteTextFile): bool
    {
        return $user->can('view_site::configuration');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, SiteTextFile $siteTextFile): bool
    {
        return $user->can('update_site::configuration');
    }

    public function delete(User $user, SiteTextFile $siteTextFile): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, SiteTextFile $siteTextFile): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, SiteTextFile $siteTextFile): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, SiteTextFile $siteTextFile): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
