<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandRecommendation extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'land_grid_id',
        'sensor_log_id',
        'ai_model_used',
        'soil_condition_summary',
        'fertilizer_dosage',
        'lime_treatment',
        'action_plan',
        'is_applied',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_applied' => 'boolean',
        ];
    }

    /**
     * @param  Builder<LandRecommendation>  $query
     * @return Builder<LandRecommendation>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_applied', false);
    }

    /**
     * @return BelongsTo<LandGrid, $this>
     */
    public function landGrid(): BelongsTo
    {
        return $this->belongsTo(LandGrid::class);
    }

    /**
     * @return BelongsTo<SensorLog, $this>
     */
    public function sensorLog(): BelongsTo
    {
        return $this->belongsTo(SensorLog::class);
    }
}
