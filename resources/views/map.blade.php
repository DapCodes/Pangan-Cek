<!DOCTYPE html>
<html lang="id">
<head>
  <!-- ===== Meta & Title ===== -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>PanganCek — Monitoring Harga & Kelangkaan Sembako Jawa Barat</title>

  <!-- ===== Fonts & Styles ===== -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

  <style>
    /* =========================================================
       TOKENS
       ========================================================= */
    :root{
      --primary:#ff7a59;--primary-2:#ffb86c;--secondary:#5f7cff;
      --accent:#2ed573;--accent-2:#00d2d3;--dark:#1f2d3d;
      --bg:#0f172a;--card:#0b1220;--border:rgba(255,255,255,.08);
      --ring:rgba(255,122,89,.35);--ring-2:rgba(95,124,255,.25);

      /* Chart palette */
      --chart-avg:#ffd166;--chart-min:#00e6a7;--chart-max:#ff5b6e;
      --chart-grid:rgba(148,163,184,.18);--chart-axis:#cbd5e1;--chart-title:#e2e8f0;

      /* Unified control tokens */
      --control-bg: rgba(255,255,255,.03);
      --control-bg-hover: rgba(255,255,255,.05);
      --control-border: rgba(255,255,255,.12);
      --control-radius: 12px;
      --control-padding-y: .65rem;
      --control-padding-x: .9rem;
      --control-placeholder: #94a3b8;
      --control-text: #e5e7eb;
      --dropdown-bg: #0a1220;
      --dropdown-border: rgba(255,255,255,.08);
      --dropdown-item: #dbeafe;
      --dropdown-item-hover: #111827;
      --dropdown-item-active-bg: rgba(95,124,255,.25);
      --dropdown-item-active-text: #ffffff;
    }

    /* =========================================================
       BASE
       ========================================================= */
    html,body{
      height:100%;
      background:
        radial-gradient(1200px 1200px at -10% -10%, rgba(255,122,89,.18), transparent 50%),
        radial-gradient(900px 900px at 110% 0%, rgba(95,124,255,.20), transparent 55%),
        radial-gradient(900px 900px at 120% 120%, rgba(46,213,115,.15), transparent 55%),
        var(--bg);
      color:#eef2ff;
      font-family:'Poppins',system-ui,-apple-system,Segoe UI,Roboto,Oxygen,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif;
      letter-spacing:.1px;
    }

    /* =========================================================
       NAVBAR
       ========================================================= */
    .navbar{
      background:linear-gradient(135deg, rgba(255,122,89,.95) 0%, rgba(255,184,108,.95) 50%, rgba(95,124,255,.95) 100%);
      box-shadow:0 10px 30px rgba(255,122,89,.25), inset 0 -1px 0 rgba(255,255,255,.12);
      backdrop-filter:blur(10px);
      border-bottom:1px solid rgba(255,255,255,.12);
    }
    .navbar-brand{color:#fff!important;font-weight:700;letter-spacing:.3px;display:flex;align-items:center;gap:.6rem;text-shadow:0 2px 10px rgba(0,0,0,.25);}
    .navbar .nav-pills .btn{border-radius:999px;padding:.55rem 1rem;box-shadow:0 10px 20px rgba(0,0,0,.08);transition:transform .15s ease, box-shadow .2s ease, opacity .2s ease;}
    .navbar .nav-pills .btn:hover{transform:translateY(-1px);box-shadow:0 14px 30px rgba(0,0,0,.12);opacity:.96;}

    /* =========================================================
       LAYOUT & CARD
       ========================================================= */
    .container-xxl{padding-block:20px;}
    .page-grid{display:grid;grid-template-columns:2fr 1fr;gap:20px;}
    @media (max-width: 992px){.page-grid{grid-template-columns:1fr;}}
    .card{
      background:linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,.01)) padding-box,
      linear-gradient(135deg, rgba(255,122,89,.5), rgba(95,124,255,.4)) border-box;
      border:1px solid transparent;border-radius:16px;
      box-shadow:0 10px 30px rgba(0,0,0,.25), inset 0 0 0 1px rgba(255,255,255,.02);
      overflow:hidden;
    }
    .card-header{
      background:linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
      border-bottom:1px dashed var(--border);border-radius:16px 16px 0 0!important;
      font-weight:700;display:flex;align-items:center;gap:.6rem;padding:.9rem 1.1rem;color:#f8fafc;
    }
    .card-body{padding:1rem 1.1rem 1.2rem;}

    /* =========================================================
       MAP + INFO
       ========================================================= */
    #map{height:520px;border-radius:12px;box-shadow:inset 0 0 0 1px rgba(255,255,255,.04);outline:1px solid rgba(255,255,255,.06);}
    .map-info{
      background:linear-gradient(180deg, rgba(95,124,255,.14), rgba(95,124,255,.08));
      border:1px dashed rgba(95,124,255,.35);padding:.8rem 1rem;border-radius:10px;margin-bottom:.75rem;font-size:.95rem;color:#dbeafe;
    }
    .geo-spinner{display:none;margin:.5rem 0 0;font-size:.875rem;color:#cbd5e1;}

    /* =========================================================
       UNIFIED FORM CONTROLS (INPUT + SELECT + SELECT2)
       ========================================================= */
    .form-label{font-weight:600;display:flex;gap:.45rem;align-items:center;color:#e5e7eb;}
    .form-control,
    .form-select{
      background:var(--control-bg);
      border:1px solid var(--control-border);
      color:var(--control-text);
      padding:var(--control-padding-y) var(--control-padding-x);
      border-radius:var(--control-radius);
      height:auto; /* biar tinggi seragam */
      transition:border-color .15s ease, box-shadow .15s ease, background .15s ease;
    }
    .form-control:hover,
    .form-select:hover{background:var(--control-bg-hover);}
    .form-control:focus,
    .form-select:focus{
      border-color:transparent;
      box-shadow:0 0 0 .2rem var(--ring), 0 0 0 .35rem var(--ring-2);
      background:var(--control-bg-hover);
      color:var(--control-text);
    }
    ::placeholder{color:var(--control-placeholder)!important;}

    /* Input group match style */
    .input-group-text{
      background:var(--control-bg)!important;
      border:1px solid var(--control-border)!important;
      border-right:0!important;
      color:var(--control-text)!important;
      border-radius:var(--control-radius) 0 0 var(--control-radius)!important;
    }
    .input-group .form-control{
      border-left:0!important;
      border-radius:0 var(--control-radius) var(--control-radius) 0!important;
    }

    /* Select2 (field) — seragam dengan input */
    .select2-container--bootstrap-5 .select2-selection{
      background:var(--control-bg)!important;
      border:1px solid var(--control-border)!important;
      border-radius:var(--control-radius)!important;
      min-height:44px;
      padding:.35rem .55rem;
      color:var(--control-text)!important;
    }
    .select2-container--bootstrap-5 .select2-selection:hover{background:var(--control-bg-hover)!important;}
    .select2-container--bootstrap-5 .select2-selection:focus{
      border-color:transparent!important;
      box-shadow:0 0 0 .2rem var(--ring), 0 0 0 .35rem var(--ring-2)!important;
      background:var(--control-bg-hover)!important;
    }
    .select2-container--bootstrap-5 .select2-selection__rendered{color:var(--control-text)!important;}
    .select2-container--bootstrap-5 .select2-selection__placeholder{color:var(--control-placeholder)!important;}
    .select2-container--bootstrap-5 .select2-selection__arrow b{border-color:var(--control-text) transparent transparent transparent!important;}

    /* Select2 (dropdown list) — dark & senada */
    .select2-dropdown{
      background:var(--dropdown-bg)!important;
      border:1px solid var(--dropdown-border)!important;
      border-radius:12px!important;
      box-shadow:0 10px 30px rgba(0,0,0,.35);
      overflow:hidden;
    }
    .select2-results__option{
      color:var(--dropdown-item)!important;
      padding:.55rem .75rem!important;
    }
    .select2-results__option--highlighted{
      background:var(--dropdown-item-active-bg)!important;
      color:var(--dropdown-item-active-text)!important;
    }
    .select2-search--dropdown .select2-search__field{
      background:rgba(255,255,255,.06)!important;
      border:1px solid var(--control-border)!important;
      color:#fff!important;border-radius:10px!important;
    }

    /* =========================================================
       LEGEND & INFO
       ========================================================= */
    .legend{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:.75rem;}
    .legend-item{display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.03);padding:.5rem .6rem;border-radius:10px;border:1px solid var(--border);}
    .legend-color{width:20px;height:20px;border-radius:4px;border:1px solid rgba(255,255,255,.15);box-shadow:inset 0 0 0 1px rgba(0,0,0,.12);}
    .location-info{background:linear-gradient(180deg, rgba(46,213,115,.15), rgba(46,213,115,.08));border:1px dashed rgba(46,213,115,.35);padding:.7rem .9rem;border-radius:10px;font-size:.92rem;color:#eafff2;}

    /* =========================================================
       CHART
       ========================================================= */
    .chart-container{
      height:360px;background:rgba(9,14,26,.88);
      border-radius:12px;border:1px solid rgba(255,255,255,.06);
      box-shadow:inset 0 0 0 1px rgba(255,255,255,.02);padding:.6rem;
    }
    #trendChart{background:transparent!important;}

    /* Alerts */
    .alert{border-radius:12px;border:1px solid transparent;color:#0b1220;font-weight:600;}
    .alert-success{background:linear-gradient(180deg,#87f5a2,#49e38b);box-shadow:0 10px 20px rgba(46,213,115,.25);}
    .alert-danger{background:linear-gradient(180deg,#ff9aa2,#ff6b6b);box-shadow:0 10px 20px rgba(255,107,107,.25);}
    .alert-warning{background:linear-gradient(180deg,#ffe08a,#ffc048);box-shadow:0 10px 20px rgba(255,192,72,.25);}
    .alert-info{background:linear-gradient(180deg,#a0c4ff,#5f7cff);box-shadow:0 10px 20px rgba(95,124,255,.25);}

    .leaflet-container:focus,
    .leaflet-container:focus-visible,
    .leaflet-container .leaflet-interactive:focus,
    .leaflet-container .leaflet-marker-icon:focus,
    .leaflet-container .leaflet-pane img:focus{outline:none!important;}

    /* Ornamen halus */
    .ornament{pointer-events:none;position:fixed;inset:0;z-index:-1;opacity:.2;
      background-image:radial-gradient(#fff 1px,transparent 1px);background-size:18px 18px;
      mask-image:radial-gradient(65% 55% at 10% 0%, rgba(0,0,0,1), transparent 70%),
                  radial-gradient(60% 50% at 100% 100%, rgba(0,0,0,1), transparent 70%);
      animation:drift 24s linear infinite;}
    @keyframes drift{0%{transform:translateY(0)}50%{transform:translateY(-12px)}100%{transform:translateY(0)}}
  </style>
</head>

<body>
  <div class="ornament"></div>

  <!-- ===== Navbar ===== -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-xxl position-relative">
      <a class="navbar-brand" href="#"><i class="bx bx-shopping-bag fs-4"></i> PanganCek Jawa Barat</a>
      <ul class="nav nav-pills ms-auto gap-2">
        <li class="nav-item">
          <a id="priceTab" class="btn btn-sm btn-light fw-semibold" href="#" onclick="switchTab('price'); return false;">
            <i class='bx bx-money'></i> Laporan Harga
          </a>
        </li>
        <li class="nav-item">
          <a id="dearthTab" class="btn btn-sm btn-outline-light text-white fw-semibold" href="#"
             onclick="switchTab('dearth'); return false;">
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
        <div class="card position-relative">
          <div class="card-body">
            <div id="mapInfo" class="map-info">
              <i class='bx bx-info-circle'></i>
              <strong>Mode: Laporan Harga</strong> — Klik ganda pada peta untuk menandai lokasi. Data administratif akan terisi otomatis.
              <div id="loadingGeocode" class="geo-spinner"><i class='bx bx-loader bx-spin'></i> Mengambil data lokasi…</div>
            </div>
            <div id="map"></div>

            <div id="mapLegend" class="legend" style="display:none;">
              <div class="legend-item"><span class="legend-color" style="background:#2ECC71"></span><span><strong>Aman</strong> — Tidak ada kelangkaan</span></div>
              <div class="legend-item"><span class="legend-color" style="background:#F39C12"></span><span><strong>Waspada</strong> — Sedikit langka</span></div>
              <div class="legend-item"><span class="legend-color" style="background:#E67E22"></span><span><strong>Rawan</strong> — Cukup langka</span></div>
              <div class="legend-item"><span class="legend-color" style="background:#E74C3C"></span><span><strong>Kritis</strong> — Sangat langka</span></div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><i class='bx bx-line-chart'></i> Tren 30 Hari Terakhir</div>
          <div class="card-body">
            <div class="chart-container"><canvas id="trendChart"></canvas></div>
          </div>
        </div>
      </div>

      <!-- Right: Forms -->
      <div class="d-flex flex-column gap-3">
        <!-- Form Harga -->
        <div id="priceFormCard" class="card">
          <div class="card-header"><i class='bx bx-edit'></i> Form Laporan Harga</div>
          <div class="card-body">
            <div id="priceAlertContainer"></div>
            <form id="priceForm" class="row g-3">
              <div class="col-12">
                <label class="form-label"><i class='bx bx-package'></i> Komoditas</label>
                <!-- Ambil dari $commodities (sudah dikirim dari controller) -->
                <select id="price_commodity_id" name="commodity_id" class="form-select select2" data-placeholder="Pilih komoditas…" required>
                  <option value=""></option>
                  @php
                    $grouped = $commodities->groupBy('category');
                  @endphp
                  @foreach($grouped as $cat => $items)
                    <optgroup label="{{ $cat ?? 'Komoditas' }}">
                      @foreach($items as $c)
                        <option value="{{ $c->id }}">
                          {{ $c->name }}{{ $c->unit ? ' ('.$c->unit.')' : '' }}
                        </option>
                      @endforeach
                    </optgroup>
                  @endforeach
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
                <select id="price_province_select" name="province_id" class="form-select select2" data-placeholder="Otomatis dari peta">
                  <option value=""></option>
                </select>
                <small class="text-muted">Pilih manual atau klik ganda di peta</small>
              </div>

              <div class="col-12">
                <label class="form-label"><i class='bx bx-buildings'></i> Kabupaten/Kota</label>
                <select id="price_regency_select" name="regency_id" class="form-select select2" data-placeholder="Pilih Kabupaten/Kota">
                  <option value=""></option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label"><i class='bx bx-map-pin'></i> Kecamatan</label>
                <select id="price_district_select" name="district_id" class="form-select select2" data-placeholder="Pilih Kecamatan">
                  <option value=""></option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label"><i class='bx bx-home'></i> Desa/Kelurahan</label>
                <select id="price_village_select" name="village_id" class="form-select select2" data-placeholder="Pilih Desa/Kelurahan">
                  <option value=""></option>
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

        <!-- Form Kelangkaan -->
        <div id="dearthFormCard" class="card" style="display:none;">
          <div class="card-header"><i class='bx bx-error-circle'></i> Form Laporan Kelangkaan</div>
          <div class="card-body">
            <div id="dearthAlertContainer"></div>
            <form id="dearthForm" class="row g-3">
              <div class="col-12">
                <label class="form-label"><i class='bx bx-package'></i> Komoditas</label>
                <!-- Ambil dari $commodities (seragam dengan form harga) -->
                <select id="dearth_commodity_id" name="commodity_id" class="form-select select2" data-placeholder="Pilih komoditas…" required>
                  <option value=""></option>
                  @foreach($grouped as $cat => $items)
                    <optgroup label="{{ $cat ?? 'Komoditas' }}">
                      @foreach($items as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                      @endforeach
                    </optgroup>
                  @endforeach
                </select>
              </div>

              <div class="col-12">
                <label class="form-label"><i class='bx bx-bar-chart-alt'></i> Tingkat Kelangkaan</label>
                <select id="severity" name="severity" class="form-select select2" data-placeholder="Pilih tingkat…" required>
                  <option value=""></option>
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
                <select id="dearth_province_select" name="province_id" class="form-select select2" data-placeholder="Otomatis dari peta">
                  <option value=""></option>
                </select>
                <small class="text-muted">Pilih manual atau klik ganda di peta</small>
              </div>

              <div class="col-12">
                <label class="form-label"><i class='bx bx-buildings'></i> Kabupaten/Kota</label>
                <select id="dearth_regency_select" name="regency_id" class="form-select select2" data-placeholder="Pilih Kabupaten/Kota">
                  <option value=""></option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label"><i class='bx bx-map-pin'></i> Kecamatan</label>
                <select id="dearth_district_select" name="district_id" class="form-select select2" data-placeholder="Pilih Kecamatan">
                  <option value=""></option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label"><i class='bx bx-home'></i> Desa/Kelurahan</label>
                <select id="dearth_village_select" name="village_id" class="form-select select2" data-placeholder="Pilih Desa/Kelurahan">
                  <option value=""></option>
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
    const PROV_GEOJSON_URL = "{{ asset('indonesia-province.json') }}";
    const KAB_GEOJSON_URL  = "{{ asset('indonesia-kabupaten.json') }}";

    let map, marker, trendChart = null;
    let currentMode = 'price';
    let isAutoFilling = false;

    let provinceLayer = null, kabupatenLayer = null, layerControl = null;
    let dearthStatsByRegencyId = {};

    const norm = (s) => (s ?? '').toString().trim().toUpperCase().replace(/\s+/g, ' ').replace(/[^\w\s-]/g, '');

    function scoreToColor(avg){
      if(avg===0) return '#2ECC71';
      if(avg<=1) return '#F1C40F';
      if(avg<=2) return '#E67E22';
      return '#E74C3C';
    }

    function tooltipHtml(name, stats, commodityName){
      const title = commodityName ? `${name} — <small>${commodityName}</small>` : name;
      if(!stats){
        return `<div><strong>${title}</strong><br>Tidak ada laporan untuk komoditas terpilih.</div>`;
      }
      const c = stats.counts || {};
      return `<div><strong>${title}</strong><br>Rata-rata kelangkaan: <b>${stats.avg.toFixed(2)}</b><br>Total laporan: ${stats.total}<br>
              <small>LOW: ${c.LOW || 0} • MED: ${c.MEDIUM || 0} • HIGH: ${c.HIGH || 0} • CRIT: ${c.CRITICAL || 0}</small></div>`;
    }

    function isJawaBaratProvince(feature){
      const p = feature.properties || {};
      const cand = [p.name, p.NAMOBJ, p.Propinsi, p.PROVINSI, p.provinsi, p.WADMPR].map(norm);
      return cand.includes('JAWA BARAT');
    }
    function isKabupatenInJawaBarat(feature){
      const p = feature.properties || {};
      const provCand = [p.WADMPR, p.PROVINSI, p.Provinsi, p.provinsi, p.PROV, p.PROVNAME].map(norm);
      return provCand.includes('JAWA BARAT');
    }
    function getKabupatenCode(feature){
      const p = feature.properties || {};
      let raw = p.KDPKAB || p.KDCBPS || p.KDBBPS || p.KDCPUM || p.KDEBPS || p.KAB_KODE || p.kode || p.KODE_KAB;
      if(!raw) return null;
      const justDigits = String(raw).match(/\d+/g);
      if(!justDigits) return null;
      const digits = justDigits.join('');
      return digits.length >= 4 ? digits.slice(0,4) : digits;
    }
    function getKabupatenName(feature){
      const p = feature.properties || {};
      return p.WADMKK || p.KABUPATEN || p.KAB_KOTA || p.name || p.NAMA || p.KABKOT || 'Kabupaten/Kota';
    }

    // ===== Inisialisasi halaman =====
    $(function(){
      // Select2: samakan semua dropdown agar placeholder aktif & tema konsisten
      $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        allowClear: true,
        placeholder: function(){
          return $(this).data('placeholder') || 'Pilih opsi…';
        },
        minimumResultsForSearch: 5, // pencarian muncul kalau itemnya cukup
        dropdownAutoWidth: true
      });

      initializeMap();
      loadProvinces();
      setupEventListeners();

      // Chart.js defaults
      Chart.defaults.font.family = "'Poppins', system-ui, -apple-system, Segoe UI, Roboto, sans-serif";
      Chart.defaults.color = getComputedStyle(document.documentElement).getPropertyValue('--chart-axis').trim();

      // Load awal
      loadTrendChart();
      loadGeoLayers().then(() => refreshDearthChoropleth());
    });

    // ===== Map & Geo =====
    function initializeMap(){
      map = L.map('map', { zoomControl: true }).setView([-6.9175, 107.6191], 8);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
        attribution:'© OpenStreetMap contributors', maxZoom:19
      }).addTo(map);

      map.on('dblclick', handleMapDoubleClick);
      if(navigator.geolocation){
        navigator.geolocation.getCurrentPosition(
          pos => map.setView([pos.coords.latitude, pos.coords.longitude], 10),
          () => {}
        );
      }
      L.control.scale({ metric:true, imperial:false }).addTo(map);
    }

    async function loadGeoLayers(){
      const provResp = await fetch(PROV_GEOJSON_URL, { cache:'default' });
      const provGeo  = await provResp.json();
      const provJabar = { type:'FeatureCollection', features:(provGeo.features || []).filter(isJawaBaratProvince) };

      if(provinceLayer) map.removeLayer(provinceLayer);
      provinceLayer = L.geoJSON(provJabar,{
        style:{ color:'#7aa2ff', weight:1.2, fillOpacity:.05 },
        onEachFeature:(f,layer)=>{
          const p = f.properties || {};
          const name = p.name || p.NAMOBJ || p.Propinsi || p.WADMPR || p.PROVINSI || 'Jawa Barat';
          layer.bindTooltip(`<b>${name}</b>`, { sticky:true });
        }
      }).addTo(map);

      const kabResp = await fetch(KAB_GEOJSON_URL, { cache:'default' });
      const kabGeo  = await kabResp.json();
      const kabJabar = { type:'FeatureCollection', features:(kabGeo.features || []).filter(isKabupatenInJawaBarat) };

      if(kabupatenLayer) map.removeLayer(kabupatenLayer);
      kabupatenLayer = L.geoJSON(kabJabar,{
        style:(feature)=>{
          const code  = getKabupatenCode(feature);
          const stats = code ? dearthStatsByRegencyId[code] : null;
          const color = (currentMode==='dearth' && stats) ? scoreToColor(stats.avg) : '#5b6b7b';
          return { color:'#9aa5b1', weight:.8, fillColor:color, fillOpacity:(currentMode==='dearth' && stats) ? .55 : .18 };
        },
        onEachFeature:(feature, layer)=>{
          const kabName = getKabupatenName(feature);
          const code    = getKabupatenCode(feature);
          const stats   = code ? dearthStatsByRegencyId[code] : null;
          layer.bindTooltip(tooltipHtml(kabName, stats, getSelectedCommodityName()), { sticky:true });
          layer.on({
            mouseover:(e)=> e.target.setStyle({ weight:2, color:'#e5e7eb' }),
            mouseout:(e)=> kabupatenLayer.resetStyle(e.target),
            click:()=> {
              const s = code ? dearthStatsByRegencyId[code] : null;
              const html = tooltipHtml(kabName, s, getSelectedCommodityName());
              L.popup({ maxWidth:340 }).setLatLng(layer.getBounds().getCenter()).setContent(html).openOn(map);
            }
          });
        }
      }).addTo(map);

      if(layerControl) map.removeControl(layerControl);
      layerControl = L.control.layers({},{
        "Batas Provinsi (Jawa Barat)": provinceLayer,
        "Choropleth Kabupaten (Jawa Barat)": kabupatenLayer
      }, { collapsed:true }).addTo(map);

      try{ map.fitBounds(kabupatenLayer.getBounds(), { padding:[10,10] }); }catch(e){}
    }

    async function refreshDearthChoropleth(){
      const commodityId = $('#dearth_commodity_id').val() || '';
      try{
        const res  = await fetch(`${API_BASE_URL}/dearth/map?days=30&commodity_id=${commodityId}`, { cache:'no-cache' });
        const json = await res.json();
        if(json?.success){
          dearthStatsByRegencyId = {};
          (json.data || []).forEach(item=>{
            const key = String(item.regency_id || '').replace(/\D/g,'');
            dearthStatsByRegencyId[key] = {
              avg: Number(item.average_severity || 0),
              total: Number(item.total_reports || 0),
              counts: item.severity_distribution || {},
              kab: item.kabupaten || null
            };
          });
          if(kabupatenLayer){
            kabupatenLayer.eachLayer(layer=>{
              const code = getKabupatenCode(layer.feature);
              const s = code ? dearthStatsByRegencyId[code] : null;
              const color = (currentMode==='dearth' && s) ? scoreToColor(s.avg) : '#5b6b7b';
              layer.setStyle({ fillColor:color, fillOpacity:(currentMode==='dearth' && s) ? .55 : .18 });
              layer.bindTooltip(tooltipHtml(getKabupatenName(layer.feature), s, getSelectedCommodityName()), { sticky:true });
            });
          }
          updateLegend();
        }
      }catch(e){}
    }

    function getSelectedCommodityName(){
      const $opt = $('#dearth_commodity_id option:selected');
      return $opt.text ? $opt.text().trim() : null;
    }

    function updateLegend(){
      const $legend = $('#mapLegend');
      if(!$legend.length) return;
      if(currentMode!=='dearth'){ $legend.hide(); return; }
      const commodity = getSelectedCommodityName();
      $legend.show().html(`
        <div class="small">
          <strong>Choropleth Kelangkaan</strong><br>
          ${commodity ? `<div>Komoditas: <b>${commodity}</b></div>` : ''}
          <div><span style="display:inline-block;width:10px;height:10px;background:#2ECC71;margin-right:6px;"></span> Avg = 0</div>
          <div><span style="display:inline-block;width:10px;height:10px;background:#F1C40F;margin-right:6px;"></span> 0 &lt; Avg ≤ 1</div>
          <div><span style="display:inline-block;width:10px;height:10px;background:#E67E22;margin-right:6px;"></span> 1 &lt; Avg ≤ 2</div>
          <div><span style="display:inline-block;width:10px;height:10px;background:#E74C3C;margin-right:6px;"></span> Avg &gt; 2</div>
          <div class="text-muted mt-1">*Avg dihitung dari LOW=0, MED=1, HIGH=2, CRIT=3</div>
        </div>`);
    }

    function handleMapDoubleClick(e){
      const lat = e.latlng.lat.toFixed(6);
      const lng = e.latlng.lng.toFixed(6);
      if(marker) map.removeLayer(marker);
      marker = L.marker([lat,lng], { draggable:true }).addTo(map);
      marker.bindPopup(`Lokasi Laporan<br>Lat: ${lat}<br>Lng: ${lng}`).openPopup();

      const prefix = currentMode === 'price' ? 'price' : 'dearth';
      $(`#${prefix}_lat`).val(lat); $(`#${prefix}_lng`).val(lng);
      getAdministrativeData(lat, lng);

      marker.on('dragend', (ev)=>{
        const p = ev.target.getLatLng();
        const newLat = p.lat.toFixed(6), newLng = p.lng.toFixed(6);
        $(`#${prefix}_lat`).val(newLat); $(`#${prefix}_lng`).val(newLng);
        marker.setPopupContent(`Lokasi Laporan<br>Lat: ${newLat}<br>Lng: ${newLng}`);
        getAdministrativeData(newLat, newLng);
      });
    }

    // ===== Wilayah (Select) =====
    function loadProvinces(){
      return $.get('/regencies/provinces').then(res=>{
        let opt = '<option value=""></option>';
        res.data.forEach(i => opt += `<option value="${i.id}">${i.name}</option>`);
        $('#price_province_select').html(opt).trigger('change.select2');
        $('#dearth_province_select').html(opt).trigger('change.select2');
      });
    }
    function loadRegencies(prefix, provinceId, selectedId=null){
      const $reg  = $(`#${prefix}_regency_select`);
      const $dist = $(`#${prefix}_district_select`);
      const $vill = $(`#${prefix}_village_select`);
      if(!provinceId){
        $reg.html('<option value=""></option>').trigger('change.select2');
        $dist.html('<option value=""></option>').trigger('change.select2');
        $vill.html('<option value=""></option>').trigger('change.select2');
        return Promise.resolve();
      }
      return $.get(`/regencies/${provinceId}`).then(res=>{
        let opt = '<option value=""></option>';
        res.data.forEach(i => opt += `<option value="${i.id}">${i.name}</option>`);
        $reg.html(opt); if(selectedId) $reg.val(String(selectedId));
        $reg.trigger('change.select2');
      });
    }
    function loadDistricts(prefix, regencyId, selectedId=null){
      const $dist = $(`#${prefix}_district_select`);
      const $vill = $(`#${prefix}_village_select`);
      if(!regencyId){
        $dist.html('<option value=""></option>').trigger('change.select2');
        $vill.html('<option value=""></option>').trigger('change.select2');
        return Promise.resolve();
      }
      return $.get(`/districts/${regencyId}`).then(res=>{
        let opt = '<option value=""></option>';
        res.data.forEach(i => opt += `<option value="${i.id}">${i.name}</option>`);
        $dist.html(opt); if(selectedId) $dist.val(String(selectedId));
        $dist.trigger('change.select2');
      });
    }
    function loadVillages(prefix, districtId, selectedId=null){
      const $vill = $(`#${prefix}_village_select`);
      if(!districtId){ $vill.html('<option value=""></option>').trigger('change.select2'); return Promise.resolve(); }
      return $.get(`/villages/${districtId}`).then(res=>{
        let opt = '<option value=""></option>';
        res.data.forEach(i => opt += `<option value="${i.id}">${i.name}</option>`);
        $vill.html(opt); if(selectedId) $vill.val(String(selectedId));
        $vill.trigger('change.select2');
      });
    }

    async function getAdministrativeData(lat, lng){
      $('#loadingGeocode').show();
      const prefix = currentMode === 'price' ? 'price' : 'dearth';
      try{
        const res = await $.get(`${API_BASE_URL}/reverse-geocode`, { lat, lng });
        if(res.success && res.data){
          const d = res.data; isAutoFilling = true;
          if(d.province_id){
            $(`#${prefix}_province_select`).val(d.province_id).trigger('change.select2');
            await loadRegencies(prefix, d.province_id, d.regency_id);
            if(d.regency_id) await loadDistricts(prefix, d.regency_id, d.district_id);
            if(d.district_id) await loadVillages(prefix, d.district_id, d.village_id);
          }
          if(d.regency_id)  $(`#${prefix}_regency_select`).val(d.regency_id).trigger('change.select2');
          if(d.district_id) $(`#${prefix}_district_select`).val(d.district_id).trigger('change.select2');
          if(d.village_id)  $(`#${prefix}_village_select`).val(d.village_id).trigger('change.select2');
          isAutoFilling = false;

          const $info = $(`#${prefix}LocationInfo`);
          $info.show().html(`
            <strong><i class='bx bx-map-pin'></i> Lokasi Terdeteksi</strong><br>
            ${d.village_name ? `Desa: ${d.village_name}<br>` : ``}
            ${d.district_name ? `Kec: ${d.district_name}<br>` : ``}
            ${d.regency_name ? `Kab: ${d.regency_name}<br>` : ``}
            ${d.province_name ? `Prov: ${d.province_name}<br>` : ``}
            <small class="text-muted">Jarak ~${d.distance} km dari titik terdekat</small>`);
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

    // ===== Form & Event =====
    function setupEventListeners(){
      $('#price_province_select').on('change', function(){ if(isAutoFilling) return; loadRegencies('price', $(this).val()); });
      $('#price_regency_select').on('change', function(){ if(isAutoFilling) return; loadDistricts('price', $(this).val()); });
      $('#price_district_select').on('change', function(){ if(isAutoFilling) return; loadVillages('price', $(this).val()); });

      $('#dearth_province_select').on('change', function(){ if(isAutoFilling) return; loadRegencies('dearth', $(this).val()); });
      $('#dearth_regency_select').on('change', function(){ if(isAutoFilling) return; loadDistricts('dearth', $(this).val()); });
      $('#dearth_district_select').on('change', function(){ if(isAutoFilling) return; loadVillages('dearth', $(this).val()); });

      $('#priceForm').on('submit', handlePriceFormSubmit);
      $('#dearthForm').on('submit', handleDearthFormSubmit);

      $('#price_commodity_id').on('change', loadTrendChart);
      $('#dearth_commodity_id').on('change', function(){ loadTrendChart(); refreshDearthChoropleth(); });
    }

    function handlePriceFormSubmit(e){
      e.preventDefault();
      if(!$('#price_lat').val() || !$('#price_lng').val()){ showAlert('priceAlertContainer', 'warning', 'Tandai lokasi pada peta terlebih dahulu!'); return; }
      const btn = $('#priceSubmitBtn'); btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Mengirim…');
      const data = {
        commodity_id: $('#price_commodity_id').val(),
        price: $('#price').val(),
        lat: $('#price_lat').val(),
        lng: $('#price_lng').val(),
        province_id: $('#price_province_select').val(),
        regency_id:  $('#price_regency_select').val(),
        district_id: $('#price_district_select').val(),
        village_id:  $('#price_village_select').val(),
        source: 'USER'
      };
      $.ajax({ url:`${API_BASE_URL}/reports`, type:'POST', data, headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } })
        .done(res=>{ showAlert('priceAlertContainer','success',res.message || 'Laporan harga berhasil dikirim.'); $('#price').val(''); loadTrendChart(); })
        .fail(xhr=>{ showAlert('priceAlertContainer','danger',xhr.responseJSON?.message || 'Gagal mengirim laporan.'); })
        .always(()=> btn.prop('disabled', false).html('<i class="bx bx-send"></i> Kirim Laporan Harga'));
    }

    function handleDearthFormSubmit(e){
      e.preventDefault();
      if(!$('#dearth_lat').val() || !$('#dearth_lng').val()){ showAlert('dearthAlertContainer','warning','Tandai lokasi pada peta terlebih dahulu!'); return; }
      const btn = $('#dearthSubmitBtn'); btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Mengirim…');
      const data = {
        commodity_id: $('#dearth_commodity_id').val(),
        severity: $('#severity').val(),
        description: $('#description').val(),
        lat: $('#dearth_lat').val(),
        lng: $('#dearth_lng').val(),
        province_id: $('#dearth_province_select').val(),
        regency_id:  $('#dearth_regency_select').val(),
        district_id: $('#dearth_district_select').val(),
        village_id:  $('#dearth_village_select').val(),
        kabupaten: $('#dearth_regency_select option:selected').text() || 'Unknown',
        kecamatan: $('#dearth_district_select option:selected').text() || null,
        source: 'USER'
      };
      $.ajax({ url:`${API_BASE_URL}/dearth/reports`, type:'POST', data, headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } })
        .done(res=>{ showAlert('dearthAlertContainer','success',res.message || 'Laporan kelangkaan berhasil dikirim.'); $('#description').val(''); loadTrendChart(); refreshDearthChoropleth(); })
        .fail(xhr=>{ showAlert('dearthAlertContainer','danger',xhr.responseJSON?.message || 'Gagal mengirim laporan.'); })
        .always(()=> btn.prop('disabled', false).html('<i class="bx bx-send"></i> Kirim Laporan Kelangkaan'));
    }

    function switchTab(mode){
      currentMode = mode;
      $('#priceTab').toggleClass('btn-light', mode==='price').toggleClass('btn-outline-light text-white', mode!=='price');
      $('#dearthTab').toggleClass('btn-light', mode==='dearth').toggleClass('btn-outline-light text-white', mode!=='dearth');
      $('#priceFormCard').toggle(mode==='price');
      $('#dearthFormCard').toggle(mode==='dearth');
      $('#mapLegend').toggle(mode==='dearth');
      $('#mapInfo').html(
        mode==='price'
          ? `<i class="bx bx-info-circle"></i> <strong>Mode: Laporan Harga</strong> — Klik ganda pada peta untuk menandai lokasi. <div id="loadingGeocode" class="geo-spinner" style="display:none"><i class='bx bx-loader bx-spin'></i> Mengambil data lokasi…</div>`
          : `<i class="bx bx-info-circle"></i> <strong>Mode: Laporan Kelangkaan</strong> — Klik ganda pada peta untuk menandai lokasi. <div id="loadingGeocode" class="geo-spinner" style="display:none"><i class='bx bx-loader bx-spin'></i> Mengambil data lokasi…</div>`
      );
      if(marker){ map.removeLayer(marker); marker = null; }
      loadTrendChart(); updateLegend(); refreshDearthChoropleth();
    }

    // ===== Grafik (tetap) =====
    async function loadTrendChart(){
      const sel = currentMode==='price' ? $('#price_commodity_id') : $('#dearth_commodity_id');
      const commodityId = sel.val() || '';
      const qs = $.param({ type: currentMode, days: 30, commodity_id: commodityId });

      const urls = [
        `${API_BASE_URL}/trend?${qs}`,
        `${API_BASE_URL}/${currentMode}/trend?${qs}`
      ];

      let resp = null;
      for(const u of urls){
        try{
          const r = await $.get(u);
          if(r && r.success){ resp = r; break; }
        }catch(e){}
      }

      const normalized = normalizeTrendResponse(resp, currentMode);
      if(normalized && normalized.labels?.length){
        renderTrendChartLine(normalized);
      }else{
        renderEmptyChart();
      }
    }

    function normalizeTrendResponse(res, mode){
      if(!res) return null;
      if(res.chart && Array.isArray(res.chart.labels) && Array.isArray(res.chart.datasets)){
        return { labels: res.chart.labels.map(formatDateLabel), datasets: res.chart.datasets };
      }
      const arr = Array.isArray(res.data) && res.data.length ? res.data
              : (Array.isArray(res.raw)  && res.raw.length  ? res.raw  : []);
      if(!arr.length) return null;

      const labels = arr.map(x => formatDateLabel(x.date));
      if(mode==='price'){
        return {
          labels,
          datasets:[
            { label:'Harga Rata-rata', data: arr.map(x => +x.avg_price || 0), fill:true,  borderWidth:3, tension:.35, pointRadius:2, pointHoverRadius:4 },
            { label:'Harga Minimum',  data: arr.map(x => +x.min_price || 0), fill:false, borderWidth:2, borderDash:[6,4], tension:.35, pointRadius:2, pointHoverRadius:4 },
            { label:'Harga Maksimum', data: arr.map(x => +x.max_price || 0), fill:false, borderWidth:2, borderDash:[6,4], tension:.35, pointRadius:2, pointHoverRadius:4 }
          ]
        };
      }else{
        return {
          labels,
          datasets:[
            { label:'Kritis',  data: arr.map(x => parseInt(x.critical_count) || 0), fill:false, borderWidth:2, tension:.3, pointRadius:2, pointHoverRadius:4 },
            { label:'Rawan',   data: arr.map(x => parseInt(x.high_count)     || 0), fill:false, borderWidth:2, tension:.3, pointRadius:2, pointHoverRadius:4 },
            { label:'Waspada', data: arr.map(x => parseInt(x.medium_count)   || 0), fill:false, borderWidth:2, tension:.3, pointRadius:2, pointHoverRadius:4 },
            { label:'Rendah',  data: arr.map(x => parseInt(x.low_count)      || 0), fill:false, borderWidth:2, tension:.3, pointRadius:2, pointHoverRadius:4 },
          ]
        };
      }
    }

    function formatDateLabel(v){
      const d = (v && !isNaN(Date.parse(v))) ? new Date(v) : null;
      return d ? d.toLocaleDateString('id-ID', { month:'short', day:'numeric' }) : (v ?? '');
    }

    function renderTrendChartLine(chartData){
      const canvas = document.getElementById('trendChart'); if(!canvas) return;
      if(trendChart){ trendChart.destroy(); trendChart = null; }
      const ctx = canvas.getContext('2d');

      const css   = getComputedStyle(document.documentElement);
      const cAvg  = css.getPropertyValue('--chart-avg').trim()  || '#ffd166';
      const cMin  = css.getPropertyValue('--chart-min').trim()  || '#00e6a7';
      const cMax  = css.getPropertyValue('--chart-max').trim()  || '#ff5b6e';
      const grid  = css.getPropertyValue('--chart-grid').trim() || 'rgba(148,163,184,.18)';
      const axis  = css.getPropertyValue('--chart-axis').trim() || '#cbd5e1';
      const title = css.getPropertyValue('--chart-title').trim()|| '#e2e8f0';

      chartData.datasets.forEach(ds=>{
        if(!ds.borderColor){
          if(/avg|rata/i.test(ds.label)) ds.borderColor = cAvg;
          else if(/min|rendah|low/i.test(ds.label)) ds.borderColor = cMin;
          else if(/max|tinggi|krit/i.test(ds.label)) ds.borderColor = cMax;
          else ds.borderColor = '#7aa2ff';
        }
        if(ds.fill===undefined) ds.fill = false;
      });

      const commonOpts = {
        responsive:true, maintainAspectRatio:true, aspectRatio:2,
        plugins:{
          legend:{ display:true, position:'top', labels:{ color:axis, boxWidth:14, boxHeight:4, usePointStyle:true, pointStyle:'line' } },
          title:{ display:true, text: currentMode==='price' ? 'Tren Harga Komoditas — 30 Hari Terakhir' : 'Tren Kelangkaan — 30 Hari Terakhir', color:title, font:{ size:15, weight:'bold' } },
          tooltip: currentMode==='price'
            ? { callbacks:{ label:(c)=> `${c.dataset.label}: Rp ${Number(c.parsed.y).toLocaleString('id-ID')}` } }
            : {}
        },
        scales:{
          x:{ grid:{ color:grid, tickBorderDash:[2,3] }, ticks:{ color:axis }, title:{ display:true, text:'Tanggal', color:axis } },
          y:{
            beginAtZero: currentMode!=='price',
            grid:{ color:grid },
            ticks: currentMode==='price'
              ? { color:axis, callback:(v)=> 'Rp ' + Number(v).toLocaleString('id-ID') }
              : { color:axis, stepSize:1 },
            title:{ display:true, text: currentMode==='price' ? 'Harga (Rp)' : 'Jumlah Laporan', color:axis }
          }
        }
      };

      trendChart = new Chart(ctx, { type:'line', data:{ labels:chartData.labels, datasets:chartData.datasets }, options:commonOpts });
    }

    function renderEmptyChart(){
      const canvas = document.getElementById('trendChart'); if(!canvas) return;
      if(trendChart){ trendChart.destroy(); trendChart = null; }
      const ctx = canvas.getContext('2d');
      ctx.clearRect(0,0,canvas.width,canvas.height);
      ctx.font = '15px Poppins, system-ui, -apple-system, Segoe UI';
      ctx.fillStyle = '#cbd5e1';
      ctx.textAlign = 'center';
      ctx.fillText('Belum ada data laporan', canvas.width/2, canvas.height/2);
    }

    // ===== Alert helper =====
    function showAlert(containerId, type, message){
      const icon = { success:'bx-check-circle', danger:'bx-error', warning:'bx-error-circle', info:'bx-info-circle' }[type] || 'bx-info-circle';
      $(`#${containerId}`).html(`
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
          <i class='bx ${icon}'></i> ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`);
      setTimeout(()=>{
        const el = $(`#${containerId} .alert`);
        if(el.length){ el.removeClass('show'); setTimeout(()=> $(`#${containerId}`).html(''), 300); }
      }, 5000);
    }
  </script>
</body>
</html>
