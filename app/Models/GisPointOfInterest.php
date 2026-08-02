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
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => PoiCategory::class,
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }
}
