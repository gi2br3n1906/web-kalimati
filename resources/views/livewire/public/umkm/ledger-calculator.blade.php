<main class="umkm-ledger-shell">
    <header class="umkm-ledger-header">
        <a class="umkm-back-link" href="{{ route('public.umkm.directory') }}">Direktori UMKM</a>
        <div>
            <p class="umkm-eyebrow">Pencatatan Usaha</p>
            <h1>Ringkasan Kas</h1>
        </div>
        <a class="umkm-owner-link" href="{{ url('/admin/umkm-ledgers') }}">Kelola Kas</a>
    </header>

    <section class="umkm-ledger-filters" aria-label="Filter laporan kas">
        <label>Usaha
            <select wire:model.live="businessId">
                @foreach ($businesses as $availableBusiness)
                    <option value="{{ $availableBusiness->id }}">{{ $availableBusiness->business_name }}</option>
                @endforeach
            </select>
        </label>
        <label>Dari
            <input type="date" wire:model.live="from">
        </label>
        <label>Sampai
            <input type="date" wire:model.live="until">
        </label>
    </section>

    @if ($business)
        <section class="umkm-summary-grid">
            <article class="umkm-summary-card income"><span>Pemasukan</span><strong>Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</strong></article>
            <article class="umkm-summary-card expense"><span>Pengeluaran</span><strong>Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</strong></article>
            <article class="umkm-summary-card balance"><span>Saldo Bersih</span><strong>Rp {{ number_format($summary['net_balance'], 0, ',', '.') }}</strong></article>
        </section>

        <section class="umkm-ledger-entries">
            <div class="umkm-section-heading"><h2>Transaksi Terbaru</h2><span>{{ $business->business_name }}</span></div>
            @forelse ($entries as $entry)
                <div class="umkm-ledger-entry">
                    <div><strong>{{ $entry->category }}</strong><span>{{ $entry->transaction_date->format('d M Y') }}</span></div>
                    <span @class(['umkm-entry-amount', 'income' => $entry->type->value === 'income', 'expense' => $entry->type->value === 'expense'])>{{ $entry->type->value === 'income' ? '+' : '-' }} {{ $entry->formatted_amount }}</span>
                </div>
            @empty
                <div class="umkm-empty-state">Belum ada transaksi pada periode ini.</div>
            @endforelse
        </section>
    @else
        <div class="umkm-empty-state">Belum ada profil usaha yang terhubung dengan akun ini.</div>
    @endif
</main>