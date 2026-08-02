<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ResearchFile;
use App\Models\User;

class ResearchFilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_research::file');
    }

    public function view(User $user, ResearchFile $researchFile): bool
    {
        return $user->can('view_research::file');
    }

    public function create(User $user): bool
    {
        return $user->can('create_research::file');
    }

    public function update(User $user, ResearchFile $researchFile): bool
    {
        return $user->can('update_research::file');
    }

    public function delete(User $user, ResearchFile $researchFile): bool
    {
        return $user->can('delete_research::file');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_research::file');
    }

    public function forceDelete(User $user, ResearchFile $researchFile): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, ResearchFile $researchFile): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, ResearchFile $researchFile): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
