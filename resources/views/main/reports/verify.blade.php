@extends('layouts.app')

@section('title', 'Verifikasi Laporan - PanganCek Admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
<style>
/* =========================================================================
   VERIFIKASI LAPORAN — Dark theme yang rapi, kontras bagus, dan konsisten
   Semua style DISKOP ke #verify-page agar tidak mengganggu halaman lain.
   ========================================================================= */
#verify-page{
  /* Palet warna (bisa disesuaikan dari layout utama) */
  --bg-surface:          rgba(18,22,34,0.9);
  --bg-elev-1:           rgba(28,34,54,0.9);
  --bg-elev-2:           rgba(36,44,70,0.85);
  --text-strong:         #f1f6ff;
  --text-muted:          #c8d6ff;
  --line-soft:           rgba(255,255,255,.08);
  --line-dashed:         rgba(255,255,255,.14);
  --control-bg:          rgba(255,255,255,.04);
  --control-bg-hover:    rgba(255,255,255,.07);
  --control-border:      rgba(255,255,255,.16);
  --focus-ring-1:        rgba(99,132,255,.35);
  --focus-ring-2:        rgba(255,136,102,.30);
  --accent-1:            #6f8bff;   /* ungu kebiruan */
  --accent-2:            #ff8a66;   /* oranye lembut */
  --success:             #21c17a;
  --danger:              #ff6b6b;
}

/* ---------- Tipografi & warna dasar hanya pada kartu di halaman ini ---------- */
#verify-page .card,
#verify-page .card *:not(.bx){
  color: var(--text-strong);
}
#verify-page .text-muted{ color:var(--text-muted) !important; }

/* ---------- Card ---------- */
#verify-page .card{
  background: var(--bg-elev-1);
  border: 1px solid var(--line-soft);
  border-radius: 14px;
  box-shadow: 0 10px 30px rgba(0,0,0,.28);
}

/* Header: gradient halus + kontrol di kanan */
#verify-page .card-header.theme{
  background:
    linear-gradient(135deg, rgba(111,139,255,.25), rgba(255,138,102,.20));
  border-bottom: 1px dashed var(--line-dashed);
  color:#fff;
  border-top-left-radius:14px;
  border-top-right-radius:14px;
}
#verify-page .card-header .form-select.form-select-sm{
  background: var(--control-bg);
  color: var(--text-strong);
  border: 1px solid var(--control-border);
  border-radius: 999px;
  padding:.35rem .9rem;
}
#verify-page .card-header .form-select.form-select-sm:focus{
  border-color: transparent;
  box-shadow: 0 0 0 .18rem var(--focus-ring-1), 0 0 0 .34rem var(--focus-ring-2);
  background: var(--control-bg-hover);
}

/* ---------- Tabel DataTables ---------- */
#verify-page #reportsTable{
  border-radius: 12px;
  overflow: hidden;
  border: 1px solid var(--line-soft);
  background: var(--bg-surface);
}
#verify-page #reportsTable thead th{
  background: linear-gradient(180deg, rgba(255,255,255,.07), rgba(255,255,255,.03));
  color:#fff;
  border-bottom:1px dashed var(--line-dashed) !important;
  vertical-align: middle;
}
#verify-page .table.table-striped>tbody>tr:nth-of-type(odd)>*{
  --bs-table-accent-bg: rgba(255,255,255,.03);
  color:inherit;
}
#verify-page #reportsTable tbody tr{ background: transparent; }
#verify-page #reportsTable tbody tr:hover{
  background: rgba(111,139,255,.10);
}
#verify-page #reportsTable td,
#verify-page #reportsTable th{
  border-color: var(--line-soft);
}
#verify-page #reportsTable td{ color:#eaf2ff; }

/* Badge dari backend tetap terlihat */
#verify-page #reportsTable .badge{
  border:1px solid rgba(255,255,255,.2);
}

