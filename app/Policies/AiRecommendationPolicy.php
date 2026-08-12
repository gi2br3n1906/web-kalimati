<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AiRecommendation;
use App\Models\User;

final class AiRecommendationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_land::recommendation');
    }

    public function view(User $user, AiRecommendation $recommendation): bool
    {
        return $user->can('view_land::recommendation');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AiRecommendation $recommendation): bool
    {
        return false;
    }

    public function delete(User $user, AiRecommendation $recommendation): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
