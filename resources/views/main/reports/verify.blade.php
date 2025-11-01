@extends('layouts.app')

@section('title', 'Verifikasi Laporan - PanganCek Admin')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* =========================================================================
                                           VERIFIKASI LAPORAN — Konsisten dengan theme utama, kontras optimal
                                           ========================================================================= */
        #verify-page {
            /* Palet diselaraskan dengan layout utama */
            --bg-surface: rgba(11, 18, 32, 0.95);
            /* Lebih gelap untuk card */
            --bg-elev-1: rgba(15, 23, 42, 0.92);
            /* Card background */
            --bg-elev-2: rgba(30, 41, 59, 0.85);
            /* Elevated elements */
            --text-primary: #f8fafc;
            /* Text utama - sangat terang */
            --text-secondary: #cbd5e1;
            /* Text secondary */
            --text-muted: #94a3b8;
            /* Text muted */
            --line-soft: rgba(255, 255, 255, .08);
            --line-dashed: rgba(255, 255, 255, .14);
            --control-bg: rgba(255, 255, 255, .04);
            --control-bg-hover: rgba(255, 255, 255, .08);
            --control-border: rgba(255, 255, 255, .16);
            --focus-ring-1: rgba(255, 122, 89, .35);
            /* Primary dari layout */
            --focus-ring-2: rgba(95, 124, 255, .25);
            /* Secondary dari layout */
            --accent-1: #ff7a59;
            /* Primary */
            --accent-2: #5f7cff;
            /* Secondary */
            --success: #2ed573;
            --danger: #ff6b6b;
        }

        /* ---------- Card ---------- */
        #verify-page .card {
            background: var(--bg-elev-1);
            border: 1px solid var(--line-soft);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .3);
            color: var(--text-primary);
        }

        /* Header: gradient konsisten dengan navbar */
        #verify-page .card-header.theme {
            background: linear-gradient(135deg,
                    rgba(255, 122, 89, .25) 0%,
                    rgba(255, 184, 108, .22) 50%,
                    rgba(95, 124, 255, .25) 100%);
            border-bottom: 1px dashed var(--line-dashed);
            color: var(--text-primary);
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            padding: 1rem 1.25rem;
        }

        #verify-page .card-header .form-select.form-select-sm {
            background: var(--control-bg);
            color: var(--text-primary);
            border: 1px solid var(--control-border);
            border-radius: 999px;
            padding: .4rem .9rem;
            font-weight: 500;
        }

        #verify-page .card-header .form-select.form-select-sm:focus {
            border-color: transparent;
            box-shadow: 0 0 0 .2rem var(--focus-ring-1), 0 0 0 .35rem var(--focus-ring-2);
            background: var(--control-bg-hover);
        }

        /* ---------- Tabel DataTables - Background Gelap ---------- */
        /* ---------- Tabel DataTables - Background Gelap Solid ---------- */
        #verify-page #reportsTable {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--line-soft);
            background: var(--bg-surface) !important;
            color: var(--text-secondary) !important;
        }

        /* Header */
        #verify-page #reportsTable thead th {
            background: linear-gradient(180deg,
                    rgba(255, 122, 89, .15),
                    rgba(95, 124, 255, .12)) !important;
            color: var(--text-primary) !important;
            border-bottom: 1px dashed var(--line-dashed) !important;
            vertical-align: middle;
            font-weight: 600;
            padding: 1rem 0.75rem;
        }

        /* Baris tabel — semua sama, tanpa strip */
        #verify-page #reportsTable tbody tr {
            background-color: rgba(15, 23, 42, 0.95) !important;
            transition: all .15s ease;
        }

        /* Sel tabel */
        #verify-page #reportsTable tbody td {
            background-color: rgba(15, 23, 42, 0.95) !important;
            color: var(--text-secondary) !important;
            border-color: var(--line-soft) !important;
        }

        /* Hover efek */
        #verify-page #reportsTable tbody tr:hover td {
            background-color: rgba(255, 122, 89, .12) !important;
            color: var(--text-primary) !important;
        }

        /* Hilangkan strip Bootstrap */
        #verify-page .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: rgba(15, 23, 42, 0.95) !important;
        }

        /* DataTables wrapper background */
        #verify-page .dataTables_wrapper {
            background: transparent !important;
        }


        /* ---------- Text helpers ---------- */
        #verify-page .text-muted {
            color: var(--text-muted) !important;
        }

        #verify-page .small {
            color: var(--text-secondary);
        }

        /* ---------- Kontrol DataTables ---------- */
        #verify-page .dataTables_wrapper .dataTables_filter label,
        #verify-page .dataTables_wrapper .dataTables_length label,
        #verify-page .dataTables_wrapper .dataTables_info {
            color: var(--text-secondary);
            font-weight: 500;
        }

        #verify-page .dataTables_wrapper .dataTables_filter input,
        #verify-page .dataTables_wrapper .dataTables_length select {
            background: var(--control-bg);
            border: 1px solid var(--control-border);
            color: var(--text-primary);
            border-radius: 12px;
            padding: .5rem .75rem;
            outline: none;
            transition: all .15s ease;
        }

        #verify-page .dataTables_wrapper .dataTables_filter input:focus,
        #verify-page .dataTables_wrapper .dataTables_length select:focus {
            border-color: transparent;
            box-shadow: 0 0 0 .2rem var(--focus-ring-1), 0 0 0 .35rem var(--focus-ring-2);
            background: var(--control-bg-hover);
        }

        /* Pagination */
        /* ---------- Pagination DataTables - Tema Gelap ---------- */
        #verify-page .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
            text-align: center;
        }

        /* Tombol pagination */
        #verify-page .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: var(--text-secondary) !important;
            border: 1px solid var(--line-soft) !important;
            background: rgba(15, 23, 42, 0.85) !important;
            border-radius: 10px !important;
            margin: 0 .25rem;
            font-weight: 500;
            transition: all .2s ease;
            box-shadow: 0 0 4px rgba(0, 0, 0, .25);
        }

        /* Tombol aktif (halaman sekarang) */
        #verify-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg,
                    rgba(255, 122, 89, .35),
                    rgba(95, 124, 255, .25)) !important;
            color: #fff !important;
            border-color: rgba(255, 122, 89, .5) !important;
            box-shadow: 0 0 6px rgba(255, 122, 89, .25);
        }

        /* Hover */
        #verify-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(135deg,
                    rgba(255, 122, 89, .25),
                    rgba(95, 124, 255, .2)) !important;
            color: var(--text-primary) !important;
            border-color: rgba(255, 122, 89, .4) !important;
            transform: translateY(-1px);
        }

        /* Disabled */
        #verify-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: .4 !important;
            background: rgba(15, 23, 42, 0.6) !important;
            border-color: var(--line-soft) !important;
            color: var(--text-secondary) !important;
            cursor: not-allowed !important;
        }

        /* Teks “Showing X of Y entries” juga dibuat lebih lembut */
        #verify-page .dataTables_wrapper .dataTables_info {
            color: var(--text-secondary) !important;
            font-weight: 400;
        }


        /* ---------- Map & Modal ---------- */
        #verify-page #leafletMap {
            height: 400px;
            border-radius: 12px;
            border: 1px solid var(--line-soft);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .02);
            background: rgba(11, 18, 32, .95);
            z-index: 1;
        }

        /* Modal dengan glass effect */
        #verify-page .modal-content {
            background: linear-gradient(180deg,
                    rgba(15, 23, 42, .98),
                    rgba(11, 18, 32, .98)) padding-box,
                linear-gradient(135deg,
                    rgba(255, 122, 89, .3),
                    rgba(95, 124, 255, .25)) border-box;
            border: 1px solid transparent;
            border-radius: 16px;
            color: var(--text-primary);
            backdrop-filter: saturate(120%) blur(10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, .5);
        }

        #verify-page .modal-header {
            border-bottom: 1px dashed var(--line-dashed);
            background: rgba(255, 255, 255, .03);
            padding: 1.25rem;
        }

        #verify-page .modal-header .modal-title {
            color: var(--text-primary);
            font-weight: 700;
        }

        #verify-page .modal-body {
            padding: 1.5rem;
        }

        #verify-page .modal-footer {
            border-top: 1px dashed var(--line-dashed);
            padding: 1.25rem;
            background: rgba(0, 0, 0, .15);
        }

        /* ---------- Tombol aksi ---------- */
        #verify-page .btn-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: all .12s ease;
            font-weight: 600;
            font-size: 1.1rem;
        }

        #verify-page .btn-icon:active {
            transform: scale(.94);
        }

        /* Tombol dengan warna konsisten */
        #verify-page .btn-success {
            background: linear-gradient(135deg, var(--success), #21a366);
            border: none;
            color: #fff;
            box-shadow: 0 4px 12px rgba(46, 213, 115, .25);
        }

        #verify-page .btn-success:hover {
            background: linear-gradient(135deg, #21a366, var(--success));
            box-shadow: 0 6px 16px rgba(46, 213, 115, .35);
        }

        #verify-page .btn-danger {
            background: linear-gradient(135deg, var(--danger), #ff5252);
            border: none;
            color: #fff;
            box-shadow: 0 4px 12px rgba(255, 107, 107, .25);
        }

        #verify-page .btn-danger:hover {
            background: linear-gradient(135deg, #ff5252, var(--danger));
            box-shadow: 0 6px 16px rgba(255, 107, 107, .35);
        }

        #verify-page .btn-outline-secondary {
            color: var(--text-secondary);
            border: 1px solid var(--control-border);
            background: var(--control-bg);
        }

        #verify-page .btn-outline-secondary:hover {
            background: var(--control-bg-hover);
            color: var(--text-primary);
            border-color: var(--control-border);
        }

        #verify-page .btn-outline-danger {
            color: var(--danger);
            border: 1px solid rgba(255, 107, 107, .4);
            background: rgba(255, 107, 107, .05);
        }

        #verify-page .btn-outline-danger:hover {
            background: rgba(255, 107, 107, .15);
            border-color: var(--danger);
            color: var(--danger);
        }

        /* ---------- Form controls ---------- */
        #verify-page .form-label {
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: .5rem;
            font-size: .9rem;
        }

        #verify-page .form-control,
        #verify-page .form-select {
            background: var(--control-bg);
            border: 1px solid var(--control-border);
            color: var(--text-primary);
            border-radius: 12px;
            padding: .6rem .9rem;
            transition: all .15s ease;
        }

        #verify-page .form-control::placeholder {
            color: var(--text-muted);
        }

        #verify-page .form-control:disabled,
        #verify-page .form-select:disabled {
            opacity: .7;
            color: var(--text-muted);
            background: rgba(255, 255, 255, .02);
        }

        #verify-page .form-control:hover:not(:disabled),
        #verify-page .form-select:hover:not(:disabled) {
            background: var(--control-bg-hover);
            border-color: rgba(255, 255, 255, .2);
        }

        #verify-page .form-control:focus,
        #verify-page .form-select:focus {
            border-color: transparent;
            box-shadow: 0 0 0 .2rem var(--focus-ring-1), 0 0 0 .35rem var(--focus-ring-2);
            background: var(--control-bg-hover);
            color: var(--text-primary);
        }

        /* ---------- Close button di modal ---------- */
        #verify-page .btn-close {
            filter: brightness(0) invert(1);
            opacity: .7;
        }

        #verify-page .btn-close:hover {
            opacity: 1;
        }

        /* ---------- Responsif ---------- */
        @media (max-width: 768px) {
            #verify-page .card-header.theme {
                flex-direction: column;
                align-items: flex-start !important;
                gap: .75rem;
            }

            #verify-page .btn-icon {
                width: 32px;
                height: 32px;
                font-size: 1rem;
            }

            #verify-page #leafletMap {
                height: 300px;
            }
        }

        /* ========== Dropdown Jenis (Kelangkaan / Harga) ========== */
        #verify-page .card-header .form-select.form-select-sm {
            background: var(--control-bg);
            color: var(--text-primary);
            border: 1px solid var(--control-border);
            border-radius: 999px;
            padding: .4rem .9rem;
            font-weight: 500;
            transition: all .2s ease;
            appearance: none;
            background-image: linear-gradient(135deg, rgba(255, 255, 255, .1), rgba(255, 255, 255, .05));
            background-repeat: no-repeat;
        }

        /* Hover dan fokus — lebih kontras dengan efek glow */
        #verify-page .card-header .form-select.form-select-sm:hover {
            background: var(--control-bg-hover);
            border-color: rgba(255, 255, 255, .25);
        }

        #verify-page .card-header .form-select.form-select-sm:focus {
            border-color: transparent;
            background: var(--control-bg-hover);
            box-shadow: 0 0 0 .2rem var(--focus-ring-1),
                0 0 0 .35rem var(--focus-ring-2);
        }

        /* Option dropdown (native) — warna teks dan latar disesuaikan */
        #verify-page .card-header .form-select.form-select-sm option {
            background: rgba(15, 23, 42, 0.95);
            color: var(--text-primary);
        }
    </style>
