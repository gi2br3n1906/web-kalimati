<?php

declare(strict_types=1);

namespace App\Data\Gis;

final readonly class ParsedKmlPlacemark
{
    /**
     * @param  array{type: 'Point'|'Polygon', coordinates: array<mixed>}  $geometry
     */
    public function __construct(
        public string $name,
        public ?string $description,
        public float $latitude,
        public float $longitude,
        public array $geometry,
    ) {}
}
