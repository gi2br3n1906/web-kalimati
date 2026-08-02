import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet-draw';
import 'leaflet-draw/dist/leaflet.draw.css';

window.landGridPolygonEditor = (wire, statePath, initialState, center, zoom) => ({
    map: null,
    layers: null,
    init() {
        this.map = L.map(this.$refs.map).setView([center.latitude, center.longitude], zoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(this.map);

        this.layers = new L.FeatureGroup();
        this.map.addLayer(this.layers);
        this.loadInitialPolygon(initialState);

        this.map.addControl(new L.Control.Draw({
            draw: {
                polygon: { allowIntersection: false, showArea: true },
                polyline: false,
                rectangle: false,
                circle: false,
                marker: false,
                circlemarker: false,
            },
            edit: { featureGroup: this.layers },
        }));

        this.map.on(L.Draw.Event.CREATED, (event) => {
            this.layers.clearLayers();
            this.layers.addLayer(event.layer);
            this.sync();
        });
        this.map.on(L.Draw.Event.EDITED, () => this.sync());
        this.map.on(L.Draw.Event.DELETED, () => this.sync());
        setTimeout(() => this.map.invalidateSize(), 0);
    },
    loadInitialPolygon(state) {
        if (!state || !state.type || !state.coordinates) {
            return;
        }
        const layer = L.geoJSON(state);
        layer.eachLayer((item) => this.layers.addLayer(item));
        const bounds = this.layers.getBounds();
        if (bounds.isValid()) {
            this.map.fitBounds(bounds, { padding: [24, 24] });
        }
    },
    sync() {
        const layers = this.layers.toGeoJSON();
        const polygon = layers.features[0]?.geometry ?? null;
        wire.set(statePath, polygon);
    },
});