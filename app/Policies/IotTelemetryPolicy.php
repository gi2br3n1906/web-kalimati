<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\IotTelemetry;
use App\Models\User;

final class IotTelemetryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_sensor::log');
    }

    public function view(User $user, IotTelemetry $telemetry): bool
    {
        return $user->can('view_sensor::log');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, IotTelemetry $telemetry): bool
    {
        return false;
    }

    public function delete(User $user, IotTelemetry $telemetry): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
