# Implementation Roadmap - Website Desa Kalimati

## 1. Project Milestone Overview
Roadmap ini membagi eksekusi pengembangan Website Desa Kalimati menjadi 6 fase terstruktur, mulai dari fondasi sistem hingga pengujian dan siap rilis.

---

## 2. Development Phases

### Phase 1: Environment & Core Setup (Week 1)
- [ ] Inisialisasi project Laravel 11 & setup DB (MySQL / PostgreSQL).
- [ ] Install & konfigurasikan Filament v3 Admin Panel.
- [ ] Setup RBAC menggunakan Spatie Permissions & Filament Shield (Super Admin, Admin Desa, Kelompok Tani, Pelaku UMKM, Warga).
- [ ] Eksekusi seluruh Database Migrations & Seeders awal (Default Users & Roles).
- [ ] Setup Laravel Pint & PHPCS untuk penegakan koding standar PSR-12.

### Phase 2: Core Public Portal & Web GIS Layer (Week 2)
- [ ] Implementasi Filament Resource untuk `NewsArticle` (CRUD Berita & Pengumuman Desa).
- [ ] Implementasi Filament Resource untuk `GisPointOfInterest` (Data Fasilitas Publik & Geotagging).
- [ ] Integrasi Leaflet.js / Mapbox pada Halaman Public Web GIS.
- [ ] Buat API Endpoint `/api/v1/gis/points-of-interest` untuk menyuplai marker ke peta GIS.
- [ ] Integrasi Spatie Media Library untuk upload thumbnail & galeri kegiatan.

### Phase 3: Smart Agriculture & IoT Telemetry (Week 3 - 4)
- [ ] Implementasi Filament Resource untuk `LandGrid` (Manajemen Plot 10x10m).
- [ ] Integrasi Leaflet Draw untuk input GeoJSON Polygon area pertanian.
- [ ] Buat Ingestion API Endpoint `/api/v1/iot/telemetry` + Autentikasi Header `X-IoT-Device-Token`.
- [ ] Buat Service Action `FetchLLMRecommendationAction` untuk komunikasi HTTP Outbound ke Python FastAPI / Ollama RAG Engine.
- [ ] Implementasi UI Visualisasi Chart.js pada Dashboard Filament & Public Grid Map (pH, Kelembapan, Suhu).
- [ ] Buat modul riwayat & cetak rekomendasi penanganan tanah (`land_recommendations`).

### Phase 4: UMKM Showcase & Simple Financial Ledger (Week 5)
- [ ] Implementasi Filament Resource untuk `UmkmBusiness` (Katalog Usaha Desa).
- [ ] Buat modul Buku Kas Sederhana (`umkm_ledgers`) dengan kalkulasi otomatis Pemasukan vs Pengeluaran.
- [ ] Halaman Public Katalog UMKM + Integrasi Direct Chat WhatsApp ke Pemilik Usaha.

### Phase 5: KKN Research Hub (Week 6)
- [ ] Implementasi Filament Resource untuk `ResearchFile` (Arsip Dokumen Monografi & Laporan KKN).
- [ ] Validasi Upload File ketat (PDF, XLSX, DOCX) dengan pembatasan MIME type & ukuran file.
- [ ] Public Repository UI dengan fitur pencarian, filter kategori, dan viewer PDF embedded.

### Phase 6: Testing, Optimization, & Deployment (Week 7)
- [ ] Database Indexing & Query Optimization (Cek efisiensi Query Telemetri & GIS).
- [ ] Testing Integrasi IoT Webhook & Fallback Handling jika RAG/LLM Down.
- [ ] Configuration Web Server (Nginx, PHP-FPM 8.2+, SSL Certificate, & Cloudflare Tunnels).
- [ ] User Acceptance Testing (UAT) bersama Perangkat Desa & Kelompok Tani.

---

## 3. Definition of Done (DoD)
Setiap fitur dianggap selesai jika memenuhi kriteria berikut:
1. Lulus pemeriksaan Laravel Pint (tidak ada linting error).
2. Memiliki Type Hinting penuh dan `declare(strict_types=1);` pada file PHP backend.
3. Fitur CRUD berfungsi normal pada Filament Backoffice sesuai hak akses Role-nya.
4. UI Frontend responsif pada perangkat Mobile maupun Desktop.