<?php

declare(strict_types=1);

namespace App\Services\Gis;

use App\Enums\PoiCategory;
use App\Models\GisPointOfInterest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use JsonException;
use RuntimeException;

final class GeminiGisCategorizationService
{
    /**
     * @param  Collection<int, GisPointOfInterest>  $points
     * @return array<int, PoiCategory>
     */
    public function categorize(Collection $points): array
    {
        if ($points->isEmpty()) {
            return [];
        }

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
            ->timeout((int) config('services.gemini.timeout', 30))
            ->retry(3, 500)
            ->post($url, [
                'contents' => [[
                    'role' => 'user',
                    'parts' => [['text' => $this->prompt($points)]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'responseMimeType' => 'application/json',
                    'responseSchema' => $this->responseSchema(),
                ],
            ])
            ->throw()
            ->json();
        $text = Arr::get($response, 'candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini returned an empty GIS categorization response.');
        }

        return $this->decodeAndValidate($text, $points);
    }

    /**
     * @param  Collection<int, GisPointOfInterest>  $points
     */
    private function prompt(Collection $points): string
    {
        $locations = $points
            ->map(static fn (GisPointOfInterest $point): array => [
                'id' => (int) $point->getKey(),
                'name' => $point->name,
            ])
            ->values()
            ->all();

        return implode("\n", [
            'Kamu adalah sistem pengelompokan GIS Desa Kalimati. Klasifikasikan setiap nama lokasi berikut ke dalam SALAH SATU dari kategori resmi ini:',
            '1. Fasilitas Umum & Pemerintahan',
            '2. UMKM & Ekonomi',
            '3. Tempat Ibadah',
            '4. Pendidikan & Kesehatan',
            '5. Infrastruktur & Transportasi',
            '6. Pertanian & Lingkungan',
            '',
            'Kembalikan HANYA format JSON Array murni seperti ini:',
            '[',
            '  {"id": 1, "category": "UMKM & Ekonomi"},',
            '  {"id": 2, "category": "Fasilitas Umum & Pemerintahan"}',
            ']',
            '',
            'Semua ID input wajib muncul tepat satu kali. Jangan menambah ID lain.',
            'Data lokasi:',
            json_encode($locations, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function responseSchema(): array
    {
        return [
            'type' => 'ARRAY',
            'items' => [
                'type' => 'OBJECT',
                'required' => ['id', 'category'],
                'properties' => [
                    'id' => ['type' => 'INTEGER'],
                    'category' => [
                        'type' => 'STRING',
                        'enum' => array_values(array_map(
                            static fn (PoiCategory $category): string => $category->label(),
                            PoiCategory::cases(),
                        )),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  Collection<int, GisPointOfInterest>  $points
     * @return array<int, PoiCategory>
     *
     * @throws JsonException
     */
    private function decodeAndValidate(string $text, Collection $points): array
    {
        $decoded = json_decode($this->stripMarkdownFence($text), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decoded) || ! array_is_list($decoded)) {
            throw new RuntimeException('Gemini GIS response must be a JSON array.');
        }

        $expectedIds = $points
            ->map(static fn (GisPointOfInterest $point): int => (int) $point->getKey())
            ->all();
        $expectedLookup = array_fill_keys($expectedIds, true);
        $categories = [];

        foreach ($decoded as $item) {
            if (! is_array($item) || ! is_int($item['id'] ?? null) || ! is_string($item['category'] ?? null)) {
                throw new RuntimeException('Gemini GIS response contains an invalid item.');
            }

            $id = $item['id'];

            if (! isset($expectedLookup[$id])) {
                throw new RuntimeException("Gemini GIS response contains unknown ID [{$id}].");
            }

            if (isset($categories[$id])) {
                throw new RuntimeException("Gemini GIS response contains duplicate ID [{$id}].");
            }

            $category = PoiCategory::fromLabel($item['category']);

            if ($category === null) {
                throw new RuntimeException("Gemini GIS response contains invalid category [{$item['category']}].");
            }

            $categories[$id] = $category;
        }

        $missingIds = array_values(array_diff($expectedIds, array_keys($categories)));

        if ($missingIds !== []) {
            throw new RuntimeException('Gemini GIS response omitted IDs ['.implode(', ', $missingIds).'].');
        }

        ksort($categories);

        return $categories;
    }

    private function stripMarkdownFence(string $text): string
    {
        $cleaned = trim($text);

        return trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $cleaned) ?? $cleaned);
    }
}
