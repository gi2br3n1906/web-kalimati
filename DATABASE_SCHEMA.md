# Database Schema Blueprint - Website Desa Kalimati

## 1. Schema Overview
Database System: MySQL 8.0+ / PostgreSQL
Primary Key Strategy: `unsignedBigInteger` (Auto-incrementing) or UUID for public entities.
Timestamp Strategy: Standard Laravel `created_at` and `updated_at` (TIMESTAMP).

---

## 2. Entity Relationship Diagram & Table Definitions

### A. Authentication & RBAC (Spatie Shield Integration)

#### `users`
| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK, Auto Increment |
| `name` | VARCHAR(255) | User's full name |
| `email` | VARCHAR(255) | Unique, Indexed |
| `password` | VARCHAR(255) | Hashed |
| `phone_number` | VARCHAR(20) | Nullable |
| `role_type` | ENUM | `'super_admin'`, `'admin_desa'`, `'kelompok_tani'`, `'umkm'`, `'warga'` |
| `email_verified_at` | TIMESTAMP | Nullable |
| `remember_token` | VARCHAR(100) | Nullable |
| `timestamps` | TIMESTAMP | Created & Updated at |

---

### B. Core Village Information & News Portal

#### `news_articles`
| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK |
| `author_id` | BIGINT UNSIGNED | FK -> `users.id` |
| `title` | VARCHAR(255) | Article headline |
| `slug` | VARCHAR(255) | Unique slug for SEO |
| `category` | ENUM | `'kegiatan'`, `'pengumuman'`, `'potensi_desa'`, `'kesehatan'` |
| `content` | LONGTEXT | Rich text body |
| `thumbnail_path` | VARCHAR(255) | Nullable image storage path |
| `is_published` | BOOLEAN | Default: `false` |
| `published_at` | TIMESTAMP | Nullable |
| `timestamps` | TIMESTAMP | |

---

### C. Smart Agriculture & AI Soil Recommendation Engine

#### `land_grids`
Represents a physical 10x10m plot within Desa Kalimati agricultural zones[cite: 1].

| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK |
| `grid_code` | VARCHAR(50) | Unique Identifier (e.g., `KAL-DAMPIT-A12`) |
| `dusun_name` | VARCHAR(100) | e.g., `'Kedungrandu'`, `'Brojo'`, `'Dampit'`[cite: 1] |
| `commodity_type` | ENUM | `'jagung'`, `'pisang'`, `'singkong'`, `'lainnya'`[cite: 1] |
| `latitude` | DECIMAL(10,8) | Center point latitude |
| `longitude` | DECIMAL(11,8) | Center point longitude |
| `geojson_polygon` | JSON | Nullable boundary polygon coordinates |
| `owner_name` | VARCHAR(255) | Nullable farmer name |
| `status` | ENUM | `'active'`, `'fallow'`, `'harvested'` |
| `timestamps` | TIMESTAMP | |

#### `sensor_logs`
High-frequency telemetry logs from soil sensors[cite: 1].

| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK |
| `land_grid_id` | BIGINT UNSIGNED | FK -> `land_grids.id` (ON DELETE CASCADE) |
| `device_id` | VARCHAR(100) | Hardware serial/MAC ID |
| `ph_level` | DECIMAL(4,2) | Soil pH value (0.00 - 14.00)[cite: 1] |
| `moisture_percentage` | DECIMAL(5,2) | Moisture content (0.00 - 100.00%)[cite: 1] |
| `temperature_celsius` | DECIMAL(4,2) | Soil temperature[cite: 1] |
| `raw_payload` | JSON | Full raw payload from IoT device |
| `recorded_at` | TIMESTAMP | Sensor reading timestamp (Indexed) |
| `timestamps` | TIMESTAMP | |

#### `land_recommendations`
LLM/RAG generated reasoning and treatment recommendations[cite: 1].

| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK |
| `land_grid_id` | BIGINT UNSIGNED | FK -> `land_grids.id` (ON DELETE CASCADE) |
| `sensor_log_id` | BIGINT UNSIGNED | FK -> `sensor_logs.id` (ON DELETE SET NULL), Nullable |
| `ai_model_used` | VARCHAR(100) | e.g., `'GPT-4o'`, `'Ollama-Llama3'`, `'RAG-v1'` |
| `soil_condition_summary`| TEXT | Diagnostic overview |
| `fertilizer_dosage` | TEXT | Recommended fertilizer type & dosage |
| `lime_treatment` | TEXT | Recommended lime (kapur) application |
| `action_plan` | LONGTEXT | Complete step-by-step guidance |
| `is_applied` | BOOLEAN | Status if farmer implemented recommendation |
| `timestamps` | TIMESTAMP | |

#### `iot_devices`
Perangkat monitoring pertanian spasial dengan kredensial unik per perangkat.

| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK |
| `device_code` | VARCHAR(100) | Unique hardware identifier |
| `name` | VARCHAR(255) | Nama perangkat |
| `api_token` | VARCHAR(512) | Unique ciphertext, encrypted at rest |
| `api_token_hash` | CHAR(64) | Unique SHA-256 lookup hash; internal only |
| `latitude` / `longitude` | DECIMAL(10,8) / DECIMAL(11,8) | Titik perangkat |
| `coverage_radius_meters` | INT UNSIGNED | Default 100 meter |
| `crop_type` | VARCHAR(100) | Default `Jagung` |
| `is_active` | BOOLEAN | Default true |
| `last_active_at` | TIMESTAMP | Nullable, indexed |

