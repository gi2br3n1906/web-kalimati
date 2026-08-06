<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AiConditionStatus;
use App\Models\AiRecommendation;
use App\Models\IotTelemetry;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class IotAiReasoningService
{
    public function process(IotTelemetry $telemetry): AiRecommendation
    {
        $telemetry->loadMissing('device');
        $apiKey = (string) config('services.gemini.key');

        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $response = Http::acceptJson()
            ->asJson()
            ->timeout((int) config('services.gemini.timeout', 30))
            ->post(rtrim((string) config('services.gemini.url'), '/').'?key='.rawurlencode($apiKey), [
                'model' => (string) config('services.gemini.model'),
                'system_instruction' => ['parts' => [['text' => $this->systemInstruction()]]],
                'user_input' => ['parts' => [['text' => $this->prompt($telemetry)]]],
                'generation_config' => [
                    'temperature' => 0.2,
                    'response_mime_type' => 'application/json',
                    'response_schema' => [
                        'type' => 'object',
                        'required' => ['condition_status', 'action_title', 'recommendation_text'],
                        'properties' => [
                            'condition_status' => ['type' => 'string', 'enum' => array_column(AiConditionStatus::cases(), 'value')],
                            'action_title' => ['type' => 'string'],
                            'recommendation_text' => ['type' => 'string'],
                        ],
                    ],
                ],
                'store' => false,
            ])->throw()->json();

        $text = $response['model_output']['parts'][0]['text'] ?? null;

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini returned an empty IoT recommendation.');
        }

        /** @var array<string, mixed> $data */
        $data = json_decode($this->stripMarkdownFence($text), true, 512, JSON_THROW_ON_ERROR);
        $status = AiConditionStatus::tryFrom((string) ($data['condition_status'] ?? ''));
        $title = trim((string) ($data['action_title'] ?? ''));
        $recommendation = trim((string) ($data['recommendation_text'] ?? ''));

        if ($status === null || $title === '' || $recommendation === '') {
            throw new RuntimeException('Gemini IoT response did not match the required schema.');
        }

        return AiRecommendation::query()->create([
            'iot_device_id' => $telemetry->iot_device_id,
            'iot_telemetry_id' => $telemetry->getKey(),
            'condition_status' => $status,
            'action_title' => $title,
            'recommendation_text' => $recommendation,
        ]);
    }

    private function systemInstruction(): string
    {
        return implode("\n", [
            'Anda adalah AgriBot Kalimati, ahli monitoring pertanian Desa Kalimati, Juwangi, Boyolali.',
            'Konteks lokal: sawah 100% tadah hujan; komoditas utama Jagung dan Pisang dengan pola tumpang sari.',
            'Acuan hama lokal: Tikus, Ulat Grayak, Bule, serta Engkok (Uret).',
            'Berikan saran aman, praktis, ringkas, dan berbahasa Indonesia. Jangan mendiagnosis hama tanpa bukti telemetry/lapangan.',
            'Kembalikan JSON valid saja: condition_status (optimal|caution|warning|critical), action_title, recommendation_text.',
        ]);
    }

    private function prompt(IotTelemetry $telemetry): string
    {
        return json_encode([
            'device' => [
                'code' => $telemetry->device->device_code,
                'name' => $telemetry->device->name,
                'crop_type' => $telemetry->device->crop_type,
            ],
            'telemetry' => [
                'temp_air_celsius' => $telemetry->temp_air,
                'air_humidity_percent' => $telemetry->hum_air,
                'temp_soil_celsius' => $telemetry->temp_soil,
                'soil_humidity_percent' => $telemetry->hum_soil_percent,
                'raw_soil' => $telemetry->raw_soil,
                'light_lux' => $telemetry->lux_light,
                'recorded_at' => $telemetry->created_at->toISOString(),
            ],
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    private function stripMarkdownFence(string $text): string
    {
        $cleaned = trim($text);

        return trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $cleaned) ?? $cleaned);
    }
}
