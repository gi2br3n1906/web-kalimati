import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const categoryColors = {
    pemerintahan: '#155e75',
    umkm_ekonomi: '#7c3aed',
    ibadah: '#a16207',
    pendidikan: '#1d4ed8',
    infrastruktur_transportasi: '#475569',
    pertanian_iot: '#15803d',
    fasilitas_umum: '#155e75',
    posyandu: '#be123c',
};

const markerSymbols = {
    'building-government': 'G',
    storefront: 'U',
    'place-of-worship': 'I',
    'education-health': 'D',
    transport: 'J',
    'agriculture-environment': 'T',
    landmark: 'F',
    school: 'S',
    'agriculture-iot': 'T',
    'health-center': 'P',
};

const categoryLabels = {
    pemerintahan: 'Fasilitas Umum & Pemerintahan',
    umkm_ekonomi: 'UMKM & Ekonomi',
    ibadah: 'Tempat Ibadah',
    pendidikan: 'Pendidikan & Kesehatan',
    infrastruktur_transportasi: 'Infrastruktur & Transportasi',
    pertanian_iot: 'Pertanian & Lingkungan',
    fasilitas_umum: 'Fasilitas Umum & Pemerintahan',
    posyandu: 'Pendidikan & Kesehatan',
};

const escapeHtml = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const conditionColors = {
    optimal: '#16a34a',
    caution: '#eab308',
    warning: '#dc2626',
};

const conditionLabels = {
    optimal: 'Optimal',
    caution: 'Waspada',
    warning: 'Peringatan',
};

const makeTelemetryPopup = (point) => {
    const sensorData = point.sensor_data;
    const recommendation = point.recommendation;
    const status = point.condition_status ?? 'caution';
    const metrics = sensorData ? `
        <dl class="gis-iot-metrics">
            <div><dt>Suhu udara</dt><dd>${escapeHtml(sensorData.temp_air)} °C</dd></div>
            <div><dt>Kelembapan udara</dt><dd>${escapeHtml(sensorData.hum_air)}%</dd></div>
            <div><dt>Suhu tanah</dt><dd>${escapeHtml(sensorData.temp_soil)} °C</dd></div>
            <div><dt>Kelembapan tanah</dt><dd>${escapeHtml(sensorData.hum_soil_percent)}%</dd></div>
            <div><dt>Intensitas cahaya</dt><dd>${escapeHtml(sensorData.lux_light)} lux</dd></div>
        </dl>` : '<p>Data sensor tidak tersedia.</p>';

    return `<article class="gis-popup gis-iot-popup">
        <span style="background:${conditionColors[status]};color:white;padding:.2rem .5rem;border-radius:999px">${escapeHtml(conditionLabels[status])}</span>
        <h2>Titik Pengukuran Lapangan #${escapeHtml(point.id)}</h2>
        <p>${escapeHtml(point.measured_at)}</p>
        <p>${escapeHtml(point.device?.name ?? 'Perangkat tidak diketahui')} · ${escapeHtml(point.device?.device_code ?? '-')}</p>
        ${metrics}
        <h3>${escapeHtml(recommendation?.action_title ?? 'Menunggu analisis AI')}</h3>
        <p>${escapeHtml(recommendation?.recommendation_text ?? 'Rekomendasi Gemini AI akan tersedia setelah telemetri diproses.')}</p>
    </article>`;
};

const addTelemetryPoints = (layer, points, bounds) => {
    points.forEach((point) => {
        const coordinates = [point.latitude, point.longitude];
        const status = point.condition_status ?? 'caution';
        const color = conditionColors[status] ?? conditionColors.caution;
        const marker = L.circleMarker(coordinates, {
            radius: 9,
            color,
            fillColor: color,
            fillOpacity: 0.72,
            opacity: 1,
            weight: 3,
        }).bindPopup(makeTelemetryPopup(point));

        marker.addTo(layer);
        bounds.extend(coordinates);
    });
};

const makeMarkerIcon = (point) => {
    const color = categoryColors[point.category] ?? '#166534';
    const marker = point.icon_marker ?? '';
    const symbol = markerSymbols[marker] ?? 'L';

    return L.divIcon({
        className: 'gis-marker-wrap',
        html: `<span class="gis-marker" style="--marker-color: ${color}"><span>${symbol}</span></span>`,
        iconSize: [36, 44],
        iconAnchor: [18, 42],
        popupAnchor: [0, -38],
    });
};

const makePopup = (point) => {
    const name = escapeHtml(point.name);
    const category = escapeHtml(categoryLabels[point.category] ?? point.category);
    const description = point.description
        ? `<p>${escapeHtml(point.description)}</p>`
        : '';

    return `
        <article class="gis-popup">
            <span>${category}</span>
            <h2>${name}</h2>
            ${description}
        </article>
    `;
};

