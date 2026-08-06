# Testing & Security Blueprint - Website Desa Kalimati

## 1. Testing Framework & Strategy

### Testing Stack
- Testing Framework: Pest PHP (v2 / v3) atau PHPUnit
- Mocks & Stubs: Laravel HTTP Facade Mocking (`Http::fake()`)
- Database Testing: RefreshDatabase Trait dengan SQLite In-Memory / MySQL Test Database

### Coverage Targets
- Core Service Actions (LLM Integration, Telemetry Parsing, Ledger Calculation): Minimal 85% Code Coverage
- API Endpoints (IoT Ingestion, GIS GeoJSON): Minimal 90% Code Coverage
- RBAC Rules (Filament Resource Access Controls): Minimal 100% Policy Coverage

---

## 2. Critical Test Scenarios & Test Cases

### Scenario A: IoT Telemetry Ingestion (/api/v1/iot/telemetry)
1. test_can_successfully_store_valid_sensor_telemetry:
   - Mengirim payload JSON valid dengan header `X-IoT-Device-Token` yang benar.
   - Assert HTTP Status `201 Created`.
   - Assert Database `sensor_logs` memiliki data sesuai payload.

2. test_rejects_telemetry_with_invalid_token:
   - Mengirim payload valid dengan token salah/kosong.
   - Assert HTTP Status `401 Unauthorized`.

3. test_validates_out_of_bounds_sensor_metrics:
   - Mengirim `ph_level` di luar jangkauan (misal: 16.0 atau -2.0).
   - Assert HTTP Status `422 Unprocessable Entity`.
   - Assert error key `ph_level` ada pada response validation.

4. Per-device monitoring (`/api/v1/telemetry`):
   - Validasi `X-Device-Token`, perangkat aktif, penyimpanan `iot_telemetries`, update `last_active_at`, dan dispatch queued AI job.
   - Verifikasi public IoT GIS payload tidak mengekspos token serta `/peta` dan `/pertanian` memuat konfigurasi layer IoT.

---

### Scenario B: LLM / RAG Service Integration & Fallback Handling
1. test_fetch_llm_recommendation_handles_api_success:
   - Mock HTTP endpoint `{LLM_SERVICE_URL}` mengembalikan JSON rekomendasi pupuk & dolomit.
   - Jalankan `FetchLLMRecommendationAction`.
   - Assert record `land_recommendations` tersimpan secara tepat.

2. test_fetch_llm_recommendation_handles_service_timeout_gracefully:
   - Mock HTTP endpoint mengembalikan HTTP `500 Server Error` atau `Timeout`.
   - Jalankan `FetchLLMRecommendationAction`.
   - Assert aplikasi tidak crash (`Exception` ditangkap dengan baik).
   - Assert record `land_recommendations` mencatat status fallback atau log error tanpa merusak data sensor.

---

### Scenario C: Role-Based Access Control (RBAC) & Filament Resources
1. test_super_admin_can_access_all_resources:
   - Login sebagai user dengan role `super_admin`.
   - Assert bisa mengakses halaman index & create untuk `LandGridResource`, `ResearchFileResource`, dan `UmkmBusinessResource`.

2. test_kelompok_tani_cannot_access_umkm_ledger:
   - Login sebagai user dengan role `kelompok_tani`.
   - Coba akses URL `UmkmLedgerResource`.
   - Assert HTTP Status `403 Forbidden`.

3. test_public_user_can_only_view_published_news:
   - Buat 1 berita `is_published = false` dan 1 berita `is_published = true`.
   - Buka halaman public news index.
   - Assert hanya berita `is_published = true` yang tampil.

---

### Scenario D: UMKM Bookkeeping & Ledger Accuracy
1. test_calculates_correct_ledger_total_cash_flow:
   - Tambahkan 3 transaksi Income (total Rp 500.000) dan 2 transaksi Expense (total Rp 200.000).
   - Jalankan `CalculateLedgerSummaryAction`.
   - Assert Net Balance sama dengan Rp 300.000.

---

## 3. Security Hardening Guidelines

### A. Input Sanitization & File Upload Safety
- Research Hub Uploads:
  - Wajib memeriksa ekstensi file dan MIME Type secara eksplisit (`application/pdf`, `application/vnd.openxmlformats-officedocument.wordprocessingml.document`).
  - Maksimal ukuran file: 20 MB per dokumen.
  - Nama file yang disimpan harus disanitasi menggunakan UUID / Hash unik untuk mencegah Path Traversal Attack.

### B. Cross-Site Scripting (XSS) & CSRF Protection
- Semua form Livewire dan Filament wajib menyertakan CSRF Token bawaan Laravel.
- Sanitasi input Rich Text Editor pada Berita Desa untuk membuang tag HTML berbahaya (`<script>`, `<iframe>`, `javascript:` URI) menggunakan HTMLPurifier.

### C. Rate Limiting Policy
- Endpoint `/api/v1/iot/telemetry`: Limit `120` request / minute per IP/Device.
- Public GIS Endpoint `/api/v1/gis/*`: Limit `60` request / minute per IP.
- Login Endpoint `/admin/login`: Throttle maksimal 5 percobaan gagal per menit.