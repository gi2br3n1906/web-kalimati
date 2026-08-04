# Tech Blueprint - Website Desa Kalimati

## 1. Core Technology Stack

| Layer | Technology | Version | Purpose |
| :--- | :--- | :--- | :--- |
| **Backend Framework** | Laravel | `^11.0` | Core application framework, routing, ORM, & API |
| **Admin Panel** | Filament PHP | `^3.2` | Backoffice management dashboard & CRUD interfaces |
| **Reactivity Layer** | Livewire | `^3.0` | Server-driven dynamic UI components |
| **Frontend Styling** | Tailwind CSS | `^3.4` | Utility-first CSS framework |
| **Client Scripting** | Alpine.js | `^3.x` | Lightweight JavaScript reactivity for UI states |
| **Database Engine** | MySQL / PostgreSQL | `8.0+` / `15+` | Relational database storage |
| **Asset Bundler** | Vite | `^5.0` | Asset compilation and HMR |
| **Runtime / PHP** | PHP | `>=8.2` | Server runtime with strict typing enabled |

---

## 2. Required Composer Packages

### Primary Dependencies
- `filament/filament`: `^3.2` — Filament v3 Admin Panel framework.
- `bezhansalleh/filament-shield`: `^3.0` — Spatie Roles & Permissions integration for Filament.
- `spatie/laravel-permission`: `^6.0` — Core Role-Based Access Control (RBAC).
- `spatie/laravel-medialibrary`: `^11.0` — File uploads management (Research Hub PDFs, Logos, Images)[cite: 1].
- `guzzlehttp/guzzle`: `^7.8` — HTTP client for LLM/RAG REST API communications[cite: 1].

### Development & Tooling
- `laravel/pint`: `^1.13` — PHP code style fixer (PSR-12 enforcement).
- `barryvdh/laravel-debugbar`: `^3.9` — Performance and query debugging (Dev environment only).

---

## 3. Required Frontend & NPM Packages

- **Mapping & GIS**:
  - `leaflet`: `^1.9.4` — Open-source interactive map rendering.
  - `leaflet-draw`: `^1.0.4` — Vector drawing tools for 10x10m land grid polygons[cite: 1].
- **Data Visualization**:
  - `chart.js`: `^4.4.0` — Charting library for telemetry log trends (pH, soil moisture, temperature)[cite: 1].
- **Build Tools**:
  - `postcss`, `autoprefixer`, `tailwindcss` — Tailwind pipeline.

---

## 4. Environment Configuration (.env.example)

```ini
APP_NAME="Desa Kalimati"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE="Asia/Jakarta"
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=desa_kalimati
DB_USERNAME=root
DB_PASSWORD=

# Queue & Cache
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database

# GIS & Mapping Keys
LEAFLET_TILE_PROVIDER=https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
MAPBOX_API_KEY=

# Smart Agriculture & AI Engine (LLM / RAG)
LLM_SERVICE_URL=[http://127.0.0.1:8001/api/v1/recommend](http://127.0.0.1:8001/api/v1/recommend)
LLM_SERVICE_API_KEY=your_secret_llm_api_key
IOT_WEBHOOK_SECRET=your_iot_device_auth_token

# Mail Configuration (For Notifications/Admin Invites)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

---

## 5. Server Runtime & Extension Requirements

To ensure smooth operation, the host server (VPS) must have the following PHP extensions enabled:
- `pdo_mysql` or `pdo_pgsql`
- `mbstring`
- `dom` / `xml` / `ctype` / `iconv` (KML XML parsing and framework requirements)
- `zip` (KMZ archive import from Google Earth)
- `gd` or `imagick` (For image thumbnail generation and avatar resizing)
- `curl` (For HTTP API communications to LLM service)
- `fileinfo` (For MIME-type verification on PDF/Document uploads in Research Hub)[cite: 1]

---

## 6. Directory Structure & Architecture Conventions

```text
app/
├── Actions/                  # Single-action business logic classes
│   ├── Agriculture/          # ProcessSensorDataAction, FetchLLMRecommendationAction
│   └── Umkm/                 # RecordLedgerEntryAction
├── Enums/                    # Strong-typed PHP Enums (CommodityType, RoleType, etc.)
├── Filament/                 # Filament Admin Resources & Pages
│   └── Resources/            # LandGridResource, NewsResource, ResearchFileResource
├── Http/
│   ├── Controllers/          # Thin Controllers (Api & Web Public)
│   └── Requests/             # Form Validation Request Classes
├── Models/                   # Eloquent Models with explicit relationships & type casts
└── Services/                 # External service integrations (LLM/RAG Client, GIS Parser)
```