const initializeMap = (root) => {
    if (root.dataset.initialized === 'true') {
        return;
    }

    root.dataset.initialized = 'true';

    const configuration = JSON.parse(root.dataset.configuration);
    const canvas = root.querySelector('[data-map-canvas]');
    const status = root.querySelector('[data-map-status]');
    const count = root.querySelector('[data-map-count]');
    const filters = [...root.querySelectorAll('[data-category]')];
    const map = L.map(canvas, {
        zoomControl: false,
    }).setView(configuration.center, configuration.zoom);
    const featureLayer = L.layerGroup().addTo(map);
    const iotLayer = L.layerGroup().addTo(map);
    let featurePoints = [];
    let telemetryPoints = [];

    L.control.zoom({ position: 'bottomright' }).addTo(map);
    L.tileLayer(configuration.tileProvider, {
        attribution: configuration.tileAttribution,
        maxZoom: 19,
    }).addTo(map);

    const setLoading = () => {
        status.hidden = false;
        status.classList.remove('is-error');
        status.textContent = 'Memuat titik lokasi...';
        count.hidden = true;
    };

    const fitCombinedBounds = () => {
        const bounds = L.latLngBounds();

        featurePoints.forEach((point) => {
            const geometry = point.geometry ?? {
                type: 'Point',
                coordinates: [point.longitude, point.latitude],
            };

            if (geometry.type === 'Point') {
                bounds.extend([geometry.coordinates[1], geometry.coordinates[0]]);
            } else {
                bounds.extend(L.geoJSON(geometry).getBounds());
            }
        });
        telemetryPoints.forEach((point) => bounds.extend([point.latitude, point.longitude]));

        if (bounds.isValid()) {
            map.fitBounds(bounds, { padding: [56, 56], maxZoom: 16 });
            status.hidden = true;
        } else {
            map.setView(configuration.center, configuration.zoom);
            status.hidden = false;
            status.textContent = 'Belum ada titik lokasi atau pengukuran pada kategori ini.';
        }
    };

    const renderFeatures = (points) => {
        featureLayer.clearLayers();
        featurePoints = points;

        points.forEach((point) => {
            const geometry = point.geometry ?? {
                type: 'Point',
                coordinates: [point.longitude, point.latitude],
            };

            if (geometry.type === 'Polygon') {
                const color = categoryColors[point.category] ?? '#166534';
                const polygon = L.geoJSON(geometry, {
                    style: {
                        color,
                        fillColor: color,
                        fillOpacity: 0.24,
                        weight: 3,
                    },
                });

                polygon.bindPopup(makePopup(point));
                polygon.addTo(featureLayer);

                return;
            }

            const coordinates = [geometry.coordinates[1], geometry.coordinates[0]];
            const marker = L.marker(coordinates, {
                icon: makeMarkerIcon(point),
                title: point.name,
            });

            marker.bindPopup(makePopup(point));
            marker.addTo(featureLayer);
        });

        fitCombinedBounds();
        count.textContent = `${points.length} fitur lokasi · ${telemetryPoints.length} titik pengukuran`;
        count.hidden = false;
    };

    const loadTelemetryPoints = async () => {
        if (!configuration.telemetryEndpoint) return;

        const response = await fetch(configuration.telemetryEndpoint, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error(`Telemetry GIS request failed with status ${response.status}`);
        const payload = await response.json();
        const bounds = L.latLngBounds();
        iotLayer.clearLayers();
        telemetryPoints = payload.data;
        addTelemetryPoints(iotLayer, telemetryPoints, bounds);
        fitCombinedBounds();
        count.textContent = `${featurePoints.length} fitur lokasi · ${telemetryPoints.length} titik pengukuran`;
        count.hidden = false;
    };

    const loadPoints = async (category = '') => {
        setLoading();

        const url = new URL(configuration.endpoint, window.location.origin);

        if (category) {
            url.searchParams.set('category', category);
        }

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error(`GIS request failed with status ${response.status}`);
            }

            const payload = await response.json();

            renderFeatures(payload.data);
        } catch (error) {
            featureLayer.clearLayers();
            status.hidden = false;
            status.classList.add('is-error');
            status.textContent = 'Data peta tidak dapat dimuat.';
            count.hidden = true;
            console.error(error);
        }
    };

    filters.forEach((filter) => {
        filter.addEventListener('click', () => {
            filters.forEach((item) => item.classList.remove('is-active'));
            filter.classList.add('is-active');
            loadPoints(filter.dataset.category);
        });
    });

    Promise.all([loadPoints(), loadTelemetryPoints()]).catch(console.error);

    window.setTimeout(() => map.invalidateSize(), 0);
};

const initializeMaps = () => {
    document.querySelectorAll('[data-gis-map]').forEach(initializeMap);
};

document.addEventListener('DOMContentLoaded', initializeMaps);
document.addEventListener('livewire:navigated', initializeMaps);

const initializeIotMap = (root) => {
    if (root.dataset.initialized === 'true') return;
    root.dataset.initialized = 'true';
    const configuration = JSON.parse(root.dataset.configuration);
    const canvas = root.querySelector('[data-map-canvas]');
    const status = root.querySelector('[data-map-status]');
    const map = L.map(canvas).setView(configuration.center, configuration.zoom);
    const layer = L.layerGroup().addTo(map);
    L.tileLayer(configuration.tileProvider, { attribution: configuration.tileAttribution, maxZoom: 19 }).addTo(map);

    fetch(configuration.telemetryEndpoint, { headers: { Accept: 'application/json' } })
        .then((response) => {
            if (!response.ok) throw new Error(`IoT GIS request failed with status ${response.status}`);
            return response.json();
        })
        .then((payload) => {
            const bounds = L.latLngBounds();
            addTelemetryPoints(layer, payload.data, bounds);
            status.textContent = `${payload.data.length} titik pengukuran telemetri`;
            if (bounds.isValid()) map.fitBounds(bounds, { padding: [40, 40], maxZoom: 16 });
        })
        .catch((error) => {
            status.textContent = 'Data titik pengukuran tidak dapat dimuat.';
            console.error(error);
        });
};

const initializeIotMaps = () => document.querySelectorAll('[data-iot-map]').forEach(initializeIotMap);
document.addEventListener('DOMContentLoaded', initializeIotMaps);
document.addEventListener('livewire:navigated', initializeIotMaps);