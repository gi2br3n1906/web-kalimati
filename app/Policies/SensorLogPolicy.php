<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SensorLog;
use App\Models\User;

class SensorLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_sensor::log');
    }

    public function view(User $user, SensorLog $sensorLog): bool
    {
        return $user->can('view_sensor::log');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, SensorLog $sensorLog): bool
    {
        return false;
    }

    public function delete(User $user, SensorLog $sensorLog): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, SensorLog $sensorLog): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, SensorLog $sensorLog): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, SensorLog $sensorLog): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
