<?php

declare(strict_types=1);

use App\Enums\NewsCategory;
use App\Enums\PoiCategory;
use App\Models\GisPointOfInterest;
use App\Models\NewsArticle;

it('generates unique slugs for news articles', function (): void {
    $first = NewsArticle::factory()->create([
        'title' => 'Panen Raya Desa Kalimati',
        'slug' => null,
        'category' => NewsCategory::KEGIATAN,
    ]);

    $second = NewsArticle::factory()->create([
        'title' => 'Panen Raya Desa Kalimati',
        'slug' => null,
        'category' => NewsCategory::KEGIATAN,
    ]);

    expect($first->slug)->toBe('panen-raya-desa-kalimati')
        ->and($second->slug)->toBe('panen-raya-desa-kalimati-2');
});

it('scopes only published news articles', function (): void {
    $published = NewsArticle::factory()->published()->create([
        'title' => 'Pengumuman Pelayanan Desa',
    ]);

    NewsArticle::factory()->create([
        'title' => 'Draft Internal',
        'is_published' => false,
        'published_at' => null,
    ]);

    NewsArticle::factory()->create([
        'title' => 'Terjadwal Besok',
        'is_published' => true,
        'published_at' => now()->addDay(),
    ]);

    expect(NewsArticle::query()->published()->pluck('id')->all())->toBe([$published->id]);
});

it('casts gis point category and coordinates', function (): void {
    $point = GisPointOfInterest::factory()->create([
        'category' => PoiCategory::POSYANDU,
        'latitude' => -7.21450000,
        'longitude' => 110.82340000,
    ])->refresh();

    expect($point->category)->toBe(PoiCategory::POSYANDU)
        ->and($point->latitude)->toBeFloat()
        ->and($point->longitude)->toBeFloat();
});
