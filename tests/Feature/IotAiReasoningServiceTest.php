<?php

declare(strict_types=1);

use App\Enums\AiConditionStatus;
use App\Jobs\ProcessTelemetryAiReasoning;
use App\Models\IotDevice;
use App\Models\IotTelemetry;
use App\Services\IotAiReasoningService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('processes telemetry through the direct Gemini provider', function (): void {
    config()->set('services.llm.provider', 'gemini');
    config()->set('services.gemini.api_key', 'gemini-test-key');
    config()->set('services.gemini.model', 'gemini-2.5-flash');
    config()->set('services.gemini.models_url', 'https://generativelanguage.googleapis.com/v1beta/models');

    $device = IotDevice::factory()->create([
        'crop_type' => 'Jagung & Pisang',
        'last_active_at' => null,
    ]);
    $telemetry = IotTelemetry::factory()->for($device, 'device')->create([
        'temp_air' => 31.2,
        'hum_air' => 68.4,
        'temp_soil' => 28.1,
        'hum_soil_percent' => 45.6,
        'lux_light' => 15400,
    ]);

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [[
                'content' => ['parts' => [['text' => json_encode([
                    'condition_status' => 'warning',
                    'headline' => 'Kelembapan tanah perlu perhatian',
                    'action_recommendation' => 'Periksa kelembapan langsung dan lakukan penyiraman bertahap pada tanaman jagung dan pisang.',
                ], JSON_THROW_ON_ERROR)]]],
            ]],
        ]),
    ]);

    (new ProcessTelemetryAiReasoning($telemetry))->handle(app(IotAiReasoningService::class));

    $this->assertDatabaseHas('ai_recommendations', [
        'iot_device_id' => $device->id,
        'iot_telemetry_id' => $telemetry->id,
        'condition_status' => AiConditionStatus::WARNING->value,
        'action_title' => 'Kelembapan tanah perlu perhatian',
        'recommendation_text' => 'Periksa kelembapan langsung dan lakukan penyiraman bertahap pada tanaman jagung dan pisang.',
    ]);
    expect($device->fresh()->last_active_at)->not->toBeNull();

    Http::assertSent(function (Request $request): bool {
        $instruction = (string) $request['systemInstruction']['parts'][0]['text'];
        $prompt = (string) $request['contents'][0]['parts'][0]['text'];

        return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=gemini-test-key'
            && $request['generationConfig']['responseMimeType'] === 'application/json'
            && $request['generationConfig']['responseSchema']['required'] === ['condition_status', 'headline', 'action_recommendation']
            && str_contains($instruction, 'Desa Kalimati')
            && str_contains($instruction, 'sawah tadah hujan')
            && str_contains($instruction, 'Jagung dan Pisang')
            && str_contains($instruction, 'Tikus')
            && str_contains($instruction, 'Ulat Grayak')
            && str_contains($instruction, 'Bule (Fungisida)')
            && str_contains($instruction, 'Engkok/Uret')
            && str_contains($prompt, 'temp_air')
            && str_contains($prompt, 'hum_air')
            && str_contains($prompt, 'temp_soil')
            && str_contains($prompt, 'hum_soil_percent')
            && str_contains($prompt, 'lux_light');
    });
});

it('uses the RAG service only when the RAG provider is selected', function (): void {
    config()->set('services.llm.provider', 'rag');
    config()->set('services.llm.url', 'http://127.0.0.1:8001/api/v1/recommend');
    config()->set('services.llm.api_key', 'rag-test-key');
    $device = IotDevice::factory()->create(['last_active_at' => null]);
    $telemetry = IotTelemetry::factory()->for($device, 'device')->create();

    Http::fake([
        'http://127.0.0.1:8001/*' => Http::response([
            'success' => true,
            'recommendation' => [
                'condition_status' => 'optimal',
                'headline' => 'Kondisi lahan stabil',
                'action_recommendation' => 'Pertahankan pemantauan dan pola penyiraman saat ini.',
            ],
        ]),
    ]);

    (new ProcessTelemetryAiReasoning($telemetry))->handle(app(IotAiReasoningService::class));

    $this->assertDatabaseHas('ai_recommendations', [
        'iot_telemetry_id' => $telemetry->id,
        'condition_status' => AiConditionStatus::OPTIMAL->value,
        'action_title' => 'Kondisi lahan stabil',
    ]);
    expect($device->fresh()->last_active_at)->not->toBeNull();

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'http://127.0.0.1:8001/api/v1/recommend'
            && $request->hasHeader('X-API-Key', 'rag-test-key')
            && $request['input']['telemetry']['temp_air'] !== null
            && $request['expected_output']['condition_status'] === 'optimal|caution|warning';
    });
    Http::assertSentCount(1);
});
