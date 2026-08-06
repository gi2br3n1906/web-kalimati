<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreDeviceTelemetryRequest;
use App\Jobs\ProcessTelemetryAiReasoning;
use App\Models\IotTelemetry;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class IotTelemetryController extends Controller
{
    public function store(StoreDeviceTelemetryRequest $request): JsonResponse
    {
        $device = $request->device();

        /** @var IotTelemetry $telemetry */
        $telemetry = DB::transaction(function () use ($device, $request): IotTelemetry {
            $telemetry = $device->telemetries()->create($request->validated());
            $device->forceFill(['last_active_at' => now()])->save();

            return $telemetry;
        });

        ProcessTelemetryAiReasoning::dispatch($telemetry)->afterCommit();

        return response()->json([
            'success' => true,
            'message' => 'Telemetry successfully received.',
            'data' => [
                'telemetry_id' => $telemetry->getKey(),
                'device_code' => $device->device_code,
                'received_at' => $telemetry->created_at->toISOString(),
            ],
        ]);
    }
}
