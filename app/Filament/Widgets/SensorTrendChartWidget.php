<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\SensorLog;
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
        /** @var Collection<int, SensorLog> $logs */
        $logs = SensorLog::query()->latestRecorded()->limit(24)->get()->reverse()->values();

        return [
            'datasets' => [
                ['label' => 'pH', 'data' => $logs->pluck('ph_level')->all(), 'borderColor' => '#2563eb', 'backgroundColor' => 'rgb(37 99 235 / 15%)', 'tension' => 0.3],
                ['label' => 'Kelembapan (%)', 'data' => $logs->pluck('moisture_percentage')->all(), 'borderColor' => '#15803d', 'backgroundColor' => 'rgb(21 128 61 / 15%)', 'tension' => 0.3],
                ['label' => 'Suhu (C)', 'data' => $logs->pluck('temperature_celsius')->all(), 'borderColor' => '#b45309', 'backgroundColor' => 'rgb(180 83 9 / 15%)', 'tension' => 0.3],
            ],
            'labels' => $logs->map(static fn (SensorLog $log): string => $log->recorded_at->format('d M H:i'))->all(),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->can('view_any_sensor::log') ?? false;
    }
}
