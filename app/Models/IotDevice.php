<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IotDevice extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'land_grid_id',
        'device_code',
        'name',
        'api_token',
        'latitude',
        'longitude',
        'coverage_radius_meters',
        'crop_type',
        'is_active',
        'last_active_at',
    ];

    /** @var array<int, string> */
    protected $hidden = ['api_token', 'api_token_hash'];

    protected static function booted(): void
    {
        static::saving(static function (IotDevice $device): void {
            if ($device->isDirty('api_token')) {
                $device->api_token_hash = hash('sha256', (string) $device->api_token);
            }
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'api_token' => 'encrypted',
            'latitude' => 'float',
            'longitude' => 'float',
            'coverage_radius_meters' => 'integer',
            'is_active' => 'boolean',
            'last_active_at' => 'immutable_datetime',
        ];
    }

    /** @param Builder<IotDevice> $query @return Builder<IotDevice> */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function findActiveByToken(string $token): ?self
    {
        if ($token === '') {
            return null;
        }

        return self::query()
            ->active()
            ->where('api_token_hash', hash('sha256', $token))
            ->first();
    }

    /** @return HasMany<IotTelemetry, $this> */
    public function telemetries(): HasMany
    {
        return $this->hasMany(IotTelemetry::class);
    }

    /** @return HasMany<AiRecommendation, $this> */
    public function recommendations(): HasMany
    {
        return $this->hasMany(AiRecommendation::class);
    }

    /** @return HasOne<IotTelemetry, $this> */
    public function latestTelemetry(): HasOne
    {
        return $this->hasOne(IotTelemetry::class)->latestOfMany();
    }

    /** @return HasOne<AiRecommendation, $this> */
    public function latestRecommendation(): HasOne
    {
        return $this->hasOne(AiRecommendation::class)->latestOfMany();
    }

    /** @return BelongsTo<LandGrid, $this> */
    public function landGrid(): BelongsTo
    {
        return $this->belongsTo(LandGrid::class);
    }
}
