<?php

declare(strict_types=1);

namespace App\Services\Agriculture;

use Illuminate\Support\Facades\Http;
use RuntimeException;

final class GeminiRecommendationClient
{
    /**
     * @return array{model_used: string, recommendation: array<string, string>}
     */
    public function generate(string $contextPrompt): array
    {
        $apiKey = (string) config('services.gemini.key');
        $model = (string) config('services.gemini.model', 'gemini-3.6-flash');
        $endpoint = rtrim((string) config('services.gemini.url'), '/');

        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $response = Http::acceptJson()
            ->asJson()
            ->timeout((int) config('services.gemini.timeout', 30))
            ->post($endpoint.'?key='.rawurlencode($apiKey), [
                'model' => $model,
                'system_instruction' => [
                    'parts' => [[
                        'text' => $this->systemInstruction(),
                    ]],
                ],
                'user_input' => [
                    'parts' => [[
                        'text' => $contextPrompt,
                    ]],
                ],
                'generation_config' => [
                    'temperature' => 0.2,
                    'response_mime_type' => 'application/json',
                ],
                'store' => false,
            ])
            ->throw()
            ->json();

        $text = $response['model_output']['parts'][0]['text'] ?? null;

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini returned an empty recommendation.');
        }

        $recommendation = json_decode($this->stripMarkdownFence($text), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($recommendation)) {
            throw new RuntimeException('Gemini recommendation was not a JSON object.');
        }

        return [
            'model_used' => $model,
            'recommendation' => $recommendation,
        ];
    }

    private function systemInstruction(): string
    {
        return implode("\n", [
            'Anda adalah AgriBot Kalimati, ahli pertanian untuk Desa Kalimati, Kecamatan Juwangi, Boyolali.',
            'Gunakan Bahasa Indonesia yang ramah dan mudah dipahami petani.',
            'Analisis data telemetry tanah dan komoditas yang diberikan.',
            'Jangan merekomendasikan pestisida terlarang atau dosis kimia berisiko tanpa data pendukung.',
            'Kembalikan JSON valid saja dengan empat string: soil_condition_summary, fertilizer_dosage, lime_treatment, action_plan.',
        ]);
    }

    private function stripMarkdownFence(string $text): string
    {
        $cleaned = trim($text);

        if (str_starts_with($cleaned, '```')) {
            $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $cleaned) ?? $cleaned;
        }

        return trim($cleaned);
    }
}
