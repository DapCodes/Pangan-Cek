@extends('layouts.app')

@section('title', 'Manajemen Komoditas - PanganCek Admin')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <style>
        /* =========================================================================
               MANAJEMEN KOMODITAS — Konsisten dengan theme Verify Reports
               ========================================================================= */
        #commodity-page {
            /* Palet diselaraskan dengan layout utama */
            --bg-surface: rgba(11, 18, 32, 0.95);
            --bg-elev-1: rgba(15, 23, 42, 0.92);
            --bg-elev-2: rgba(30, 41, 59, 0.85);
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --line-soft: rgba(255, 255, 255, .08);
            --line-dashed: rgba(255, 255, 255, .14);
            --control-bg: rgba(255, 255, 255, .04);
            --control-bg-hover: rgba(255, 255, 255, .08);
            --control-border: rgba(255, 255, 255, .16);
            --focus-ring-1: rgba(255, 122, 89, .35);
            --focus-ring-2: rgba(95, 124, 255, .25);
            --accent-1: #ff7a59;
            --accent-2: #5f7cff;
            --success: #2ed573;
            --danger: #ff6b6b;
            --warning: #ffa502;
        }

        /* ---------- Card ---------- */
        #commodity-page .card {
            background: var(--bg-elev-1);
            border: 1px solid var(--line-soft);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .3);
            color: var(--text-primary);
        }

        /* Header: gradient konsisten dengan navbar */
        #commodity-page .card-header.theme {
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

        #commodity-page .card-header.theme h5 {
            font-weight: 700;
            margin: 0;
        }

        /* ---------- Tombol Tambah ---------- */
        #commodity-page .btn-add {
            background: linear-gradient(135deg, var(--accent-1), var(--warning));
            border: none;
            color: #fff;
            border-radius: 12px;
            padding: .6rem 1.2rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(255, 122, 89, .25);
            transition: all .2s ease;
        }

        #commodity-page .btn-add:hover {
            background: linear-gradient(135deg, var(--warning), var(--accent-1));
            box-shadow: 0 6px 16px rgba(255, 122, 89, .35);
            transform: translateY(-2px);
        }

        #commodity-page .btn-add:active {
            transform: translateY(0) scale(.96);
        }

        /* ---------- Tabel DataTables - Background Gelap ---------- */
        #commodity-page #commoditiesTable {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--line-soft);
            background: var(--bg-surface) !important;
            color: var(--text-secondary) !important;
        }

        /* Header */
        #commodity-page #commoditiesTable thead th {
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
        #commodity-page #commoditiesTable tbody tr {
            background-color: rgba(15, 23, 42, 0.95) !important;
            transition: all .15s ease;
        }

        /* Sel tabel */
        #commodity-page #commoditiesTable tbody td {
            background-color: rgba(15, 23, 42, 0.95) !important;
            color: var(--text-secondary) !important;
            border-color: var(--line-soft) !important;
            vertical-align: middle;
        }

        /* Hover efek */
        #commodity-page #commoditiesTable tbody tr:hover td {
            background-color: rgba(255, 122, 89, .12) !important;
            color: var(--text-primary) !important;
        }

        /* Hilangkan strip Bootstrap */
        #commodity-page .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: rgba(15, 23, 42, 0.95) !important;
        }

        /* DataTables wrapper background */
        #commodity-page .dataTables_wrapper {
            background: transparent !important;
        }

        /* ---------- Kontrol DataTables ---------- */
        #commodity-page .dataTables_wrapper .dataTables_filter label,
        #commodity-page .dataTables_wrapper .dataTables_length label,
        #commodity-page .dataTables_wrapper .dataTables_info {
            color: var(--text-secondary);
            font-weight: 500;
        }

        #commodity-page .dataTables_wrapper .dataTables_filter input,
        #commodity-page .dataTables_wrapper .dataTables_length select {
            background: var(--control-bg);
            border: 1px solid var(--control-border);
            color: var(--text-primary);
            border-radius: 12px;
            padding: .5rem .75rem;
            outline: none;
            transition: all .15s ease;
        }

        #commodity-page .dataTables_wrapper .dataTables_filter input:focus,
        #commodity-page .dataTables_wrapper .dataTables_length select:focus {
            border-color: transparent;
            box-shadow: 0 0 0 .2rem var(--focus-ring-1), 0 0 0 .35rem var(--focus-ring-2);
            background: var(--control-bg-hover);
        }

        /* ---------- Pagination DataTables - Tema Gelap ---------- */
        #commodity-page .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
            text-align: center;
        }

        #commodity-page .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: var(--text-secondary) !important;
            border: 1px solid var(--line-soft) !important;
            background: rgba(15, 23, 42, 0.85) !important;
            border-radius: 10px !important;
            margin: 0 .25rem;
            font-weight: 500;
            transition: all .2s ease;
            box-shadow: 0 0 4px rgba(0, 0, 0, .25);
        }

        #commodity-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg,
                    rgba(255, 122, 89, .35),
                    rgba(95, 124, 255, .25)) !important;
            color: #fff !important;
            border-color: rgba(255, 122, 89, .5) !important;
            box-shadow: 0 0 6px rgba(255, 122, 89, .25);
        }

        #commodity-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(135deg,
                    rgba(255, 122, 89, .25),
                    rgba(95, 124, 255, .2)) !important;
            color: var(--text-primary) !important;
            border-color: rgba(255, 122, 89, .4) !important;
            transform: translateY(-1px);
        }

        #commodity-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: .4 !important;
            background: rgba(15, 23, 42, 0.6) !important;
            border-color: var(--line-soft) !important;
            color: var(--text-secondary) !important;
            cursor: not-allowed !important;
        }

        /* ---------- Tombol Aksi ---------- */
        #commodity-page .btn-icon {
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

        #commodity-page .btn-icon:active {
            transform: scale(.94);
        }

        #commodity-page .btn-warning {
            background: linear-gradient(135deg, var(--warning), #ff9500);
            border: none;
            color: #fff;
            box-shadow: 0 4px 12px rgba(255, 165, 2, .25);
        }

        #commodity-page .btn-warning:hover {
            background: linear-gradient(135deg, #ff9500, var(--warning));
            box-shadow: 0 6px 16px rgba(255, 165, 2, .35);
        }

        #commodity-page .btn-danger {
            background: linear-gradient(135deg, var(--danger), #ff5252);
            border: none;
            color: #fff;
            box-shadow: 0 4px 12px rgba(255, 107, 107, .25);
        }

        #commodity-page .btn-danger:hover {
            background: linear-gradient(135deg, #ff5252, var(--danger));
            box-shadow: 0 6px 16px rgba(255, 107, 107, .35);
        }

        /* ---------- Modal dengan glass effect ---------- */
        #commodity-page .modal-content {
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

        #commodity-page .modal-header {
            border-bottom: 1px dashed var(--line-dashed);
            background: rgba(255, 255, 255, .03);
            padding: 1.25rem;
        }

        #commodity-page .modal-header .modal-title {
            color: var(--text-primary);
            font-weight: 700;
        }

        #commodity-page .modal-body {
            padding: 1.5rem;
        }

        #commodity-page .modal-footer {
            border-top: 1px dashed var(--line-dashed);
            padding: 1.25rem;
            background: rgba(0, 0, 0, .15);
        }

        /* ---------- Form controls ---------- */
        #commodity-page .form-label {
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: .5rem;
            font-size: .9rem;
        }

        #commodity-page .form-control,
        #commodity-page .form-select {
            background: var(--control-bg);
            border: 1px solid var(--control-border);
            color: var(--text-primary);
            border-radius: 12px;
            padding: .6rem .9rem;
            transition: all .15s ease;
        }

        #commodity-page .form-control::placeholder {
            color: var(--text-muted);
        }

        #commodity-page .form-control:hover:not(:disabled),
        #commodity-page .form-select:hover:not(:disabled) {
            background: var(--control-bg-hover);
            border-color: rgba(255, 255, 255, .2);
        }

        #commodity-page .form-control:focus,
        #commodity-page .form-select:focus {
            border-color: transparent;
            box-shadow: 0 0 0 .2rem var(--focus-ring-1), 0 0 0 .35rem var(--focus-ring-2);
            background: var(--control-bg-hover);
            color: var(--text-primary);
        }

        #commodity-page .form-select option {
            background: rgba(15, 23, 42, 0.95);
            color: var(--text-primary);
        }

        /* ---------- Alert ---------- */
        #commodity-page .alert-danger {
            background: rgba(255, 107, 107, .15);
            border: 1px solid rgba(255, 107, 107, .3);
            color: #ffcdd2;
            border-radius: 12px;
        }

        /* ---------- Tombol Modal ---------- */
        #commodity-page .btn-secondary {
            background: var(--control-bg);
            border: 1px solid var(--control-border);
            color: var(--text-secondary);
            border-radius: 12px;
        }

        #commodity-page .btn-secondary:hover {
            background: var(--control-bg-hover);
            color: var(--text-primary);
            border-color: var(--control-border);
        }

        #commodity-page .btn-primary {
            background: linear-gradient(135deg, var(--accent-2), var(--accent-1));
            border: none;
            color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(95, 124, 255, .25);
        }

        #commodity-page .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            box-shadow: 0 6px 16px rgba(95, 124, 255, .35);
        }

        /* ---------- Close button di modal ---------- */
        #commodity-page .btn-close {
            filter: brightness(0) invert(1);
            opacity: .7;
        }

        #commodity-page .btn-close:hover {
            opacity: 1;
        }

        /* ---------- Responsif ---------- */
        @media (max-width: 768px) {
            #commodity-page .card-header.theme {
                flex-direction: column;
                align-items: flex-start !important;
                gap: .75rem;
            }

            #commodity-page .btn-icon {
                width: 32px;
                height: 32px;
                font-size: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div id="commodity-page">
        <div class="container-xxl py-3">
            <div class="card">
                <div class="card-header theme d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bx bx-package"></i> Manajemen Komoditas
                    </h5>
                    <button class="btn btn-add" id="btnAdd" type="button">
                        <i class="bx bx-plus"></i> Tambah Komoditas
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered align-middle" id="commoditiesTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Satuan</th>
                                <th>Kategori</th>
                                <th>Dibuat</th>
                                <th>Diupdate</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Form -->
        <div class="modal fade" id="commodityModal" tabindex="-1" aria-labelledby="commodityModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="commodityForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="commodityModalLabel">Tambah Komoditas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" id="id" name="id">

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Komoditas</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Contoh: Beras" required>
                            </div>

                            <div class="mb-3">
                                <label for="unit" class="form-label">Satuan</label>
                                <select class="form-select" id="unit" name="unit" required>
                                    <option value="">Pilih Satuan</option>
                                    <option value="kg">kg</option>
                                    <option value="liter">liter</option>
                                    <option value="butir">butir</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Bahan Pokok">Bahan Pokok</option>
                                    <option value="Sayuran">Sayuran</option>
                                    <option value="Protein">Protein</option>
                                    <option value="Sembako">Sembako</option>
                                    <option value="Pangan">Pangan</option>
                                </select>
                            </div>

                            <div class="alert alert-danger d-none" id="errorMsg"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
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
        $(function() {
            // Inisialisasi DataTable
            let table = $('#commoditiesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.commodities.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-warning btn-icon edit-btn" title="Edit" data-id="${row.id}">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-icon delete-btn" title="Hapus" data-id="${row.id}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>`;
                        }
                    }
                ],
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
                    paginate: {
                        first: "Pertama",
                        previous: "Sebelumnya",
                        next: "Selanjutnya",
                        last: "Terakhir"
                    }
                }
            });

            // Tombol Tambah
            $('#btnAdd').on('click', function() {
                $('#commodityForm')[0].reset();
                $('#id').val('');
                $('#errorMsg').addClass('d-none').text('');
                $('#commodityModalLabel').text('Tambah Komoditas');

                var modal = new bootstrap.Modal(document.getElementById('commodityModal'));
                modal.show();
            });

            // Submit Form
            $('#commodityForm').on('submit', function(e) {
                e.preventDefault();

                let id = $('#id').val();
                let url = id ? `/admin/commodities/${id}` : '{{ route('admin.commodities.store') }}';
                let method = id ? 'PUT' : 'POST';

                $('#btnSave').prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            var modal = bootstrap.Modal.getInstance(document.getElementById(
                                'commodityModal'));
                            modal.hide();
                            table.ajax.reload();
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        $('#errorMsg').removeClass('d-none').text(message);
                    },
                    complete: function() {
                        $('#btnSave').prop('disabled', false).text('Simpan');
                    }
                });
            });

            // Tombol Edit
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');

                $.get(`/admin/commodities/${id}/edit`, function(res) {
                    $('#id').val(res.id);
                    $('#name').val(res.name);
                    $('#unit').val(res.unit);
                    $('#category').val(res.category);
                    $('#errorMsg').addClass('d-none').text('');
                    $('#commodityModalLabel').text('Edit Komoditas');

                    var modal = new bootstrap.Modal(document.getElementById('commodityModal'));
                    modal.show();
                }).fail(function(xhr) {
                    alert('Gagal mengambil data: ' + (xhr.responseJSON?.message ||
                        'Terjadi kesalahan'));
                });
            });

            // Tombol Hapus
            $(document).on('click', '.delete-btn', function() {
                if (!confirm('Yakin ingin menghapus komoditas ini?')) return;

                let id = $(this).data('id');

                $.ajax({
                    url: `/admin/commodities/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            table.ajax.reload();
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus: ' + (xhr.responseJSON?.message ||
                            'Terjadi kesalahan'));
                    }
                });
            });
        });
    </script>
@endpush
