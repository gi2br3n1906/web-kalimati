# Components Blueprint - Website Desa Kalimati

## 1. Filament v3 Resources & Backoffice Components

### A. Core & Public Content Management
- Resource: `App\Filament\Resources\NewsArticleResource`
  - Navigation Group: `Informasi Publik`
  - Form Schema: Title, Slug (auto-generated), Category (Enum), Content (RichEditor), Thumbnail (Spatie Media Library Attachment), Is Published, Published At.
  - Table Columns: Thumbnail, Title, Category, Is Published, Published At.

### B. Smart Agriculture & IoT Module
- Resource: `App\Filament\Resources\LandGridResource`
  - Navigation Group: `Smart Agriculture`
  - Form Schema: Grid Code, Dusun Name, Commodity Type, Latitude, Longitude, GeoJSON Boundary Polygon, Owner Name, Status.
  - Actions: 
    - `RequestAiRecommendationAction`: Trigger manual untuk memanggil LLM/RAG service berdasarkan log sensor terbaru.
- Resource: `App\Filament\Resources\SensorLogResource`
  - Navigation Group: `Smart Agriculture`
  - Table Columns: Land Grid Code, Device ID, pH Level, Moisture (%), Temp (°C), Recorded At.
  - Relation Manager / Widgets: `SensorTrendChartWidget` (Visualisasi tren pH dan Kelembapan via Chart.js).
- Resource: `App\Filament\Resources\LandRecommendationResource`
  - Navigation Group: `Smart Agriculture`
  - View / Form: Diagnostic Summary, Recommended Fertilizer Dosage, Lime Treatment (Dolomit), Action Plan, Implementation Status Toggle (`is_applied`).
- Resource: `App\Filament\Resources\IotDeviceResource`
  - Form: nama, kode perangkat, token otomatis, koordinat, radius, komoditas, status aktif.
  - Relation managers: histori telemetry dan riwayat rekomendasi AI.

### C. Web GIS & Geotagging
- Resource: `App\Filament\Resources\GisPointOfInterestResource`
  - Navigation Group: `Web GIS`
  - Form Schema: Name, Category (Enum), Latitude, Longitude, Description, Icon Marker Picker.
  - Header Action: `Import KML Google Earth` untuk bulk import Placemark Point/Polygon dari KML, KMZ, atau XML dengan kategori default.

### D. KKN Research Hub
- Resource: `App\Filament\Resources\ResearchFileResource`
  - Navigation Group: `Research Hub`
  - Form Schema: Title, KKN Cohort, Category (Monografi/Saintek/Soshum), Author Names, File Path (PDF/DOCX Upload), Abstract, Public Access Toggle.

### E. UMKM Directory & Accounting
- Resource: `App\Filament\Resources\UmkmBusinessResource`
  - Navigation Group: `UMKM & Ekonomi`
  - Form Schema: Business Name, Owner Name, Category, WhatsApp Contact Number, Logo, Address, Description.
- Resource: `App\Filament\Resources\UmkmLedgerResource`
  - Navigation Group: `UMKM & Ekonomi`
  - Form Schema: Business Relation, Transaction Date, Type (Income/Expense), Amount, Category, Notes.

---

## 2. Livewire v3 Components (Public Frontend)

- `App\Livewire\Public\LandingPage`
  - Component view untuk halaman utama: Hero Section, Running News, Quick GIS Preview, dan Profil Singkat Desa.
- `App\Livewire\Public\GisInteractiveMap`
  - Component peta interaktif Leaflet.js full-screen dengan POI serta marker/radius perangkat IoT berwarna sesuai status AI.
- `App\Livewire\Public\AgricultureIndex`
  - Menampilkan embedded Leaflet map perangkat IoT aktif beserta telemetry dan rekomendasi terbaru.
- `App\Livewire\Public\SmartAgri\GridCatalog`
  - Katalogue visual plot tanah 10x10m dengan filter per dusun dan komoditas, disertai popup ringkasan kesehatan tanah.
- `App\Livewire\Public\ResearchHub\ArchiveIndex`
  - Portal pencarian dokumen KKN/Monografi dengan filter kategori, kata kunci, dan modal viewer PDF.
