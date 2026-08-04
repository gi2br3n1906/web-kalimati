<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PoiCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GisPointOfInterest extends Model
{
    use HasFactory;

    protected $table = 'gis_points_of_interest';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'latitude',
        'longitude',
        'description',
        'icon_marker',
        'geojson_geometry',
    ];

    /**
     * @return array{type: string, coordinates: array<mixed>}
     */
    public function mapGeometry(): array
    {
        if (is_array($this->geojson_geometry)) {
            return $this->geojson_geometry;
        }

        return [
            'type' => 'Point',
            'coordinates' => [
                (float) $this->longitude,
                (float) $this->latitude,
            ],
        ];
    }

    public function geometryTypeLabel(): string
    {
        return $this->mapGeometry()['type'] === 'Polygon' ? 'Area / Polygon' : 'Titik / Marker';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => PoiCategory::class,
            'latitude' => 'float',
            'longitude' => 'float',
            'geojson_geometry' => 'array',
        ];
    }
}
