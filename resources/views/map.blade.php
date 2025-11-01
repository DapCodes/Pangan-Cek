<!DOCTYPE html>
<html lang="id">
<head>
    <!-- ===== Meta & Title ===== -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Ambil token dari Laravel -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PanganCek — Monitoring Harga & Kelangkaan Sembako Jawa Barat</title>

    <!-- ===== Styles ===== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        :root{
            --primary: #FF8C42;
            --primary-2: #FFA766;
            --green: #2ECC71;
            --dark: #2C3E50;
            --muted: #6c757d;
            --bg: #F6F7FB;
            --card: #ffffff;
            --border: #E7EAF0;
        }

        html, body{ height: 100%; background: var(--bg); color: var(--dark); }

        /* ===== Navbar ===== */
        .navbar{
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
        }
        .navbar-brand{
            color:#fff!important; font-weight:700; letter-spacing:.2px;
            display:flex; align-items:center; gap:.6rem;
        }

        /* ===== Layout ===== */
        .container-xxl{ padding-block: 18px; }
        .page-grid{
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 18px;
        }
        @media (max-width: 992px){ .page-grid{ grid-template-columns: 1fr; } }

        .card{
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(20,20,20,.04);
        }
        .card-header{
            background: var(--card);
            border-bottom: 1px dashed var(--border);
            border-radius: 14px 14px 0 0 !important;
            font-weight: 700;
            display:flex; align-items:center; gap:.5rem;
            padding: 1rem 1.25rem;
        }
        .card-body{ padding: 1rem 1.25rem 1.25rem; }

        /* ===== Map ===== */
        #map{ height: 520px; border-radius: 10px; }
        .map-info{
            background: #EAF6FF;
            border: 1px dashed #B9DEFF;
            padding: .85rem 1rem; border-radius: 10px;
            margin-bottom: .75rem; font-size: .95rem;
        }

        /* ===== Legend & Info ===== */
        .legend{
            display:grid; grid-template-columns: repeat(2, minmax(0,1fr));
            gap:10px; margin-top: .75rem;
        }
        .legend-item{ display:flex; align-items:center; gap:10px; }
        .legend-color{ width:20px; height:20px; border-radius:4px; border: 1px solid #0001; }

        .location-info{
            background: #F3FFF7; border: 1px dashed #BEEBD0;
            padding: .7rem .9rem; border-radius: 10px; font-size: .92rem;
        }

        /* ===== Forms ===== */
        .form-label{ font-weight: 600; display:flex; gap:.45rem; align-items:center; }
        .select2-container--bootstrap-5 .select2-selection{
            min-height: 44px; border-radius: 10px;
            border: 1px solid var(--border);
        }
        .form-control, .form-select{
            border: 1px solid var(--border); border-radius: 10px; padding: .65rem .9rem;
        }
        .form-control:focus, .form-select:focus{
            border-color: var(--primary);
            box-shadow: 0 0 0 .2rem rgba(255,140,66,.2);
        }

        /* ===== Buttons ===== */
        .btn-primary{
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
            border: none; border-radius: 10px; padding: .75rem 1.2rem; font-weight: 600;
            box-shadow: 0 10px 20px rgba(255,140,66,.18);
        }
        .btn-primary:hover{ transform: translateY(-1px); }

        /* ===== Chart ===== */
        .chart-container{ height: 360px; }

        /* ===== Alerts ===== */
        .alert{ border-radius: 10px; border: none; }
    </style>
</head>
<body>
    <!-- ===== Navbar ===== -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-xxl">
            <a class="navbar-brand" href="#">
                <i class="bx bx-shopping-bag"></i> PanganCek Jawa Barat
            </a>
            <ul class="nav nav-pills ms-auto gap-2">
                <li class="nav-item">
                    <a id="priceTab" class="btn btn-sm btn-light fw-semibold" href="#" onclick="switchTab('price'); return false;">
                        <i class='bx bx-money'></i> Laporan Harga
                    </a>
                </li>
                <li class="nav-item">
                    <a id="dearthTab" class="btn btn-sm btn-outline-light text-white fw-semibold" href="#" onclick="switchTab('dearth'); return false;">
                        <i class='bx bx-error-circle'></i> Laporan Kelangkaan
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- ===== Content ===== -->
    <div class="container-xxl">
        <div class="page-grid">
            <!-- Left: Map & Chart -->
            <div class="d-flex flex-column gap-3">
                <div class="card">
                    <div class="card-body">
                        <div id="mapInfo" class="map-info">
                            <i class='bx bx-info-circle'></i>
                            <strong>Mode: Laporan Harga</strong> — Klik ganda pada peta untuk menandai lokasi. Data administratif akan terisi otomatis.
                        </div>
                        <div id="map"></div>



                        <div id="mapLegend" class="legend" style="display:none;">
                            <div class="legend-item">
                                <span class="legend-color" style="background:#2ECC71"></span><span><strong>Aman</strong> — Tidak ada kelangkaan</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background:#F39C12"></span><span><strong>Waspada</strong> — Sedikit langka</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background:#E67E22"></span><span><strong>Rawan</strong> — Cukup langka</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background:#E74C3C"></span><span><strong>Kritis</strong> — Sangat langka</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><i class='bx bx-line-chart'></i> Tren 30 Hari Terakhir</div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Forms -->
            <div class="d-flex flex-column gap-3">
                <!-- Price Form -->
                <div id="priceFormCard" class="card">
                    <div class="card-header"><i class='bx bx-edit'></i> Form Laporan Harga</div>
                    <div class="card-body">
                        <div id="priceAlertContainer"></div>
                        <form id="priceForm" class="row g-3">
                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-package'></i> Komoditas</label>
                                <select id="price_commodity_id" name="commodity_id" class="form-select" required>
                                    <option value="">-- Pilih Komoditas --</option>
                                    <option value="1">Beras Premium (kg)</option>
                                    <option value="2">Beras Medium (kg)</option>
                                    <option value="3">Minyak Goreng (liter)</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-money'></i> Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input id="price" name="price" type="number" class="form-control" placeholder="15000" step="0.01" min="0.01" required />
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-map'></i> Provinsi</label>
                                <select id="price_province_select" name="province_id" class="form-select select2">
                                    <option value="">-- Otomatis dari Peta --</option>
                                </select>
                                <small class="text-muted">Pilih manual atau klik ganda di peta</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-buildings'></i> Kabupaten/Kota</label>
                                <select id="price_regency_select" name="regency_id" class="form-select select2">
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-map-pin'></i> Kecamatan</label>
                                <select id="price_district_select" name="district_id" class="form-select select2">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-home'></i> Desa/Kelurahan</label>
                                <select id="price_village_select" name="village_id" class="form-select select2">
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
                            </div>

                            <div class="col-6">
                                <label class="form-label"><i class='bx bx-current-location'></i> Latitude</label>
                                <input id="price_lat" name="lat" type="number" class="form-control" step="0.0000001" readonly required />
                            </div>
                            <div class="col-6">
                                <label class="form-label"><i class='bx bx-current-location'></i> Longitude</label>
                                <input id="price_lng" name="lng" type="number" class="form-control" step="0.0000001" readonly required />
                            </div>

                            <div id="priceLocationInfo" class="location-info" style="display:none;"></div>

                            <div class="col-12">
                                <button id="priceSubmitBtn" type="submit" class="btn btn-primary w-100">
                                    <i class='bx bx-send'></i> Kirim Laporan Harga
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Dearth Form -->
                <div id="dearthFormCard" class="card" style="display:none;">
                    <div class="card-header"><i class='bx bx-error-circle'></i> Form Laporan Kelangkaan</div>
                    <div class="card-body">
                        <div id="dearthAlertContainer"></div>
                        <form id="dearthForm" class="row g-3">
                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-package'></i> Komoditas</label>
                                <select id="dearth_commodity_id" name="commodity_id" class="form-select" required>
                                    <option value="">-- Pilih Komoditas --</option>
                                    <option value="1">Beras Premium</option>
                                    <option value="2">Beras Medium</option>
                                    <option value="3">Minyak Goreng</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-bar-chart-alt'></i> Tingkat Kelangkaan</label>
                                <select id="severity" name="severity" class="form-select" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="LOW">Sedikit Langka</option>
                                    <option value="MEDIUM">Cukup Langka</option>
                                    <option value="HIGH">Sangat Langka</option>
                                    <option value="CRITICAL">Tidak Tersedia</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-note'></i> Deskripsi</label>
                                <textarea id="description" name="description" rows="3" class="form-control" placeholder="Jelaskan kondisi kelangkaan..."></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-map'></i> Provinsi</label>
                                <select id="dearth_province_select" name="province_id" class="form-select select2">
                                    <option value="">-- Otomatis dari Peta --</option>
                                </select>
                                <small class="text-muted">Pilih manual atau klik ganda di peta</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-buildings'></i> Kabupaten/Kota</label>
                                <select id="dearth_regency_select" name="regency_id" class="form-select select2">
                                    <option value="">-- Pilih Kabupaten/Kota --</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-map-pin'></i> Kecamatan</label>
                                <select id="dearth_district_select" name="district_id" class="form-select select2">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label"><i class='bx bx-home'></i> Desa/Kelurahan</label>
                                <select id="dearth_village_select" name="village_id" class="form-select select2">
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
                            </div>

                            <div class="col-6">
                                <label class="form-label"><i class='bx bx-current-location'></i> Latitude</label>
                                <input id="dearth_lat" name="lat" type="number" class="form-control" step="0.0000001" readonly required />
                            </div>
                            <div class="col-6">
                                <label class="form-label"><i class='bx bx-current-location'></i> Longitude</label>
                                <input id="dearth_lng" name="lng" type="number" class="form-control" step="0.0000001" readonly required />
                            </div>

                            <div id="dearthLocationInfo" class="location-info" style="display:none;"></div>

                            <div class="col-12">
                                <button id="dearthSubmitBtn" type="submit" class="btn btn-primary w-100">
                                    <i class='bx bx-send'></i> Kirim Laporan Kelangkaan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div> <!-- /Right -->
        </div>
    </div>

    <!-- ===== Scripts ===== -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        const API_BASE_URL = '/api';
        let map, marker, trendChart = null;
        let currentMode = 'price';
        // Flag untuk mencegah race condition saat autofill dropdown
        let isAutoFilling = false;

        $(function () {
            $('.select2').select2({ theme: 'bootstrap-5', width:'100%', allowClear:true });

            initializeMap();
            loadProvinces();
            setupEventListeners();
            loadTrendChart();
        });

        function initializeMap(){
            map = L.map('map', { zoomControl: true }).setView([-6.9175, 107.6191], 10);

            // Layer dasar
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors', maxZoom: 19
            }).addTo(map);

            // Batas area kasar Jawa Barat
            const bounds = L.latLngBounds(L.latLng(-7.8,106.5), L.latLng(-6.0,108.8));
            map.setMaxBounds(bounds);
            map.on('drag', () => map.panInsideBounds(bounds, { animate:false }));

            // Event
            map.on('dblclick', handleMapDoubleClick);

            // Lokasi user jika tersedia
            if(navigator.geolocation){
                navigator.geolocation.getCurrentPosition(pos=>{
                    map.setView([pos.coords.latitude, pos.coords.longitude], 13);
                }, ()=>{});
            }

            // Kontrol tambahan
            L.control.scale({ metric: true, imperial: false }).addTo(map);
        }

        function handleMapDoubleClick(e){
            const lat = e.latlng.lat.toFixed(6);
            const lng = e.latlng.lng.toFixed(6);

            if (marker) map.removeLayer(marker);
            marker = L.marker([lat,lng], { draggable:true }).addTo(map);
            marker.bindPopup(`Lokasi Laporan<br>Lat: ${lat}<br>Lng: ${lng}`).openPopup();

            const prefix = currentMode === 'price' ? 'price' : 'dearth';
            $(`#${prefix}_lat`).val(lat);
            $(`#${prefix}_lng`).val(lng);

            getAdministrativeData(lat,lng);

            marker.on('dragend', function(ev){
                const p = ev.target.getLatLng();
                const newLat = p.lat.toFixed(6), newLng = p.lng.toFixed(6);
                $(`#${prefix}_lat`).val(newLat);
                $(`#${prefix}_lng`).val(newLng);
                marker.setPopupContent(`Lokasi Laporan<br>Lat: ${newLat}<br>Lng: ${newLng}`);
                getAdministrativeData(newLat,newLng);
            });
        }

        // ========= Loaders (return Promise) =========
        function loadProvinces(){
            return $.get('/regencies/provinces').then(res=>{
                let opt = '<option value="">-- Otomatis dari Peta --</option>';
                res.data.forEach(i=> opt += `<option value="${i.id}">${i.name}</option>`);
                $('#price_province_select').html(opt).trigger('change.select2');
                $('#dearth_province_select').html(opt).trigger('change.select2');
            });
        }

        function loadRegencies(prefix, provinceId, selectedId=null){
            const $reg = $(`#${prefix}_regency_select`);
            const $dist = $(`#${prefix}_district_select`);
            const $vill = $(`#${prefix}_village_select`);

            if(!provinceId){
                $reg.html('<option value="">-- Pilih Kabupaten/Kota --</option>').trigger('change.select2');
                $dist.html('<option value="">-- Pilih Kecamatan --</option>').trigger('change.select2');
                $vill.html('<option value="">-- Pilih Desa/Kelurahan --</option>').trigger('change.select2');
                return Promise.resolve();
            }

            return $.get(`/regencies/${provinceId}`).then(res=>{
                let opt = '<option value="">-- Pilih Kabupaten/Kota --</option>';
                res.data.forEach(i=> opt += `<option value="${i.id}">${i.name}</option>`);
                $reg.html(opt);
                if (selectedId) $reg.val(String(selectedId));
                $reg.trigger('change.select2');
            });
        }

        function loadDistricts(prefix, regencyId, selectedId=null){
            const $dist = $(`#${prefix}_district_select`);
            const $vill = $(`#${prefix}_village_select`);

            if(!regencyId){
                $dist.html('<option value="">-- Pilih Kecamatan --</option>').trigger('change.select2');
                $vill.html('<option value="">-- Pilih Desa/Kelurahan --</option>').trigger('change.select2');
                return Promise.resolve();
            }

            return $.get(`/districts/${regencyId}`).then(res=>{
                let opt = '<option value="">-- Pilih Kecamatan --</option>';
                res.data.forEach(i=> opt += `<option value="${i.id}">${i.name}</option>`);
                $dist.html(opt);
                if (selectedId) $dist.val(String(selectedId));
                $dist.trigger('change.select2');
            });
        }

        function loadVillages(prefix, districtId, selectedId=null){
            const $vill = $(`#${prefix}_village_select`);

            if(!districtId){
                $vill.html('<option value="">-- Pilih Desa/Kelurahan --</option>').trigger('change.select2');
                return Promise.resolve();
            }

            return $.get(`/villages/${districtId}`).then(res=>{
                let opt = '<option value="">-- Pilih Desa/Kelurahan --</option>';
                res.data.forEach(i=> opt += `<option value="${i.id}">${i.name}</option>`);
                $vill.html(opt);
                if (selectedId) $vill.val(String(selectedId));
                $vill.trigger('change.select2');
            });
        }

        // ========= Reverse Geocoding =========
        async function getAdministrativeData(lat,lng){
            $('#loadingGeocode').show();
            const prefix = currentMode === 'price' ? 'price' : 'dearth';

            try{
                const res = await $.get(`${API_BASE_URL}/reverse-geocode`, { lat, lng });
                if(res.success && res.data){
                    const d = res.data;

                    // Autofill terkendali (hindari trigger handler user)
                    isAutoFilling = true;

                    if (d.province_id) {
                        $(`#${prefix}_province_select`).val(d.province_id).trigger('change.select2');
                        await loadRegencies(prefix, d.province_id, d.regency_id);
                        if (d.regency_id) await loadDistricts(prefix, d.regency_id, d.district_id);
                        if (d.district_id) await loadVillages(prefix, d.district_id, d.village_id);
                    }

                    // Sinkronisasi terakhir (pastikan terlihat selected)
                    if (d.regency_id)  $(`#${prefix}_regency_select`).val(d.regency_id).trigger('change.select2');
                    if (d.district_id) $(`#${prefix}_district_select`).val(d.district_id).trigger('change.select2');
                    if (d.village_id)  $(`#${prefix}_village_select`).val(d.village_id).trigger('change.select2');

                    isAutoFilling = false;

                    const $info = $(`#${prefix}LocationInfo`);
                    $info.show().html(`
                        <strong><i class='bx bx-map-pin'></i> Lokasi Terdeteksi</strong><br>
                        ${d.village_name ? `Desa: ${d.village_name}<br>` : ``}
                        ${d.district_name ? `Kec: ${d.district_name}<br>` : ``}
                        ${d.regency_name ? `Kab: ${d.regency_name}<br>` : ``}
                        ${d.province_name ? `Prov: ${d.province_name}<br>` : ``}
                        <small class="text-muted">Jarak ~${d.distance} km dari titik terdekat</small>
                    `);

                    showAlert(`${prefix}AlertContainer`, 'success', 'Data lokasi berhasil diambil. Periksa dropdown di atas.');
                }else{
                    showAlert(`${prefix}AlertContainer`, 'info', 'Lokasi tidak ditemukan. Pilih manual.');
                }
            }catch(err){
                showAlert(`${prefix}AlertContainer`, 'warning', 'Gagal mengambil data administratif. Pilih manual.');
            }finally{
                $('#loadingGeocode').hide();
            }
        }

        // ========= Event Listeners =========
        function setupEventListeners(){
            // Proteksi cascading saat autofill
            $('#price_province_select').on('change', function(){ if(isAutoFilling) return; loadRegencies('price', $(this).val()); });
            $('#price_regency_select').on('change', function(){ if(isAutoFilling) return; loadDistricts('price', $(this).val()); });
            $('#price_district_select').on('change', function(){ if(isAutoFilling) return; loadVillages('price', $(this).val()); });

            $('#dearth_province_select').on('change', function(){ if(isAutoFilling) return; loadRegencies('dearth', $(this).val()); });
            $('#dearth_regency_select').on('change', function(){ if(isAutoFilling) return; loadDistricts('dearth', $(this).val()); });
            $('#dearth_district_select').on('change', function(){ if(isAutoFilling) return; loadVillages('dearth', $(this).val()); });

            // Submit
            $('#priceForm').on('submit', handlePriceFormSubmit);
            $('#dearthForm').on('submit', handleDearthFormSubmit);

            // Reload chart saat komoditas berubah
            $('#price_commodity_id').on('change', loadTrendChart);
            $('#dearth_commodity_id').on('change', loadTrendChart);
        }

        // ========= Submit Handlers =========
        function handlePriceFormSubmit(e){
            e.preventDefault();
            if (!$('#price_lat').val() || !$('#price_lng').val()){
                showAlert('priceAlertContainer','warning','Tandai lokasi pada peta terlebih dahulu!');
                return;
            }

            const btn = $('#priceSubmitBtn');
            btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Mengirim…');

            const data = {
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
                url: `${API_BASE_URL}/reports`, type: 'POST', data,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            }).done(res=>{
                showAlert('priceAlertContainer','success', res.message || 'Laporan harga berhasil dikirim.');
                $('#price').val('');
                loadTrendChart();
            }).fail(xhr=>{
                const msg = xhr.responseJSON?.message || 'Gagal mengirim laporan.';
                showAlert('priceAlertContainer','danger', msg);
            }).always(()=>{
                btn.prop('disabled', false).html('<i class="bx bx-send"></i> Kirim Laporan Harga');
            });
        }

        function handleDearthFormSubmit(e){
            e.preventDefault();
            if (!$('#dearth_lat').val() || !$('#dearth_lng').val()){
                showAlert('dearthAlertContainer','warning','Tandai lokasi pada peta terlebih dahulu!');
                return;
            }

            const btn = $('#dearthSubmitBtn');
            btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Mengirim…');

            const data = {
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
                url: `${API_BASE_URL}/dearth/reports`, type: 'POST', data,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            }).done(res=>{
                showAlert('dearthAlertContainer','success', res.message || 'Laporan kelangkaan berhasil dikirim.');
                $('#description').val('');
                loadTrendChart();
            }).fail(xhr=>{
                const msg = xhr.responseJSON?.message || 'Gagal mengirim laporan.';
                showAlert('dearthAlertContainer','danger', msg);
            }).always(()=>{
                btn.prop('disabled', false).html('<i class="bx bx-send"></i> Kirim Laporan Kelangkaan');
            });
        }

        // ========= Tab Switch =========
        function switchTab(mode){
            currentMode = mode;
            $('#priceTab').toggleClass('btn-light', mode==='price').toggleClass('btn-outline-light text-white', mode!=='price');
            $('#dearthTab').toggleClass('btn-light', mode==='dearth').toggleClass('btn-outline-light text-white', mode!=='dearth');
            $('#priceFormCard').toggle(mode==='price');
            $('#dearthFormCard').toggle(mode==='dearth');
            $('#mapLegend').toggle(mode==='dearth');

            $('#mapInfo').html(
                mode==='price'
                ? `<i class="bx bx-info-circle"></i> <strong>Mode: Laporan Harga</strong> — Klik ganda pada peta untuk menandai lokasi.`
                : `<i class="bx bx-info-circle"></i> <strong>Mode: Laporan Kelangkaan</strong> — Klik ganda pada peta untuk menandai lokasi.`
            );

            if (marker){ map.removeLayer(marker); marker = null; }
            loadTrendChart();
        }

        // ========= Chart =========
        function loadTrendChart(){
            const sel = currentMode === 'price' ? $('#price_commodity_id') : $('#dearth_commodity_id');
            const commodityId = sel.val();

            $.get(`${API_BASE_URL}/trend`, { type: currentMode, days: 30, commodity_id: commodityId || '' })
            .done(res=>{ (res.success && res.data?.length) ? renderTrendChart(res.data) : renderEmptyChart(); })
            .fail(()=> renderEmptyChart());
        }

        function renderTrendChart(data){
            const canvas = document.getElementById('trendChart'); if(!canvas) return;
            if (trendChart){ trendChart.destroy(); trendChart = null; }
            const ctx = canvas.getContext('2d');

            if (currentMode === 'price'){
                const labels = data.map(d=> new Date(d.date).toLocaleDateString('id-ID',{month:'short',day:'numeric'}));
                const avg = data.map(d=> parseFloat(d.avg_price)||0);
                const min = data.map(d=> parseFloat(d.min_price)||0);
                const max = data.map(d=> parseFloat(d.max_price)||0);

                trendChart = new Chart(ctx, {
                    type:'line',
                    data:{ labels,
                        datasets:[
                            { label:'Harga Rata-rata', data:avg, borderColor:'#FF8C42', backgroundColor:'rgba(255,140,66,.1)', borderWidth:3, tension:.35, fill:true },
                            { label:'Harga Minimum', data:min, borderColor:'#2ECC71', borderWidth:2, borderDash:[5,5], tension:.35, fill:false },
                            { label:'Harga Maksimum', data:max, borderColor:'#E74C3C', borderWidth:2, borderDash:[5,5], tension:.35, fill:false },
                        ]
                    },
                    options:{
                        responsive:true, maintainAspectRatio:true, aspectRatio:2,
                        plugins:{
                            legend:{ display:true, position:'top' },
                            title:{ display:true, text:'Tren Harga Komoditas — 30 Hari Terakhir', font:{ size:15, weight:'bold' } },
                            tooltip:{ callbacks:{ label:(c)=> `${c.dataset.label}: Rp ${c.parsed.y.toLocaleString('id-ID')}` } }
                        },
                        scales:{
                            y:{ beginAtZero:false, ticks:{ callback:(v)=> 'Rp ' + Number(v).toLocaleString('id-ID') }, title:{ display:true, text:'Harga (Rp)' } },
                            x:{ title:{ display:true, text:'Tanggal' } }
                        }
                    }
                });
            }else{
                const labels = data.map(d=> new Date(d.date).toLocaleDateString('id-ID',{month:'short',day:'numeric'}));
                const critical = data.map(d=> parseInt(d.critical_count)||0);
                const high = data.map(d=> parseInt(d.high_count)||0);
                const medium = data.map(d=> parseInt(d.medium_count)||0);
                const low = data.map(d=> parseInt(d.low_count)||0);

                trendChart = new Chart(ctx, {
                    type:'bar',
                    data:{ labels,
                        datasets:[
                            { label:'Kritis', data:critical, backgroundColor:'#E74C3C', borderRadius:4 },
                            { label:'Rawan', data:high, backgroundColor:'#E67E22', borderRadius:4 },
                            { label:'Waspada', data:medium, backgroundColor:'#F39C12', borderRadius:4 },
                            { label:'Rendah', data:low, backgroundColor:'#95A5A6', borderRadius:4 },
                        ]
                    },
                    options:{
                        responsive:true, maintainAspectRatio:true, aspectRatio:2,
                        plugins:{ legend:{ display:true, position:'top' }, title:{ display:true, text:'Tren Kelangkaan — 30 Hari Terakhir', font:{ size:15, weight:'bold' } } },
                        scales:{
                            y:{ stacked:true, beginAtZero:true, ticks:{ stepSize:1 }, title:{ display:true, text:'Jumlah Laporan' } },
                            x:{ stacked:true, title:{ display:true, text:'Tanggal' } }
                        }
                    }
                });
            }
        }

        function renderEmptyChart(){
            const canvas = document.getElementById('trendChart'); if(!canvas) return;
            if (trendChart){ trendChart.destroy(); trendChart = null; }
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0,0,canvas.width,canvas.height);
            ctx.font = '15px system-ui, -apple-system, Segoe UI'; ctx.fillStyle = '#999';
            ctx.textAlign = 'center'; ctx.fillText('Belum ada data laporan', canvas.width/2, canvas.height/2);
        }

        // ========= Alerts =========
        function showAlert(containerId, type, message){
            const icon = { success:'bx-check-circle', danger:'bx-error', warning:'bx-error-circle', info:'bx-info-circle' }[type] || 'bx-info-circle';
            $(`#${containerId}`).html(`
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class='bx ${icon}'></i> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            setTimeout(()=>{
                const el = $(`#${containerId} .alert`);
                if(el.length){ el.removeClass('show'); setTimeout(()=> $(`#${containerId}`).html(''), 300); }
            }, 5000);
        }
    </script>
</body>
</html>
