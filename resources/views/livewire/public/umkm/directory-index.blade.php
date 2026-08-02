<main class="umkm-directory-shell">
    <header class="umkm-directory-header">
        <a class="umkm-back-link" href="{{ route('public.gis.map') }}">Peta Desa</a>
        <div>
            <p class="umkm-eyebrow">Direktori Lokal</p>
            <h1>UMKM Kalimati</h1>
        </div>
        <a class="umkm-owner-link" href="{{ route('public.umkm.ledger') }}">Kas Usaha</a>
    </header>

    <section class="umkm-directory-controls" aria-label="Pencarian UMKM">
        <label class="sr-only" for="umkm-search">Cari UMKM</label>
        <input id="umkm-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Cari usaha atau lokasi">
        <div class="umkm-category-tabs" role="group" aria-label="Filter kategori UMKM">
            <button type="button" wire:click="$set('category', '')" @class(['is-active' => $category === ''])>Semua</button>
            @foreach ($categories as $value => $label)
                <button type="button" wire:click="$set('category', '{{ $value }}')" @class(['is-active' => $category === $value])>{{ $label }}</button>
            @endforeach
        </div>
    </section>

    <section class="umkm-business-grid" aria-live="polite">
        @forelse ($businesses as $business)
            <article class="umkm-business-card">
                <div class="umkm-business-logo" aria-hidden="true">
                    @if ($business->logo_path)
                        <img src="{{ Storage::disk('public')->url($business->logo_path) }}" alt="">
                    @else
                        <span>{{ str($business->business_name)->substr(0, 1)->upper() }}</span>
                    @endif
                </div>
                <div class="umkm-business-meta">
                    <span class="umkm-category-badge">{{ $business->category->label() }}</span>
                    <h2>{{ $business->business_name }}</h2>
                    <p class="umkm-business-address">{{ $business->address }}</p>
                    @if ($business->description)
                        <p class="umkm-business-description">{{ $business->description }}</p>
                    @endif
                </div>
                <a class="umkm-whatsapp-link" href="{{ $business->whatsapp_url }}" target="_blank" rel="noopener noreferrer">Hubungi WhatsApp</a>
            </article>
        @empty
            <div class="umkm-empty-state">Belum ada usaha yang sesuai dengan pencarian ini.</div>
        @endforelse
    </section>
</main>