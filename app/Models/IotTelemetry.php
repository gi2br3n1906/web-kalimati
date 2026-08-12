<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IotTelemetry extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'iot_device_id',
        'latitude',
        'longitude',
        'temp_air',
        'hum_air',
        'temp_soil',
        'hum_soil_percent',
        'raw_soil',
        'lux_light',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'temp_air' => 'float',
            'hum_air' => 'float',
            'temp_soil' => 'float',
            'hum_soil_percent' => 'float',
            'raw_soil' => 'integer',
            'lux_light' => 'float',
        ];
    }

    /** @return BelongsTo<IotDevice, $this> */
    public function device(): BelongsTo
    {
        return $this->belongsTo(IotDevice::class, 'iot_device_id');
    }

    /** @return HasMany<AiRecommendation, $this> */
    public function recommendations(): HasMany
    {
        return $this->hasMany(AiRecommendation::class);
    }
}
