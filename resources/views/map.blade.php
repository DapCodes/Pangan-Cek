<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PanganCek - Monitoring Harga & Kelangkaan Sembako Jawa Barat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary-orange: #FF8C42;
            --primary-green: #2ECC71;
            --dark-green: #27AE60;
            --light-orange: #FFA766;
            --bg-light: #F8F9FA;
            --text-dark: #2C3E50;
            --border-color: #E0E0E0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--light-orange) 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            color: white !important;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            font-size: 1.8rem;
        }

        .nav-pills .nav-link {
            color: white;
            border-radius: 8px;
            transition: all 0.3s;
            margin: 0 0.25rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
        }

        .nav-pills .nav-link.active {
            background-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .nav-pills .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .nav-pills .nav-link i {
            font-size: 1.1rem;
            margin-right: 0.3rem;
        }

        .main-container {
            padding: 1.5rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        .card-header {
            background-color: white;
            border-bottom: 2px solid var(--border-color);
            padding: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-header i {
            font-size: 1.3rem;
            color: var(--primary-orange);
        }

        #map {
            height: 550px;
            border-radius: 8px;
            z-index: 1;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .form-label i {
            font-size: 1.1rem;
            color: var(--primary-green);
        }

        .form-control,
        .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.65rem 0.9rem;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 0.2rem rgba(255, 140, 66, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--light-orange) 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 140, 66, 0.4);
            background: linear-gradient(135deg, var(--light-orange) 0%, var(--primary-orange) 100%);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-success {
            background-color: var(--primary-green);
            border: none;
            padding: 0.65rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-success:hover {
            background-color: var(--dark-green);
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert i {
            font-size: 1.2rem;
        }

        .alert-info {
            background-color: #E3F2FD;
            color: #1565C0;
        }

        .alert-success {
            background-color: #E8F5E9;
            color: #2E7D32;
        }

        .alert-danger {
            background-color: #FFEBEE;
            color: #C62828;
        }

        .input-group-text {
            background-color: var(--primary-green);
            color: white;
            border: none;
            font-weight: 500;
        }

        .legend {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-top: 1rem;
        }

        .legend h6 {
            margin-bottom: 0.75rem;
            color: var(--text-dark);
            font-weight: 600;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .legend-color {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .map-info {
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid #2196F3;
        }

        .map-info i {
            color: #1976D2;
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }

        .map-info strong {
            color: #1565C0;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }

            #map {
                height: 400px;
            }

            .nav-pills {
                flex-wrap: nowrap;
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class='bx bx-shopping-bag'></i>
                PanganCek Jawa Barat
            </a>
            <ul class="nav nav-pills ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" id="priceTab" href="#"
                        onclick="switchTab('price'); return false;">
                        <i class='bx bx-money'></i> Laporan Harga
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="dearthTab" href="#" onclick="switchTab('dearth'); return false;">
                        <i class='bx bx-error-circle'></i> Laporan Kelangkaan
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid main-container">
        <div class="row g-3">
            <!-- Left Panel: Map -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="map-info" id="mapInfo">
                            <i class='bx bx-info-circle'></i>
                            <strong>Mode: Laporan Harga</strong> - Klik pada peta untuk menentukan lokasi pelaporan
                            harga
                        </div>
                        <div id="map"></div>

                        <!-- Legend for Dearth Mode -->
                        <div id="mapLegend" class="legend" style="display:none;">
                            <h6><i class='bx bx-palette'></i> Legenda Tingkat Kelangkaan</h6>
                            <div class="legend-item">
                                <div class="legend-color" style="background:#2ECC71"></div>
                                <span><strong>Aman</strong> - Tidak ada kelangkaan</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background:#F39C12"></div>
                                <span><strong>Waspada</strong> - Sedikit langka</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background:#E67E22"></div>
                                <span><strong>Rawan</strong> - Cukup langka</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background:#E74C3C"></div>
                                <span><strong>Kritis</strong> - Sangat langka / Tidak tersedia</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Forms -->
            <div class="col-lg-4">
                <!-- Price Report Form -->
                <div id="priceFormCard" class="card">
                    <div class="card-header">
                        <i class='bx bx-edit'></i>
                        Form Laporan Harga
                    </div>
                    <div class="card-body">
                        <div id="priceAlertContainer"></div>
                        <form id="priceForm">
                            <div class="mb-3">
                                <label for="price_commodity_id" class="form-label">
                                    <i class='bx bx-package'></i> Komoditas
                                </label>
                                <select id="price_commodity_id" name="commodity_id" class="form-select" required>
                                    <option value="">-- Pilih Komoditas --</option>
                                    @foreach ($commodities as $commodity)
                                        <option value="{{ $commodity->id }}" data-unit="{{ $commodity->unit }}">
                                            {{ $commodity->name }} ({{ $commodity->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">
                                    <i class='bx bx-money'></i> Harga
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" id="price" name="price" class="form-control"
                                        placeholder="15000" step="0.01" min="0.01" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="price_lat" class="form-label">
                                        <i class='bx bx-current-location'></i> Latitude
                                    </label>
                                    <input type="number" id="price_lat" name="lat" class="form-control"
                                        step="0.0000001" readonly required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="price_lng" class="form-label">
                                        <i class='bx bx-current-location'></i> Longitude
                                    </label>
                                    <input type="number" id="price_lng" name="lng" class="form-control"
                                        step="0.0000001" readonly required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" id="priceSubmitBtn">
                                <i class='bx bx-send'></i> Kirim Laporan Harga
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Dearth Report Form -->
                <div id="dearthFormCard" class="card" style="display:none;">
                    <div class="card-header">
                        <i class='bx bx-error-circle'></i>
                        Form Laporan Kelangkaan
                    </div>
                    <div class="card-body">
                        <div id="dearthAlertContainer"></div>
                        <form id="dearthForm">
                            <div class="mb-3">
                                <label for="dearth_commodity_id" class="form-label">
                                    <i class='bx bx-package'></i> Komoditas
                                </label>
                                <select id="dearth_commodity_id" name="commodity_id" class="form-select" required>
                                    <option value="">-- Pilih Komoditas --</option>
                                    @foreach ($commodities as $commodity)
                                        <option value="{{ $commodity->id }}">{{ $commodity->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="severity" class="form-label">
                                    <i class='bx bx-bar-chart-alt'></i> Tingkat Kelangkaan
                                </label>
                                <select id="severity" name="severity" class="form-select" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="LOW">Sedikit Langka</option>
                                    <option value="MEDIUM">Cukup Langka</option>
                                    <option value="HIGH">Sangat Langka</option>
                                    <option value="CRITICAL">Tidak Tersedia</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="kabupaten" class="form-label">
                                    <i class='bx bx-map-pin'></i> Kabupaten/Kota
                                </label>
                                <input type="text" id="kabupaten" name="kabupaten" class="form-control"
                                    placeholder="Klik pada peta untuk deteksi otomatis" readonly required>
                            </div>
                            <div class="mb-3">
                                <label for="kecamatan" class="form-label">
                                    <i class='bx bx-map'></i> Kecamatan (Opsional)
                                </label>
                                <input type="text" id="kecamatan" name="kecamatan" class="form-control"
                                    placeholder="Nama kecamatan">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class='bx bx-note'></i> Deskripsi
                                </label>
                                <textarea id="description" name="description" class="form-control" rows="3"
                                    placeholder="Jelaskan kondisi kelangkaan yang Anda temukan..."></textarea>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="dearth_lat" class="form-label">
                                        <i class='bx bx-current-location'></i> Latitude
                                    </label>
                                    <input type="number" id="dearth_lat" name="lat" class="form-control"
                                        step="0.0000001" readonly required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="dearth_lng" class="form-label">
                                        <i class='bx bx-current-location'></i> Longitude
                                    </label>
                                    <input type="number" id="dearth_lng" name="lng" class="form-control"
                                        step="0.0000001" readonly required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" id="dearthSubmitBtn">
                                <i class='bx bx-send'></i> Kirim Laporan Kelangkaan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) {
            console.error('CSRF token tidak ditemukan!');
        }

        // Global Variables
        let map, currentMarker, geojsonLayer, clickedKabupaten;
        let currentMode = 'price';
        let jawaBaratData = null;

        // Initialize Map
        map = L.map('map').setView([-6.9175, 107.6191], 9);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Load Jawa Barat GeoJSON Layer
        async function loadJawaBaratLayer() {
            try {
                const response = await fetch('/indonesia-kabupaten.json');
                const data = await response.json();

                // Filter only Jawa Barat
                jawaBaratData = {
                    type: 'FeatureCollection',
                    features: data.features.filter(f => f.properties.WADMPR === 'Jawa Barat')
                };

                console.log('Loaded', jawaBaratData.features.length, 'kabupaten in Jawa Barat');
                renderGeojsonLayer();
            } catch (error) {
                console.error('Error loading GeoJSON:', error);
                showAlert('priceAlertContainer', 'danger', 'Gagal memuat peta Jawa Barat');
            }
        }

        // Render GeoJSON Layer with Dynamic Colors
        function renderGeojsonLayer(dearthData = null) {
            if (geojsonLayer) {
                map.removeLayer(geojsonLayer);
            }

            if (!jawaBaratData) return;

            geojsonLayer = L.geoJSON(jawaBaratData, {
                style: function(feature) {
                    const kabupaten = feature.properties.WADMKK;
                    const color = getKabupatenColor(kabupaten, dearthData);

                    return {
                        fillColor: color,
                        weight: 2,
                        opacity: 1,
                        color: '#666',
                        fillOpacity: currentMode === 'dearth' ? 0.65 : 0.2
                    };
                },
                onEachFeature: function(feature, layer) {
                    const kabupaten = feature.properties.WADMKK;

                    // Popup content
                    let popupContent = `<div style="text-align:center;"><strong>${kabupaten}</strong>`;

                    if (currentMode === 'dearth' && dearthData) {
                        const data = dearthData.find(d => d.kabupaten === kabupaten);
                        if (data) {
                            popupContent += `<br><small>Status: <strong>${data.status}</strong></small>`;
                            popupContent += `<br><small>Laporan: ${data.total_reports}</small>`;
                        }
                    }

                    popupContent += '</div>';
                    layer.bindPopup(popupContent);

                    // Click handler
                    layer.on('click', function(e) {
                        clickedKabupaten = kabupaten;

                        if (currentMode === 'dearth') {
                            document.getElementById('kabupaten').value = kabupaten;
                            document.getElementById('dearth_lat').value = e.latlng.lat.toFixed(7);
                            document.getElementById('dearth_lng').value = e.latlng.lng.toFixed(7);
                        }
                    });

                    // Hover effect
                    layer.on('mouseover', function() {
                        this.setStyle({
                            weight: 3,
                            color: '#333',
                            fillOpacity: 0.8
                        });
                    });

                    layer.on('mouseout', function() {
                        geojsonLayer.resetStyle(this);
                    });
                }
            }).addTo(map);
        }

        // Get Color for Kabupaten based on Dearth Data
        function getKabupatenColor(kabupaten, dearthData) {
            if (!dearthData || currentMode !== 'dearth') {
                return '#2ECC71'; // Green (default for price mode)
            }

            const data = dearthData.find(d => d.kabupaten === kabupaten);
            return data ? data.color : '#2ECC71';
        }

        // Load Dearth Map Data
        async function loadDearthMap() {
            const commodityId = document.getElementById('dearth_commodity_id').value;

            if (!commodityId) {
                renderGeojsonLayer(); // Reset to green
                return;
            }

            try {
                const response = await fetch(`/api/dearth/map?commodity_id=${commodityId}&days=7`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    renderGeojsonLayer(result.data);
                } else {
                    console.error('Failed to load dearth map');
                }
            } catch (error) {
                console.error('Error loading dearth map:', error);
            }
        }

        // Switch Tab (Price / Dearth)
        function switchTab(mode) {
            currentMode = mode;

            // Update tab active state
            document.getElementById('priceTab').classList.toggle('active', mode === 'price');
            document.getElementById('dearthTab').classList.toggle('active', mode === 'dearth');

            // Toggle forms
            document.getElementById('priceFormCard').style.display = mode === 'price' ? 'block' : 'none';
            document.getElementById('dearthFormCard').style.display = mode === 'dearth' ? 'block' : 'none';

            // Toggle legend
            document.getElementById('mapLegend').style.display = mode === 'dearth' ? 'block' : 'none';

            // Update map info
            const mapInfo = document.getElementById('mapInfo');
            if (mode === 'price') {
                mapInfo.innerHTML =
                    '<i class="bx bx-info-circle"></i><strong>Mode: Laporan Harga</strong> - Klik pada peta untuk menentukan lokasi pelaporan harga';
            } else {
                mapInfo.innerHTML =
                    '<i class="bx bx-info-circle"></i><strong>Mode: Laporan Kelangkaan</strong> - Klik pada kabupaten untuk melapor kelangkaan barang';
            }

            // Reset map layer
            renderGeojsonLayer();

            // Remove marker if exists
            if (currentMarker) {
                map.removeLayer(currentMarker);
                currentMarker = null;
            }
        }

        // Map Click Handler for Price Mode
        map.on('click', function(e) {
            if (currentMode === 'price') {
                const lat = e.latlng.lat.toFixed(7);
                const lng = e.latlng.lng.toFixed(7);

                document.getElementById('price_lat').value = lat;
                document.getElementById('price_lng').value = lng;

                // Remove old marker
                if (currentMarker) {
                    map.removeLayer(currentMarker);
                }

                // Add new marker
                currentMarker = L.marker([lat, lng], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    })
                }).addTo(map);

                currentMarker.bindPopup('<strong>Lokasi Pelaporan</strong>').openPopup();
            }
        });

        // Price Form Submit
        document.getElementById('priceForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('priceSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Mengirim...';

            const formData = {
                commodity_id: document.getElementById('price_commodity_id').value,
                price: parseFloat(document.getElementById('price').value),
                lat: parseFloat(document.getElementById('price_lat').value),
                lng: parseFloat(document.getElementById('price_lng').value),
                source: 'USER'
            };

            try {
                const response = await fetch('/api/reports', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (response.ok) {
                    showAlert('priceAlertContainer', 'success', result.message ||
                        'Laporan harga berhasil dikirim!');
                    document.getElementById('price').value = '';
                } else {
                    if (result.errors) {
                        const errorMessages = Object.values(result.errors).flat().join(', ');
                        showAlert('priceAlertContainer', 'danger', errorMessages);
                    } else {
                        showAlert('priceAlertContainer', 'danger', result.message || 'Gagal mengirim laporan');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('priceAlertContainer', 'danger', 'Terjadi kesalahan jaringan. Silakan coba lagi.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bx bx-send"></i> Kirim Laporan Harga';
            }
        });

        // Dearth Form Submit
        document.getElementById('dearthForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('dearthSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bx bx-loader bx-spin"></i> Mengirim...';

            const formData = {
                commodity_id: document.getElementById('dearth_commodity_id').value,
                severity: document.getElementById('severity').value,
                kabupaten: document.getElementById('kabupaten').value,
                kecamatan: document.getElementById('kecamatan').value || null,
                description: document.getElementById('description').value || null,
                lat: parseFloat(document.getElementById('dearth_lat').value),
                lng: parseFloat(document.getElementById('dearth_lng').value),
                source: 'USER'
            };

            try {
                const response = await fetch('/api/dearth/reports', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (response.ok) {
                    showAlert('dearthAlertContainer', 'success', result.message ||
                        'Laporan kelangkaan berhasil dikirim!');
                    document.getElementById('description').value = '';
                    document.getElementById('kecamatan').value = '';
                    loadDearthMap(); // Reload map with new data
                } else {
                    if (result.errors) {
                        const errorMessages = Object.values(result.errors).flat().join(', ');
                        showAlert('dearthAlertContainer', 'danger', errorMessages);
                    } else {
                        showAlert('dearthAlertContainer', 'danger', result.message || 'Gagal mengirim laporan');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('dearthAlertContainer', 'danger', 'Terjadi kesalahan jaringan. Silakan coba lagi.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bx bx-send"></i> Kirim Laporan Kelangkaan';
            }
        });

        // Auto-load dearth map when commodity is selected
        document.getElementById('dearth_commodity_id').addEventListener('change', loadDearthMap);

        // Helper: Show Alert
        function showAlert(containerId, type, message) {
            const container = document.getElementById(containerId);
            const iconMap = {
                'success': 'bx-check-circle',
                'danger': 'bx-error',
                'info': 'bx-info-circle'
            };

            container.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class='bx ${iconMap[type]}'></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            // Auto-hide after 5 seconds
            setTimeout(() => {
                const alertElement = container.querySelector('.alert');
                if (alertElement) {
                    alertElement.classList.remove('show');
                    setTimeout(() => container.innerHTML = '', 300);
                }
            }, 5000);
        }

        // Initialize: Load GeoJSON on page load
        loadJawaBaratLayer();

        // Geolocation (Optional)
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 13);
                },
                error => {
                    console.log('Geolocation not available or denied');
                }
            );
        }
    </script>
</body>

</html>
