<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\AiConditionStatus;
use App\Models\AiRecommendation;
use App\Models\IotTelemetry;
use App\Services\IotAiReasoningService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessTelemetryAiReasoning implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [30, 120, 300];

    public function __construct(public readonly IotTelemetry $telemetry) {}

    public function handle(IotAiReasoningService $service): void
    {
        if (AiRecommendation::query()->where('iot_telemetry_id', $this->telemetry->getKey())->exists()) {
            return;
        }

        $service->process($this->telemetry);
    }

    public function failed(?Throwable $exception): void
    {
        Log::warning('IoT AI reasoning failed after all retries.', [
            'iot_telemetry_id' => $this->telemetry->getKey(),
            'exception' => $exception?->getMessage(),
        ]);

        AiRecommendation::query()->firstOrCreate(
            ['iot_telemetry_id' => $this->telemetry->getKey()],
            [
                'iot_device_id' => $this->telemetry->iot_device_id,
                'condition_status' => AiConditionStatus::CAUTION,
                'action_title' => 'Periksa kondisi lahan secara manual',
                'recommendation_text' => 'Analisis AI sedang tidak tersedia. Gunakan pembacaan sensor sebagai acuan awal dan lakukan pemeriksaan langsung sebelum mengambil tindakan.',
            ],
        );
    }
}
