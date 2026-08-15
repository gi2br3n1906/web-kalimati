<?php

declare(strict_types=1);

use App\Enums\PoiCategory;
use App\Models\GisPointOfInterest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    config()->set([
        'services.gemini.api_key' => 'test-gemini-key',
        'services.gemini.model' => 'gemini-test-model',
        'services.gemini.models_url' => 'https://gemini.test/v1beta/models',
    ]);
});

it('categorizes all GIS points in chunks and updates their markers', function (): void {
    $points = collect([
        ['name' => 'Balai Desa Kalimati', 'category' => PoiCategory::PERTANIAN_LINGKUNGAN],
        ['name' => 'Warung Bu Siti', 'category' => PoiCategory::PERTANIAN_LINGKUNGAN],
        ['name' => 'Masjid Al Ikhlas', 'category' => PoiCategory::PERTANIAN_LINGKUNGAN],
    ])->map(static fn (array $attributes): GisPointOfInterest => GisPointOfInterest::factory()->create($attributes));

    Http::fakeSequence()
        ->push(geminiCategorizationResponse([
            ['id' => $points[0]->id, 'category' => 'Fasilitas Umum & Pemerintahan'],
            ['id' => $points[1]->id, 'category' => 'UMKM & Ekonomi'],
        ]))
        ->push(geminiCategorizationResponse([
            ['id' => $points[2]->id, 'category' => 'Tempat Ibadah'],
        ]));

    $this->artisan('gis:ai-categorize', ['--chunk' => 2])
        ->expectsOutputToContain('Successfully categorized 3 GIS points.')
        ->assertSuccessful();

    expect($points[0]->refresh()->category)->toBe(PoiCategory::FASILITAS_UMUM_PEMERINTAHAN)
        ->and($points[0]->icon_marker)->toBe('building-government')
        ->and($points[1]->refresh()->category)->toBe(PoiCategory::UMKM_EKONOMI)
        ->and($points[1]->icon_marker)->toBe('storefront')
        ->and($points[2]->refresh()->category)->toBe(PoiCategory::TEMPAT_IBADAH)
        ->and($points[2]->icon_marker)->toBe('place-of-worship');

    Http::assertSentCount(2);
    Http::assertSent(static function (Request $request): bool {
        $prompt = (string) data_get($request->data(), 'contents.0.parts.0.text');

        return str_contains($request->url(), 'gemini-test-model:generateContent')
            && str_contains($prompt, 'Kamu adalah sistem pengelompokan GIS Desa Kalimati')
            && str_contains($prompt, 'UMKM & Ekonomi');
    });
});

it('does not update any GIS point when a later Gemini chunk is invalid', function (): void {
    $points = GisPointOfInterest::factory()
        ->count(3)
        ->create(['category' => PoiCategory::PERTANIAN_LINGKUNGAN]);

    Http::fakeSequence()
        ->push(geminiCategorizationResponse([
            ['id' => $points[0]->id, 'category' => 'UMKM & Ekonomi'],
            ['id' => $points[1]->id, 'category' => 'Tempat Ibadah'],
        ]))
        ->push(geminiCategorizationResponse([]));

    $this->artisan('gis:ai-categorize', ['--chunk' => 2])
        ->expectsOutputToContain('Categorization aborted without database changes')
        ->assertFailed();

    expect($points->map(fn (GisPointOfInterest $point): PoiCategory => $point->refresh()->category)->all())
        ->each->toBe(PoiCategory::PERTANIAN_LINGKUNGAN);
});

it('supports a dry run without changing GIS rows', function (): void {
    $point = GisPointOfInterest::factory()->create([
        'category' => PoiCategory::PERTANIAN_LINGKUNGAN,
        'icon_marker' => 'agriculture-environment',
    ]);

    Http::fake([
        '*' => Http::response(geminiCategorizationResponse([
            ['id' => $point->id, 'category' => 'UMKM & Ekonomi'],
        ])),
    ]);

    $this->artisan('gis:ai-categorize', ['--dry-run' => true])
        ->expectsOutputToContain('Dry run completed. No database rows were changed.')
        ->assertSuccessful();

    expect($point->refresh()->category)->toBe(PoiCategory::PERTANIAN_LINGKUNGAN)
        ->and($point->icon_marker)->toBe('agriculture-environment');
});

it('rejects invalid chunk sizes before contacting Gemini', function (): void {
    GisPointOfInterest::factory()->create();
    Http::fake();

    $this->artisan('gis:ai-categorize', ['--chunk' => 0])
        ->expectsOutputToContain('The --chunk option must be an integer between 1 and 100.')
        ->assertExitCode(2);

    Http::assertNothingSent();
});

/**
 * @param  array<int, array{id: int, category: string}>  $assignments
 * @return array<string, mixed>
 */
function geminiCategorizationResponse(array $assignments): array
{
    return [
        'candidates' => [[
            'content' => [
                'parts' => [[
                    'text' => json_encode($assignments, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                ]],
            ],
        ]],
    ];
}
