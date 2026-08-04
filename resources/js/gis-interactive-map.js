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

    loadPoints();

    window.setTimeout(() => map.invalidateSize(), 0);
};

const initializeMaps = () => {
    document.querySelectorAll('[data-gis-map]').forEach(initializeMap);
};

document.addEventListener('DOMContentLoaded', initializeMaps);
document.addEventListener('livewire:navigated', initializeMaps);