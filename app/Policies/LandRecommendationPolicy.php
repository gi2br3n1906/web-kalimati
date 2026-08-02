<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LandRecommendation;
use App\Models\User;

class LandRecommendationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_land::recommendation');
    }

    public function view(User $user, LandRecommendation $landRecommendation): bool
    {
        return $user->can('view_land::recommendation');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, LandRecommendation $landRecommendation): bool
    {
        return $user->can('update_land::recommendation');
    }

    public function delete(User $user, LandRecommendation $landRecommendation): bool
    {
        return $user->can('delete_land::recommendation');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_land::recommendation');
    }

    public function forceDelete(User $user, LandRecommendation $landRecommendation): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, LandRecommendation $landRecommendation): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, LandRecommendation $landRecommendation): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
