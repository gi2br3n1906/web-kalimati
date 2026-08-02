<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SensorLog extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'land_grid_id',
        'device_id',
        'ph_level',
        'moisture_percentage',
        'temperature_celsius',
        'raw_payload',
        'recorded_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ph_level' => 'float',
            'moisture_percentage' => 'float',
            'temperature_celsius' => 'float',
            'raw_payload' => 'array',
            'recorded_at' => 'immutable_datetime',
        ];
    }

    /**
     * @param  Builder<SensorLog>  $query
     * @return Builder<SensorLog>
     */
    public function scopeLatestRecorded(Builder $query): Builder
    {
        return $query->orderByDesc('recorded_at');
    }

    /**
     * @return BelongsTo<LandGrid, $this>
     */
    public function landGrid(): BelongsTo
    {
        return $this->belongsTo(LandGrid::class);
    }

    /**
     * @return HasMany<LandRecommendation, $this>
     */
    public function recommendations(): HasMany
    {
        return $this->hasMany(LandRecommendation::class);
    }
}
