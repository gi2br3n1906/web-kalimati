# Domain Blueprint - Website Desa Kalimati

## 1. Domain Overview & Objectives
Application Domain: Official Village Web Platform & Smart Agriculture System for **Desa Kalimati, Kecamatan Juwangi, Kabupaten Boyolali** (KKN Undip 2026)[cite: 1].

Primary Objectives:
1. Modernize village governance and digital public information[cite: 1].
2. Provide interactive Web GIS mapping for administrative boundaries, public amenities, and agricultural assets[cite: 1].
3. Implement Smart Agriculture via 10x10m Land Grid spatial tracking, IoT soil sensor telemetry, and automated LLM/RAG soil treatment recommendations[cite: 1].
4. Establish a KKN Research Hub for continuous village research archive and monograph storage[cite: 1].
5. Empathetically support local UMKM through a business showcase directory and simple financial bookkeeping modules[cite: 1].

---

## 2. User Roles & RBAC (Role-Based Access Control)

| Role | Access Level | Description & Scope |
| :--- | :--- | :--- |
| **Super Admin** | Full System | System developers and technical leads. Full access to DB, logs, and system config. |
| **Admin Desa** | Backoffice Admin | Perangkat Desa (e.g., Sekretaris Desa). Manages news, public info, village profile, and Research Hub[cite: 1]. |
| **Kelompok Tani** | Agriculture Operator | Farmers / Agricultural Heads. Manages land grid records, views sensor metrics, and triggers LLM recommendations[cite: 1]. |
| **Pelaku UMKM** | Business Owner | Local MSME owners. Manages business profile, product showcase, and simple financial ledgers[cite: 1]. |
| **Public / Warga** | Guest / Frontend | General public & villagers. Views news, village profile, Web GIS, UMKM catalog, and public research archives[cite: 1]. |

---

## 3. Core Modules & Feature Breakdown

### Module 1: Core Village Information & Public Portal
- **Village Profile & History**: Static/dynamic presentation of Desa Kalimati history, origins, vision-mission, and administrative organizational chart[cite: 1].
- **News & Announcements**: Articles, community event schedules, and official village notices.
- **Media Gallery**: High-resolution showcase of village culture, activities, and natural landscape[cite: 1].

### Module 2: Web GIS & Interactive Spatial Mapping
- **Interactive Map Engine**: Mapbox/Leaflet-based interactive map of Desa Kalimati[cite: 1].
- **Boundary & Facility Layers**: Toggleable layers for RT/RW boundaries, public health centers (posyandu), places of worship, village hall, and schools[cite: 1].
- **Agricultural Land Layer**: Spatial representation of agricultural zones (corn, banana, cassava commodities)[cite: 1].
- **Geotagging & POI**: Point of Interest markers with detail modals (location, photos, description)[cite: 1].

### Module 3: Smart Agriculture & AI Soil Recommendation Engine
- **10x10m Land Grid Management**: Spatial division of farming areas into standard 10x10m grid cells[cite: 1].
- **IoT Telemetry Teleprocessing**: Real-time/batch log ingestion from soil sensors measuring:
  - Soil pH[cite: 1]
  - Soil Moisture (%)[cite: 1]
  - Soil Temperature (°C)[cite: 1]
  - Air temperature/humidity, raw soil reading, and light intensity for spatial devices.
- **Automated LLM/RAG Recommendation**:
  - Processing sensor data against localized agricultural knowledge bases[cite: 1].
  - Automated calculation and reasoning for dosage of fertilizer, lime treatment, or crop rotation tips[cite: 1].
  - Historical recommendation logs per land grid cell.
- **Crop & Commodity Knowledge Hub**: Profiling main commodities (corn, banana, cassava), planting calendars, and dryland cultivation guides[cite: 1].

### Module 4: KKN Research Hub
- **Continuous Research Archive**: Central repository for monograph data and research documentation generated across KKN cohorts[cite: 1].
- **Document Manager**: Public/Restricted PDF viewer and metadata tagger (category, year, author, keywords)[cite: 1].

### Module 5: UMKM Showcase & Simple Financial Bookkeeping
- **UMKM Directory**: Online showcase for local micro-enterprises (processed banana snacks, food stalls, grocery stores) with branding support (names, logos, contact info)[cite: 1].
- **Simple Financial Ledger**: Digital cash flow bookkeeping tool (income/expense recording) tailored for non-accountant micro-business owners to separate personal and business funds[cite: 1].

---

## 4. Key Workflows & Data Pipelines

### A. Smart Agriculture AI Recommendation Pipeline
[ IoT Sensor Device ]
│ (pings soil data)
▼
[ API Webhook / Ingestion Endpoint ] ──► [ Store to sensor_logs ]
│
▼
[ Trigger RAG Engine / LLM API ] ◄──► [ Vector DB / Agricultural Knowledge ]
│
▼ (returns reasoning & action plan)
[ Save to land_recommendations ] ──► [ Render on Grid Map UI & Filament Admin ]

Pipeline perangkat spasial: `IotDevice` -> `POST /api/v1/telemetry` -> `IotTelemetry` -> queued `ProcessTelemetryAiReasoning` -> `AiRecommendation` -> Leaflet `/peta` dan `/pertanian`.


### B. Research & Monograph Publishing Pipeline
[ KKN Student / Admin ] ──► [ Upload Document via Filament ]
│
▼
[ File Validation & Storage ] ──► [ Metadata Indexing ] ──► [ Publish to Research Hub ]