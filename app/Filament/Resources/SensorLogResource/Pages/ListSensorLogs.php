<?php

declare(strict_types=1);

namespace App\Filament\Resources\SensorLogResource\Pages;

use App\Filament\Resources\SensorLogResource;
use App\Filament\Widgets\SensorTrendChartWidget;
use Filament\Resources\Pages\ListRecords;

class ListSensorLogs extends ListRecords
{
    protected static string $resource = SensorLogResource::class;

    /**
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            SensorTrendChartWidget::class,
        ];
    }
}
