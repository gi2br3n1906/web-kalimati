<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Agriculture\ProcessSensorTelemetryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreIotTelemetryRequest;
use Illuminate\Http\JsonResponse;

class IotTelemetryController extends Controller
{
    public function store(StoreIotTelemetryRequest $request, ProcessSensorTelemetryAction $processTelemetry): JsonResponse
    {
        $result = $processTelemetry->execute($request->all());
        $sensorLog = $result['sensor_log'];

        return response()->json([
            'success' => true,
            'message' => 'Telemetry log successfully stored.',
            'data' => [
                'sensor_log_id' => $sensorLog->getKey(),
                'land_grid_id' => $sensorLog->land_grid_id,
                'recorded_at' => $sensorLog->recorded_at->toISOString(),
            ],
        ], 201);
    }
}
