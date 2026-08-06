<?php

declare(strict_types=1);

use App\Enums\AiConditionStatus;
use App\Models\IotDevice;
use App\Models\IotTelemetry;
use App\Services\IotAiReasoningService;
use Illuminate\Support\Facades\Http;

it('creates a structured recommendation from Gemini with Kalimati context', function (): void {
    config()->set('services.gemini.key', 'test-key');
    config()->set('services.gemini.url', 'https://gemini.test/interactions');
    $device = IotDevice::factory()->create(['crop_type' => 'Jagung & Pisang']);
    $telemetry = IotTelemetry::factory()->for($device, 'device')->create();

    Http::fake([
        'https://gemini.test/*' => Http::response([
            'model_output' => ['parts' => [['text' => json_encode([
                'condition_status' => 'warning',
                'action_title' => 'Periksa kelembapan lahan',
                'recommendation_text' => 'Lakukan pemeriksaan langsung dan atur pemberian air secara bertahap.',
            ], JSON_THROW_ON_ERROR)]]],
        ]),
    ]);

    $recommendation = app(IotAiReasoningService::class)->process($telemetry);

    expect($recommendation->condition_status)->toBe(AiConditionStatus::WARNING);
    Http::assertSent(function ($request): bool {
        $instruction = $request->data()['system_instruction']['parts'][0]['text'];

        return str_contains($instruction, '100% tadah hujan')
            && str_contains($instruction, 'Ulat Grayak')
            && str_contains($instruction, 'Engkok (Uret)');
    });
});
