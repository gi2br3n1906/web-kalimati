<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleType;
use App\Models\UmkmBusiness;
use App\Models\User;

class UmkmBusinessPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole(RoleType::SUPER_ADMIN->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_umkm::business');
    }

    public function view(User $user, UmkmBusiness $business): bool
    {
        return $user->can('view_umkm::business') && $business->owner_id === $user->getKey();
    }

    public function create(User $user): bool
    {
        return $user->can('create_umkm::business');
    }

    public function update(User $user, UmkmBusiness $business): bool
    {
        return $user->can('update_umkm::business') && $business->owner_id === $user->getKey();
    }

    public function delete(User $user, UmkmBusiness $business): bool
    {
        return $user->can('delete_umkm::business') && $business->owner_id === $user->getKey();
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_umkm::business');
    }

    public function forceDelete(User $user, UmkmBusiness $business): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, UmkmBusiness $business): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, UmkmBusiness $business): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
