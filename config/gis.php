<?php

declare(strict_types=1);

return [
    'tile_provider' => env(
        'LEAFLET_TILE_PROVIDER',
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    ),
    'tile_attribution' => '&copy; OpenStreetMap contributors',
    'center' => [
        'latitude' => (float) env('GIS_CENTER_LATITUDE', -7.2145),
        'longitude' => (float) env('GIS_CENTER_LONGITUDE', 110.8234),
    ],
    'default_zoom' => (int) env('GIS_DEFAULT_ZOOM', 15),
];
