@extends('layouts.app')

@section('title', 'Verifikasi Laporan - PanganCek Admin')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <style>
        #leafletMap{height:280px;border-radius:.5rem;border:1px solid #e9ecef}
        .btn-icon{width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center}
    </style>
@endpush

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">Verifikasi Laporan</h5>

                <div class="d-flex align-items-center gap-2">
                    <label class="mb-0 small">Jenis:</label>
                    <select id="reportType" class="form-select form-select-sm">
                        <option value="dearth" selected>Kelangkaan</option>
                        <option value="price">Harga</option>
                    </select>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-striped" id="reportsTable" style="width:100%">
                    <thead class="table-light" id="theadDynamic"></thead>
                </table>
                <small class="text-muted d-block mt-2">Klik tombol ✔ untuk review/ubah status, tombol ✕ untuk hapus.</small>
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

                            {{-- Map --}}
                            <div class="col-md-6">
                                <label class="form-label">Koordinat (lat, lng)</label>
                                <input class="form-control" id="f_coord" disabled>
                            </div>
                            <div class="col-12">
                                <div id="leafletMap"></div>
                            </div>

                            {{-- Catatan opsional (tanpa alert) --}}
                            <div class="col-12">
                                <label class="form-label">Catatan (opsional)</label>
                                <textarea class="form-control" id="f_note" rows="2" placeholder="Tulis alasan jika menolak..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="btnApprove">Setujui</button>
                        <button type="button" class="btn btn-danger"  id="btnReject">Tolak</button>
                        <button type="button" class="btn btn-outline-secondary" id="btnDelete">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        let table;

        // ====== THEAD ======
        function theadFor(type){
            if(type === 'price'){
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

        // ====== Kolom sesuai response backend getData() + kolom Action client-side ======
        function columnsFor(type){
            const base = (type === 'price')
                ? [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
                    { data: 'commodity',   name: 'commodity' },
                    { data: 'kabupaten',   name: 'kabupaten', defaultContent: '-' },
                    { data: 'kecamatan',   name: 'kecamatan', defaultContent: '-' },
                    { data: 'price_unit',  name: 'price_unit', orderable:false, searchable:false },
                    { data: 'source',      name: 'source' },
                    { data: 'reported',    name: 'reported' },
                    { data: 'status_badge',name: 'status_badge', orderable:false, searchable:false }
                ]
                : [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
                    { data: 'commodity',   name: 'commodity' },
                    { data: 'kabupaten',   name: 'kabupaten' },
                    { data: 'kecamatan',   name: 'kecamatan' },
                    { data: 'severity',    name: 'severity' },
                    { data: 'source',      name: 'source' },
                    { data: 'reported',    name: 'reported' },
                    { data: 'status_badge',name: 'status_badge', orderable:false, searchable:false }
                ];

            // Tambah kolom Action -> tombol ✔ (review) & ✕ (hapus)
            base.push({
                data: null,
                orderable:false,
                searchable:false,
                render: function(data, typeData, row){
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

        function loadTable(type='dearth'){
            $('#theadDynamic').html(theadFor(type));
            if(table) table.destroy();

            table = $('#reportsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.verify-reports.data") }}', // VerifyReportController@getData
                    data: { type }
                },
                columns: columnsFor(type),
                order: [],
                createdRow: function(row, data){
                    $(row).attr('data-id', data.id);
                    $(row).attr('data-type', type);
                    // render badge html
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
                    paginate: { first: "Pertama", previous: "Sebelumnya", next: "Selanjutnya", last: "Terakhir" }
                }
            });
        }

        $(function(){
            // initial
            loadTable($('#reportType').val());

            // ganti jenis
            $('#reportType').on('change', function(){
                loadTable($(this).val());
            });

            // ==== ACTION BUTTONS ====

            // ✔ Review -> buka modal dan isi dari endpoint show
            $(document).on('click', '.review-btn', function(e){
                e.stopPropagation();
                const id   = $(this).data('id');
                const type = $('#reportType').val();

                $('#report_id').val(id);
                $('#report_type').val(type);

                // reset tampilan modal
                $('#priceOnly').addClass('d-none');
                $('#dearthOnly').addClass('d-none');
                $('#f_note').val('');
                $('#f_commodity,#f_source,#f_province,#f_regency,#f_district,#f_village,#f_price,#f_unit,#f_severity,#f_description,#f_coord,#f_reported_at').val('');

                // Ambil detail -> VerifyReportController@show
                $.get('{{ route("admin.verify-reports.index") }}/' + id, { type }, function(r){
                    // Umum
                    $('#f_commodity').val(r.commodity ?? '-');
                    $('#f_source').val(r.source ?? '-');
                    $('#f_reported_at').val(r.reported_at ?? '-');

                    // Lokasi
                    $('#f_province').val(r.province ?? '-');
                    $('#f_regency').val(r.regency ?? '-');
                    $('#f_district').val(r.district ?? '-');
                    $('#f_village').val(r.village ?? '-');

                    // Koordinat
                    const lat = r.map?.lat ?? r.lat ?? '-';
                    const lng = r.map?.lng ?? r.lng ?? '-';
                    $('#f_coord').val(lat + ', ' + lng);

                    // Khusus tipe
                    if(type === 'price'){
                        $('#priceOnly').removeClass('d-none');
                        $('#f_price').val(
                            new Intl.NumberFormat('id-ID', {minimumFractionDigits:2}).format(parseFloat(r.price || 0))
                        );
                        $('#f_unit').val(r.unit || '-');
                    }else{
                        $('#dearthOnly').removeClass('d-none');
                        $('#f_severity').val(r.severity || '-');
                        $('#f_description').val(r.description || '-');
                    }

                    // Tampilkan modal
                    new bootstrap.Modal(document.getElementById('reviewModal')).show();

                    // TODO: Inisialisasi Leaflet di sini menggunakan lat/lng
                });
            });

            // ✔ Setujui
            $('#btnApprove').on('click', function(){
                const id   = $('#report_id').val();
                const type = $('#report_type').val();

                $.ajax({
                    url: '{{ route("admin.verify-reports.index") }}/' + id,   // VerifyReportController@updateStatus
                    method: 'PUT',
                    data: { _token: '{{ csrf_token() }}', type, decision: 'approved', note: $('#f_note').val() },
                    success: function(){
                        bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
                        table.ajax.reload(null, false);
                    }
                });
            });

            // ✖ Tolak
            $('#btnReject').on('click', function(){
                const id   = $('#report_id').val();
                const type = $('#report_type').val();

                $.ajax({
                    url: '{{ route("admin.verify-reports.index") }}/' + id,   // VerifyReportController@updateStatus
                    method: 'PUT',
                    data: { _token: '{{ csrf_token() }}', type, decision: 'rejected', note: $('#f_note').val() },
                    success: function(){
                        bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
                        table.ajax.reload(null, false);
                    }
                });
            });

            // ✕ Hapus
            $(document).on('click', '.delete-btn, #btnDelete', function(e){
                e.stopPropagation();
                // jika dari table: ambil id dari data-id tombol; jika dari modal: gunakan hidden field
                const id   = $(this).data('id') || $('#report_id').val();
                const type = $('#report_type').val() || $('#reportType').val();

                $.ajax({
                    url: '{{ route("admin.verify-reports.index") }}/' + id,   // VerifyReportController@destroy
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}', type },
                    success: function(){
                        const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
                        if(modal) modal.hide();
                        table.ajax.reload(null, false);
                    }
                });
            });
        });
    </script>
@endpush
