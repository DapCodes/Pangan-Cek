@extends('layouts.app')

@section('title', 'Verifikasi Laporan - PanganCek Admin')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <h5 class="mb-0">Verifikasi Laporan</h5>
            <div class="d-flex align-items-center gap-2">
                <label class="me-1 mb-0">Jenis:</label>
                <select id="reportType" class="form-select form-select-sm">
                    <option value="dearth" selected>Kelangkaan (DearthReport)</option>
                    <option value="price">Harga (PriceReport)</option>
                </select>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped" id="reportsTable" style="width:100%">
                <thead class="table-light" id="theadDynamic">
                    <!-- akan diisi dinamis sesuai tipe -->
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal Review -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
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

                <div id="detailDearth" class="row g-3 d-none">
                    <div class="col-md-6">
                        <label class="form-label">Komoditas</label>
                        <input class="form-control" id="d_commodity_id" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Severity</label>
                        <input class="form-control" id="d_severity" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kabupaten</label>
                        <input class="form-control" id="d_kabupaten" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kecamatan</label>
                        <input class="form-control" id="d_kecamatan" disabled>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="d_description" rows="2" disabled></textarea>
                    </div>
                </div>

                <div id="detailPrice" class="row g-3 d-none">
                    <div class="col-md-6">
                        <label class="form-label">Komoditas</label>
                        <input class="form-control" id="p_commodity_id" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Harga</label>
                        <input class="form-control" id="p_price" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Satuan</label>
                        <input class="form-control" id="p_unit" disabled>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Koordinat</label>
                        <input class="form-control" id="d_coord" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Source</label>
                        <input class="form-control" id="d_source" disabled>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea class="form-control" id="note" rows="2" placeholder="Tulis alasan jika menolak..."></textarea>
                    </div>
                </div>

                <div class="alert alert-danger d-none mt-3" id="errorMsg"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnApproveModal">Setujui</button>
                <button type="button" class="btn btn-danger" id="btnRejectModal">Tolak</button>
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

    function theadFor(type){
        if(type === 'price'){
            return `
                <tr>
                    <th>No</th>
                    <th>Komoditas</th>
                    <th>Harga</th>
                    <th>Source</th>
                    <th>Reported</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Diupdate</th>
                    <th>Aksi</th>
                </tr>`;
        }
        // dearth
        return `
            <tr>
                <th>No</th>
                <th>Komoditas</th>
                <th>Kabupaten</th>
                <th>Kecamatan</th>
                <th>Severity</th>
                <th>Source</th>
                <th>Reported</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th>Diupdate</th>
                <th>Aksi</th>
            </tr>`;
    }

    function columnsFor(type){
        if(type === 'price'){
            return [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
                { data: 'commodity_id', name: 'commodity_id' },
                { data: 'price_pretty',  name: 'price', orderable:false, searchable:false },
                { data: 'source',       name: 'source' },
                { data: 'reported_at',  name: 'reported_at' },
                { data: 'status',       name: 'status', orderable:false, searchable:false },
                { data: 'created_at',   name: 'created_at' },
                { data: 'updated_at',   name: 'updated_at' },
                { data: 'action',       name: 'action', orderable:false, searchable:false }
            ];
        }
        return [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'commodity_id', name: 'commodity_id' },
            { data: 'kabupaten',    name: 'kabupaten' },
            { data: 'kecamatan',    name: 'kecamatan' },
            { data: 'severity',     name: 'severity' },
            { data: 'source',       name: 'source' },
            { data: 'reported_at',  name: 'reported_at' },
            { data: 'status',       name: 'status', orderable:false, searchable:false },
            { data: 'created_at',   name: 'created_at' },
            { data: 'updated_at',   name: 'updated_at' },
            { data: 'action',       name: 'action', orderable:false, searchable:false }
        ];
    }

    function loadTable(type){
        $('#theadDynamic').html(theadFor(type));
        if(table){ table.destroy(); $('#reportsTable').empty().append('<thead id="theadDynamic">'+theadFor(type)+'</thead>'); }
        table = $('#reportsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.verify-reports.data') }}',
                data: { type: type }
            },
            columns: columnsFor(type),
            language: {
                processing: "Memproses...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                loadingRecords: "Memuat...",
                zeroRecords: "Tidak ada data yang ditemukan",
                emptyTable: "Tidak ada data",
                paginate: { first: "Pertama", previous: "Sebelumnya", next: "Selanjutnya", last: "Terakhir" }
            }
        });
    }

    $(function(){
        const typeSel = $('#reportType');

        // pertama kali muat: dearth
        loadTable(typeSel.val());

        // ganti jenis -> reload table
        typeSel.on('change', function(){ loadTable($(this).val()); });

        // Approve cepat (✔)
        $(document).on('click', '.approve-btn', function(){
            const id = $(this).data('id');
            const type = $(this).data('type');
            if(!confirm('Setujui laporan ini?')) return;

            $.post('{{ route('admin.verify-reports.store') }}', {
                _token: '{{ csrf_token() }}',
                id: id,
                type: type
            }).done((res) => {
                alert(res.message);
                table.ajax.reload(null, false);
            }).fail((xhr) => {
                alert('Gagal menyetujui: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
            });
        });

        // Review (✕) -> buka modal
        $(document).on('click', '.review-btn', function(){
            const id = $(this).data('id');
            const type = $(this).data('type');

            $.get(`/admin/verify-reports/${id}/edit`, { type: type }, function(r){
                $('#report_id').val(r.id);
                $('#report_type').val(type);
                $('#d_coord').val((r.lat ?? '-') + ', ' + (r.lng ?? '-'));
                $('#d_source').val(r.source ?? '-');
                $('#note').val('');
                $('#errorMsg').addClass('d-none').text('');

                if(type === 'price'){
                    $('#detailDearth').addClass('d-none');
                    $('#detailPrice').removeClass('d-none');
                    $('#p_commodity_id').val(r.commodity_id);
                    $('#p_price').val(new Intl.NumberFormat('id-ID', {minimumFractionDigits:2}).format(parseFloat(r.price || 0)));
                    $('#p_unit').val(r.quantity_unit || '-');
                }else{
                    $('#detailPrice').addClass('d-none');
                    $('#detailDearth').removeClass('d-none');
                    $('#d_commodity_id').val(r.commodity_id);
                    $('#d_severity').val(r.severity);
                    $('#d_kabupaten').val(r.kabupaten);
                    $('#d_kecamatan').val(r.kecamatan);
                    $('#d_description').val(r.description || '-');
                }

                new bootstrap.Modal(document.getElementById('reviewModal')).show();
            }).fail((xhr)=>{
                alert('Gagal mengambil data: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
            });
        });

        function sendDecision(decision){
            const id   = $('#report_id').val();
            const type = $('#report_type').val();
            const note = $('#note').val();

            $.ajax({
                url: `/admin/verify-reports/${id}`,
                method: 'PUT',
                data: { _token: '{{ csrf_token() }}', type, decision, note },
                success: function(res){
                    bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
                    alert(res.message);
                    table.ajax.reload(null, false);
                },
                error: function(xhr){
                    let message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                    $('#errorMsg').removeClass('d-none').text(message);
                }
            });
        }

        $('#btnApproveModal').on('click', function(){ sendDecision('approved'); });
        $('#btnRejectModal').on('click', function(){
            if(!confirm('Yakin menolak laporan ini?')) return;
            sendDecision('rejected');
        });
    });
</script>
@endpush