@endpush

@section('content')
    <div id="verify-page">
        <div class="container-xxl py-3">
            <div class="card">
                <div class="card-header theme d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bx bx-check-shield"></i> Verifikasi Laporan
                    </h5>

                    <div class="d-flex align-items-center gap-2">
                        <label class="mb-0 small">Jenis:</label>
                        <select id="reportType" class="form-select form-select-sm">
                            <option value="dearth" selected>Kelangkaan</option>
                            <option value="price">Harga</option>
                        </select>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered align-middle" id="reportsTable" style="width:100%">
                        <thead id="theadDynamic"></thead>
                    </table>
                    <small class="text-muted d-block mt-2">Klik tombol ✔ untuk review/ubah status, tombol ✕ untuk
                        hapus.</small>
                </div>
            </div>
        </div>

        {{-- Modal Detail + Aksi Verifikasi --}}
        <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form id="decisionForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reviewModalLabel">Detail Laporan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>

                        <div class="modal-body">
                            @csrf
                            <input type="hidden" id="report_id">
                            <input type="hidden" id="report_type">

                            <div class="row g-3">
                                {{-- Umum --}}
                                <div class="col-md-6">
                                    <label class="form-label">Komoditas</label>
                                    <input class="form-control" id="f_commodity" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Sumber</label>
                                    <input class="form-control" id="f_source" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Dilaporkan</label>
                                    <input class="form-control" id="f_reported_at" disabled>
                                </div>

                                {{-- Lokasi --}}
                                <div class="col-md-3">
                                    <label class="form-label">Provinsi</label>
                                    <input class="form-control" id="f_province" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kabupaten</label>
                                    <input class="form-control" id="f_regency" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kecamatan</label>
                                    <input class="form-control" id="f_district" disabled>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Desa</label>
                                    <input class="form-control" id="f_village" disabled>
                                </div>

                                {{-- Khusus Harga --}}
                                <div id="priceOnly" class="col-12 d-none">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Harga</label>
                                            <input class="form-control" id="f_price" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Satuan</label>
                                            <input class="form-control" id="f_unit" disabled>
                                        </div>
                                    </div>
                                </div>

                                {{-- Khusus Kelangkaan --}}
                                <div id="dearthOnly" class="col-12 d-none">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Severity</label>
                                            <input class="form-control" id="f_severity" disabled>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Deskripsi</label>
                                            <textarea class="form-control" id="f_description" rows="2" disabled></textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Koordinat & Map --}}
                                <div class="col-12">
                                    <label class="form-label">Koordinat (lat, lng)</label>
                                    <input class="form-control" id="f_coord" disabled>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Lokasi pada Peta</label>
                                    <div id="leafletMap"></div>
                                </div>

                                {{-- Catatan opsional --}}
                                <div class="col-12">
                                    <label class="form-label">Catatan (opsional)</label>
                                    <textarea class="form-control" id="f_note" rows="2" placeholder="Tulis alasan jika menolak..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="btnApprove"><i class="bx bx-check"></i>
                                Setujui</button>
                            <button type="button" class="btn btn-danger" id="btnReject"><i class="bx bx-x"></i>
                                Tolak</button>
                            <button type="button" class="btn btn-outline-secondary" id="btnDelete"><i
                                    class="bx bx-trash"></i> Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


    <script>
        let table;
        let map = null;
        let marker = null;

        /* ======================= THEAD ======================= */
        function theadFor(type) {
            if (type === 'price') {
                return `
  <tr>
    <th>No</th>
    <th>Komoditas</th>
    <th>Kabupaten</th>
    <th>Kecamatan</th>
    <th>Price (Unit)</th>
    <th>Sumber</th>
    <th>Reported</th>
    <th>Status</th>
    <th>Aksi</th>
  </tr>`;
            }
            return `
<tr>
  <th>No</th>
  <th>Komoditas</th>
  <th>Kabupaten</th>
  <th>Kecamatan</th>
  <th>Severity</th>
  <th>Sumber</th>
  <th>Reported</th>
  <th>Status</th>
  <th>Aksi</th>
</tr>`;
        }

        /* ========== Kolom sesuai response backend + kolom Action ========== */
        function columnsFor(type) {
            const base = (type === 'price') ? [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'commodity',
                    name: 'commodity'
                },
                {
                    data: 'kabupaten',
                    name: 'kabupaten',
                    defaultContent: '-'
                },
                {
                    data: 'kecamatan',
                    name: 'kecamatan',
                    defaultContent: '-'
                },
                {
                    data: 'price_unit',
                    name: 'price_unit',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'source',
                    name: 'source'
                },
                {
                    data: 'reported',
                    name: 'reported'
                },
                {
                    data: 'status_badge',
                    name: 'status_badge',
                    orderable: false,
                    searchable: false
                }
            ] : [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'commodity',
                    name: 'commodity'
                },
                {
                    data: 'kabupaten',
                    name: 'kabupaten'
                },
                {
                    data: 'kecamatan',
                    name: 'kecamatan'
                },
                {
                    data: 'severity',
                    name: 'severity'
                },
                {
                    data: 'source',
                    name: 'source'
                },
                {
                    data: 'reported',
                    name: 'reported'
                },
                {
                    data: 'status_badge',
                    name: 'status_badge',
                    orderable: false,
                    searchable: false
                }
            ];

            base.push({
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, typeData, row) {
                    return `
    <div class="d-flex gap-1">
      <button type="button" class="btn btn-success btn-icon review-btn" title="Review" data-id="${row.id}">
        ✔
      </button>
      <button type="button" class="btn btn-outline-danger btn-icon delete-btn" title="Hapus" data-id="${row.id}">
        ✕
      </button>
    </div>`;
                }
            });
            return base;
        }

        function loadTable(type = 'dearth') {
            $('#theadDynamic').html(theadFor(type));
            if (table) table.destroy();

            table = $('#reportsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.verify-reports.data') }}',
                    data: {
                        type
                    }
                },
                columns: columnsFor(type),
                order: [],
                createdRow: function(row, data) {
                    $(row).attr('data-id', data.id);
                    $(row).attr('data-type', type);
                    $('td', row).eq(7).html(data.status_badge);
                },
                language: {
                    processing: "Memproses...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data",
                    emptyTable: "Tidak ada data",
                    paginate: {
                        first: "Pertama",
                        previous: "Sebelumnya",
                        next: "Selanjutnya",
                        last: "Terakhir"
                    }
                }
            });
        }

        // Initialize map
        function initMap() {
            if (map) {
                map.remove();
            }

            // Default center (Indonesia)
            map = L.map('leafletMap').setView([-2.5, 118], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Custom marker icon
            const customIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            return customIcon;
        }

        // Update map marker
        function updateMapMarker(lat, lng) {
            if (!map) return;

            const customIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Remove existing marker
            if (marker) {
                map.removeLayer(marker);
            }

            // Add new marker
            marker = L.marker([lat, lng], {
                icon: customIcon
            }).addTo(map);
            marker.bindPopup(`<b>Lokasi Laporan</b><br>Lat: ${lat}<br>Lng: ${lng}`).openPopup();

            // Center map on marker
            map.setView([lat, lng], 13);

            // Force map to refresh
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }

        $(function() {
            loadTable($('#reportType').val());

            $('#reportType').on('change', function() {
                loadTable($(this).val());
            });

            // Review - Initialize map when modal opens
            $(document).on('click', '.review-btn', function(e) {
                e.stopPropagation();
                const id = $(this).data('id');
                const type = $('#reportType').val();

                $('#report_id').val(id);
                $('#report_type').val(type);

                $('#priceOnly').addClass('d-none');
                $('#dearthOnly').addClass('d-none');
                $('#f_note').val('');
                $('#f_commodity,#f_source,#f_province,#f_regency,#f_district,#f_village,#f_price,#f_unit,#f_severity,#f_description,#f_coord,#f_reported_at')
                    .val('');

                $.get('{{ route('admin.verify-reports.index') }}/' + id, {
                    type
                }, function(r) {
                    $('#f_commodity').val(r.commodity ?? '-');
                    $('#f_source').val(r.source ?? '-');
                    $('#f_reported_at').val(r.reported_at ?? '-');

                    $('#f_province').val(r.province ?? '-');
                    $('#f_regency').val(r.regency ?? '-');
                    $('#f_district').val(r.district ?? '-');
                    $('#f_village').val(r.village ?? '-');

                    // Get coordinates
                    const lat = parseFloat(r.map?.lat ?? r.lat ?? -2.5);
                    const lng = parseFloat(r.map?.lng ?? r.lng ?? 118);
                    $('#f_coord').val(lat + ', ' + lng);

                    if (type === 'price') {
                        $('#priceOnly').removeClass('d-none');
                        const priceNum = parseFloat(r.price || 0);
                        $('#f_price').val(Number.isFinite(priceNum) ?
                            new Intl.NumberFormat('id-ID', {
                                minimumFractionDigits: 2
                            }).format(priceNum) :
                            '-');
                        $('#f_unit').val(r.unit || '-');
                    } else {
                        $('#dearthOnly').removeClass('d-none');
                        $('#f_severity').val(r.severity || '-');
                        $('#f_description').val(r.description || '-');
                    }

                    // Show modal first
                    const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
                    modal.show();

                    // Initialize map after modal is shown
                    $('#reviewModal').on('shown.bs.modal', function() {
                        initMap();

                        // Update marker if coordinates are valid
                        if (!isNaN(lat) && !isNaN(lng)) {
                            updateMapMarker(lat, lng);
                        }
                    });
                });
            });

            // Clean up map when modal is hidden
            $('#reviewModal').on('hidden.bs.modal', function() {
                if (map) {
                    map.remove();
                    map = null;
                    marker = null;
                }
            });

            // Approve
            $('#btnApprove').on('click', function() {
                const id = $('#report_id').val();
                const type = $('#report_type').val();

                $.ajax({
                    url: '{{ route('admin.verify-reports.index') }}/' + id,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type,
                        decision: 'approved',
                        note: $('#f_note').val()
                    },
                    success: function() {
                        bootstrap.Modal.getInstance(document.getElementById('reviewModal'))
                            .hide();
                        table.ajax.reload(null, false);
                    }
                });
            });

            // Reject
            $('#btnReject').on('click', function() {
                const id = $('#report_id').val();
                const type = $('#report_type').val();

                $.ajax({
                    url: '{{ route('admin.verify-reports.index') }}/' + id,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type,
                        decision: 'rejected',
                        note: $('#f_note').val()
                    },
                    success: function() {
                        bootstrap.Modal.getInstance(document.getElementById('reviewModal'))
                            .hide();
                        table.ajax.reload(null, false);
                    }
                });
            });

            // Delete (dari tabel atau modal)
            $(document).on('click', '.delete-btn, #btnDelete', function(e) {
                e.stopPropagation();
                const id = $(this).data('id') || $('#report_id').val();
                const type = $('#report_type').val() || $('#reportType').val();

                if (!confirm('Yakin ingin menghapus laporan ini?')) {
                    return;
                }

                $.ajax({
                    url: '{{ route('admin.verify-reports.index') }}/' + id,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type
                    },
                    success: function() {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'reviewModal'));
                        if (modal) modal.hide();
                        table.ajax.reload(null, false);
                    }
                });
            });
        });
    </script>
@endpush
