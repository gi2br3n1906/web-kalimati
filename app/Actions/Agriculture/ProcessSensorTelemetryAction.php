<?php

declare(strict_types=1);

namespace App\Actions\Agriculture;

use App\Models\LandGrid;
use App\Models\SensorLog;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class ProcessSensorTelemetryAction
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{sensor_log: SensorLog, land_grid: LandGrid}
     *
     * @throws ValidationException
     */
    public function execute(array $payload): array
    {
        $validated = Validator::make($payload, [
            'device_id' => ['required', 'string', 'max:100'],
            'grid_code' => ['required', 'string', 'exists:land_grids,grid_code'],
            'ph_level' => ['required', 'numeric', 'between:0,14'],
            'moisture_percentage' => ['required', 'numeric', 'between:0,100'],
            'temperature_celsius' => ['required', 'numeric', 'between:-99.99,99.99'],
            'recorded_at' => ['required', 'date'],
        ])->validate();

        $landGrid = LandGrid::query()
            ->where('grid_code', $validated['grid_code'])
            ->firstOrFail();

        $sensorLog = $landGrid->sensorLogs()->create([
            'device_id' => $validated['device_id'],
            'ph_level' => $validated['ph_level'],
            'moisture_percentage' => $validated['moisture_percentage'],
            'temperature_celsius' => $validated['temperature_celsius'],
            'raw_payload' => $payload,
            'recorded_at' => CarbonImmutable::parse($validated['recorded_at']),
        ]);

        return [
            'sensor_log' => $sensorLog,
            'land_grid' => $landGrid,
        ];
    }
}
