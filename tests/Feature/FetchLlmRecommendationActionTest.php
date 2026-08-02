<?php

declare(strict_types=1);

use App\Actions\Agriculture\FetchLLMRecommendationAction;
use App\Models\LandGrid;
use App\Models\LandRecommendation;
use App\Models\SensorLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    config()->set('services.gemini.key', 'gemini-test-key');
    config()->set('services.gemini.model', 'gemini-3.6-flash');
    config()->set('services.gemini.url', 'https://generativelanguage.googleapis.com/v1beta/interactions');
    config()->set('services.gemini.timeout', 5);
});

it('persists an Interactions API recommendation with a private JSON-only request', function (): void {
    $grid = LandGrid::factory()->create(['grid_code' => 'KAL-DAMPIT-A12']);
    $sensorLog = SensorLog::factory()->for($grid)->create([
        'ph_level' => 5.85,
        'moisture_percentage' => 42.50,
        'temperature_celsius' => 28.40,
    ]);

    Http::fake([
        'https://generativelanguage.googleapis.com/v1beta/interactions?key=gemini-test-key' => Http::response([
            'model_output' => [
                'parts' => [[
                    'text' => json_encode([
                        'soil_condition_summary' => 'Tanah agak asam dengan kelembapan sedang.',
                        'fertilizer_dosage' => 'Kompos 5 kg per lubang tanam.',
                        'lime_treatment' => 'Dolomit 200 gram per 10 meter persegi.',
                        'action_plan' => '1. Aplikasikan dolomit. 2. Beri kompos. 3. Pantau kelembapan.',
                    ], JSON_THROW_ON_ERROR),
                ]],
            ],
        ]),
    ]);

    $recommendation = app(FetchLLMRecommendationAction::class)->execute($grid, $sensorLog);

    expect($recommendation->ai_model_used)->toBe('gemini-3.6-flash')
        ->and($recommendation->sensor_log_id)->toBe($sensorLog->id);

    $this->assertDatabaseHas('land_recommendations', [
        'id' => $recommendation->id,
        'land_grid_id' => $grid->id,
        'sensor_log_id' => $sensorLog->id,
    ]);

    Http::assertSent(function (Request $request) use ($grid): bool {
        return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/interactions?key=gemini-test-key'
            && $request['model'] === 'gemini-3.6-flash'
            && $request['store'] === false
            && $request['generation_config']['response_mime_type'] === 'application/json'
            && $request['generation_config']['temperature'] === 0.2
            && str_contains($request['user_input']['parts'][0]['text'], $grid->grid_code);
    });
});

it('persists a safe fallback when the Interactions API returns an error response', function (): void {
    $grid = LandGrid::factory()->create();
    $sensorLog = SensorLog::factory()->for($grid)->create();

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response(['error' => ['message' => 'Unavailable']], 500),
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

it('persists a safe fallback when the Interactions API times out', function (): void {
    $grid = LandGrid::factory()->create();
    $sensorLog = SensorLog::factory()->for($grid)->create();

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => static fn (): never => throw new ConnectionException('Connection timed out.'),
    ]);

    $recommendation = app(FetchLLMRecommendationAction::class)->execute($grid, $sensorLog);

    expect($recommendation->ai_model_used)->toBe('fallback-offline');
});

it('persists a safe fallback when the Interactions API returns malformed JSON output', function (): void {
    $grid = LandGrid::factory()->create();
    $sensorLog = SensorLog::factory()->for($grid)->create();

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'model_output' => ['parts' => [['text' => 'not valid JSON']]],
        ]),
    ]);

    $recommendation = app(FetchLLMRecommendationAction::class)->execute($grid, $sensorLog);

    expect($recommendation->ai_model_used)->toBe('fallback-offline');
});