/* ---------- Kontrol DataTables ---------- */
#verify-page .dataTables_wrapper .dataTables_filter label,
#verify-page .dataTables_wrapper .dataTables_length label,
#verify-page .dataTables_wrapper .dataTables_info{
  color: var(--text-strong);
}
#verify-page .dataTables_wrapper .dataTables_filter input,
#verify-page .dataTables_wrapper .dataTables_length select{
  background: var(--control-bg);
  border:1px solid var(--control-border);
  color:var(--text-strong);
  border-radius:12px;
  padding:.45rem .6rem;
  outline:none;
}
#verify-page .dataTables_wrapper .dataTables_filter input:focus,
#verify-page .dataTables_wrapper .dataTables_length select:focus{
  border-color:transparent;
  box-shadow:0 0 0 .18rem var(--focus-ring-1), 0 0 0 .34rem var(--focus-ring-2);
  background:var(--control-bg-hover);
}

/* Pagination */
#verify-page .dataTables_wrapper .dataTables_paginate .paginate_button{
  color:#eaf2ff !important;
  border:1px solid var(--control-border) !important;
  background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.02)) !important;
  border-radius:10px !important;
  margin: 0 .15rem;
}
#verify-page .dataTables_wrapper .dataTables_paginate .paginate_button.current{
  background: linear-gradient(180deg, rgba(111,139,255,.28), rgba(111,139,255,.18)) !important;
  color:#fff !important;
  border-color: rgba(111,139,255,.45) !important;
}
#verify-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover{
  background: rgba(255,138,102,.12) !important;
}

/* ---------- Map & Modal ---------- */
#verify-page #leafletMap{
  height: 320px;
  border-radius:12px;
  border:1px solid var(--line-soft);
  box-shadow: inset 0 0 0 1px rgba(255,255,255,.02);
  background: rgba(9,14,26,.88);
}

/* Modal dengan glass effect ringan */
#verify-page .modal-content{
  background:
    linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02)) padding-box,
    linear-gradient(135deg, rgba(111,139,255,.28), rgba(255,138,102,.22)) border-box;
  border:1px solid transparent;
  border-radius:16px;
  color:var(--text-strong);
  backdrop-filter: saturate(120%) blur(6px);
}
#verify-page .modal-header{
  border-bottom:1px dashed var(--line-dashed);
  background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
}
#verify-page .modal-footer{
  border-top:1px dashed var(--line-dashed);
}

/* ---------- Tombol aksi kecil ---------- */
#verify-page .btn-icon{
  width:34px;height:34px;
  display:inline-flex;align-items:center;justify-content:center;
  border-radius:10px;
  transition: transform .08s ease;
}
#verify-page .btn-icon:active{ transform: scale(.96); }

/* Override visual tombol utama agar seragam */
#verify-page .btn-success{
  background: var(--success); border-color: var(--success);
}
#verify-page .btn-danger{
  background: var(--danger);  border-color: var(--danger);
}
#verify-page .btn-outline-secondary{
  color: var(--text-strong);
  border-color: var(--control-border);
}
#verify-page .btn-outline-danger{
  color: var(--danger); border-color: var(--danger);
}
#verify-page .btn-outline-danger:hover{
  background: rgba(255,107,107,.12);
}

/* ---------- Form controls (fallback) ---------- */
#verify-page .form-control,
#verify-page .form-select{
  background:var(--control-bg);
  border:1px solid var(--control-border);
  color:var(--text-strong);
  border-radius:12px;
}
#verify-page .form-control:disabled,
#verify-page .form-select:disabled{
  opacity:.9; color:var(--text-muted);
}
#verify-page .form-control:focus,
#verify-page .form-select:focus{
  border-color:transparent;
  box-shadow:0 0 0 .18rem var(--focus-ring-1), 0 0 0 .34rem var(--focus-ring-2);
  background:var(--control-bg-hover);
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
        <table class="table table-bordered table-striped align-middle" id="reportsTable" style="width:100%">
          <thead id="theadDynamic"></thead>
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

              {{-- Map --}}
              <div class="col-md-6">
                <label class="form-label">Koordinat (lat, lng)</label>
                <input class="form-control" id="f_coord" disabled>
              </div>
              <div class="col-12">
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
            <button type="button" class="btn btn-success" id="btnApprove"><i class="bx bx-check"></i> Setujui</button>
            <button type="button" class="btn btn-danger"  id="btnReject"><i class="bx bx-x"></i> Tolak</button>
            <button type="button" class="btn btn-outline-secondary" id="btnDelete"><i class="bx bx-trash"></i> Hapus</button>
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

