<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LandGrid;
use App\Models\User;

class LandGridPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_land::grid');
    }

    public function view(User $user, LandGrid $landGrid): bool
    {
        return $user->can('view_land::grid');
    }

    public function create(User $user): bool
    {
        return $user->can('create_land::grid');
    }

    public function update(User $user, LandGrid $landGrid): bool
    {
        return $user->can('update_land::grid');
    }

    public function delete(User $user, LandGrid $landGrid): bool
    {
        return $user->can('delete_land::grid');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_land::grid');
    }

    public function forceDelete(User $user, LandGrid $landGrid): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, LandGrid $landGrid): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, LandGrid $landGrid): bool
    {
        return $user->can('replicate_land::grid');
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
