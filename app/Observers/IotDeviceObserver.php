<?php

declare(strict_types=1);

namespace App\Observers;

use App\Actions\Agriculture\AssociateIotDeviceToNearestLandGrid;
use App\Models\IotDevice;

final class IotDeviceObserver
{
    public function __construct(
        private readonly AssociateIotDeviceToNearestLandGrid $associateToNearestGrid,
    ) {}

    public function saved(IotDevice $device): void
    {
        if (! $device->wasRecentlyCreated
            && ! $device->wasChanged(['latitude', 'longitude', 'is_active'])
            && $device->land_grid_id !== null) {
            return;
        }

        $this->associateToNearestGrid->execute($device);
    }
}
