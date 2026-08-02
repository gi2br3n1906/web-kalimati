<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleType;
use App\Models\UmkmLedger;
use App\Models\User;

class UmkmLedgerPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole(RoleType::SUPER_ADMIN->value) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_umkm::ledger');
    }

    public function view(User $user, UmkmLedger $ledger): bool
    {
        return $user->can('view_umkm::ledger') && $ledger->business->owner_id === $user->getKey();
    }

    public function create(User $user): bool
    {
        return $user->can('create_umkm::ledger');
    }

    public function update(User $user, UmkmLedger $ledger): bool
    {
        return $user->can('update_umkm::ledger') && $ledger->business->owner_id === $user->getKey();
    }

    public function delete(User $user, UmkmLedger $ledger): bool
    {
        return $user->can('delete_umkm::ledger') && $ledger->business->owner_id === $user->getKey();
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_umkm::ledger');
    }

    public function forceDelete(User $user, UmkmLedger $ledger): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, UmkmLedger $ledger): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, UmkmLedger $ledger): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
