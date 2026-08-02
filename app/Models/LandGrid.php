<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CommodityType;
use App\Enums\LandGridStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandGrid extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'grid_code',
        'dusun_name',
        'commodity_type',
        'latitude',
        'longitude',
        'geojson_polygon',
        'owner_name',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'commodity_type' => CommodityType::class,
            'status' => LandGridStatus::class,
            'latitude' => 'float',
            'longitude' => 'float',
            'geojson_polygon' => 'array',
        ];
    }

    /**
     * @param  Builder<LandGrid>  $query
     * @return Builder<LandGrid>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', LandGridStatus::ACTIVE->value);
    }

    /**
     * @return HasMany<SensorLog, $this>
     */
    public function sensorLogs(): HasMany
    {
        return $this->hasMany(SensorLog::class);
    }

    /**
     * @return HasMany<LandRecommendation, $this>
     */
    public function recommendations(): HasMany
    {
        return $this->hasMany(LandRecommendation::class);
    }
}
