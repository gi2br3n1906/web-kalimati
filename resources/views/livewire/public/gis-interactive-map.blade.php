<main
    class="gis-shell"
    data-gis-map
    data-configuration='@json($mapConfiguration)'
>
    <header class="gis-toolbar">
        <div class="gis-brand">
            <span class="gis-brand-mark" aria-hidden="true"></span>
            <div>
                <p class="gis-kicker">Web GIS</p>
                <h1>Peta Desa Kalimati</h1>
            </div>
        </div>

        <div class="gis-filters" role="group" aria-label="Filter kategori lokasi">
            <button type="button" class="gis-filter is-active" data-category="">
                Semua
            </button>

            @foreach ($categories as $value => $label)
                <button type="button" class="gis-filter" data-category="{{ $value }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <a class="gis-admin-link" href="{{ url('/admin') }}">
            Admin
        </a>
    </header>

    <section class="gis-map-stage" aria-label="Peta interaktif lokasi dan area Desa Kalimati">
        <div class="gis-map-canvas" data-map-canvas wire:ignore></div>

        <div class="gis-status" data-map-status role="status" aria-live="polite">
            Memuat titik lokasi dan pengukuran telemetri...
        </div>

        <div class="gis-count" data-map-count hidden></div>
    </section>
</main>