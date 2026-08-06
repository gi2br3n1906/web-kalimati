<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Enums\PoiCategory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class GisInteractiveMap extends Component
{
    public function render(): View
    {
        return view('livewire.public.gis-interactive-map', [
            'categories' => PoiCategory::options(),
            'mapConfiguration' => [
                'endpoint' => route('v1.gis.points-of-interest', absolute: false),
                'iotEndpoint' => route('v1.gis.iot-devices', absolute: false),
                'tileProvider' => config('gis.tile_provider'),
                'tileAttribution' => config('gis.tile_attribution'),
                'center' => [
                    (float) config('gis.center.latitude'),
                    (float) config('gis.center.longitude'),
                ],
                'zoom' => (int) config('gis.default_zoom'),
            ],
        ])->layout('components.layouts.app', [
            'title' => 'Peta Desa Kalimati',
        ]);
    }
}