- `App\Livewire\Public\Umkm\DirectoryIndex`
  - Katalog bisnis UMKM lokal dengan filter kategori dan tombol direct CTA ke WhatsApp pemilik usaha.
- `App\Livewire\Public\Umkm\LedgerCalculator`
  - Interface pencatatan kas sederhana bagi pelaku UMKM untuk melakukan kalkulasi laba/rugi harian/bulanan.

---

## 3. Actions & Service Classes (Single Responsibility)

### A. Domain Agriculture
- `App\Actions\Agriculture\ProcessSensorTelemetryAction`
  - Menerima payload IoT, melakukan validasi range (pH 0-14, Kelembapan 0-100%), menyimpan ke `sensor_logs`, dan memperbarui status pada `land_grids`.
- `App\Actions\Agriculture\FetchLLMRecommendationAction`
  - Menyusun context data dari `land_grids` dan `sensor_logs`, melakukan HTTP POST request ke LLM/RAG Python FastAPI Endpoint, dan menyimpan output ke `land_recommendations`.

### B. Domain UMKM
- `App\Actions\Umkm\CalculateLedgerSummaryAction`
  - Mengkalkulasi total pemasukan, pengeluaran, dan net cash-flow untuk usaha UMKM tertentu dalam rentang periode tanggal.

### D. Domain IoT Monitoring
- `App\Services\IotAiReasoningService`
  - Mengirim telemetry terstruktur dan konteks tadah hujan, tumpang sari, serta hama lokal Kalimati ke Gemini.
- `App\Jobs\ProcessTelemetryAiReasoning`
  - Memproses AI secara asynchronous dengan retry dan fallback rekomendasi aman.

### C. Domain Web GIS
- `App\Services\GisKmlParserService`
  - Memvalidasi dan membaca KML/KMZ Google Earth, lalu menormalisasi Placemark Point/Polygon menjadi GeoJSON tanpa mengekstrak arsip ke filesystem.
- `App\Actions\Gis\ImportKmlLocationsAction`
  - Menyimpan hasil parsing secara atomik melalui chunked bulk insert menggunakan kategori default yang dipilih admin.

---

## 4. API Controllers & Webhooks

- `App\Http\Controllers\Api\V1\IotTelemetryController`
  - Endpoint `POST /api/v1/iot/telemetry`: Menangani pesan masuk dari sensor IoT tanah.
- `App\Http\Controllers\Api\V1\GisDataController`
  - Endpoint POI serta `GET /api/v1/gis/iot-devices` untuk frontend map.
- `App\Http\Controllers\Api\IotTelemetryController`
  - Endpoint `POST /api/v1/telemetry` dengan autentikasi `X-Device-Token` per perangkat.

---

## 5. Strongly Typed Enums (PHP 8.2+)

- `App\Enums\CommodityType`: `JAGUNG = 'jagung'`, `PISANG = 'pisang'`, `SINGKONG = 'singkong'`, `LAINNYA = 'lainnya'`
- `App\Enums\PoiCategory`: `PEMERINTAHAN = 'pemerintahan'`, `FASILITAS_UMUM = 'fasilitas_umum'`, `PENDIDIKAN = 'pendidikan'`, `PERTANIAN_IOT = 'pertanian_iot'`, `IBADAH = 'ibadah'`, `POSYANDU = 'posyandu'`
- `App\Enums\ResearchCategory`: `MONOGRAFI = 'monografi'`, `SAINTEK = 'saintek'`, `SOSHUM = 'soshum'`, `PETA = 'peta'`, `LAPORAN_KKN = 'laporan_kkn'`
- `App\Enums\LedgerType`: `INCOME = 'income'`, `EXPENSE = 'expense'`
- `App\Enums\RoleType`: `SUPER_ADMIN = 'super_admin'`, `ADMIN_DESA = 'admin_desa'`, `KELOMPOK_TANI = 'kelompok_tani'`, `UMKM = 'umkm'`, `WARGA = 'warga'`
- `App\Enums\AiConditionStatus`: `OPTIMAL`, `CAUTION`, `WARNING`, `CRITICAL`.