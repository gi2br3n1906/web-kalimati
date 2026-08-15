<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Enums\AiConditionStatus;
use App\Models\AiRecommendation;
use App\Models\IotTelemetry;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin IotTelemetry */
final class IotTelemetryPointResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $recommendation = $this->aiRecommendation;

        return [
            'id' => $this->getKey(),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'measured_at' => $this->formatMeasuredAt($this->created_at),
            'sensor_data' => [
                'temp_air' => $this->temp_air,
                'hum_air' => $this->hum_air,
                'temp_soil' => $this->temp_soil,
                'hum_soil_percent' => $this->hum_soil_percent,
                'lux_light' => $this->lux_light,
            ],
            'condition_status' => $this->conditionStatus($recommendation),
            'recommendation' => $recommendation === null ? null : [
                'action_title' => $recommendation->action_title,
                'recommendation_text' => $recommendation->recommendation_text,
            ],
            'device' => [
                'id' => $this->device->getKey(),
                'device_code' => $this->device->device_code,
                'name' => $this->device->name,
            ],
        ];
    }

    private function conditionStatus(?AiRecommendation $recommendation): string
    {
        return match ($recommendation?->condition_status) {
            AiConditionStatus::OPTIMAL => AiConditionStatus::OPTIMAL->value,
            AiConditionStatus::CAUTION => AiConditionStatus::CAUTION->value,
            AiConditionStatus::WARNING,
            AiConditionStatus::CRITICAL => AiConditionStatus::WARNING->value,
            null => AiConditionStatus::CAUTION->value,
        };
    }

    private function formatMeasuredAt(CarbonInterface $measuredAt): string
    {
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];
        $localTime = $measuredAt->timezone(config('app.timezone'));

        return sprintf(
            '%s %s %s, %s WIB',
            $localTime->format('d'),
            $months[$localTime->month],
            $localTime->format('Y'),
            $localTime->format('H:i'),
        );
    }
}
