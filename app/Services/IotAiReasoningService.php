<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AiConditionStatus;
use App\Models\AiRecommendation;
use App\Models\IotTelemetry;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class IotAiReasoningService
{
    public function process(IotTelemetry $telemetry): AiRecommendation
    {
        $telemetry->loadMissing('device');
        $provider = strtolower((string) config('services.llm.provider', 'rag'));
        $data = match ($provider) {
            'gemini' => $this->requestGemini($telemetry),
            'rag' => $this->requestRag($telemetry),
            default => throw new RuntimeException("Unsupported LLM provider [{$provider}]."),
        };
        $status = AiConditionStatus::tryFrom((string) ($data['condition_status'] ?? ''));
        $headline = trim((string) ($data['headline'] ?? $data['action_title'] ?? ''));
        $recommendation = trim((string) ($data['action_recommendation'] ?? $data['recommendation_text'] ?? ''));

        if (! in_array($status, [AiConditionStatus::OPTIMAL, AiConditionStatus::CAUTION, AiConditionStatus::WARNING], true)
            || $headline === ''
            || $recommendation === '') {
            throw new RuntimeException('LLM IoT response did not match the required schema.');
        }

        return DB::transaction(function () use ($telemetry, $status, $headline, $recommendation): AiRecommendation {
            $result = AiRecommendation::query()->create([
                'iot_device_id' => $telemetry->iot_device_id,
                'iot_telemetry_id' => $telemetry->getKey(),
                'condition_status' => $status,
                'action_title' => $headline,
                'recommendation_text' => $recommendation,
            ]);

            $telemetry->device->forceFill(['last_active_at' => now()])->save();

            return $result;
        });
    }

    /** @return array<string, mixed> */
    private function requestGemini(IotTelemetry $telemetry): array
    {
        $apiKey = (string) (config('services.gemini.api_key') ?: config('services.gemini.key'));
        $model = (string) config('services.gemini.model');

        if ($apiKey === '' || $model === '') {
            throw new RuntimeException('GEMINI_API_KEY and GEMINI_MODEL must be configured.');
        }

        $url = rtrim((string) config('services.gemini.models_url'), '/')
            .'/'.rawurlencode($model)
            .':generateContent?key='.rawurlencode($apiKey);
        $response = Http::acceptJson()
            ->asJson()
            ->timeout(30)
            ->post($url, [
                'systemInstruction' => ['parts' => [['text' => $this->systemInstruction()]]],
                'contents' => [[
                    'role' => 'user',
                    'parts' => [['text' => $this->prompt($telemetry)]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'responseMimeType' => 'application/json',
                    'responseSchema' => [
                        'type' => 'OBJECT',
                        'required' => ['condition_status', 'headline', 'action_recommendation'],
                        'properties' => [
                            'condition_status' => ['type' => 'STRING', 'enum' => ['optimal', 'caution', 'warning']],
                            'headline' => ['type' => 'STRING'],
                            'action_recommendation' => ['type' => 'STRING'],
                        ],
                    ],
                ],
            ])->throw()->json();

        $text = Arr::get($response, 'candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini returned an empty IoT recommendation.');
        }

        return $this->decodeRecommendation($text);
    }

    /** @return array<string, mixed> */
    private function requestRag(IotTelemetry $telemetry): array
    {
        $url = (string) config('services.llm.url');

        if ($url === '') {
            throw new RuntimeException('LLM_SERVICE_URL is not configured.');
        }

        $response = $this->ragRequest()
            ->post($url, [
                'context' => $this->systemInstruction(),
                'input' => json_decode($this->prompt($telemetry), true, 512, JSON_THROW_ON_ERROR),
                'expected_output' => [
                    'condition_status' => 'optimal|caution|warning',
                    'headline' => 'string ringkas status lahan',
                    'action_recommendation' => 'saran tindakan konkret bercocok tanam untuk petani',
                ],
            ])
            ->throw()
            ->json();

        $data = Arr::get($response, 'recommendation', $response);

        if (! is_array($data)) {
            throw new RuntimeException('RAG returned an invalid IoT recommendation.');
        }

        return $data;
    }

    private function ragRequest(): PendingRequest
    {
        $request = Http::acceptJson()
            ->asJson()
            ->timeout((int) config('services.llm.timeout', 15));
        $apiKey = (string) config('services.llm.api_key');

        if ($apiKey !== '') {
            $request = $request->withHeaders(['X-API-Key' => $apiKey]);
        }

        return $request;
    }

    private function systemInstruction(): string
    {
        return implode("\n", [
            'Anda adalah AgriBot Kalimati, ahli monitoring pertanian Desa Kalimati, Juwangi, Boyolali.',
            'Lokasi dan konteks lokal: Desa Kalimati, sawah tadah hujan, komoditas utama Jagung dan Pisang.',
            'Acuan hama lokal: Tikus, Ulat Grayak, Bule (Fungisida), dan Engkok/Uret.',
            'Berikan saran aman, praktis, ringkas, dan berbahasa Indonesia. Jangan mendiagnosis hama tanpa bukti telemetry/lapangan.',
            'Kembalikan JSON valid saja: condition_status (optimal|caution|warning), headline, action_recommendation.',
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
                'temp_air' => $telemetry->temp_air,
                'hum_air' => $telemetry->hum_air,
                'temp_soil' => $telemetry->temp_soil,
                'hum_soil_percent' => $telemetry->hum_soil_percent,
                'lux_light' => $telemetry->lux_light,
                'recorded_at' => $telemetry->created_at->toISOString(),
            ],
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /** @return array<string, mixed> */
    private function decodeRecommendation(string $text): array
    {
        $data = json_decode($this->stripMarkdownFence($text), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($data)) {
            throw new RuntimeException('Gemini IoT recommendation was not a JSON object.');
        }

        return $data;
    }

    private function stripMarkdownFence(string $text): string
    {
        $cleaned = trim($text);

        return trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $cleaned) ?? $cleaned);
    }
}
