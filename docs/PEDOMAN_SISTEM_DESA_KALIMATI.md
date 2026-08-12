# PEDOMAN UTAMA SISTEM DESA KALIMATI

## Portal Informasi Desa, Web GIS, Smart Agriculture, IoT, dan Analisis Gemini AI

**Desa Kalimati, Kecamatan Juwangi, Kabupaten Boyolali, Jawa Tengah**  
**Jenis dokumen:** Pedoman operasional, teknis, pemeliharaan, dan serah terima sistem  
**Sasaran pembaca:** Super Admin, Pemerintah Desa, Tim KKN/Pengembang, Kelompok Tani, serta Pengelola Server dan IoT  
**Status dokumen:** Dokumen induk siap baca dan siap cetak  
**Versi dokumen:** 1.0  
**Tanggal pemutakhiran:** Agustus 2026

---

> **Kedudukan dokumen**  
> Dokumen ini adalah pedoman operasional utama. Apabila terdapat perbedaan antara contoh di dokumen dan implementasi yang sedang berjalan, kode aplikasi, migration database, konfigurasi `.env`, dan firmware yang benar-benar dipasang pada alat merupakan sumber kebenaran teknis terakhir. Setiap perubahan produksi wajib diikuti pemutakhiran dokumen ini.

> **Peringatan keamanan**  
> Jangan menuliskan kata sandi, `APP_KEY`, `GEMINI_API_KEY`, token perangkat, kredensial database, atau kunci privat ke dokumentasi publik, grup percakapan, tangkapan layar, maupun Git. Contoh token dalam dokumen ini hanya placeholder dan **tidak boleh** digunakan di produksi.

---

## DAFTAR ISI

