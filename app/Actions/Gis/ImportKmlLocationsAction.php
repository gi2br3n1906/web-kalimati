<?php

declare(strict_types=1);

namespace App\Actions\Gis;

use App\Data\Gis\ParsedKmlPlacemark;
use App\Enums\PoiCategory;
use App\Models\GisPointOfInterest;
use App\Services\GisKmlParserService;
use Illuminate\Support\Facades\DB;
use JsonException;

final readonly class ImportKmlLocationsAction
{
    public function __construct(
        private GisKmlParserService $parser,
    ) {}

    /**
     * @throws JsonException
     */
    public function execute(
        string $filePath,
        PoiCategory $category,
        ?string $sourceName = null,
    ): int {
        $placemarks = $this->parser->parseFile($filePath, $sourceName);
        $timestamp = now();
        $rows = array_map(
            static fn (ParsedKmlPlacemark $placemark): array => [
                'name' => $placemark->name,
                'category' => $category->value,
                'latitude' => $placemark->latitude,
                'longitude' => $placemark->longitude,
                'description' => $placemark->description,
                'icon_marker' => $category->defaultMarker(),
                'geojson_geometry' => json_encode(
                    $placemark->geometry,
                    JSON_THROW_ON_ERROR,
                ),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            $placemarks,
        );

        DB::transaction(static function () use ($rows): void {
            foreach (array_chunk($rows, 500) as $chunk) {
                GisPointOfInterest::query()->insert($chunk);
            }
        });

        return count($rows);
    }
}