<script>
let table;

/* ======================= THEAD ======================= */
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

/* ========== Kolom sesuai response backend + kolom Action ========== */
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
      url: '{{ route("admin.verify-reports.data") }}',
      data: { type }
    },
    columns: columnsFor(type),
    order: [],
    createdRow: function(row, data){
      $(row).attr('data-id', data.id);
      $(row).attr('data-type', type);
      $('td', row).eq(7).html(data.status_badge); // pastikan kolom Status tetap tampil HTML dari backend
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
  loadTable($('#reportType').val());

  $('#reportType').on('change', function(){
    loadTable($(this).val());
  });

  // Review
  $(document).on('click', '.review-btn', function(e){
    e.stopPropagation();
    const id   = $(this).data('id');
    const type = $('#reportType').val();

    $('#report_id').val(id);
    $('#report_type').val(type);

    $('#priceOnly').addClass('d-none');
    $('#dearthOnly').addClass('d-none');
    $('#f_note').val('');
    $('#f_commodity,#f_source,#f_province,#f_regency,#f_district,#f_village,#f_price,#f_unit,#f_severity,#f_description,#f_coord,#f_reported_at').val('');

    $.get('{{ route("admin.verify-reports.index") }}/' + id, { type }, function(r){
      $('#f_commodity').val(r.commodity ?? '-');
      $('#f_source').val(r.source ?? '-');
      $('#f_reported_at').val(r.reported_at ?? '-');

      $('#f_province').val(r.province ?? '-');
      $('#f_regency').val(r.regency ?? '-');
      $('#f_district').val(r.district ?? '-');
      $('#f_village').val(r.village ?? '-');

      const lat = r.map?.lat ?? r.lat ?? '-';
      const lng = r.map?.lng ?? r.lng ?? '-';
      $('#f_coord').val(lat + ', ' + lng);

      if(type === 'price'){
        $('#priceOnly').removeClass('d-none');
        const priceNum = parseFloat(r.price || 0);
        $('#f_price').val(Number.isFinite(priceNum)
          ? new Intl.NumberFormat('id-ID', {minimumFractionDigits:2}).format(priceNum)
          : '-');
        $('#f_unit').val(r.unit || '-');
      }else{
        $('#dearthOnly').removeClass('d-none');
        $('#f_severity').val(r.severity || '-');
        $('#f_description').val(r.description || '-');
      }

      new bootstrap.Modal(document.getElementById('reviewModal')).show();
    });
  });

  // Approve
  $('#btnApprove').on('click', function(){
    const id   = $('#report_id').val();
    const type = $('#report_type').val();

    $.ajax({
      url: '{{ route("admin.verify-reports.index") }}/' + id,
      method: 'PUT',
      data: { _token: '{{ csrf_token() }}', type, decision: 'approved', note: $('#f_note').val() },
      success: function(){
        bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
        table.ajax.reload(null, false);
      }
    });
  });

  // Reject
  $('#btnReject').on('click', function(){
    const id   = $('#report_id').val();
    const type = $('#report_type').val();

    $.ajax({
      url: '{{ route("admin.verify-reports.index") }}/' + id,
      method: 'PUT',
      data: { _token: '{{ csrf_token() }}', type, decision: 'rejected', note: $('#f_note').val() },
      success: function(){
        bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
        table.ajax.reload(null, false);
      }
    });
  });

  // Delete (dari tabel atau modal)
  $(document).on('click', '.delete-btn, #btnDelete', function(e){
    e.stopPropagation();
    const id   = $(this).data('id') || $('#report_id').val();
    const type = $('#report_type').val() || $('#reportType').val();

    $.ajax({
      url: '{{ route("admin.verify-reports.index") }}/' + id,
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
