<?php

declare(strict_types=1);

namespace App\Actions\Agriculture;

use App\Models\IotDevice;
use App\Models\LandGrid;

final class AssociateIotDeviceToNearestLandGrid
{
    public function execute(IotDevice $device): ?LandGrid
    {
        $nearestGrid = LandGrid::query()
            ->active()
            ->get()
            ->sortBy(fn (LandGrid $grid): float => $this->distanceInMeters($device, $grid))
            ->first();

        $nearestGridId = $nearestGrid?->getKey();

        if ($device->land_grid_id !== $nearestGridId) {
            $device->forceFill(['land_grid_id' => $nearestGridId])->saveQuietly();
        }

        return $nearestGrid;
    }

    private function distanceInMeters(IotDevice $device, LandGrid $grid): float
    {
        $earthRadiusMeters = 6_371_000;
        $latitudeDelta = deg2rad($grid->latitude - $device->latitude);
        $longitudeDelta = deg2rad($grid->longitude - $device->longitude);
        $deviceLatitude = deg2rad($device->latitude);
        $gridLatitude = deg2rad($grid->latitude);
        $haversine = sin($latitudeDelta / 2) ** 2
            + cos($deviceLatitude) * cos($gridLatitude) * sin($longitudeDelta / 2) ** 2;

        return $earthRadiusMeters * 2 * atan2(sqrt($haversine), sqrt(1 - $haversine));
    }
}
