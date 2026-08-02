<?php

declare(strict_types=1);

namespace App\Actions\Agriculture;

use App\Models\LandGrid;
use App\Models\LandRecommendation;
use App\Models\SensorLog;
use App\Services\Agriculture\GeminiRecommendationClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

final class FetchLLMRecommendationAction
{
    public function __construct(private readonly GeminiRecommendationClient $geminiClient) {}

    public function execute(LandGrid $landGrid, ?SensorLog $sensorLog = null): LandRecommendation
    {
        $sensorLog ??= $landGrid->sensorLogs()->latestRecorded()->first();

        if ($sensorLog === null || $sensorLog->ph_level <= 0) {
            return $this->fallback($landGrid, $sensorLog, 'Data sensor tanah tidak valid. Harap periksa koneksi perangkat IoT di lahan.');
        }

        try {
            $response = $this->fetchRecommendation($landGrid, $sensorLog);

            $recommendation = Arr::get($response, 'recommendation');

            if (! is_array($recommendation) || ! $this->hasRequiredRecommendationFields($recommendation)) {
                throw new \UnexpectedValueException('LLM recommendation response did not match the required schema.');
            }

            return LandRecommendation::query()->create([
                'land_grid_id' => $landGrid->getKey(),
                'sensor_log_id' => $sensorLog->getKey(),
                'ai_model_used' => (string) Arr::get($response, 'model_used', 'LLM-RAG'),
                'soil_condition_summary' => $recommendation['soil_condition_summary'],
                'fertilizer_dosage' => $recommendation['fertilizer_dosage'],
                'lime_treatment' => $recommendation['lime_treatment'],
                'action_plan' => $recommendation['action_plan'],
                'is_applied' => false,
            ]);
        } catch (Throwable $exception) {
            Log::warning('LLM soil recommendation request failed.', [
                'land_grid_id' => $landGrid->getKey(),
                'sensor_log_id' => $sensorLog->getKey(),
                'exception' => $exception->getMessage(),
            ]);

            return $this->fallback($landGrid, $sensorLog);
        }
    }

    /**
     * @return array{model_used: string, recommendation: array<array-key, mixed>}
     */
    private function fetchRecommendation(LandGrid $landGrid, SensorLog $sensorLog): array
    {
        return $this->geminiClient->generate($this->buildContextPrompt($landGrid, $sensorLog));
    }

    /**
     * Builds the factual user context independently from Gemini's behavioral instruction.
     */
    private function buildContextPrompt(LandGrid $landGrid, SensorLog $sensorLog): string
    {
        return json_encode([
            'land_grid_id' => (int) $landGrid->getKey(),
            'grid_code' => $landGrid->grid_code,
            'dusun_name' => $landGrid->dusun_name,
            'commodity_type' => $landGrid->commodity_type->value,
            'telemetry_metrics' => [
                'ph_level' => $sensorLog->ph_level,
                'moisture_percentage' => $sensorLog->moisture_percentage,
                'temperature_celsius' => $sensorLog->temperature_celsius,
            ],
            'historical_treatments_count' => $landGrid->recommendations()->where('is_applied', true)->count(),
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param  array<array-key, mixed>  $recommendation
     */
    private function hasRequiredRecommendationFields(array $recommendation): bool
    {
        foreach (['soil_condition_summary', 'fertilizer_dosage', 'lime_treatment', 'action_plan'] as $field) {
            if (! is_string($recommendation[$field] ?? null) || blank($recommendation[$field])) {
                return false;
            }
        }

        return true;
    }

    private function fallback(LandGrid $landGrid, ?SensorLog $sensorLog, ?string $summary = null): LandRecommendation
    {
        return LandRecommendation::query()->create([
            'land_grid_id' => $landGrid->getKey(),
            'sensor_log_id' => $sensorLog?->getKey(),
            'ai_model_used' => 'fallback-offline',
            'soil_condition_summary' => $summary ?? 'Layanan rekomendasi AI sedang tidak tersedia. Gunakan pembacaan sensor terbaru sebagai dasar pemeriksaan lapangan.',
            'fertilizer_dosage' => 'Tunda perubahan dosis pupuk hingga layanan rekomendasi tersedia atau konsultasikan dengan penyuluh pertanian setempat.',
            'lime_treatment' => 'Periksa pH tanah secara manual sebelum melakukan aplikasi dolomit.',
            'action_plan' => '1. Periksa koneksi sensor dan ulangi pengambilan data. 2. Amati kondisi tanaman di lahan. 3. Hubungi penyuluh pertanian bila gejala tanaman memburuk.',
            'is_applied' => false,
        ]);
    }
}
