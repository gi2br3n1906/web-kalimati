<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AiConditionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRecommendation extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'iot_device_id',
        'iot_telemetry_id',
        'condition_status',
        'action_title',
        'recommendation_text',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['condition_status' => AiConditionStatus::class];
    }

    /** @return BelongsTo<IotDevice, $this> */
    public function device(): BelongsTo
    {
        return $this->belongsTo(IotDevice::class, 'iot_device_id');
    }

    /** @return BelongsTo<IotTelemetry, $this> */
    public function telemetry(): BelongsTo
    {
        return $this->belongsTo(IotTelemetry::class, 'iot_telemetry_id');
    }
}
