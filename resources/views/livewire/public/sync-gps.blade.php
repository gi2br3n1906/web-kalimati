<div
    class="min-h-[calc(100dvh-4rem)] bg-slate-50"
    data-sync-gps
    data-configuration='@json($bluetoothConfiguration)'
>
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            <div class="flex items-start gap-4">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-emerald-700 text-white" aria-hidden="true">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                        <path d="m7 7 10 10-5 5V2l5 5L7 17" />
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase text-emerald-700">Perangkat Lapangan</p>
                    <h1 class="mt-1 text-2xl font-extrabold text-slate-950 sm:text-3xl">Sinkron GPS HP ke Alat Sawah</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Koordinat diproses langsung antara HP dan ESP32 melalui Bluetooth.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto grid max-w-5xl gap-5 px-4 py-6 sm:px-6 sm:py-8 lg:grid-cols-[minmax(0,1fr)_minmax(280px,0.7fr)] lg:px-8">
        <div class="border border-slate-200 bg-white p-5 shadow-sm sm:p-6" style="border-radius: 8px;">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-5">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-500">Status Bluetooth</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900" data-device-name>Belum ada alat dipilih</p>
                </div>
                <div class="gps-connection-status" data-connection-status data-state="disconnected" role="status" aria-live="polite">
                    <span class="gps-connection-dot" aria-hidden="true"></span>
                    <span data-connection-label>Terputus</span>
                </div>
            </div>

            <button
                type="button"
                class="mt-6 flex min-h-16 w-full items-center justify-center gap-3 rounded-lg bg-emerald-700 px-5 py-4 text-center text-base font-extrabold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-200 disabled:cursor-not-allowed disabled:bg-slate-400 sm:text-lg"
                data-connect-bluetooth
            >
                <svg class="h-6 w-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" aria-hidden="true">
                    <path d="m7 7 10 10-5 5V2l5 5L7 17" />
                </svg>
                <span data-connect-label>Hubungkan Bluetooth ke Alat Sawah</span>
            </button>

            <div class="mt-4 hidden border border-rose-200 bg-rose-50 px-4 py-3 text-sm leading-6 text-rose-800" style="border-radius: 6px;" data-sync-error role="alert"></div>

            <dl class="mt-6 grid gap-3 sm:grid-cols-3" aria-label="Koordinat GPS HP saat ini">
                <div class="border border-slate-200 bg-slate-50 p-4" style="border-radius: 6px;">
                    <dt class="text-xs font-bold uppercase text-slate-500">Latitude</dt>
                    <dd class="mt-2 break-all font-mono text-base font-bold text-slate-950" data-gps-latitude>Belum tersedia</dd>
                </div>
                <div class="border border-slate-200 bg-slate-50 p-4" style="border-radius: 6px;">
                    <dt class="text-xs font-bold uppercase text-slate-500">Longitude</dt>
                    <dd class="mt-2 break-all font-mono text-base font-bold text-slate-950" data-gps-longitude>Belum tersedia</dd>
                </div>
                <div class="border border-slate-200 bg-slate-50 p-4" style="border-radius: 6px;">
                    <dt class="text-xs font-bold uppercase text-slate-500">Akurasi</dt>
                    <dd class="mt-2 font-mono text-base font-bold text-slate-950" data-gps-accuracy>Belum tersedia</dd>
                </div>
            </dl>
        </div>

        <aside class="border border-slate-200 bg-white p-5 shadow-sm sm:p-6" style="border-radius: 8px;" aria-labelledby="sync-activity-title">
            <h2 id="sync-activity-title" class="text-base font-extrabold text-slate-950">Aktivitas Sinkronisasi</h2>
            <div class="mt-5 grid gap-5">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-500">Status GPS</p>
                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-800" data-gps-status aria-live="polite">Menunggu koneksi Bluetooth.</p>
                </div>
                <div class="border-t border-slate-200 pt-5">
                    <p class="text-xs font-bold uppercase text-slate-500">Pengiriman Terakhir</p>
                    <p class="mt-2 text-sm font-semibold leading-6 text-slate-800" data-transfer-status aria-live="polite">Belum ada permintaan dari alat.</p>
                </div>
                <div class="border-t border-slate-200 pt-5">
                    <p class="text-xs font-bold uppercase text-slate-500">Service BLE</p>
                    <p class="mt-2 break-all font-mono text-xs leading-5 text-slate-600">{{ $bluetoothConfiguration['serviceUuid'] }}</p>
                </div>
            </div>
        </aside>
    </section>
</div>