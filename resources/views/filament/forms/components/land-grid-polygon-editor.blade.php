<div
    x-data="landGridPolygonEditor($wire, @js($getStatePath()), @js($getState()), @js(config('gis.center')), @js(config('gis.default_zoom')))">
    <div x-ref="map" class="land-grid-polygon-map" wire:ignore></div>
    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Gunakan kontrol polygon untuk menggambar, menyunting, atau menghapus batas lahan.</p>
</div>