#### `iot_telemetries`
| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `iot_device_id` | BIGINT UNSIGNED | FK, cascade delete |
| `temp_air`, `hum_air` | FLOAT | Suhu dan kelembapan udara |
| `temp_soil`, `hum_soil_percent` | FLOAT | Suhu dan kelembapan tanah |
| `raw_soil` | INT | Pembacaan analog mentah |
| `lux_light` | FLOAT | Intensitas cahaya (lux) |
| `timestamps` | TIMESTAMP | Indexed bersama device ID |

#### `ai_recommendations`
| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `iot_device_id` | BIGINT UNSIGNED | FK, cascade delete |
| `iot_telemetry_id` | BIGINT UNSIGNED | FK, cascade delete |
| `condition_status` | ENUM | `optimal`, `caution`, `warning`, `critical` |
| `action_title` | VARCHAR(255) | Judul tindakan |
| `recommendation_text` | TEXT | Narasi Gemini/fallback |
| `timestamps` | TIMESTAMP | Indexed per device/status |

---

### D. Web GIS & Spatial Layers

#### `gis_points_of_interest`
| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK |
| `name` | VARCHAR(255) | Feature name (e.g., `'Balai Desa Kalimati'`, `'SDN 02 Kalimati'`)[cite: 1] |
| `category` | VARCHAR(50) | Application enum: `'pemerintahan'`, `'fasilitas_umum'`, `'pendidikan'`, `'pertanian_iot'`, `'ibadah'`, `'posyandu'`[cite: 1] |
| `latitude` | DECIMAL(10,8) | |
| `longitude` | DECIMAL(11,8) | |
| `description` | TEXT | Nullable detail text |
| `icon_marker` | VARCHAR(100) | Nullable icon identifier for map rendering |
| `geojson_geometry` | JSON | Nullable GeoJSON Point/Polygon imported from KML/KMZ; latitude/longitude store the point or polygon centroid |
| `timestamps` | TIMESTAMP | |

---

### E. KKN Research Hub

#### `research_files`
Archive repository for monographs and KKN research outputs[cite: 1].

| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK |
| `uploader_id` | BIGINT UNSIGNED | FK -> `users.id` |
| `title` | VARCHAR(255) | Research or monograph title[cite: 1] |
| `kkn_cohort` | VARCHAR(50) | e.g., `'Tim II 2026'`, `'Tim I 2025'`[cite: 1] |
| `category` | ENUM | `'monografi'`, `'saintek'`, `'soshum'`, `'peta'`, `'laporan_kkn'`[cite: 1] |
| `author_names` | TEXT | Comma-separated author list |
| `file_path` | VARCHAR(255) | Storage path (PDF/DOCX) |
| `file_size_kb` | INT UNSIGNED | Size in Kilobytes |
| `abstract` | TEXT | Research summary |
| `is_public` | BOOLEAN | Default: `true` |
| `timestamps` | TIMESTAMP | |

---

### F. UMKM Directory & Financial Bookkeeping

#### `umkm_businesses`
Directory of local MSME vendors[cite: 1].

| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK |
| `owner_id` | BIGINT UNSIGNED | FK -> `users.id` (Nullable for non-registered users) |
| `business_name` | VARCHAR(255) | e.g., `'Toko Kelontong Abadi'`, `'Olahan Pisang Kalimati'`[cite: 1] |
| `category` | ENUM | `'kuliner'`, `'kelontong'`, `'pertanian'`, `'jasa'`, `'kerajinan'` |
| `description` | TEXT | Nullable business overview |
| `phone_number` | VARCHAR(20) | WhatsApp contact |
| `logo_path` | VARCHAR(255) | Nullable image path |
| `address` | TEXT | Physical location in Desa Kalimati |
| `timestamps` | TIMESTAMP | |

#### `umkm_ledgers`
Simple financial cash-flow entries for business management[cite: 1].

| Column | Type | Constraints / Details |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | PK |
| `umkm_business_id` | BIGINT UNSIGNED | FK -> `umkm_businesses.id` (ON DELETE CASCADE) |
| `transaction_date` | DATE | Date of record |
| `type` | ENUM | `'income'` (Pemasukan), `'expense'` (Pengeluaran)[cite: 1] |
| `amount` | DECIMAL(12,2) | Monetary amount (IDR) |
| `category` | VARCHAR(100) | e.g., `'Bahan Baku'`, `'Penjualan Harian'`, `'Operasional'` |
| `notes` | TEXT | Nullable entry detail |
| `timestamps` | TIMESTAMP | |

---

## 3. Recommended Database Indexes

```sql
-- High Frequency Telemetry Logs Optimization
CREATE INDEX idx_sensor_logs_land_grid_id ON sensor_logs(land_grid_id);
CREATE INDEX idx_sensor_logs_recorded_at ON sensor_logs(recorded_at);

-- Spatial & Map Query Optimization
CREATE INDEX idx_land_grids_coordinates ON land_grids(latitude, longitude);
CREATE INDEX idx_gis_poi_coordinates ON gis_points_of_interest(latitude, longitude);

-- News & Research Hub Search Indexes
CREATE INDEX idx_news_published_slug ON news_articles(is_published, slug);
CREATE INDEX idx_research_files_category ON research_files(category, kkn_cohort);