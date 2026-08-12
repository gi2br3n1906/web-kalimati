<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\IotTelemetry;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class SensorTrendChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Tren Sensor Tanah';

    protected static ?string $description = '24 pembacaan sensor terbaru dari seluruh grid lahan.';

    protected static ?int $sort = 1;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        /** @var Collection<int, IotTelemetry> $logs */
        $logs = IotTelemetry::query()->latest()->limit(24)->get()->reverse()->values();

        return [
            'datasets' => [
                ['label' => 'Suhu Udara (C)', 'data' => $logs->pluck('temp_air')->all(), 'borderColor' => '#b45309', 'backgroundColor' => 'rgb(180 83 9 / 15%)', 'tension' => 0.3],
                ['label' => 'Kelembapan Udara (%)', 'data' => $logs->pluck('hum_air')->all(), 'borderColor' => '#2563eb', 'backgroundColor' => 'rgb(37 99 235 / 15%)', 'tension' => 0.3],
                ['label' => 'Kelembapan Tanah (%)', 'data' => $logs->pluck('hum_soil_percent')->all(), 'borderColor' => '#15803d', 'backgroundColor' => 'rgb(21 128 61 / 15%)', 'tension' => 0.3],
            ],
            'labels' => $logs->map(static fn (IotTelemetry $log): string => $log->created_at->format('d M H:i'))->all(),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('view_any_sensor::log') ?? false;
    }
}
