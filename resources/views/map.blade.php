<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="YOUR_CSRF_TOKEN_HERE">
    <title>PanganCek - Monitoring Harga & Kelangkaan Sembako Jawa Barat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
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

        #map {
            height: 500px;
            border-radius: 8px;
            z-index: 1;
        }

        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
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
            box-shadow: 0 0 0 0.2rem rgba(255, 140, 66, 0.25);
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
        }

        .alert {
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .map-info {
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border-left: 4px solid #2196F3;
        }

        .location-info {
            background: #E8F5E9;
            padding: 0.75rem;
            border-radius: 6px;
            margin-top: 1rem;
            font-size: 0.9rem;
            border-left: 3px solid var(--primary-green);
        }

        .legend {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-top: 1rem;
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

        #loadingGeocode {
            background: rgba(255, 255, 255, 0.95);
            padding: 0.75rem;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            min-height: 42px;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }

            #map {
                height: 400px;
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
            <!-- Left Panel: Map & Trends -->
            <div class="col-lg-8">
                <!-- Map Card -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="map-info" id="mapInfo">
                            <i class='bx bx-info-circle'></i>
                            <strong>Mode: Laporan Harga</strong> - Klik 2x pada peta untuk menandai lokasi. Data
                            administratif akan terisi otomatis.
                        </div>
                        <div id="map"></div>

                        <div id="loadingGeocode" class="text-center mt-2" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <small class="ms-2 text-muted">Mengambil data lokasi...</small>
                        </div>

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
                                <span><strong>Kritis</strong> - Sangat langka</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trend Chart Card -->
                <div class="card">
                    <div class="card-header">
                        <i class='bx bx-line-chart'></i>
                        Tren 30 Hari Terakhir
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="trendChart"></canvas>
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
                                    <option value="1">Beras Premium (kg)</option>
                                    <option value="2">Beras Medium (kg)</option>
                                    <option value="3">Minyak Goreng (liter)</option>
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

                            <!-- Administrative Select Dropdowns -->
                            <div class="mb-3">
                                <label for="price_province_select" class="form-label">
                                    <i class='bx bx-map'></i> Provinsi
                                </label>
                                <select id="price_province_select" name="province_id" class="form-select select2">
                                    <option value="">-- Otomatis dari Peta --</option>
                                </select>
                                <small class="text-muted">Pilih manual atau klik 2x di peta</small>
                            </div>

                            <div class="mb-3">
                                <label for="price_regency_select" class="form-label">
                                    <i class='bx bx-buildings'></i> Kabupaten/Kota
                                </label>
                                <select id="price_regency_select" name="regency_id" class="form-select select2">
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="price_district_select" class="form-label">
                                    <i class='bx bx-map-pin'></i> Kecamatan
                                </label>
                                <select id="price_district_select" name="district_id" class="form-select select2">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="price_village_select" class="form-label">
                                    <i class='bx bx-home'></i> Desa/Kelurahan
                                </label>
                                <select id="price_village_select" name="village_id" class="form-select select2">
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
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

                            <div id="priceLocationInfo" class="location-info" style="display:none;"></div>

                            <button type="submit" class="btn btn-primary w-100 mt-3" id="priceSubmitBtn">
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
                                    <option value="1">Beras Premium</option>
                                    <option value="2">Beras Medium</option>
                                    <option value="3">Minyak Goreng</option>
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
                                <label for="description" class="form-label">
                                    <i class='bx bx-note'></i> Deskripsi
                                </label>
                                <textarea id="description" name="description" class="form-control" rows="3"
                                    placeholder="Jelaskan kondisi kelangkaan..."></textarea>
                            </div>

                            <!-- Administrative Select Dropdowns for Dearth -->
                            <div class="mb-3">
                                <label for="dearth_province_select" class="form-label">
                                    <i class='bx bx-map'></i> Provinsi
                                </label>
                                <select id="dearth_province_select" name="province_id" class="form-select select2">
                                    <option value="">-- Otomatis dari Peta --</option>
                                </select>
                                <small class="text-muted">Pilih manual atau klik 2x di peta</small>
                            </div>

                            <div class="mb-3">
                                <label for="dearth_regency_select" class="form-label">
                                    <i class='bx bx-buildings'></i> Kabupaten/Kota
                                </label>
                                <select id="dearth_regency_select" name="regency_id" class="form-select select2">
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="dearth_district_select" class="form-label">
                                    <i class='bx bx-map-pin'></i> Kecamatan
                                </label>
                                <select id="dearth_district_select" name="district_id" class="form-select select2">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="dearth_village_select" class="form-label">
                                    <i class='bx bx-home'></i> Desa/Kelurahan
                                </label>
                                <select id="dearth_village_select" name="village_id" class="form-select select2">
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
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

                            <div id="dearthLocationInfo" class="location-info" style="display:none;"></div>

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const API_BASE_URL = '/api'; // Sesuaikan dengan Laravel route Anda
        let map, marker, geojsonLayer;
        let currentMode = 'price';
        let jawaBaratData = null;
        let trendChart = null;

        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: true
            });

            initializeMap();
            loadProvinces();
            setupEventListeners();
            loadTrendChart();
        });

        function initializeMap() {
            map = L.map('map').setView([-6.9175, 107.6191], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            const southWest = L.latLng(-7.8, 106.5);
            const northEast = L.latLng(-6.0, 108.8);
            const bounds = L.latLngBounds(southWest, northEast);
            map.setMaxBounds(bounds);
            map.on('drag', function() {
                map.panInsideBounds(bounds, {
                    animate: false
                });
            });

            map.on('dblclick', handleMapDoubleClick);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        map.setView([position.coords.latitude, position.coords.longitude], 13);
                    },
                    error => console.log('Geolocation not available')
                );
            }
        }

        function handleMapDoubleClick(e) {
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);

            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);
            marker.bindPopup(`Lokasi Laporan<br>Lat: ${lat}<br>Lng: ${lng}`).openPopup();

            const prefix = currentMode === 'price' ? 'price' : 'dearth';
            $(`#${prefix}_lat`).val(lat);
            $(`#${prefix}_lng`).val(lng);

            getAdministrativeData(lat, lng);

            marker.on('dragend', function(e) {
                const newLat = e.target.getLatLng().lat.toFixed(6);
                const newLng = e.target.getLatLng().lng.toFixed(6);
                $(`#${prefix}_lat`).val(newLat);
                $(`#${prefix}_lng`).val(newLng);
                marker.setPopupContent(`Lokasi Laporan<br>Lat: ${newLat}<br>Lng: ${newLng}`);
                getAdministrativeData(newLat, newLng);
            });
        }

        function getAdministrativeData(lat, lng) {
            $('#loadingGeocode').show();
            const prefix = currentMode === 'price' ? 'price' : 'dearth';

            $.ajax({
                url: `${API_BASE_URL}/reverse-geocode`,
                type: 'GET',
                data: {
                    lat: lat,
                    lng: lng
                },
                success: function(response) {
                    console.log('Reverse Geocoding Response:', response);

                    if (response.success && response.data) {
                        const data = response.data;

                        // Set Province
                        if (data.province_id) {
                            $(`#${prefix}_province_select`).val(data.province_id).trigger('change');

                            // Load Regencies and set selected
                            if (data.regency_id) {
                                setTimeout(() => {
                                    loadRegencies(prefix, data.province_id, data.regency_id);

                                    // Load Districts and set selected
                                    if (data.district_id) {
                                        setTimeout(() => {
                                            loadDistricts(prefix, data.regency_id, data
                                                .district_id);

                                            // Load Villages and set selected
                                            if (data.village_id) {
                                                setTimeout(() => {
                                                    loadVillages(prefix, data
                                                        .district_id, data
                                                        .village_id);
                                                }, 200);
                                            }
                                        }, 200);
                                    }
                                }, 200);
                            }
                        }

                        // Display location info
                        const locationInfo = $(`#${prefix}LocationInfo`);
                        locationInfo.show();
                        locationInfo.html(`
                            <strong><i class='bx bx-map-pin'></i> Lokasi Terdeteksi:</strong><br>
                            ${data.village_name ? `Desa: ${data.village_name}<br>` : ''}
                            ${data.district_name ? `Kec: ${data.district_name}<br>` : ''}
                            ${data.regency_name ? `Kab: ${data.regency_name}<br>` : ''}
                            ${data.province_name ? `Prov: ${data.province_name}<br>` : ''}
                            <small class="text-muted">Jarak: ~${data.distance} km dari titik terdekat</small>
                        `);

                        showAlert(`${prefix}AlertContainer`, 'success',
                            'Data lokasi berhasil diambil! Silakan cek dropdown di atas.');
                    } else {
                        showAlert(`${prefix}AlertContainer`, 'info',
                            'Lokasi tidak ditemukan. Silakan pilih secara manual.');
                    }
                },
                error: function(xhr) {
                    console.error('Error getting administrative data:', xhr);
                    showAlert(`${prefix}AlertContainer`, 'warning',
                        'Gagal mengambil data administratif. Silakan pilih manual.');
                },
                complete: function() {
                    $('#loadingGeocode').hide();
                }
            });
        }

        function loadProvinces() {
            // Load untuk price form
            $.get('/regencies/provinces', function(response) {
                let options = '<option value="">-- Otomatis dari Peta --</option>';
                response.data.forEach(item => {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });
                $('#price_province_select').html(options);
                $('#dearth_province_select').html(options);
            });
        }

        function loadRegencies(prefix, provinceId, selectedId = null) {
            if (!provinceId) {
                $(`#${prefix}_regency_select`).html('<option value="">-- Pilih Kabupaten/Kota --</option>').trigger(
                    'change');
                $(`#${prefix}_district_select`).html('<option value="">-- Pilih Kecamatan --</option>').trigger('change');
                $(`#${prefix}_village_select`).html('<option value="">-- Pilih Desa/Kelurahan --</option>').trigger(
                    'change');
                return;
            }

            $.get(`/regencies/${provinceId}`, function(response) {
                let options = '<option value="">-- Pilih Kabupaten/Kota --</option>';
                response.data.forEach(item => {
                    const selected = selectedId && item.id == selectedId ? 'selected' : '';
                    options += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                });
                $(`#${prefix}_regency_select`).html(options).trigger('change');
            });
        }

        function loadDistricts(prefix, regencyId, selectedId = null) {
            if (!regencyId) {
                $(`#${prefix}_district_select`).html('<option value="">-- Pilih Kecamatan --</option>').trigger('change');
                $(`#${prefix}_village_select`).html('<option value="">-- Pilih Desa/Kelurahan --</option>').trigger(
                    'change');
                return;
            }

            $.get(`/districts/${regencyId}`, function(response) {
                let options = '<option value="">-- Pilih Kecamatan --</option>';
                response.data.forEach(item => {
                    const selected = selectedId && item.id == selectedId ? 'selected' : '';
                    options += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                });
                $(`#${prefix}_district_select`).html(options).trigger('change');
            });
        }

        function loadVillages(prefix, districtId, selectedId = null) {
            if (!districtId) {
                $(`#${prefix}_village_select`).html('<option value="">-- Pilih Desa/Kelurahan --</option>').trigger(
                    'change');
                return;
            }

            $.get(`/villages/${districtId}`, function(response) {
                let options = '<option value="">-- Pilih Desa/Kelurahan --</option>';
                response.data.forEach(item => {
                    const selected = selectedId && item.id == selectedId ? 'selected' : '';
                    options += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                });
                $(`#${prefix}_village_select`).html(options).trigger('change');
            });
        }

        function setupEventListeners() {
            // Price form cascading dropdowns
            $('#price_province_select').on('change', function() {
                const provinceId = $(this).val();
                loadRegencies('price', provinceId);
            });

            $('#price_regency_select').on('change', function() {
                const regencyId = $(this).val();
                loadDistricts('price', regencyId);
            });

            $('#price_district_select').on('change', function() {
                const districtId = $(this).val();
                loadVillages('price', districtId);
            });

            // Dearth form cascading dropdowns
            $('#dearth_province_select').on('change', function() {
                const provinceId = $(this).val();
                loadRegencies('dearth', provinceId);
            });

            $('#dearth_regency_select').on('change', function() {
                const regencyId = $(this).val();
                loadDistricts('dearth', regencyId);
            });

            $('#dearth_district_select').on('change', function() {
                const districtId = $(this).val();
                loadVillages('dearth', districtId);
            });

            // Form submissions
            $('#priceForm').on('submit', handlePriceFormSubmit);
            $('#dearthForm').on('submit', handleDearthFormSubmit);

            // Commodity change triggers chart reload
            $('#price_commodity_id').on('change', loadTrendChart);
            $('#dearth_commodity_id').on('change', function() {
                loadTrendChart();
            });
        }

        function handlePriceFormSubmit(e) {
            e.preventDefault();

            if (!$('#price_lat').val() || !$('#price_lng').val()) {
                showAlert('priceAlertContainer', 'warning', 'Silakan tandai lokasi pada peta terlebih dahulu!');
                return;
            }

            const submitBtn = $('#priceSubmitBtn');
            submitBtn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Mengirim...');

            const formData = {
                commodity_id: $('#price_commodity_id').val(),
                price: $('#price').val(),
                lat: $('#price_lat').val(),
                lng: $('#price_lng').val(),
                province_id: $('#price_province_select').val(),
                regency_id: $('#price_regency_select').val(),
                district_id: $('#price_district_select').val(),
                village_id: $('#price_village_select').val(),
                source: 'USER'
            };

            $.ajax({
                url: `${API_BASE_URL}/reports`,
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showAlert('priceAlertContainer', 'success', response.message ||
                        'Laporan harga berhasil dikirim!');
                    $('#price').val('');
                    loadTrendChart();
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Gagal mengirim laporan';
                    showAlert('priceAlertContainer', 'danger', message);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="bx bx-send"></i> Kirim Laporan Harga');
                }
            });
        }

        function handleDearthFormSubmit(e) {
            e.preventDefault();

            if (!$('#dearth_lat').val() || !$('#dearth_lng').val()) {
                showAlert('dearthAlertContainer', 'warning', 'Silakan tandai lokasi pada peta terlebih dahulu!');
                return;
            }

            const submitBtn = $('#dearthSubmitBtn');
            submitBtn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Mengirim...');

            const formData = {
                commodity_id: $('#dearth_commodity_id').val(),
                severity: $('#severity').val(),
                description: $('#description').val(),
                lat: $('#dearth_lat').val(),
                lng: $('#dearth_lng').val(),
                province_id: $('#dearth_province_select').val(),
                regency_id: $('#dearth_regency_select').val(),
                district_id: $('#dearth_district_select').val(),
                village_id: $('#dearth_village_select').val(),
                kabupaten: $('#dearth_regency_select option:selected').text() || 'Unknown',
                kecamatan: $('#dearth_district_select option:selected').text() || null,
                source: 'USER'
            };

            $.ajax({
                url: `${API_BASE_URL}/dearth/reports`,
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showAlert('dearthAlertContainer', 'success', response.message ||
                        'Laporan kelangkaan berhasil dikirim!');
                    $('#description').val('');
                    loadTrendChart();
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Gagal mengirim laporan';
                    showAlert('dearthAlertContainer', 'danger', message);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(
                        '<i class="bx bx-send"></i> Kirim Laporan Kelangkaan');
                }
            });
        }

        function switchTab(mode) {
            currentMode = mode;
            $('#priceTab').toggleClass('active', mode === 'price');
            $('#dearthTab').toggleClass('active', mode === 'dearth');
            $('#priceFormCard').toggle(mode === 'price');
            $('#dearthFormCard').toggle(mode === 'dearth');
            $('#mapLegend').toggle(mode === 'dearth');

            const mapInfo = $('#mapInfo');
            if (mode === 'price') {
                mapInfo.html(
                    '<i class="bx bx-info-circle"></i><strong>Mode: Laporan Harga</strong> - Klik 2x pada peta untuk menandai lokasi. Data administratif akan terisi otomatis.'
                    );
            } else {
                mapInfo.html(
                    '<i class="bx bx-info-circle"></i><strong>Mode: Laporan Kelangkaan</strong> - Klik 2x pada peta untuk menandai lokasi. Data administratif akan terisi otomatis.'
                    );
            }

            if (marker) {
                map.removeLayer(marker);
                marker = null;
            }

            loadTrendChart();
        }

        function loadTrendChart() {
            const commoditySelect = currentMode === 'price' ?
                $('#price_commodity_id') : $('#dearth_commodity_id');
            const commodityId = commoditySelect.val();

            $.ajax({
                url: `${API_BASE_URL}/trend`,
                type: 'GET',
                data: {
                    type: currentMode,
                    days: 30,
                    commodity_id: commodityId || ''
                },
                success: function(response) {
                    console.log('Trend data:', response);
                    if (response.success && response.data && response.data.length > 0) {
                        renderTrendChart(response.data);
                    } else {
                        renderEmptyChart();
                    }
                },
                error: function(xhr) {
                    console.error('Error loading trend:', xhr);
                    renderEmptyChart();
                }
            });
        }

        function renderTrendChart(data) {
            const canvas = document.getElementById('trendChart');
            if (!canvas) return;

            if (trendChart) {
                trendChart.destroy();
                trendChart = null;
            }

            const ctx = canvas.getContext('2d');

            if (currentMode === 'price') {
                const labels = data.map(d => {
                    const date = new Date(d.date);
                    return date.toLocaleDateString('id-ID', {
                        month: 'short',
                        day: 'numeric'
                    });
                });
                const avgPrices = data.map(d => parseFloat(d.avg_price) || 0);
                const minPrices = data.map(d => parseFloat(d.min_price) || 0);
                const maxPrices = data.map(d => parseFloat(d.max_price) || 0);

                trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Harga Rata-rata',
                            data: avgPrices,
                            borderColor: '#FF8C42',
                            backgroundColor: 'rgba(255, 140, 66, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Harga Minimum',
                            data: minPrices,
                            borderColor: '#2ECC71',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            tension: 0.4,
                            fill: false
                        }, {
                            label: 'Harga Maksimum',
                            data: maxPrices,
                            borderColor: '#E74C3C',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            tension: 0.4,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        aspectRatio: 2,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Tren Harga Komoditas - 30 Hari Terakhir',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp ' +
                                            context.parsed.y.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Harga (Rp)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Tanggal'
                                }
                            }
                        }
                    }
                });
            } else {
                // Dearth chart
                const labels = data.map(d => {
                    const date = new Date(d.date);
                    return date.toLocaleDateString('id-ID', {
                        month: 'short',
                        day: 'numeric'
                    });
                });
                const criticalCount = data.map(d => parseInt(d.critical_count) || 0);
                const highCount = data.map(d => parseInt(d.high_count) || 0);
                const mediumCount = data.map(d => parseInt(d.medium_count) || 0);
                const lowCount = data.map(d => parseInt(d.low_count) || 0);

                trendChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Kritis',
                            data: criticalCount,
                            backgroundColor: '#E74C3C',
                            borderRadius: 4
                        }, {
                            label: 'Rawan',
                            data: highCount,
                            backgroundColor: '#E67E22',
                            borderRadius: 4
                        }, {
                            label: 'Waspada',
                            data: mediumCount,
                            backgroundColor: '#F39C12',
                            borderRadius: 4
                        }, {
                            label: 'Rendah',
                            data: lowCount,
                            backgroundColor: '#95A5A6',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        aspectRatio: 2,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Tren Laporan Kelangkaan - 30 Hari Terakhir',
                                font: {
                                    size: 16,
                                    weight: 'bold'
                                }
                            }
                        },
                        scales: {
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                title: {
                                    display: true,
                                    text: 'Jumlah Laporan'
                                }
                            },
                            x: {
                                stacked: true,
                                title: {
                                    display: true,
                                    text: 'Tanggal'
                                }
                            }
                        }
                    }
                });
            }
        }

        function renderEmptyChart() {
            const canvas = document.getElementById('trendChart');
            if (!canvas) return;

            if (trendChart) {
                trendChart.destroy();
                trendChart = null;
            }

            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.font = '16px Arial';
            ctx.fillStyle = '#999';
            ctx.textAlign = 'center';
            ctx.fillText('Belum ada data laporan', canvas.width / 2, canvas.height / 2);
        }

        function showAlert(containerId, type, message) {
            const container = $(`#${containerId}`);
            const iconMap = {
                'success': 'bx-check-circle',
                'danger': 'bx-error',
                'warning': 'bx-error-circle',
                'info': 'bx-info-circle'
            };

            container.html(`
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class='bx ${iconMap[type]}'></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);

            setTimeout(() => {
                const alertElement = container.find('.alert');
                if (alertElement.length) {
                    alertElement.removeClass('show');
                    setTimeout(() => container.html(''), 300);
                }
            }, 5000);
        }
    </script>
</body>

</html>
