# API & Integration Blueprint - Website Desa Kalimati

## 1. Overview & Authentication Standard
Dokumen ini mendefinisikan spesifikasi API internal, webhook IoT, integrasi service LLM/RAG, dan public endpoint untuk Website Desa Kalimati.

### Authentication Types:
1. IoT Webhook Header Auth: Menggunakan header `X-IoT-Device-Token` untuk autentikasi perangkat sensor tanah.
2. LLM Service Auth: Menggunakan `Bearer Token` atau `X-API-Key` internal untuk komunikasi microservice Laravel <-> Python FastAPI/RAG.
3. Public API: Open access (dengan rate limiter) untuk rendering peta GIS dan direktori UMKM.

---

## 2. IoT Telemetry Ingestion API (Smart Agriculture)

### Endpoint: Push Sensor Telemetry Data
- Method: POST
- Path: /api/v1/iot/telemetry
- Headers:
  - Content-Type: application/json
  - X-IoT-Device-Token: {IOT_WEBHOOK_SECRET}

Request Body Schema:
{
  "device_id": "ESP32-SOIL-KAL-001",
  "grid_code": "KAL-DAMPIT-A12",
  "ph_level": 5.85,
  "moisture_percentage": 42.50,
  "temperature_celsius": 28.40,
  "recorded_at": "2026-08-01T10:30:00Z"
}

Successful Response (201 Created):
{
  "success": true,
  "message": "Telemetry log successfully stored.",
  "data": {
    "sensor_log_id": 1042,
    "land_grid_id": 12,
    "recorded_at": "2026-08-01T10:30:00Z"
  }
}

Validation Error Response (422 Unprocessable Entity):
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "ph_level": ["The ph level must be between 0 and 14."],
    "grid_code": ["The selected grid code is invalid."]
  }
}

---

## 3. LLM / RAG Soil Recommendation Service Integration

### Endpoint Per-Device Agricultural Monitoring
- Method: `POST`
- Path: `/api/v1/telemetry`
- Header: `X-Device-Token` (token unik dari `iot_devices`)
- Payload: `latitude`, `longitude`, `temp_air`, `hum_air`, `temp_soil`, `hum_soil_percent`, `raw_soil`, `lux_light`.
- Response: HTTP `200`; telemetry dan snapshot koordinat disimpan, posisi/`last_active_at` perangkat diperbarui, perangkat diasosiasikan ke grid aktif terdekat, dan `ProcessTelemetryAiReasoning` dijalankan synchronous sebelum respons dikirim.
- `data.recommendation_id` selalu tersedia. Jika provider AI gagal, endpoint menyimpan rekomendasi fallback berstatus `caution` agar ingestion tidak bergantung pada `queue:work`.
- Token invalid, kosong, atau perangkat nonaktif mengembalikan HTTP `401`.

Endpoint legacy `/api/v1/iot/telemetry` tetap dipertahankan untuk sensor grid pH lama.

Komunikasi outbound dari Laravel Service (FetchLLMRecommendationAction) ke Python FastAPI / Ollama RAG Engine.

### Endpoint Outbound: Request Soil Analysis & Action Plan
- Method: POST
- Target URL: {LLM_SERVICE_URL} (e.g., http://127.0.0.1:8001/api/v1/recommend)
- Headers:
  - Content-Type: application/json
  - X-API-Key: {LLM_SERVICE_API_KEY}

Outbound Request Body Payload:
{
  "land_grid_id": 12,
  "grid_code": "KAL-DAMPIT-A12",
  "dusun_name": "Dampit",
  "commodity_type": "pisang",
  "telemetry_metrics": {
    "ph_level": 5.85,
    "moisture_percentage": 42.50,
    "temperature_celsius": 28.40
  },
  "historical_treatments_count": 2
}

Expected Inbound Response Schema (200 OK):
{
  "success": true,
  "model_used": "Ollama-Llama3-RAG-Agri",
  "recommendation": {
    "soil_condition_summary": "Kondisi tanah cenderung agak asam (pH 5.85) dengan kelembapan sedang (42.5%). Cocok untuk komoditas pisang, namun membutuhkan penyesuaian pH sedikit.",
    "fertilizer_dosage": "Pupuk Organik / Kompos 5kg per lubang tanam. Tambahkan NPK 15-15-15 dosis 150 gram per rumpun.",
    "lime_treatment": "Aplikasi Kapur Pertanian (Dolomit) sekitar 200 gram per 10m² untuk menaikkan pH ke angka ideal 6.5.",
    "action_plan": "1. Lakukan pengapuran dolomit 1 minggu sebelum pemupukan.\n2. Jaga kelembapan tanah di kisaran 50-60%.\n3. Pantau drainsase agar air tidak menggenang di sekitar pangkal pisang."
  }
}

---

## 4. Public Web GIS Endpoints

### Endpoint IoT Devices
- Method: GET
- Path: `/api/v1/gis/iot-devices`
- Mengembalikan perangkat aktif, koordinat, radius, telemetry terbaru, dan rekomendasi AI terbaru.
- `api_token` dan `api_token_hash` tidak pernah diekspos.

### Endpoint 1: Get Points of Interest (POIs)
- Method: GET
- Path: /api/v1/gis/points-of-interest
- Query Parameters:
  - category (optional): pemerintahan, fasilitas_umum, pendidikan, pertanian_iot, ibadah, posyandu

Response (200 OK):
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Balai Desa Kalimati",
      "category": "pemerintahan",
      "latitude": -7.21450000,
      "longitude": 110.82340000,
      "description": "Pusat pelayanan administrasi Desa Kalimati",
      "icon_marker": "building-government",
      "geometry": {
        "type": "Point",
        "coordinates": [110.82340000, -7.21450000]
      }
    }
  ]
}

### Endpoint 2: Get Land Grids GeoJSON
- Method: GET
- Path: /api/v1/gis/land-grids
- Query Parameters:
  - dusun (optional): Kedungrandu, Brojo, Dampit
  - commodity (optional): jagung, pisang, singkong

Response (200 OK - Standard GeoJSON FeatureCollection):
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "geometry": {
        "type": "Polygon",
        "coordinates": [
          [
            [110.8231, -7.2141],
            [110.8232, -7.2141],
            [110.8232, -7.2142],
            [110.8231, -7.2142],
            [110.8231, -7.2141]
          ]
        ]
      },
      "properties": {
        "id": 12,
        "grid_code": "KAL-DAMPIT-A12",
        "dusun_name": "Dampit",
        "commodity_type": "pisang",
        "owner_name": "Pak Suwandi",
        "latest_ph": 5.85,
        "latest_moisture": 42.50,
        "status": "active"
      }
    }
  ]
}

---

## 5. Rate Limiting & Error Standard
- Public API Rate Limit: Maximum 60 requests / minute per IP.
- IoT Endpoint Rate Limit: Maximum 120 requests / minute per device payload.
- Standard Error Response Payload:
{
  "success": false,
  "message": "Detailed error message here.",
  "code": "ERROR_CODE_IDENTIFIER"
}