- [Petunjuk Penggunaan Dokumen](#petunjuk-penggunaan-dokumen)
- [Matriks Pembaca dan Tanggung Jawab](#matriks-pembaca-dan-tanggung-jawab)
- [BAB 1 — Pengenalan dan Arsitektur Sistem](#bab-1--pengenalan-dan-arsitektur-sistem)
  - [1.1 Gambaran Umum](#11-gambaran-umum)
  - [1.2 Visi dan Tujuan](#12-visi-dan-tujuan)
  - [1.3 Ruang Lingkup Sistem](#13-ruang-lingkup-sistem)
  - [1.4 Arsitektur dan Tech Stack](#14-arsitektur-dan-tech-stack)
  - [1.5 Diagram Alur Data End-to-End](#15-diagram-alur-data-end-to-end)
  - [1.6 Komponen Data Utama](#16-komponen-data-utama)
- [BAB 2 — Katalog dan Panduan Web Publik](#bab-2--katalog-dan-panduan-web-publik)
  - [2.1 Katalog Rute Publik](#21-katalog-rute-publik)
  - [2.2 Beranda](#22-beranda)
  - [2.3 Kabar Desa](#23-kabar-desa)
  - [2.4 Peta Spasial Web GIS](#24-peta-spasial-web-gis)
  - [2.5 Sinkronisasi GPS BLE](#25-sinkronisasi-gps-ble)
- [BAB 3 — Panduan Operasional Admin Panel Filament](#bab-3--panduan-operasional-admin-panel-filament)
  - [3.1 Login dan Tata Kelola Akses](#31-login-dan-tata-kelola-akses)
  - [3.2 Manajemen Pengguna dan Otorisasi](#32-manajemen-pengguna-dan-otorisasi)
  - [3.3 Kabar Desa dan Berita](#33-kabar-desa-dan-berita)
  - [3.4 Manajemen Perangkat IoT](#34-manajemen-perangkat-iot)
  - [3.5 Titik Lokasi](#35-titik-lokasi)
  - [3.6 Log Sensor](#36-log-sensor)
  - [3.7 Rekomendasi Lahan](#37-rekomendasi-lahan)
  - [3.8 Grid Lahan](#38-grid-lahan)
- [BAB 4 — Setup dan Operasional Alat IoT](#bab-4--setup-dan-operasional-alat-iot)
  - [4.1 Keselamatan dan Persiapan](#41-keselamatan-dan-persiapan)
  - [4.2 Spesifikasi Komponen dan Pinout](#42-spesifikasi-komponen-dan-pinout)
  - [4.3 Cara Kerja Tombol Keypad](#43-cara-kerja-tombol-keypad)
  - [4.4 Siklus Kerja Firmware](#44-siklus-kerja-firmware)
  - [4.5 Flashing melalui Arduino IDE](#45-flashing-melalui-arduino-ide)
  - [4.6 Contoh Konfigurasi Firmware](#46-contoh-konfigurasi-firmware)
  - [4.7 Checklist Uji Alat](#47-checklist-uji-alat)
- [BAB 5 — Mekanisme AI Reasoning dan Sinkronisasi Data](#bab-5--mekanisme-ai-reasoning-dan-sinkronisasi-data)
  - [5.1 Kontrak API Telemetri](#51-kontrak-api-telemetri)
  - [5.2 Alur Transaksi dan Sinkronisasi](#52-alur-transaksi-dan-sinkronisasi)
  - [5.3 Direct Call Google Gemini](#53-direct-call-google-gemini)
  - [5.4 Status Kondisi AI](#54-status-kondisi-ai)
  - [5.5 Konektivitas BLE dan Web Bluetooth](#55-konektivitas-ble-dan-web-bluetooth)
  - [5.6 Struktur Data dan Relasi](#56-struktur-data-dan-relasi)
- [BAB 6 — Maintenance, Deployment, dan Troubleshooting](#bab-6--maintenance-deployment-dan-troubleshooting)
  - [6.1 Pembagian Tanggung Jawab Operasional](#61-pembagian-tanggung-jawab-operasional)
  - [6.2 Deployment ke VPS if62](#62-deployment-ke-vps-if62)
  - [6.3 Konfigurasi Environment Penting](#63-konfigurasi-environment-penting)
  - [6.4 Backup dan Pemulihan](#64-backup-dan-pemulihan)
  - [6.5 Monitoring Berkala](#65-monitoring-berkala)
  - [6.6 Troubleshooting dan FAQ](#66-troubleshooting-dan-faq)
  - [6.7 Checklist Serah Terima](#67-checklist-serah-terima)
- [Lampiran A — Referensi Cepat Endpoint](#lampiran-a--referensi-cepat-endpoint)
- [Lampiran B — Referensi Cepat BLE](#lampiran-b--referensi-cepat-ble)
- [Lampiran C — SOP Harian Kelompok Tani](#lampiran-c--sop-harian-kelompok-tani)
- [Lampiran D — SOP Insiden](#lampiran-d--sop-insiden)
- [Lampiran E — Riwayat Perubahan Dokumen](#lampiran-e--riwayat-perubahan-dokumen)

---

## PETUNJUK PENGGUNAAN DOKUMEN

1. **Pengguna umum dan Pemerintah Desa** dapat memulai dari BAB 2 untuk memahami layanan publik.
2. **Super Admin** sebaiknya membaca BAB 3, BAB 5, dan BAB 6 secara lengkap.
3. **Kelompok Tani** dapat berfokus pada BAB 2.4, BAB 2.5, BAB 3.4–3.8, BAB 4, dan Lampiran C.
4. **Tim KKN/Pengembang** wajib memahami BAB 1, BAB 5, BAB 6, serta blueprint teknis lain di root repositori.
5. **Teknisi server/IoT** wajib mengikuti peringatan keamanan, prosedur deployment, backup, flashing, dan checklist uji.

Konvensi yang digunakan:

- Teks `monospace` menunjukkan nama route, field, tabel, file, variabel, atau perintah.
- Nilai `<PLACEHOLDER>` harus diganti dengan nilai instalasi masing-masing.
- Istilah **telemetri** berarti satu paket pembacaan sensor pada satu waktu.
- Istilah **synchronous AI reasoning** berarti proses AI diselesaikan dalam request HTTP yang sama, tanpa menunggu worker queue manual.

## MATRIKS PEMBACA DAN TANGGUNG JAWAB

| Peran | Fokus Utama | Kewenangan Umum | Larangan/Pembatasan |
|---|---|---|---|
| Super Admin | Tata kelola seluruh sistem | Pengguna, role, berita, GIS, IoT, pertanian, UMKM, riset | Tidak membagikan kredensial; perubahan permission harus terdokumentasi |
| Admin Desa | Informasi publik dan administrasi desa | Berita, titik/fasilitas GIS, berkas riset sesuai permission | Tidak mengubah konfigurasi server/IoT tanpa mandat |
| Kelompok Tani | Operasional pertanian dan alat | Perangkat IoT, grid, log sensor, rekomendasi AI | Tidak mengubah role global atau data publik yang tidak terkait |
| Tim KKN/Pengembang | Pengembangan, pengujian, dokumentasi | Kode, migration, test, build, perbaikan terkontrol | Tidak melakukan perubahan produksi tanpa backup dan persetujuan |
| Teknisi Server/IoT | Ketersediaan layanan dan perangkat | Deployment, `.env`, HTTPS, database, flashing, jaringan | Tidak menaruh secret di Git; tidak menjalankan `migrate:fresh` di produksi |
| Warga | Konsumsi informasi publik | Web publik dan peta | Tidak memiliki akses modul administrasi |

---

# BAB 1 — PENGENALAN DAN ARSITEKTUR SISTEM

## 1.1 Gambaran Umum

Sistem Desa Kalimati adalah platform terpadu yang menghubungkan portal informasi desa, publikasi berita, peta spasial, perangkat Internet of Things (IoT), pembacaan kondisi lahan, serta rekomendasi pertanian berbasis Google Gemini AI. Platform dirancang untuk mendukung tata kelola data Desa Kalimati di Kecamatan Juwangi, Kabupaten Boyolali secara terbuka, terukur, dan berkelanjutan.

Sistem terdiri atas empat lapisan utama:

1. **Lapisan layanan publik**, berupa website untuk warga dan pemangku kepentingan.
2. **Lapisan administrasi**, berupa panel Filament untuk pengelolaan data berbasis role dan permission.
3. **Lapisan integrasi pertanian**, berupa REST API, Web GIS, dan proses AI reasoning.
4. **Lapisan lapangan**, berupa ESP32, sensor, koneksi Wi-Fi, BLE, serta GPS telepon genggam.

## 1.2 Visi dan Tujuan

### Visi

Mewujudkan ekosistem digital Desa Kalimati yang transparan, mudah dikelola, responsif terhadap kebutuhan warga, dan mampu membantu pengambilan keputusan pertanian berbasis data lapangan.

### Tujuan

- Menyediakan satu portal resmi untuk profil, statistik, kabar, dan potensi desa.
- Memudahkan Pemerintah Desa dan Tim KKN mempublikasikan informasi secara terstruktur.
- Menampilkan fasilitas, lokasi penting, dan titik IoT pada peta interaktif.
- Mengumpulkan data suhu, kelembapan, dan cahaya dari alat di lahan.
- Memperbarui koordinat perangkat menggunakan GPS HP melalui BLE.
- Menghasilkan rekomendasi tindakan pertanian secara cepat melalui Gemini AI.
- Menyimpan histori telemetri dan rekomendasi untuk evaluasi Kelompok Tani.
- Menjaga keberlanjutan sistem melalui role, SOP, test, deployment, dan dokumentasi.

## 1.3 Ruang Lingkup Sistem

| Domain | Fungsi |
|---|---|
| Portal Desa | Beranda, profil, statistik, akses cepat, dan informasi publik |
| Kabar Desa | Pencarian, kategori, pagination, artikel, thumbnail, draft/published |
| Web GIS | POI, geometri Point/Polygon, marker IoT, radius sensor, popup telemetri |
| Smart Agriculture | Perangkat, grid, telemetri, tren sensor, rekomendasi AI |
| Sinkronisasi GPS | Web Bluetooth + Geolocation untuk mengirim koordinat HP ke ESP32 |
| Admin Panel | CRUD terotorisasi dengan Filament dan Shield/Spatie Permission |
| Deployment | Build aset, migration, cache, maintenance mode, dan restart layanan |

## 1.4 Arsitektur dan Tech Stack

| Lapisan | Teknologi | Peran |
|---|---|---|
| Backend/MVC | Laravel 11, PHP 8.2+ | Routing, request validation, model, service, transaksi, API |
| UI reaktif | Livewire v3 | Halaman publik interaktif tanpa SPA terpisah |
| Admin | Filament v3 | Panel pengelolaan data, form, table, filter, relation manager |
| Otorisasi | Filament Shield + Spatie Laravel Permission | Role dan permission per resource |
| Frontend | Blade, Tailwind CSS, Vite | Layout, komponen visual, responsive design, asset bundling |
| Peta | Leaflet.js + OpenStreetMap | Marker, circle coverage, polygon, popup, layer GIS |
| Media | Spatie Media Library | Thumbnail berita dan konversi gambar |
| Database | SQLite untuk pengembangan/test; MySQL untuk produksi yang direkomendasikan | Penyimpanan data relasional |
| AI | Google Gemini REST API | Analisis telemetri dan rekomendasi tindakan |
| Hardware | ESP32 + sensor | Akuisisi data, BLE, Wi-Fi, dan HTTP telemetry |
| Firmware | C++/Arduino ecosystem | Kontrol pin, sensor, keypad, BLE, WiFiManager, HTTP client |

> **Catatan database**  
> SQLite sesuai untuk pengembangan lokal dan automated test. Produksi multi-pengguna disarankan memakai MySQL 8.x dengan backup terjadwal. Jangan mengganti driver produksi tanpa uji migration dan restore.

## 1.5 Diagram Alur Data End-to-End

```text
┌───────────────────────────────────────────────────────────────────────────────┐
│                           AREA LAHAN / SAWAH                                  │
│                                                                               │
│  DHT21   DS18B20   Soil Moisture   BH1750   Keypad   LCD/LED                 │
│    │        │             │           │        │        │                     │
│    └────────┴─────────────┴───────────┴────────┴────────┘                     │
│                                  │                                            │
│                                  ▼                                            │
│                         ┌─────────────────┐                                    │
│                         │      ESP32      │                                    │
│                         │ Firmware C++    │                                    │
│                         └───────┬─────────┘                                    │
└─────────────────────────────────┼──────────────────────────────────────────────┘
                                  │
                    Tombol 5      │ BLE Notify: meminta GPS
                                  ▼
                         ┌──────────────────┐
                         │ HP Android       │
                         │ Chrome + HTTPS   │
                         │ /sync-gps        │
                         └────────┬─────────┘
                                  │ Geolocation API
                                  │ BLE Write: "latitude,longitude"
                                  ▼
                         ┌──────────────────┐
                         │ ESP32 menyimpan  │
                         │ koordinat terbaru│
                         └────────┬─────────┘
                                  │ Wi-Fi + HTTPS
                                  │ POST /api/v1/telemetry
                                  │ X-Device-Token
                                  ▼
┌───────────────────────────────────────────────────────────────────────────────┐
│                            SERVER LARAVEL                                     │
│                                                                               │
│  Validasi token & payload                                                     │
│          │                                                                    │
│          ▼                                                                    │
│  Simpan iot_telemetries + update iot_devices + asosiasi grid terdekat         │
│          │                                                                    │
│          ▼                                                                    │
│  Direct synchronous call ───────────────► Google Gemini API                   │
│          │                                      │                             │
│          ◄──────────────────────────────────────┘ JSON rekomendasi            │
│          │                                                                    │
│          ▼                                                                    │
│  Simpan ai_recommendations (atau fallback caution jika provider gagal)        │
│          │                                                                    │
│          ├────────► Filament /admin (monitoring dan pengelolaan)              │
│          └────────► REST GIS ─► Leaflet /peta (marker, radius, popup)          │
└───────────────────────────────────────────────────────────────────────────────┘
```

### Ringkasan urutan transaksi

1. Sensor dibaca oleh ESP32.
2. Jika tombol `5` ditekan, ESP32 mengirim notifikasi BLE untuk meminta koordinat.
3. Chrome pada HP menerima notifikasi dan menulis koordinat GPS ke characteristic write.
4. ESP32 mengirim sensor dan koordinat ke API melalui Wi-Fi.
5. Laravel memvalidasi token serta rentang nilai.
6. Telemetri disimpan; lokasi dan waktu aktif perangkat diperbarui.
7. Perangkat diasosiasikan dengan grid aktif terdekat.
8. Laravel memanggil Gemini secara langsung dalam request yang sama.
9. Hasil AI atau fallback disimpan ke database.
10. Data tersedia di Filament dan peta publik.

## 1.6 Komponen Data Utama

| Model/Tabel | Isi | Sumber | Konsumen |
|---|---|---|---|
| `users` | Akun pengguna | Super Admin | Login dan policy |
| `roles`, `permissions` | RBAC | Seeder/Shield | Filament policy |
| `news_articles` | Artikel dan status publikasi | Admin Desa/Super Admin | Beranda dan Kabar Desa |
| `media` | Thumbnail/media berita | Upload Filament | Halaman publik |
| `gis_points_of_interest` | Fasilitas dan geometri GIS | Admin Desa | Peta Leaflet |
| `iot_devices` | Identitas, token terenkripsi, posisi, radius, komoditas | Kelompok Tani/Super Admin + telemetry | API, peta, admin |
| `iot_telemetries` | Snapshot sensor dan koordinat | ESP32 | Grafik, log, Gemini, popup |
| `ai_recommendations` | Status, headline, rekomendasi | Gemini/fallback | Admin dan popup peta |
| `land_grids` | Blok lahan, titik tengah, komoditas, status | Admin pertanian | Asosiasi perangkat dan farm grid |

---

# BAB 2 — KATALOG DAN PANDUAN WEB PUBLIK

## 2.1 Katalog Rute Publik

| URL | Nama route | Fungsi |
|---|---|---|
| `/` | `public.home` | Beranda portal desa |
| `/profil` | `public.profile` | Profil, sejarah, dan informasi desa |
| `/pertanian` | `public.agriculture` | Ringkasan Smart Agriculture dan peta IoT |
| `/peta` | `public.gis.map` | Peta spasial interaktif |
| `/sync-gps` | `public.gps.sync` | Sinkronisasi GPS HP ke ESP32 via BLE |
| `/berita` | `public.news.index` | Daftar, pencarian, filter, dan pagination berita |
| `/berita/{slug}` | `public.news.show` | Detail artikel yang sudah diterbitkan |

## 2.2 Beranda

**URL:** `/`

Beranda merupakan pintu masuk utama untuk warga. Bagian utamanya meliputi:

1. **Hero section dinamis** dengan dua tema: portal informasi/pelayanan publik dan pertanian presisi berbasis data.
2. **Statistik desa**, antara lain total penduduk, jumlah kepala keluarga, luas wilayah, dan wilayah administratif.
3. **Sambutan Kepala Desa** dan identitas wilayah.
4. **Akses cepat** menuju profil, pertanian, peta, dan kabar desa.
5. **Tiga berita terbaru** yang diambil secara dinamis dari `news_articles`.

Berita pada beranda hanya muncul apabila:

- `is_published = true`;
- waktu terbit telah sesuai dengan scope publikasi model; dan
- data tersedia pada database aktif.

### Cara navigasi

1. Buka domain resmi melalui browser.
2. Gunakan menu utama di bagian atas atau tombol akses cepat.
3. Pilih kartu berita untuk membuka `/berita/{slug}`.
4. Gunakan tautan “lihat semua berita” untuk menuju `/berita`.

> **Catatan konten statistik**  
> Statistik pada hero/beranda saat ini berasal dari konfigurasi komponen Livewire. Bila data kependudukan berubah, Tim Pengembang harus memperbarui sumber datanya secara terkontrol dan menguji tampilan.

## 2.3 Kabar Desa

### Halaman daftar

**URL:** `/berita`

Fitur yang tersedia:

- pencarian pada **judul** dan **isi artikel**;
- filter kategori berbasis enum `NewsCategory`;
- urutan terbaru berdasarkan `published_at`;
- pagination sebanyak **9 artikel per halaman**;
- thumbnail dari Spatie Media Library;
- informasi penulis dan waktu publikasi.

Kategori aktual:

| Nilai database | Label publik |
|---|---|
| `kkn` | KKN |
| `karang_taruna` | Karang Taruna |
| `pemdes` | Pemerintah Desa |

### Cara mencari dan memfilter

1. Masukkan kata kunci pada kotak pencarian.
2. Pilih kategori jika diperlukan.
3. Tunggu Livewire memuat hasil tanpa reload halaman penuh.
4. Gunakan kontrol pagination di bawah daftar.
5. Kosongkan pencarian dan kategori untuk menampilkan seluruh artikel terbit.

### Halaman detail

**URL:** `/berita/{slug}`

`slug` adalah identitas URL unik yang dibuat dari judul saat admin mengisi form. Halaman detail memuat judul, kategori, penulis, tanggal, thumbnail, dan isi rich text. Artikel draft tidak boleh muncul di halaman publik.

## 2.4 Peta Spasial Web GIS

**URL:** `/peta`

Peta dibangun dengan Leaflet.js dan tile OpenStreetMap. Titik pusat default Desa Kalimati adalah:

```text
Latitude  : -7.2145
Longitude : 110.8234
Zoom      : 15
```

### Layer peta

1. **Layer titik/fasilitas desa** dari `gis_points_of_interest`.
2. **Geometri Point dan Polygon** yang dapat berasal dari input admin atau impor KML.
3. **Layer perangkat IoT aktif** dari `/api/v1/gis/iot-devices`.
4. **Circle coverage** sesuai `coverage_radius_meters` setiap perangkat.

Kategori POI meliputi pemerintahan, fasilitas umum, pendidikan, pertanian/IoT, tempat ibadah, dan posyandu. Tombol filter kategori hanya memengaruhi layer POI; layer IoT dimuat dari endpoint tersendiri.

### Indikator warna kondisi lahan

| Status | Warna | Arti operasional |
|---|---|---|
| `optimal` | Hijau | Kondisi berada dalam rentang yang dinilai baik |
| `caution` | Kuning | Perlu perhatian atau data AI belum tersedia/fallback |
| `warning` | Merah | Perlu pemeriksaan dan tindakan lebih cepat |
| `critical` | Merah tua | Status didukung enum/tampilan untuk kondisi sangat serius |

> **Penting**  
> Warna dan rekomendasi AI adalah alat bantu. Keputusan pemupukan, pestisida, pengairan, atau tindakan yang berisiko wajib mempertimbangkan inspeksi lapangan dan arahan penyuluh pertanian.

### Isi popup perangkat IoT

- nama dan kode perangkat;
- komoditas;
- suhu udara;
- kelembapan udara;
- suhu tanah;
- kelembapan tanah;
- intensitas cahaya;
- status kondisi AI;
- headline dan rekomendasi tindakan.

Jika belum ada telemetri, popup menampilkan informasi bahwa data belum tersedia. Jika belum ada rekomendasi, status visual menggunakan `caution` sampai analisis tersedia.

## 2.5 Sinkronisasi GPS BLE

**URL:** `/sync-gps`

Fitur ini memanfaatkan **Web Bluetooth API** dan **Geolocation API** pada Chrome Android. Halaman harus dibuka melalui HTTPS karena Web Bluetooth tidak berjalan pada koneksi HTTP biasa, kecuali konteks pengembangan lokal tertentu.

### Prasyarat

- HP Android dengan Bluetooth dan GPS aktif;
- Chrome Android versi terbaru;
- izin Bluetooth/Perangkat Sekitar dan Lokasi;
- halaman diakses melalui HTTPS;
- ESP32 menyala dan mengiklankan nama `ESP32-GPS-Sync`;
- firmware menyediakan service dan characteristic dengan kapabilitas notify/write.

### Langkah penggunaan

1. Bawa HP sedekat mungkin ke alat.
2. Aktifkan Bluetooth, GPS, data internet, dan Wi-Fi/hotspot sesuai kebutuhan.
3. Buka `/sync-gps` melalui Chrome Android.
4. Tekan **Hubungkan Bluetooth ke Alat Sawah**.
5. Pilih `ESP32-GPS-Sync` pada dialog perangkat.
6. Izinkan akses lokasi presisi.
7. Tunggu status **Terhubung ke ESP32** dan **Koordinat GPS aktif dan siap dikirim**.
8. Diam di lokasi alat sampai akurasi GPS cukup baik; semakin kecil nilai meter, semakin baik.
9. Tekan tombol fisik `5` pada keypad alat.
10. ESP32 mengirim BLE notify; halaman menulis payload `latitude,longitude` ke alat.
11. Pastikan halaman menampilkan waktu dan koordinat yang berhasil dikirim.
12. Periksa `/admin/location-points` atau `/peta` setelah ESP32 mengirim telemetri ke server.

Contoh payload BLE:

```text
-7.2145000,110.8234000
```

> **Peringatan akurasi**  
> Jangan melakukan sinkronisasi dari dalam bangunan, kendaraan bergerak, atau lokasi jauh dari alat. GPS HP yang buruk dapat memindahkan marker jauh dari lahan sebenarnya.

---

# BAB 3 — PANDUAN OPERASIONAL ADMIN PANEL FILAMENT

## 3.1 Login dan Tata Kelola Akses

**URL panel:** `/admin`

1. Buka domain resmi lalu tambahkan `/admin`.
2. Masukkan email dan kata sandi.
3. Pastikan akun menggunakan role yang benar.
4. Setelah selesai, keluar dari akun terutama pada komputer bersama.

### Ringkasan hak akses

| Modul | Super Admin | Admin Desa | Kelompok Tani |
|---|:---:|:---:|:---:|
| Role/permission | Penuh | Terbatas sesuai permission | Tidak |
| Berita | Penuh | Ya | Tidak secara default |
| POI/Web GIS administratif | Penuh | Ya | Tidak secara default |
| Perangkat IoT | Penuh | Sesuai permission jika diberikan | Ya |
| Grid, sensor, rekomendasi | Penuh | Sesuai permission jika diberikan | Ya |
| UMKM dan riset | Penuh | Sesuai domain | Tidak kecuali diberi permission |

`super_admin` memiliki bypass `Gate::before`, sedangkan role lain mengikuti permission Shield/Spatie. Karena itu Super Admin harus digunakan secara terbatas.

## 3.2 Manajemen Pengguna dan Otorisasi

### Prinsip least privilege

- Berikan permission hanya sesuai tugas.
- Gunakan akun personal; jangan memakai satu akun bersama.
- Nonaktifkan atau ganti akses saat masa tugas berakhir.
- Audit role setelah pergantian perangkat desa atau Tim KKN.
- Jangan menghapus permission inti tanpa pengujian akses.

### Role default

| Role | Tujuan |
|---|---|
| `super_admin` | Pengelola tertinggi sistem |
| `admin_desa` | Pengelola informasi dan administrasi desa |
| `kelompok_tani` | Pengelola perangkat dan Smart Agriculture |
| `umkm` | Pengelola data UMKM miliknya/sesuai policy |
| `warga` | Akun warga dengan akses minimal |

### SOP perubahan role

1. Verifikasi identitas dan mandat pemohon.
2. Catat role lama dan role baru.
3. Lakukan perubahan melalui menu Role/User yang tersedia.
4. Minta pengguna logout-login kembali.
5. Uji satu route yang diizinkan dan satu route yang harus ditolak.
6. Catat tanggal, operator, alasan, dan hasil uji.

## 3.3 Kabar Desa dan Berita

**URL:** `/admin/news-articles`

### Membuat artikel

1. Masuk menu **Berita & Pengumuman**.
2. Klik **Buat** atau **New**.
3. Isi **Judul Artikel**; `slug` terisi otomatis dan harus unik.
4. Pilih kategori KKN, Karang Taruna, atau Pemerintah Desa.
5. Unggah **Foto Thumbnail** maksimal 5 MB; gunakan gambar yang memiliki izin publikasi.
6. Tulis isi menggunakan rich editor.
7. Tentukan status:
   - **Draft:** matikan `Terbitkan Langsung`;
   - **Published:** aktifkan `Terbitkan Langsung` dan isi waktu terbit.
8. Simpan.
9. Buka `/berita` untuk verifikasi publikasi.

### Mengedit artikel

1. Cari artikel dari tabel.
2. Klik **Edit**.
3. Perbarui informasi yang diperlukan.
4. Jangan mengubah slug artikel lama tanpa kebutuhan kuat karena tautan lama akan berubah.
5. Simpan dan cek tampilan desktop serta mobile.

### Media Library

Thumbnail disimpan pada collection media `thumbnail` dan dapat memiliki konversi preview. Penggantian file harus dilakukan dari field upload Filament, bukan dengan mengedit path database secara manual.

> **Peringatan konten**  
> Pastikan nama, foto, data pribadi, dan isi berita telah mendapat persetujuan yang sesuai. Hindari memuat nomor identitas, alamat pribadi rinci, atau informasi sensitif.

## 3.4 Manajemen Perangkat IoT

**URL:** `/admin/iot-devices`

### Field perangkat

| Field | Contoh | Keterangan |
|---|---|---|
| Nama Alat | `ESP32 Sawah Dampit 01` | Nama mudah dikenali operator |
| `device_code` | `IOT-KAL-001` | Identitas unik firmware/database |
| API Token | random 64 karakter | Dipakai pada header `X-Device-Token` |
| Komoditas | `Jagung` | Konteks Gemini dan tampilan peta |
| Aktif | Ya/Tidak | Perangkat nonaktif ditolak API |
| Latitude | `-7.21450000` | Posisi awal/terakhir |
| Longitude | `110.82340000` | Posisi awal/terakhir |
| Radius | `100` meter | Circle coverage pada peta |

### Mendaftarkan perangkat baru

1. Klik **Buat Perangkat IoT**.
2. Tentukan nama dan `device_code` unik.
3. Gunakan API token yang dibuat otomatis.
4. **Salin token pada saat pembuatan** dan masukkan ke firmware sebagai `DEVICE_TOKEN`.
5. Isi komoditas, koordinat awal, dan radius.
6. Aktifkan perangkat lalu simpan.
7. Flash firmware dengan kode dan token yang sama.
8. Kirim satu telemetri uji.
9. Periksa `Terakhir Aktif`, histori telemetri, dan rekomendasi.

> **Peringatan token perangkat**  
> Token disimpan terenkripsi dan dicari melalui hash SHA-256. Saat edit, kosongkan field token untuk mempertahankan token lama. Jika token dirotasi, firmware juga harus diflash/dikonfigurasi ulang. Jangan menyalin token dari database secara manual.

### Relation manager

Halaman edit perangkat menyediakan:

- **Histori Telemetri**;
- **Riwayat Rekomendasi AI**.

Gunakan dua tab/section tersebut untuk diagnosis perangkat tertentu tanpa menyaring tabel global.

## 3.5 Titik Lokasi

**URL:** `/admin/location-points`

Menu ini adalah tampilan monitoring `IotDevice`, bukan daftar fasilitas desa. Kolom utama:

- kode dan nama perangkat;
- komoditas;
- grid terdekat;
- latitude dan longitude;
- status aktif;
- waktu telemetri terakhir.

### SOP pemeriksaan lokasi

1. Filter perangkat aktif.
2. Bandingkan koordinat dengan posisi alat sebenarnya.
3. Salin koordinat bila perlu dan buka di aplikasi peta tepercaya.
4. Jika posisi salah, lakukan `/sync-gps`, lalu tekan `5` dan pastikan telemetri terkirim.
5. Jangan memperbaiki lokasi hanya dari perkiraan jika teknisi dapat melakukan sinkronisasi di lapangan.

## 3.6 Log Sensor

**URL:** `/admin/sensor-logs`

Sumber data menu ini adalah `IotTelemetry`. Setiap baris memuat:

| Metrik | Field | Unit |
|---|---|---|
| Suhu udara | `temp_air` | °C |
| Kelembapan udara | `hum_air` | % |
| Suhu tanah | `temp_soil` | °C |
| Kelembapan tanah | `hum_soil_percent` | % |
| Raw soil | `raw_soil` | nilai ADC |
| Cahaya | `lux_light` | lux |
| Koordinat | `latitude`, `longitude` | derajat desimal |
| Waktu | `created_at` | waktu server |

Menu menyediakan filter perangkat, urutan terbaru, halaman detail read-only, dan grafik 24 pembacaan terbaru. Data telemetri tidak diedit dari panel karena merupakan catatan historis alat.

## 3.7 Rekomendasi Lahan

**URL:** `/admin/land-recommendations`

Sumber data adalah model `AiRecommendation`. Informasi utama:

- perangkat;
- status kondisi;
- headline/tindakan;
- isi rekomendasi;
- waktu analisis.

Rekomendasi bersifat read-only agar hasil analisis tidak diubah seolah-olah berasal dari Gemini. Jika perlu menambahkan catatan pelaksanaan, lakukan melalui mekanisme terpisah yang diaudit, bukan mengubah hasil AI.

## 3.8 Grid Lahan

**URL monitoring:** `/admin/farm-grids`  
**URL pengelolaan legacy/detail:** `/admin/land-grids`

`/admin/farm-grids` menampilkan:

- kode grid;
- dusun;
- komoditas grid;
- koordinat titik tengah;
- jumlah perangkat aktif;
- ringkasan perangkat dan komoditas;
- status grid.

Perangkat secara otomatis diasosiasikan ke **grid aktif terdekat** saat disimpan atau saat lokasi berubah. Jika tidak ada grid aktif, `land_grid_id` dapat kosong.

> **Catatan asosiasi**  
> Mekanisme terdekat memakai jarak geografis berdasarkan titik tengah. Pastikan koordinat grid benar. Untuk batas lahan kompleks, polygon tetap berguna untuk visualisasi, tetapi asosiasi otomatis saat ini berpatokan pada titik tengah.

---

# BAB 4 — SETUP DAN OPERASIONAL ALAT IOT

## 4.1 Keselamatan dan Persiapan

> **PERINGATAN LISTRIK DAN LAPANGAN**  
> Matikan catu daya sebelum mengubah kabel. Lindungi rangkaian dari air, kondensasi, panas berlebih, korsleting, dan petir. Gunakan enclosure, gland kabel, sekering/regulator yang sesuai, serta ground yang baik. Pemasangan permanen sebaiknya ditinjau teknisi berkompeten.

Sebelum perakitan siapkan:

- ESP32 development board;
- sensor dan resistor sesuai spesifikasi;
- multimeter;
- kabel dan konektor berlabel;
- catu daya stabil;
- laptop dengan Arduino IDE;
- HP Android untuk uji BLE/GPS;
- akses admin untuk membuat `device_code` dan token.

## 4.2 Spesifikasi Komponen dan Pinout

| Kelompok | Komponen | Pin/Alamat ESP32 | Catatan |
|---|---|---|---|
| Udara | DHT21 | Data GPIO 17 | Suhu dan kelembapan udara |
| Tanah | DS18B20 | Data GPIO 16 | Gunakan pull-up 4,7 kΩ antara DATA dan 3,3 V |
| Tanah | Soil Moisture analog | ADC GPIO 34 | GPIO 34 input-only; kalibrasi nilai basah/kering |
| Anti-korosi | Power control soil sensor | GPIO 25 | Nyalakan hanya saat pembacaan; gunakan transistor/MOSFET jika arus melebihi pin |
| Cahaya | BH1750 | SDA GPIO 21, SCL GPIO 22 | Bus I²C |
| Display | LCD 16×2 I²C | Alamat `0x27`, SDA 21, SCL 22 | Berbagi bus dengan BH1750; pastikan alamat tidak konflik |
| Input | Keypad matrix 2×1 | GPIO 12, 13, 32 | Implementasi hemat tiga kabel |
| Indikator | LED biru online | GPIO 4 | Gunakan resistor pembatas arus |
| Indikator | LED merah offline/config | GPIO 15 | Perhatikan boot strapping pin pada board tertentu |

### Catatan wiring

- Semua ground harus terhubung bersama.
- Pastikan level tegangan sensor kompatibel dengan 3,3 V ESP32.
- GPIO 34 tidak memiliki pull-up/pull-down internal.
- GPIO 12 dan GPIO 15 dapat memengaruhi proses boot pada sebagian board; uji keadaan pin saat reset.
- Jangan memberi beban sensor langsung melalui GPIO 25 jika arusnya tidak aman; gunakan sakelar transistor/MOSFET.
- Lakukan kalibrasi soil moisture pada tanah kering dan basah di lokasi penggunaan.

## 4.3 Cara Kerja Tombol Keypad

| Tombol | Fungsi | Urutan proses |
|---|---|---|
| `5` | Manual Instant Sync | Baca sensor → kirim BLE Notify permintaan GPS → terima koordinat HP → POST telemetry |
| `0` | Reset Wi-Fi/config | Hapus konfigurasi Wi-Fi → restart → buka Captive Portal `Alat-Monitor-Desa` |

### Tombol `5`

1. Firmware menyalakan daya soil sensor melalui GPIO 25.
2. Firmware menunggu sensor stabil.
3. Semua sensor dibaca dan divalidasi.
4. ESP32 mengirim notify melalui characteristic request GPS.
5. Halaman `/sync-gps` menulis koordinat terbaru ke characteristic write.
6. ESP32 mengirim payload telemetri ke server.
7. LCD dan LED menunjukkan hasil online/gagal.

Firmware harus menyediakan timeout. Jika HP tidak terhubung atau GPS tidak diterima, kebijakan firmware harus jelas: gunakan koordinat NVS terakhir yang valid atau batalkan pengiriman; jangan mengirim `0,0` tanpa penanda error.

### Tombol `0`

Tombol reset Wi-Fi sebaiknya memerlukan tekan-tahan (misalnya 5–10 detik) agar tidak terpicu tanpa sengaja. Setelah reset:

1. ESP32 menghapus konfigurasi Wi-Fi tersimpan.
2. ESP32 restart.
3. Access point `Alat-Monitor-Desa` muncul.
4. Operator terhubung dan memilih Wi-Fi lahan/hotspot.
5. Setelah tersimpan, alat mencoba koneksi dan kembali ke mode normal.

> **Peringatan**  
> Reset Wi-Fi tidak boleh menghapus `DEVICE_TOKEN` kecuali firmware memang dirancang untuk provisioning token yang aman. Token bukan data yang boleh dimasukkan melalui portal Wi-Fi terbuka tanpa perlindungan.

## 4.4 Siklus Kerja Firmware

```text
BOOT
 │
 ├─► Inisialisasi LCD, LED, keypad, sensor, I2C, BLE
 ├─► Muat koordinat terakhir dari NVS
 ├─► Hubungkan Wi-Fi / buka WiFiManager bila gagal
 ├─► Advertising BLE sebagai ESP32-GPS-Sync
 │
 ▼
LOOP
 ├─► Tombol 5? ─► baca sensor ─► minta GPS BLE ─► POST telemetry
 ├─► Tombol 0 tahan? ─► reset Wi-Fi ─► restart/config portal
 ├─► Perbarui LCD/LED
 └─► Jaga watchdog dan hindari blocking tanpa timeout
```

## 4.5 Flashing melalui Arduino IDE

### Library yang diperlukan

Nama paket dapat berbeda menurut implementasi firmware, tetapi kemampuan berikut wajib tersedia:

| Library/Komponen | Fungsi |
|---|---|
| `BLEDevice` / ESP32 BLE Arduino | GATT server, service, notify, write |
| `WiFiManager` | Captive portal dan penyimpanan Wi-Fi |
| `ArduinoJson` | Serialisasi payload JSON |
| `Keypad` | Pembacaan keypad hemat kabel |
| `BH1750` | Sensor lux |
| `OneWire` + `DallasTemperature` | DS18B20 |
| `Adafruit_DHT` atau library DHT kompatibel | DHT21 |
| `HTTPClient` dan `WiFiClientSecure` | HTTPS POST telemetry |
| `LiquidCrystal_I2C` | LCD 16×2 |
| `Preferences` | NVS koordinat/config non-secret |

### Langkah flashing

1. Instal Arduino IDE versi stabil.
2. Tambahkan board package **ESP32 by Espressif Systems**.
3. Instal seluruh library dari Library Manager.
4. Buka source firmware yang telah disetujui.
5. Pilih board ESP32 yang tepat dan port COM.
6. Isi `DEVICE_CODE`, `DEVICE_TOKEN`, endpoint, UUID, dan sertifikat/TLS policy.
7. Jalankan **Verify/Compile**.
8. Hubungkan ESP32 melalui kabel data yang baik.
9. Klik **Upload**; tekan tombol BOOT bila board memerlukannya.
10. Buka Serial Monitor pada baud firmware, misalnya 115200.
11. Periksa inisialisasi semua sensor dan BLE.
12. Lakukan provisioning Wi-Fi.
13. Uji tombol `5`, tombol `0`, API, admin, dan peta.

## 4.6 Contoh Konfigurasi Firmware

> **Catatan**  
> Potongan berikut adalah referensi konfigurasi, bukan firmware lengkap. Firmware final harus memiliki validasi, timeout, TLS, debounce, watchdog, pengamanan NVS, dan penanganan error.

```cpp
#include <Arduino.h>

constexpr char DEVICE_CODE[] = "IOT-KAL-001";
constexpr char DEVICE_TOKEN[] = "<TOKEN_64_KARAKTER_DARI_FILAMENT>";
constexpr char TELEMETRY_URL[] = "https://desa.example.id/api/v1/telemetry";

constexpr char BLE_DEVICE_NAME[] = "ESP32-GPS-Sync";
constexpr char BLE_SERVICE_UUID[] = "4fafc201-1fb5-459e-8fcc-c5c9c331914b";
constexpr char BLE_NOTIFY_UUID[] = "beb5483e-36e1-4688-b7f5-ea07361b26a8";
constexpr char BLE_WRITE_UUID[] = "beb5483e-36e1-4688-b7f5-ea07361b26a9";

constexpr uint8_t PIN_DHT21 = 17;
constexpr uint8_t PIN_DS18B20 = 16;
constexpr uint8_t PIN_SOIL_ADC = 34;
constexpr uint8_t PIN_SOIL_POWER = 25;
constexpr uint8_t PIN_I2C_SDA = 21;
constexpr uint8_t PIN_I2C_SCL = 22;
constexpr uint8_t PIN_LED_ONLINE = 4;
constexpr uint8_t PIN_LED_OFFLINE = 15;
```

Contoh bentuk POST:

```cpp
http.begin(secureClient, TELEMETRY_URL);
http.addHeader("Content-Type", "application/json");
http.addHeader("Accept", "application/json");
http.addHeader("X-Device-Token", DEVICE_TOKEN);

const int status = http.POST(payloadJson);
```

> **Keamanan TLS**  
> Jangan menggunakan `secureClient.setInsecure()` di produksi. Pasang CA certificate yang sesuai dan kelola masa berlakunya.

## 4.7 Checklist Uji Alat

- [ ] Board boot tanpa reset loop.
- [ ] DHT21 menghasilkan suhu/kelembapan masuk akal.
- [ ] DS18B20 tidak menghasilkan nilai putus sensor (misalnya -127 °C).
- [ ] Soil moisture telah dikalibrasi.
- [ ] GPIO 25 memutus daya sensor setelah pembacaan.
- [ ] BH1750 dan LCD terdeteksi pada bus I²C.
- [ ] Tombol `5` hanya memicu satu siklus per tekan.
- [ ] Tombol `0` memerlukan konfirmasi/tekan-tahan.
- [ ] BLE muncul sebagai `ESP32-GPS-Sync`.
- [ ] Notify dan write characteristic dapat ditemukan.
- [ ] Koordinat dari HP tersimpan dan bukan `0,0`.
- [ ] HTTPS POST mengembalikan HTTP 200.
- [ ] `telemetry_id` dan `recommendation_id` tersedia.
- [ ] Data tampil di admin dan peta.
- [ ] LED/LCD menunjukkan status yang benar.

---

# BAB 5 — MEKANISME AI REASONING DAN SINKRONISASI DATA

## 5.1 Kontrak API Telemetri

### Endpoint utama perangkat

| Item | Nilai |
|---|---|
| Method | `POST` |
| URL | `/api/v1/telemetry` |
| Header autentikasi | `X-Device-Token` |
| Content type | `application/json` |
| Rate limit | 120 request/menit |
| Sukses | HTTP 200 |
| Token salah/nonaktif | HTTP 401 |
| Payload tidak valid | HTTP 422 |

Payload wajib:

```json
{
  "latitude": -7.2145,
  "longitude": 110.8234,
  "temp_air": 30.2,
  "hum_air": 71.4,
  "temp_soil": 27.8,
  "hum_soil_percent": 54.6,
  "raw_soil": 1850,
  "lux_light": 12750.5
}
```

Rentang validasi:

| Field | Rentang |
|---|---|
| `latitude` | -90 sampai 90 |
| `longitude` | -180 sampai 180 |
| `temp_air` | -50 sampai 80 |
| `hum_air` | 0 sampai 100 |
| `temp_soil` | -20 sampai 80 |
| `hum_soil_percent` | 0 sampai 100 |
| `raw_soil` | integer 0 sampai 65535 |
| `lux_light` | 0 sampai 200000 |

Contoh response:

```json
{
  "success": true,
  "message": "Telemetry successfully received.",
  "data": {
    "telemetry_id": 1042,
    "recommendation_id": 1042,
    "device_code": "IOT-KAL-001",
    "received_at": "2026-08-12T12:00:00.000000Z"
  }
}
```

### Endpoint legacy

`POST /api/v1/iot/telemetry` menggunakan `X-IoT-Device-Token`/webhook secret dan format sensor pH/grid lama. Endpoint ini dipertahankan untuk kompatibilitas, tetapi **bukan** endpoint firmware perangkat multi-sensor yang dijelaskan dalam dokumen ini.

## 5.2 Alur Transaksi dan Sinkronisasi

1. `StoreDeviceTelemetryRequest` mencari perangkat aktif berdasarkan hash token.
2. Laravel memvalidasi seluruh metrik dan koordinat.
3. Dalam transaksi database:
   - membuat `iot_telemetries`;
   - mengisi snapshot koordinat;
   - memperbarui latitude, longitude, dan `last_active_at` perangkat.
4. Observer mengasosiasikan perangkat ke grid aktif terdekat.
5. Controller menjalankan `ProcessTelemetryAiReasoning::dispatchSync()`.
6. Service AI menyimpan `ai_recommendations`.
7. Jika provider melempar error, method fallback membuat rekomendasi `caution`.
8. Response baru dikirim setelah rekomendasi tersedia.

Konsekuensi:

- data admin konsisten segera setelah HTTP 200;
- ingestion tidak memerlukan `php artisan queue:work` untuk AI ini;
- waktu response bergantung pada koneksi Gemini (timeout request Gemini 30 detik pada implementasi service);
- queue worker masih dapat dipakai modul lain dan tetap direstart saat deployment.

## 5.3 Direct Call Google Gemini

Konfigurasi produksi yang ditetapkan:

```env
LLM_PROVIDER=gemini
GEMINI_API_KEY=<RAHASIA_DARI_GOOGLE_AI_STUDIO>
GEMINI_MODEL=gemini-2.0-flash
GEMINI_MODELS_URL=https://generativelanguage.googleapis.com/v1beta/models
GEMINI_TIMEOUT=30
```

> **Penting tentang model**  
> Aplikasi membaca `GEMINI_MODEL` dari `.env`. Tetapkan `gemini-2.0-flash` secara eksplisit sesuai standar instalasi ini dan validasi bahwa model masih tersedia pada akun/proyek Google. Jangan mengandalkan nilai fallback di file konfigurasi karena fallback dapat berubah antarversi.

### Konteks lokal dalam prompt

Sistem menginstruksikan AI sebagai **AgriBot Kalimati** dengan konteks:

- Desa Kalimati, Juwangi, Boyolali;
- sawah tadah hujan;
- komoditas utama Jagung dan Pisang;
- hama/masalah lokal: Tikus, Ulat Grayak, Bule (konteks fungisida), dan Engkok/Uret;
- saran aman, praktis, ringkas, dan berbahasa Indonesia;
- larangan mendiagnosis hama tanpa bukti telemetri atau pemeriksaan lapangan.

Input AI mencakup kode/nama perangkat, komoditas, suhu/kelembapan udara, suhu/kelembapan tanah, cahaya, dan waktu rekam. `raw_soil` disimpan untuk diagnosis perangkat, tetapi prompt AI saat ini mengutamakan metrik yang sudah dikonversi.

### Schema respons AI

```json
{
  "condition_status": "optimal",
  "headline": "Kondisi lahan stabil",
  "action_recommendation": "Pertahankan pemantauan dan penyiraman saat ini."
}
```

Service hanya menerima `optimal`, `caution`, atau `warning` dari provider pada alur saat ini. Enum aplikasi juga memiliki `critical` untuk kompatibilitas/tampilan, tetapi schema Gemini langsung belum meminta nilai tersebut.

### Fallback

Jika API key salah, model tidak tersedia, jaringan timeout, HTTP gagal, atau JSON tidak sesuai schema, sistem menyimpan:

- status `caution`;
- headline **Periksa kondisi lahan secara manual**;
- narasi bahwa analisis AI tidak tersedia dan pembacaan sensor perlu diverifikasi langsung.

Fallback menjaga integritas data, tetapi operator tetap harus memperbaiki penyebab kegagalan Gemini.

## 5.4 Status Kondisi AI

| Status | Label | Warna Filament/Map | Respons operator |
|---|---|---|---|
| `optimal` | Optimal | Hijau | Monitoring rutin |
| `caution` | Waspada | Kuning | Tinjau sensor dan kondisi lapangan |
| `warning` | Peringatan | Merah | Periksa segera dan konsultasikan tindakan |
| `critical` | Kritis | Merah tua | Eskalasi; verifikasi manusia wajib |

> AI bukan pengganti penyuluh, agronom, atau inspeksi lapangan. Jangan menentukan dosis bahan kimia hanya dari satu pembacaan.

## 5.5 Konektivitas BLE dan Web Bluetooth

| Komponen | UUID | Kapabilitas |
|---|---|---|
| Service GPS Sync | `4fafc201-1fb5-459e-8fcc-c5c9c331914b` | Primary GATT service |
| Request GPS | `beb5483e-36e1-4688-b7f5-ea07361b26a8` | Notify atau Indicate |
| Write Coordinate | `beb5483e-36e1-4688-b7f5-ea07361b26a9` | Write atau Write Without Response |

Frontend meminta perangkat berdasarkan nama `ESP32-GPS-Sync` dan service UUID. Setelah terhubung, frontend mengambil seluruh characteristic pada service lalu memilih characteristic yang memiliki properti notify/indicate dan write/writeWithoutResponse.

### Diagram handshake BLE

```text
Chrome HP                         ESP32
   │                                │
   │── requestDevice(name/service) ►│
   │◄──────── GATT connected ───────│
   │── startNotifications ─────────►│ Request GPS characteristic
   │                                │
   │       pengguna tekan "5"       │
   │◄──────── BLE Notify ───────────│
   │                                │
   │ Geolocation watchPosition      │
   │── Write "lat,long" ───────────►│ Write coordinate characteristic
   │                                │
   │                 ESP32 simpan koordinat dan POST telemetry
```

## 5.6 Struktur Data dan Relasi

```text
land_grids (1) ──────────────── (N) iot_devices
                                           │
                                           ├── (N) iot_telemetries
                                           │          │
                                           │          └── (N) ai_recommendations
                                           │
                                           └── (N) ai_recommendations
```

- Penghapusan perangkat menghapus telemetri dan rekomendasinya melalui cascade.
- Penghapusan grid membuat `land_grid_id` perangkat menjadi null.
- Snapshot koordinat di telemetri mempertahankan posisi pada waktu pembacaan.
- Posisi di `iot_devices` menunjukkan lokasi terbaru yang dipakai peta.

---

# BAB 6 — MAINTENANCE, DEPLOYMENT, DAN TROUBLESHOOTING

## 6.1 Pembagian Tanggung Jawab Operasional

| Frekuensi | Aktivitas | Penanggung jawab |
|---|---|---|
| Harian | Cek perangkat aktif, telemetri terbaru, marker, warning | Kelompok Tani |
| Mingguan | Cek log error, ruang disk, backup, status HTTPS | Teknisi Server |
| Bulanan | Uji restore backup, audit pengguna/role, update dependency terencana | Super Admin + Pengembang |
| Musiman | Kalibrasi sensor dan validasi rekomendasi terhadap lapangan | Kelompok Tani + Penyuluh |
| Setiap rilis | Test, build, backup, migration, smoke test, catatan perubahan | Pengembang + Teknisi |

## 6.2 Deployment ke VPS if62

Repositori menyediakan `deploy.sh`. Skrip melakukan maintenance mode, `git pull --ff-only`, Composer production install, publish/cek asset Filament, migration, cache, npm build, validasi Vite manifest, restart queue, lalu mengaktifkan aplikasi kembali.

### Cara yang direkomendasikan

```bash
cd /var/www/web-kalimati
git status --short
./deploy.sh
```

Skrip akan membatalkan deployment jika ada perubahan tracked lokal. Ini mencegah `git pull` menimpa perubahan server.

### Urutan manual darurat

Gunakan hanya jika `deploy.sh` tidak dapat dipakai dan operator memahami dampaknya:

```bash
cd /var/www/web-kalimati

php artisan down || true
git pull --ff-only origin main
composer install --no-dev --optimize-autoloader --no-interaction
php artisan optimize:clear
php artisan filament:assets
php artisan migrate --force
npm ci --no-audit --no-fund --progress=false
npm run build
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components
php artisan queue:restart
php artisan up
```

Perintah minimum yang sering disebut (`git pull`, `php artisan migrate --force`, `npm run build`, `php artisan optimize:clear`) **tidak menggantikan** proses lengkap produksi di atas.

> **DILARANG DI PRODUKSI**  
> Jangan menjalankan `php artisan migrate:fresh`, `db:wipe`, menghapus file database, atau mengganti `.env` tanpa backup dan prosedur perubahan. Perintah tersebut dapat menghapus seluruh data.

### Smoke test setelah deployment

```bash
php artisan about
php artisan migrate:status
php artisan route:list --path=api/v1
php artisan route:list --path=admin
```

Kemudian uji:

- `/`;
- `/berita`;
- `/peta`;
- `/sync-gps` melalui HTTPS;
- `/admin`;
- satu POST telemetry dari perangkat uji;
- marker, log sensor, dan rekomendasi.

## 6.3 Konfigurasi Environment Penting

Contoh produksi:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://desa.example.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=desa_kalimati
DB_USERNAME=<DB_USER>
DB_PASSWORD=<DB_PASSWORD_RAHASIA>

GIS_CENTER_LATITUDE=-7.2145
GIS_CENTER_LONGITUDE=110.8234
GIS_DEFAULT_ZOOM=15
LEAFLET_TILE_PROVIDER=https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png

LLM_PROVIDER=gemini
GEMINI_API_KEY=<GEMINI_API_KEY_RAHASIA>
GEMINI_MODEL=gemini-2.0-flash
GEMINI_MODELS_URL=https://generativelanguage.googleapis.com/v1beta/models
GEMINI_TIMEOUT=30
```

Setelah mengubah `.env`:

```bash
php artisan optimize:clear
php artisan config:cache
```

### Aturan secret

- `.env` tidak boleh di-commit.
- Permission file disarankan membatasi user web/deploy yang perlu membaca.
- Rotasi API key jika pernah bocor.
- Rotasi token perangkat satu per satu dan sinkronkan firmware.
- Jangan menampilkan `phpinfo()` di produksi.

## 6.4 Backup dan Pemulihan

### MySQL

```bash
mkdir -p /var/backups/web-kalimati
mysqldump --single-transaction --routines --triggers \
  -u <DB_USER> -p desa_kalimati \
  | gzip > /var/backups/web-kalimati/db-$(date +%F-%H%M).sql.gz

tar -czf /var/backups/web-kalimati/storage-$(date +%F-%H%M).tar.gz \
  -C /var/www/web-kalimati storage/app/public
```

### SQLite

Hentikan write sementara atau gunakan mekanisme backup SQLite yang konsisten; jangan hanya menyalin file ketika transaksi aktif tanpa pengetahuan teknis.

### Prinsip 3-2-1

- 3 salinan data;
- 2 media berbeda;
- 1 salinan di lokasi berbeda/off-site.

### Uji restore

Backup yang tidak pernah diuji belum dapat dianggap valid. Lakukan restore ke database staging, jalankan migration status, lalu cek berita, media, perangkat, dan telemetri.

## 6.5 Monitoring Berkala

Perintah pemeriksaan:

```bash
php artisan about
php artisan migrate:status
php artisan queue:failed
tail -n 200 storage/logs/laravel.log
df -h
free -h
systemctl status php8.3-fpm --no-pager
systemctl status nginx --no-pager
```

Sesuaikan versi PHP dan nama service dengan VPS `if62`.

Indikator aplikasi sehat:

- route publik merespons 200;
- login admin dapat dibuka;
- asset Vite dan Filament tidak 404;
- migration tidak pending setelah deployment;
- telemetri baru masuk;
- `last_active_at` berubah;
- rekomendasi terbuat;
- marker berada di Kalimati;
- disk dan log tidak penuh.

## 6.6 Troubleshooting dan FAQ

### Masalah 1 — “Characteristic request dan write tidak ditemukan pada service ESP32”

**Gejala:** Chrome menemukan alat dan service, tetapi koneksi berhenti dengan pesan tersebut.

**Penyebab umum:**

- firmware hanya membuat satu characteristic tanpa properti yang tepat;
- Notify tidak memiliki descriptor/kapabilitas;
- characteristic write hanya read;
- service UUID benar tetapi firmware lama;
- browser masih terhubung ke firmware lama.

**Solusi:**

1. Flash firmware BLE capabilities terbaru.
2. Pastikan UUID notify dan write sesuai Lampiran B.
3. Notify harus memiliki properti `NOTIFY`/`INDICATE`.
4. Write harus memiliki `WRITE` atau `WRITE_NR`.
5. Restart ESP32, matikan-nyalakan Bluetooth HP, reload halaman, lalu pairing ulang.

### Masalah 2 — Marker peta terlempar jauh atau “nyasar”

**Penyebab:** koordinat NVS/default salah, urutan lat-long tertukar, GPS HP tidak akurat, atau telemetri lama.

**Solusi:**

1. Periksa bahwa urutan BLE adalah `latitude,longitude`.
2. Datang ke lokasi alat dan buka `/sync-gps`.
3. Tunggu akurasi GPS membaik.
4. Tekan `5`.
5. Pastikan POST telemetry sukses.
6. Cek `/admin/location-points` dan `/peta`.
7. Jika masih salah, hapus/koreksi koordinat NVS melalui firmware yang terkontrol.

### Masalah 3 — Data berita publik tidak berubah

**Pemeriksaan:**

1. Pastikan record berada di tabel `news_articles` pada database yang dipakai aplikasi.
2. Pastikan `is_published = true` dan `published_at` benar.
3. Pastikan kategori valid: `kkn`, `karang_taruna`, atau `pemdes`.
4. Bersihkan cache: `php artisan optimize:clear`.
5. Verifikasi `.env` tidak menunjuk database lain.
6. Seeder hanya untuk data awal; jangan menjalankan seeder destruktif di produksi.

### Masalah 4 — AI Recommendation `null` atau tidak muncul

1. Pastikan `.env`:

   ```env
   LLM_PROVIDER=gemini
   GEMINI_API_KEY=<VALID>
   GEMINI_MODEL=gemini-2.0-flash
   ```

2. Jalankan `php artisan optimize:clear && php artisan config:cache`.
3. Periksa `storage/logs/laravel.log`.
4. Pastikan server dapat mengakses `generativelanguage.googleapis.com`.
5. Pastikan model tersedia dan quota API cukup.
6. Kirim telemetri baru; rekomendasi lama tidak otomatis diproses ulang.
7. Jika provider gagal, seharusnya ada fallback `caution`; jika benar-benar null, periksa exception database/migration.

### Masalah 5 — HTTP 401 pada telemetry

- Header harus bernama `X-Device-Token`.
- Token firmware harus sama dengan saat perangkat dibuat.
- Perangkat harus `is_active = true`.
- Jangan memakai `IOT_WEBHOOK_SECRET` pada endpoint utama perangkat.
- Rotasi token mengharuskan pembaruan firmware.

### Masalah 6 — HTTP 422 pada telemetry

Periksa response `errors`. Semua field wajib ada dan berada dalam rentang BAB 5.1. Pastikan nilai JSON numerik tidak dikirim sebagai `NaN`, `Infinity`, string kosong, atau null.

### Masalah 7 — Web Bluetooth tidak tersedia

- gunakan Chrome Android;
- pastikan HTTPS;
- aktifkan Bluetooth dan lokasi;
- berikan izin Perangkat Sekitar dan Lokasi;
- Web Bluetooth tidak didukung pada banyak browser iOS/desktop tertentu.

### Masalah 8 — Perangkat tidak muncul di peta

- perangkat harus aktif;
- endpoint `/api/v1/gis/iot-devices` harus merespons 200;
- koordinat harus valid;
- cek error JavaScript browser;
- peta dan API dibatasi rate 60 request/menit untuk endpoint GIS.

### Masalah 9 — Asset admin/public rusak setelah deployment

```bash
php artisan optimize:clear
php artisan filament:assets
npm ci --no-audit --no-fund --progress=false
npm run build
php artisan view:clear
```

Pastikan `public/build/manifest.json` dan asset Filament tersedia serta web server dapat membacanya.

### Masalah 10 — AI lambat membuat alat tampak gagal

Karena AI synchronous, response telemetry menunggu Gemini. Periksa koneksi internet VPS, latency, quota, dan timeout. Firmware harus memiliki timeout HTTP yang lebih panjang daripada timeout AI server serta retry dengan backoff, **tanpa** membuat banjir request duplikat.

## 6.7 Checklist Serah Terima

### Aplikasi

- [ ] Domain dan HTTPS aktif.
- [ ] `.env` produksi tersimpan aman.
- [ ] Database dan storage telah dibackup.
- [ ] Seluruh migration berstatus `Ran`.
- [ ] Build Vite dan asset Filament tersedia.
- [ ] Akun Super Admin diserahkan melalui kanal aman.
- [ ] Role Kelompok Tani diuji.
- [ ] Berita draft dan published diuji.
- [ ] Peta dan endpoint GIS diuji.

### IoT

- [ ] Setiap alat memiliki `device_code` unik.
- [ ] Token tiap alat unik dan terdokumentasi aman di inventaris rahasia.
- [ ] Pinout dan foto wiring disimpan pada dokumen aset.
- [ ] Firmware source/version dan binary release diarsipkan.
- [ ] BLE notify/write diuji.
- [ ] GPS HP dan tombol `5` diuji.
- [ ] Reset Wi-Fi tombol `0` diuji.
- [ ] Telemetri, rekomendasi, marker, dan radius diuji.

### Organisasi

- [ ] PIC Super Admin ditetapkan.
- [ ] PIC server dan PIC IoT ditetapkan.
- [ ] Jadwal backup dan kalibrasi disepakati.
- [ ] Kontak eskalasi tersedia.
- [ ] Riwayat perubahan dokumen mulai digunakan.

---

# LAMPIRAN A — REFERENSI CEPAT ENDPOINT

| Method | Endpoint | Auth | Fungsi |
|---|---|---|---|
| GET | `/` | Publik | Beranda |
| GET | `/berita` | Publik | Daftar berita |
| GET | `/berita/{slug}` | Publik | Detail berita |
| GET | `/peta` | Publik | Peta Leaflet |
| GET | `/sync-gps` | Publik + izin browser | BLE/GPS HP |
| GET | `/api/v1/gis/points-of-interest` | Publik, rate limit | Data POI |
| GET | `/api/v1/gis/iot-devices` | Publik, rate limit | Perangkat aktif + latest telemetry/recommendation |
| POST | `/api/v1/telemetry` | `X-Device-Token` | Telemetri perangkat utama + AI sync |
| POST | `/api/v1/iot/telemetry` | `X-IoT-Device-Token` | Endpoint legacy pH/grid |
| GET | `/admin` | Session login | Dashboard Filament |
| GET | `/admin/news-articles` | Permission berita | Kelola berita |
| GET | `/admin/iot-devices` | Permission IoT | Kelola perangkat |
| GET | `/admin/location-points` | Permission IoT/GIS | Monitoring lokasi |
| GET | `/admin/sensor-logs` | Permission sensor | Histori telemetry |
| GET | `/admin/land-recommendations` | Permission recommendation | Hasil Gemini/fallback |
| GET | `/admin/farm-grids` | Permission grid | Monitoring grid/perangkat |

# LAMPIRAN B — REFERENSI CEPAT BLE

| Item | Nilai |
|---|---|
| Nama perangkat | `ESP32-GPS-Sync` |
| Service UUID | `4fafc201-1fb5-459e-8fcc-c5c9c331914b` |
| Notify request GPS | `beb5483e-36e1-4688-b7f5-ea07361b26a8` |
| Write koordinat | `beb5483e-36e1-4688-b7f5-ea07361b26a9` |
| Format koordinat | `latitude,longitude` UTF-8 |
| Browser | Chrome Android |
| Keamanan web | HTTPS wajib |

# LAMPIRAN C — SOP HARIAN KELOMPOK TANI

1. Periksa LED/LCD alat di lahan.
2. Buka `/admin/location-points`; cek status aktif dan waktu terakhir.
3. Buka `/admin/sensor-logs`; cek apakah data baru masuk.
4. Tinjau suhu, kelembapan, dan cahaya terhadap kondisi nyata.
5. Buka `/admin/land-recommendations`; prioritaskan warning.
6. Bila koordinat salah, lakukan sinkronisasi GPS di lokasi alat.
7. Bila data tidak masuk, cek daya, Wi-Fi, token, dan endpoint.
8. Catat tindakan lapangan dan anomali di log operasional tim.
9. Jangan menerapkan bahan kimia hanya berdasarkan AI.

# LAMPIRAN D — SOP INSIDEN

## Tingkat insiden

| Tingkat | Contoh | Respons |
|---|---|---|
| P1 Kritis | Website mati total, database hilang, kebocoran secret | Hentikan perubahan, amankan sistem, eskalasi segera, restore terkontrol |
| P2 Tinggi | Semua alat gagal kirim, Gemini gagal terus, login admin rusak | Diagnosis dalam hari yang sama |
| P3 Sedang | Satu alat offline, marker salah, satu halaman bermasalah | Jadwalkan perbaikan dan dokumentasikan |
| P4 Rendah | Perbaikan teks/tampilan minor | Masukkan backlog rilis |

## Format catatan insiden

```text
Waktu mulai       :
Pelapor            :
Layanan/perangkat  :
Gejala             :
Dampak             :
Perubahan terakhir :
Log/error           :
Tindakan sementara :
Akar masalah        :
Perbaikan permanen  :
Waktu pulih         :
PIC                 :
```

# LAMPIRAN E — RIWAYAT PERUBAHAN DOKUMEN

| Versi | Tanggal | Penyusun | Ringkasan |
|---|---|---|---|
| 1.0 | Agustus 2026 | Tim Sistem Desa Kalimati | Dokumen induk awal portal, GIS, IoT, Gemini, admin, dan deployment |

---

## PENUTUP

Keberhasilan sistem tidak hanya ditentukan oleh kode, tetapi oleh disiplin pengelolaan akun, ketepatan data, kalibrasi alat, keamanan secret, backup yang dapat dipulihkan, serta komunikasi antara Pemerintah Desa, Kelompok Tani, Tim KKN/Pengembang, dan teknisi. Gunakan pedoman ini sebagai dokumen hidup: setiap perubahan endpoint, wiring, firmware, role, model AI, atau prosedur deployment harus dicatat dan ditinjau kembali.
