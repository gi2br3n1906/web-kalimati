<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\PoiCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\GetGisPointsOfInterestRequest;
use App\Http\Resources\Api\V1\GisPointOfInterestResource;
use App\Models\GisPointOfInterest;
use App\Models\IotDevice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class GisDataController extends Controller
{
    public function pointsOfInterest(GetGisPointsOfInterestRequest $request): JsonResponse
    {
        $category = PoiCategory::tryFrom((string) $request->validated('category', ''));

        $points = GisPointOfInterest::query()
            ->when(
                $category !== null,
                static fn (Builder $query): Builder => $query->where('category', $category->value),
            )
            ->orderBy('name')
            ->get();

        $data = $points
            ->map(
                static fn (GisPointOfInterest $point): array => (new GisPointOfInterestResource($point))
                    ->toArray($request),
            )
            ->all();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function iotDevices(): JsonResponse
    {
        $devices = IotDevice::query()
            ->active()
            ->with(['latestTelemetry', 'latestRecommendation'])
            ->orderBy('name')
            ->get()
            ->map(static function (IotDevice $device): array {
                $telemetry = $device->latestTelemetry;
                $recommendation = $device->latestRecommendation;

                return [
                    'id' => $device->getKey(),
                    'device_code' => $device->device_code,
                    'name' => $device->name,
                    'latitude' => $device->latitude,
                    'longitude' => $device->longitude,
                    'coverage_radius_meters' => $device->coverage_radius_meters,
                    'crop_type' => $device->crop_type,
                    'last_active_at' => $device->last_active_at?->toISOString(),
                    'telemetry' => $telemetry === null ? null : [
                        'temp_air' => $telemetry->temp_air,
                        'hum_air' => $telemetry->hum_air,
                        'temp_soil' => $telemetry->temp_soil,
                        'hum_soil_percent' => $telemetry->hum_soil_percent,
                        'raw_soil' => $telemetry->raw_soil,
                        'lux_light' => $telemetry->lux_light,
                        'recorded_at' => $telemetry->created_at->toISOString(),
                    ],
                    'recommendation' => $recommendation === null ? null : [
                        'condition_status' => $recommendation->condition_status->value,
                        'action_title' => $recommendation->action_title,
                        'recommendation_text' => $recommendation->recommendation_text,
                        'created_at' => $recommendation->created_at->toISOString(),
                    ],
                ];
            });

        return response()->json(['success' => true, 'data' => $devices]);
    }
}
