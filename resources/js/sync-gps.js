const encoder = new TextEncoder();

const initializeSyncGps = (root) => {
    if (root.dataset.initialized === 'true') {
        return;
    }

    root.dataset.initialized = 'true';

    const configuration = JSON.parse(root.dataset.configuration);
    const connectButton = root.querySelector('[data-connect-bluetooth]');
    const connectLabel = root.querySelector('[data-connect-label]');
    const connectionStatus = root.querySelector('[data-connection-status]');
    const connectionLabel = root.querySelector('[data-connection-label]');
    const deviceName = root.querySelector('[data-device-name]');
    const latitude = root.querySelector('[data-gps-latitude]');
    const longitude = root.querySelector('[data-gps-longitude]');
    const accuracy = root.querySelector('[data-gps-accuracy]');
    const gpsStatus = root.querySelector('[data-gps-status]');
    const transferStatus = root.querySelector('[data-transfer-status]');
    const errorAlert = root.querySelector('[data-sync-error]');

    let bluetoothDevice = null;
    let requestCharacteristic = null;
    let writableCharacteristic = null;
    let geolocationWatchId = null;
    let currentPosition = null;
    let writeQueue = Promise.resolve();

    const showError = (message) => {
        errorAlert.textContent = message;
        errorAlert.classList.remove('hidden');
    };

    const clearError = () => {
        errorAlert.textContent = '';
        errorAlert.classList.add('hidden');
    };

    const setConnectionState = (state, label) => {
        connectionStatus.dataset.state = state;
        connectionLabel.textContent = label;
    };

    const stopGeolocation = () => {
        if (geolocationWatchId !== null) {
            navigator.geolocation.clearWatch(geolocationWatchId);
            geolocationWatchId = null;
        }
    };

    const disconnect = () => {
        stopGeolocation();
        currentPosition = null;
        requestCharacteristic = null;
        writableCharacteristic = null;
        bluetoothDevice = null;
        setConnectionState('disconnected', 'Terputus');
        deviceName.textContent = 'Belum ada alat dipilih';
        connectButton.disabled = false;
        connectLabel.textContent = 'Hubungkan Bluetooth ke Alat Sawah';
        gpsStatus.textContent = 'Menunggu koneksi Bluetooth.';
    };

    const writeCoordinates = async () => {
        if (!currentPosition || !writableCharacteristic) {
            transferStatus.textContent = 'Permintaan diterima, tetapi koordinat GPS belum tersedia.';
            return;
        }

        const payload = `${currentPosition.latitude.toFixed(7)},${currentPosition.longitude.toFixed(7)}`;
        const value = encoder.encode(payload);

        if (writableCharacteristic.properties.write && writableCharacteristic.writeValueWithResponse) {
            await writableCharacteristic.writeValueWithResponse(value);
        } else if (writableCharacteristic.properties.writeWithoutResponse && writableCharacteristic.writeValueWithoutResponse) {
            await writableCharacteristic.writeValueWithoutResponse(value);
        } else {
            await writableCharacteristic.writeValue(value);
        }

        transferStatus.textContent = `${payload} dikirim pada ${new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        }).format(new Date())}.`;
    };

    const handleCoordinateRequest = () => {
        writeQueue = writeQueue
            .then(writeCoordinates)
            .catch((error) => {
                transferStatus.textContent = 'Koordinat gagal dikirim ke alat.';
                showError(error instanceof Error ? error.message : 'Terjadi kesalahan saat menulis data Bluetooth.');
            });
    };

    const startGeolocation = () => {
        gpsStatus.textContent = 'Mencari koordinat GPS HP...';
        geolocationWatchId = navigator.geolocation.watchPosition(
            (position) => {
                currentPosition = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                };
                latitude.textContent = currentPosition.latitude.toFixed(7);
                longitude.textContent = currentPosition.longitude.toFixed(7);
                accuracy.textContent = `${Math.round(currentPosition.accuracy)} m`;
                gpsStatus.textContent = 'Koordinat GPS aktif dan siap dikirim.';
            },
            (error) => {
                currentPosition = null;
                gpsStatus.textContent = 'Koordinat GPS tidak tersedia.';
                showError(error.message || 'Izin lokasi ditolak atau sinyal GPS tidak tersedia.');
            },
            {
                enableHighAccuracy: true,
                maximumAge: 5000,
                timeout: 15000,
            },
        );
    };

    const connect = async () => {
        clearError();
        connectButton.disabled = true;
        connectLabel.textContent = 'Mencari ESP32...';
        setConnectionState('connecting', 'Menghubungkan');

        try {
            bluetoothDevice = await navigator.bluetooth.requestDevice({
                filters: [{ name: configuration.deviceName }],
                optionalServices: [configuration.serviceUuid],
            });
            bluetoothDevice.addEventListener('gattserverdisconnected', disconnect, { once: true });

            const server = await bluetoothDevice.gatt.connect();
            const service = await server.getPrimaryService(configuration.serviceUuid);
            const characteristics = await service.getCharacteristics();

            requestCharacteristic = characteristics.find((characteristic) => (
                characteristic.properties.notify || characteristic.properties.indicate
            ));
            writableCharacteristic = characteristics.find((characteristic) => (
                characteristic.properties.write || characteristic.properties.writeWithoutResponse
            ));

            if (!requestCharacteristic || !writableCharacteristic) {
                throw new Error('Characteristic request dan write tidak ditemukan pada service ESP32.');
            }

            requestCharacteristic.addEventListener('characteristicvaluechanged', handleCoordinateRequest);
            await requestCharacteristic.startNotifications();

            setConnectionState('connected', 'Terhubung ke ESP32');
            deviceName.textContent = bluetoothDevice.name || configuration.deviceName;
            connectLabel.textContent = 'Bluetooth Terhubung';
            transferStatus.textContent = 'Menunggu permintaan koordinat dari alat.';
            startGeolocation();
        } catch (error) {
            if (bluetoothDevice?.gatt?.connected) {
                bluetoothDevice.gatt.disconnect();
            } else {
                disconnect();
            }

            if (error instanceof DOMException && error.name === 'NotFoundError') {
                showError('Pemilihan perangkat dibatalkan atau ESP32-GPS-Sync tidak ditemukan.');
                return;
            }

            showError(error instanceof Error ? error.message : 'Bluetooth tidak dapat dihubungkan.');
        }
    };

    if (!window.isSecureContext) {
        connectButton.disabled = true;
        showError('Web Bluetooth memerlukan koneksi HTTPS yang aman.');
        return;
    }

    if (!navigator.bluetooth) {
        connectButton.disabled = true;
        showError('Browser ini belum mendukung Web Bluetooth. Gunakan Chrome di perangkat Android.');
        return;
    }

    if (!navigator.geolocation) {
        connectButton.disabled = true;
        showError('Browser ini tidak menyediakan akses lokasi GPS.');
        return;
    }

    connectButton.addEventListener('click', connect);
    window.addEventListener('pagehide', stopGeolocation, { once: true });
};

const initializeSyncGpsPages = () => {
    document.querySelectorAll('[data-sync-gps]').forEach(initializeSyncGps);
};

document.addEventListener('DOMContentLoaded', initializeSyncGpsPages);
document.addEventListener('livewire:navigated', initializeSyncGpsPages);