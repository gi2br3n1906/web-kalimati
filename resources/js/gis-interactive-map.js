import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const categoryColors = {
    pemerintahan: '#155e75',
    fasilitas_umum: '#7c3aed',
    pendidikan: '#1d4ed8',
    ibadah: '#a16207',
    posyandu: '#be123c',
};

const markerSymbols = {
    'building-government': 'G',
    landmark: 'F',
    school: 'S',
    'place-of-worship': 'I',
    'health-center': 'P',
};

const categoryLabels = {
    pemerintahan: 'Pemerintahan',
    fasilitas_umum: 'Fasilitas Umum',
    pendidikan: 'Pendidikan',
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
    const markerLayer = L.layerGroup().addTo(map);

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

    const renderPoints = (points) => {
        markerLayer.clearLayers();

        const bounds = [];

        points.forEach((point) => {
            const coordinates = [point.latitude, point.longitude];
            const marker = L.marker(coordinates, {
                icon: makeMarkerIcon(point),
                title: point.name,
            });

            marker.bindPopup(makePopup(point));
            marker.addTo(markerLayer);
            bounds.push(coordinates);
        });

        if (bounds.length > 0) {
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

        count.textContent = `${points.length} titik lokasi`;
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

            renderPoints(payload.data);
        } catch (error) {
            markerLayer.clearLayers();
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