<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\GisPointOfInterest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GisPointOfInterest
 */
class GisPointOfInterestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->getKey(),
            'name' => $this->name,
            'category' => $this->category->value,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'description' => $this->description,
            'icon_marker' => $this->icon_marker,
            'geometry' => $this->mapGeometry(),
        ];
    }
}
