<?php

declare(strict_types=1);

use App\Actions\Agriculture\FetchLLMRecommendationAction;
use App\Models\LandGrid;
use App\Models\LandRecommendation;
use App\Models\SensorLog;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    config()->set('services.llm.url', 'http://llm.test/api/v1/recommend');
    config()->set('services.llm.api_key', 'phase-three-llm-key');
    config()->set('services.llm.timeout', 5);
});

it('persists an llm recommendation using the blueprint payload and response schema', function (): void {
    $grid = LandGrid::factory()->create(['grid_code' => 'KAL-DAMPIT-A12']);
    $sensorLog = SensorLog::factory()->for($grid)->create([
        'ph_level' => 5.85,
        'moisture_percentage' => 42.50,
        'temperature_celsius' => 28.40,
    ]);

    Http::fake([
        'http://llm.test/api/v1/recommend' => Http::response([
            'success' => true,
            'model_used' => 'Ollama-Llama3-RAG-Agri',
            'recommendation' => [
                'soil_condition_summary' => 'Tanah agak asam dengan kelembapan sedang.',
                'fertilizer_dosage' => 'Kompos 5 kg per lubang tanam.',
                'lime_treatment' => 'Dolomit 200 gram per 10 meter persegi.',
                'action_plan' => '1. Aplikasikan dolomit. 2. Beri kompos. 3. Pantau kelembapan.',
            ],
        ]),
    ]);

    $recommendation = app(FetchLLMRecommendationAction::class)->execute($grid, $sensorLog);

    expect($recommendation->ai_model_used)->toBe('Ollama-Llama3-RAG-Agri')
        ->and($recommendation->sensor_log_id)->toBe($sensorLog->id);

    $this->assertDatabaseHas('land_recommendations', [
        'id' => $recommendation->id,
        'land_grid_id' => $grid->id,
        'sensor_log_id' => $sensorLog->id,
    ]);

    Http::assertSent(function (Request $request) use ($grid, $sensorLog): bool {
        return $request->url() === 'http://llm.test/api/v1/recommend'
            && $request->hasHeader('X-API-Key', 'phase-three-llm-key')
            && $request['grid_code'] === $grid->grid_code
            && $request['telemetry_metrics']['ph_level'] === $sensorLog->ph_level;
    });
});

it('persists a safe fallback recommendation when the llm service fails', function (): void {
    $grid = LandGrid::factory()->create();
    $sensorLog = SensorLog::factory()->for($grid)->create();

    Http::fake([
        'http://llm.test/api/v1/recommend' => Http::response(['message' => 'Unavailable'], 503),
    ]);

    $recommendation = app(FetchLLMRecommendationAction::class)->execute($grid, $sensorLog);

    expect($recommendation)->toBeInstanceOf(LandRecommendation::class)
        ->and($recommendation->ai_model_used)->toBe('fallback-offline')
        ->and($recommendation->is_applied)->toBeFalse();

    $this->assertDatabaseHas('land_recommendations', [
        'id' => $recommendation->id,
        'land_grid_id' => $grid->id,
        'sensor_log_id' => $sensorLog->id,
        'ai_model_used' => 'fallback-offline',
    ]);
});
