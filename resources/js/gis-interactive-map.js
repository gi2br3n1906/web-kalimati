import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const categoryColors = {
    pemerintahan: '#155e75',
    fasilitas_umum: '#7c3aed',
    pendidikan: '#1d4ed8',
    pertanian_iot: '#15803d',
    ibadah: '#a16207',
    posyandu: '#be123c',
};

const markerSymbols = {
    'building-government': 'G',
    landmark: 'F',
    school: 'S',
    'agriculture-iot': 'T',
    'place-of-worship': 'I',
    'health-center': 'P',
};

const categoryLabels = {
    pemerintahan: 'Pemerintahan',
    fasilitas_umum: 'Fasilitas Umum',
    pendidikan: 'Pendidikan',
    pertanian_iot: 'Pertanian / IoT',
    ibadah: 'Tempat Ibadah',
    posyandu: 'Posyandu',
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
    critical: '#991b1b',
};

const conditionLabels = {
    optimal: 'Optimal',
    caution: 'Waspada',
    warning: 'Peringatan',
    critical: 'Kritis',
};

const makeIotPopup = (device) => {
    const telemetry = device.telemetry;
    const recommendation = device.recommendation;
    const status = recommendation?.condition_status ?? 'caution';
    const metrics = telemetry ? `
        <dl class="gis-iot-metrics">
            <div><dt>Suhu udara</dt><dd>${escapeHtml(telemetry.temp_air)} °C</dd></div>
            <div><dt>Kelembapan udara</dt><dd>${escapeHtml(telemetry.hum_air)}%</dd></div>
            <div><dt>Suhu tanah</dt><dd>${escapeHtml(telemetry.temp_soil)} °C</dd></div>
            <div><dt>Kelembapan tanah</dt><dd>${escapeHtml(telemetry.hum_soil_percent)}%</dd></div>
            <div><dt>Cahaya</dt><dd>${escapeHtml(telemetry.lux_light)} lux</dd></div>
        </dl>` : '<p>Belum ada telemetry.</p>';

    return `<article class="gis-popup gis-iot-popup">
        <span style="background:${conditionColors[status]};color:white;padding:.2rem .5rem;border-radius:999px">${escapeHtml(conditionLabels[status])}</span>
        <h2>${escapeHtml(device.name)}</h2>
        <p>${escapeHtml(device.device_code)} · ${escapeHtml(device.crop_type)}</p>
        ${metrics}
        <h3>${escapeHtml(recommendation?.action_title ?? 'Menunggu analisis AI')}</h3>
        <p>${escapeHtml(recommendation?.recommendation_text ?? 'Rekomendasi akan tersedia setelah telemetry diproses.')}</p>
    </article>`;
};

const addIotDevices = (map, layer, devices, bounds) => {
    devices.forEach((device) => {
        const coordinates = [device.latitude, device.longitude];
        const status = device.recommendation?.condition_status ?? 'caution';
        const color = conditionColors[status] ?? conditionColors.caution;
        const popup = makeIotPopup(device);
        const marker = L.marker(coordinates, {
            icon: L.divIcon({
                className: 'gis-marker-wrap',
                html: `<span class="gis-marker" style="--marker-color:${color}"><span>IoT</span></span>`,
                iconSize: [40, 44],
                iconAnchor: [20, 42],
            }),
            title: device.name,
        }).bindPopup(popup);
        const circle = L.circle(coordinates, {
            radius: device.coverage_radius_meters,
            color,
            fillColor: color,
            fillOpacity: 0.18,
            weight: 2,
        }).bindPopup(popup);

        marker.addTo(layer);
        circle.addTo(layer);
        bounds.extend(circle.getBounds());
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

    const renderFeatures = (points) => {
        featureLayer.clearLayers();

        const bounds = L.latLngBounds();

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
                bounds.extend(polygon.getBounds());

                return;
            }

            const coordinates = [geometry.coordinates[1], geometry.coordinates[0]];
            const marker = L.marker(coordinates, {
                icon: makeMarkerIcon(point),
                title: point.name,
            });

            marker.bindPopup(makePopup(point));
            marker.addTo(featureLayer);
            bounds.extend(coordinates);
        });

        if (bounds.isValid()) {
            map.fitBounds(bounds, {
                padding: [56, 56],
                maxZoom: 16,
            });
            status.hidden = true;
        } else {
            map.setView(configuration.center, configuration.zoom);
            status.hidden = false;
            status.textContent = 'Belum ada titik lokasi pada kategori ini.';
        }

        count.textContent = `${points.length} fitur lokasi`;
        count.hidden = false;
    };

    const loadIotDevices = async () => {
        if (!configuration.iotEndpoint) return;

        const response = await fetch(configuration.iotEndpoint, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error(`IoT GIS request failed with status ${response.status}`);
        const payload = await response.json();
        const bounds = L.latLngBounds();
        iotLayer.clearLayers();
        addIotDevices(map, iotLayer, payload.data, bounds);
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

    Promise.all([loadPoints(), loadIotDevices()]).catch(console.error);

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

    fetch(configuration.iotEndpoint, { headers: { Accept: 'application/json' } })
        .then((response) => {
            if (!response.ok) throw new Error(`IoT GIS request failed with status ${response.status}`);
            return response.json();
        })
        .then((payload) => {
            const bounds = L.latLngBounds();
            addIotDevices(map, layer, payload.data, bounds);
            status.textContent = `${payload.data.length} perangkat IoT aktif`;
            if (bounds.isValid()) map.fitBounds(bounds, { padding: [40, 40], maxZoom: 16 });
        })
        .catch((error) => {
            status.textContent = 'Data perangkat IoT tidak dapat dimuat.';
            console.error(error);
        });
};

const initializeIotMaps = () => document.querySelectorAll('[data-iot-map]').forEach(initializeIotMap);
document.addEventListener('DOMContentLoaded', initializeIotMaps);
document.addEventListener('livewire:navigated', initializeIotMaps);