<?php

declare(strict_types=1);

namespace App\Services\Agriculture;

use Illuminate\Support\Facades\Http;
use RuntimeException;

final class GeminiRecommendationClient
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{model_used: string, recommendation: array<string, string>}
     */
    public function generate(array $payload): array
    {
        $apiKey = (string) config('services.gemini.api_key');
        $model = (string) config('services.gemini.model', 'gemini-2.0-flash');
        $baseUrl = rtrim((string) config('services.gemini.url'), '/');

        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $response = Http::acceptJson()
            ->asJson()
            ->withHeaders(['x-goog-api-key' => $apiKey])
            ->timeout((int) config('services.gemini.timeout', 30))
            ->post("{$baseUrl}/models/{$model}:generateContent", [
                'systemInstruction' => [
                    'parts' => [[
                        'text' => $this->systemInstruction(),
                    ]],
                ],
                'contents' => [[
                    'role' => 'user',
                    'parts' => [[
                        'text' => json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                    ]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'responseMimeType' => 'application/json',
                ],
            ])
            ->throw()
            ->json();

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

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
