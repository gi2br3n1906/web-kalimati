<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\IotDevice;
use App\Models\User;

class IotDevicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'kelompok_tani'])
            || $user->can('view_any_iot::device');
    }

    public function view(User $user, IotDevice $device): bool
    {
        return $user->can('view_iot::device');
    }

    public function create(User $user): bool
    {
        return $user->can('create_iot::device');
    }

    public function update(User $user, IotDevice $device): bool
    {
        return $user->can('update_iot::device');
    }

    public function delete(User $user, IotDevice $device): bool
    {
        return $user->can('delete_iot::device');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_iot::device');
    }
}
