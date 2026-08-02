<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GisPointOfInterest;
use App\Models\User;

class GisPointOfInterestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_gis::point::of::interest');
    }

    public function view(User $user, GisPointOfInterest $gisPointOfInterest): bool
    {
        return $user->can('view_gis::point::of::interest');
    }

    public function create(User $user): bool
    {
        return $user->can('create_gis::point::of::interest');
    }

    public function update(User $user, GisPointOfInterest $gisPointOfInterest): bool
    {
        return $user->can('update_gis::point::of::interest');
    }

    public function delete(User $user, GisPointOfInterest $gisPointOfInterest): bool
    {
        return $user->can('delete_gis::point::of::interest');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_gis::point::of::interest');
    }

    public function forceDelete(User $user, GisPointOfInterest $gisPointOfInterest): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, GisPointOfInterest $gisPointOfInterest): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, GisPointOfInterest $gisPointOfInterest): bool
    {
        return $user->can('replicate_gis::point::of::interest');
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
