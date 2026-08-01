# AI Agent Guidelines - Website Desa Kalimati

## 1. Context & Purpose
You are assisting in developing the official web application for **Desa Kalimati** (KKN Undip 2026)[cite: 1].
This application serves as a multi-functional platform:
- **Public Information & Web GIS**: Village profile, history, news, and interactive GIS mapping[cite: 1].
- **Smart Agriculture System**: 10x10m Land Grid visualization, IoT soil sensor log integration, and automated LLM/RAG fertilizer & soil treatment recommendations[cite: 1].
- **KKN Research Hub**: Institutional memory and archive for ongoing/future KKN research data[cite: 1].
- **UMKM Directory & Accounting**: Local business showcase and simple financial ledger/bookkeeping module[cite: 1].

---

## 2. Core Tech Stack
- **Framework**: Laravel 11 (PHP 8.2+)
- **Admin Panel & Backoffice**: Filament v3
- **Frontend / Components**: Livewire v3, Alpine.js, Tailwind CSS
- **Database**: MySQL 8.0+ / PostgreSQL (with Spatial support if applicable)
- **GIS / Mapping**: Leaflet.js / Mapbox API / Google Maps API[cite: 1]
- **AI / LLM Service**: Python FastAPI / OpenAI API / Ollama (RAG Engine via REST Webhook)[cite: 1]

---

## 3. Strict Coding Standards & Conventions

### A. General PHP & Laravel Rules
- Always use `declare(strict_types=1);` at the top of PHP files.
- Adhere strictly to **PSR-12** and **Laravel Pint** formatting.
- Prefer **Single Responsibility Principle (SRP)**: Keep Controllers thin, use **Actions** or **Services** for complex business logic (e.g., calculating fertilizer recommendations or parsing sensor payloads).
- Use Type Hinting for all methods, functions, parameters, and return types.

### B. Filament v3 Rules
- Use Filament Resources for managing entities (`LandGrid`, `SensorLog`, `UmkmProduct`, `ResearchFile`, `NewsArticle`).
- Keep Form and Table schema code clean and modularized.
- Utilize Filament Spatie Roles & Permissions (`FilamentShield`) for RBAC (Super Admin, Desa Admin, Kelompok Tani, Warga).

### C. Database & Migrations
- Standard foreign key constraints and cascade rules must be explicitly declared in migrations.
- Index frequently queried columns, especially timestamps on `sensor_logs` and coordinate/grid keys on `land_grids`.
- Use Eloquent Model Casts for JSON attributes (e.g., sensor payload data, AI recommendation outputs).

---

## 4. Key Domain Rules & Logic

1. **Smart Agriculture & IoT Grid**:
   - `land_grids` table represents a 10x10m physical plot[cite: 1].
   - Sensor data payloads (pH, soil moisture, temperature) must be validated before being logged into `sensor_logs`[cite: 1].
   - RAG/LLM invocation must handle fallbacks gracefully if the AI service is unreachable or rate-limited[cite: 1].

2. **Research Hub**:
   - Files uploaded must strictly validate MIME types (PDF, XLSX, DOCX) with appropriate file size limits[cite: 1].

3. **UMKM Accounting**:
   - Bookkeeping entries must maintain data integrity for debit/credit operations[cite: 1].

---

## 5. Agent Instructions during Generation
- Do NOT generate speculative code or unrequested placeholders.
- Always check existing migrations, models, and Filament resources before suggesting new files.
- Provide clean, readable, and well-commented code only where logic is non-obvious.