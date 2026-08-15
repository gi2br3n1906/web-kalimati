<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\PoiCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\GetGisPointsOfInterestRequest;
use App\Http\Resources\Api\V1\GisPointOfInterestResource;
use App\Http\Resources\Api\V1\IotTelemetryPointResource;
use App\Models\GisPointOfInterest;
use App\Models\IotTelemetry;
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

    public function telemetries(): JsonResponse
    {
        $telemetries = IotTelemetry::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['aiRecommendation', 'device'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => IotTelemetryPointResource::collection($telemetries)->resolve(),
        ]);
    }

    public function iotDevices(): JsonResponse
    {
        return $this->telemetries();
    }
}
