@extends('layouts.app')

@section('title', 'Manajemen User - PanganCek Admin')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <style>
        /* =========================================================================
               MANAJEMEN USER — Konsisten dengan theme Verify Reports
               ========================================================================= */
        #users-page {
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
        #users-page .card {
            background: var(--bg-elev-1);
            border: 1px solid var(--line-soft);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .3);
            color: var(--text-primary);
        }

        /* Header: gradient konsisten dengan navbar */
        #users-page .card-header.theme {
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

        #users-page .card-header.theme h5 {
            font-weight: 700;
            margin: 0;
        }

        /* ---------- Tombol Tambah ---------- */
        #users-page .btn-add {
            background: linear-gradient(135deg, var(--accent-1), var(--warning));
            border: none;
            color: #fff;
            border-radius: 12px;
            padding: .6rem 1.2rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(255, 122, 89, .25);
            transition: all .2s ease;
        }

        #users-page .btn-add:hover {
            background: linear-gradient(135deg, var(--warning), var(--accent-1));
            box-shadow: 0 6px 16px rgba(255, 122, 89, .35);
            transform: translateY(-2px);
        }

        #users-page .btn-add:active {
            transform: translateY(0) scale(.96);
        }

        /* ---------- Tabel DataTables - Background Gelap ---------- */
        #users-page #usersTable {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--line-soft);
            background: var(--bg-surface) !important;
            color: var(--text-secondary) !important;
        }

        /* Header */
        #users-page #usersTable thead th {
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
        #users-page #usersTable tbody tr {
            background-color: rgba(15, 23, 42, 0.95) !important;
            transition: all .15s ease;
        }

        /* Sel tabel */
        #users-page #usersTable tbody td {
            background-color: rgba(15, 23, 42, 0.95) !important;
            color: var(--text-secondary) !important;
            border-color: var(--line-soft) !important;
            vertical-align: middle;
        }

        /* Hover efek */
        #users-page #usersTable tbody tr:hover td {
            background-color: rgba(255, 122, 89, .12) !important;
            color: var(--text-primary) !important;
        }

        /* Hilangkan strip Bootstrap */
        #users-page .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: rgba(15, 23, 42, 0.95) !important;
        }

        /* DataTables wrapper background */
        #users-page .dataTables_wrapper {
            background: transparent !important;
        }

        /* ---------- Kontrol DataTables ---------- */
        #users-page .dataTables_wrapper .dataTables_filter label,
        #users-page .dataTables_wrapper .dataTables_length label,
        #users-page .dataTables_wrapper .dataTables_info {
            color: var(--text-secondary);
            font-weight: 500;
        }

        #users-page .dataTables_wrapper .dataTables_filter input,
        #users-page .dataTables_wrapper .dataTables_length select {
            background: var(--control-bg);
            border: 1px solid var(--control-border);
            color: var(--text-primary);
            border-radius: 12px;
            padding: .5rem .75rem;
            outline: none;
            transition: all .15s ease;
        }

        #users-page .dataTables_wrapper .dataTables_filter input:focus,
        #users-page .dataTables_wrapper .dataTables_length select:focus {
            border-color: transparent;
            box-shadow: 0 0 0 .2rem var(--focus-ring-1), 0 0 0 .35rem var(--focus-ring-2);
            background: var(--control-bg-hover);
        }

        /* ---------- Pagination DataTables - Tema Gelap ---------- */
        #users-page .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
            text-align: center;
        }

        #users-page .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: var(--text-secondary) !important;
            border: 1px solid var(--line-soft) !important;
            background: rgba(15, 23, 42, 0.85) !important;
            border-radius: 10px !important;
            margin: 0 .25rem;
            font-weight: 500;
            transition: all .2s ease;
            box-shadow: 0 0 4px rgba(0, 0, 0, .25);
        }

        #users-page .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg,
                    rgba(255, 122, 89, .35),
                    rgba(95, 124, 255, .25)) !important;
            color: #fff !important;
            border-color: rgba(255, 122, 89, .5) !important;
            box-shadow: 0 0 6px rgba(255, 122, 89, .25);
        }

        #users-page .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(135deg,
                    rgba(255, 122, 89, .25),
                    rgba(95, 124, 255, .2)) !important;
            color: var(--text-primary) !important;
            border-color: rgba(255, 122, 89, .4) !important;
            transform: translateY(-1px);
        }

        #users-page .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: .4 !important;
            background: rgba(15, 23, 42, 0.6) !important;
            border-color: var(--line-soft) !important;
            color: var(--text-secondary) !important;
            cursor: not-allowed !important;
        }

        /* ---------- Tombol Aksi ---------- */
        #users-page .btn-icon {
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

        #users-page .btn-icon:active {
            transform: scale(.94);
        }

        #users-page .btn-warning {
            background: linear-gradient(135deg, var(--warning), #ff9500);
            border: none;
            color: #fff;
            box-shadow: 0 4px 12px rgba(255, 165, 2, .25);
        }

        #users-page .btn-warning:hover {
            background: linear-gradient(135deg, #ff9500, var(--warning));
            box-shadow: 0 6px 16px rgba(255, 165, 2, .35);
        }

        #users-page .btn-danger {
            background: linear-gradient(135deg, var(--danger), #ff5252);
            border: none;
            color: #fff;
            box-shadow: 0 4px 12px rgba(255, 107, 107, .25);
        }

        #users-page .btn-danger:hover {
            background: linear-gradient(135deg, #ff5252, var(--danger));
            box-shadow: 0 6px 16px rgba(255, 107, 107, .35);
        }

        /* ---------- Modal dengan glass effect ---------- */
        #users-page .modal-content {
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

        #users-page .modal-header {
            border-bottom: 1px dashed var(--line-dashed);
            background: rgba(255, 255, 255, .03);
            padding: 1.25rem;
        }

        #users-page .modal-header .modal-title {
            color: var(--text-primary);
            font-weight: 700;
        }

        #users-page .modal-body {
            padding: 1.5rem;
        }

        #users-page .modal-footer {
            border-top: 1px dashed var(--line-dashed);
            padding: 1.25rem;
            background: rgba(0, 0, 0, .15);
        }

        /* ---------- Form controls ---------- */
        #users-page .form-label {
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: .5rem;
            font-size: .9rem;
        }

        #users-page .form-control,
        #users-page .form-select {
            background: var(--control-bg);
            border: 1px solid var(--control-border);
            color: var(--text-primary);
            border-radius: 12px;
            padding: .6rem .9rem;
            transition: all .15s ease;
        }

        #users-page .form-control::placeholder {
            color: var(--text-muted);
        }

        #users-page .form-control:hover:not(:disabled),
        #users-page .form-select:hover:not(:disabled) {
            background: var(--control-bg-hover);
            border-color: rgba(255, 255, 255, .2);
        }

        #users-page .form-control:focus,
        #users-page .form-select:focus {
            border-color: transparent;
            box-shadow: 0 0 0 .2rem var(--focus-ring-1), 0 0 0 .35rem var(--focus-ring-2);
            background: var(--control-bg-hover);
            color: var(--text-primary);
        }

        #users-page .form-select option {
            background: rgba(15, 23, 42, 0.95);
            color: var(--text-primary);
        }

        #users-page .form-text {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        /* ---------- Alert ---------- */
        #users-page .alert-danger {
            background: rgba(255, 107, 107, .15);
            border: 1px solid rgba(255, 107, 107, .3);
            color: #ffcdd2;
            border-radius: 12px;
        }

        /* ---------- Tombol Modal ---------- */
        #users-page .btn-secondary {
            background: var(--control-bg);
            border: 1px solid var(--control-border);
            color: var(--text-secondary);
            border-radius: 12px;
            padding: .6rem 1.2rem;
            font-weight: 600;
        }

        #users-page .btn-secondary:hover {
            background: var(--control-bg-hover);
            color: var(--text-primary);
            border-color: var(--control-border);
        }

        #users-page .btn-primary {
            background: linear-gradient(135deg, var(--accent-2), var(--accent-1));
            border: none;
            color: #fff;
            border-radius: 12px;
            padding: .6rem 1.2rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(95, 124, 255, .25);
        }

        #users-page .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-1), var(--accent-2));
            box-shadow: 0 6px 16px rgba(95, 124, 255, .35);
        }

        /* ---------- Close button di modal ---------- */
        #users-page .btn-close {
            filter: brightness(0) invert(1);
            opacity: .7;
        }

        #users-page .btn-close:hover {
            opacity: 1;
        }

        /* ---------- Badge Role ---------- */
        #users-page .badge {
            padding: .4rem .8rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        #users-page .badge.bg-danger {
            background: linear-gradient(135deg, var(--danger), #ff5252) !important;
        }

        #users-page .badge.bg-warning {
            background: linear-gradient(135deg, var(--warning), #ff9500) !important;
        }

        #users-page .badge.bg-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268) !important;
        }

        /* ---------- Responsif ---------- */
        @media (max-width: 768px) {
            #users-page .card-header.theme {
                flex-direction: column;
                align-items: flex-start !important;
                gap: .75rem;
            }

            #users-page .btn-icon {
                width: 32px;
                height: 32px;
                font-size: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div id="users-page">
        <div class="container-xxl py-3">
            <div class="card">
                <div class="card-header theme d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bx bx-user"></i> Manajemen User
                    </h5>
                    <button class="btn btn-add" id="btnAdd" type="button">
                        <i class="bx bx-plus"></i> Tambah User
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered align-middle" id="usersTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
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
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="userForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="userModalLabel">Tambah User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            @csrf
                            <input type="hidden" id="id" name="id">

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="contoh@email.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="ADMIN">ADMIN</option>
                                    <option value="OFFICIAL">OFFICIAL</option>
                                    <option value="USER">USER</option>
                                </select>
                            </div>

                            <div class="mb-3 password-group">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Minimal 6 karakter">
                                <div class="form-text">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password.
                                </div>
                            </div>

                            <div class="mb-3 password-group">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="Ulangi password">
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
            let table = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.users.data') }}',
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
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role',
                        render: function(data) {
                            let badgeClass = 'bg-secondary';
                            if (data === 'ADMIN') badgeClass = 'bg-danger';
                            else if (data === 'OFFICIAL') badgeClass = 'bg-warning';
                            return `<span class="badge ${badgeClass}">${data}</span>`;
                        }
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

            // Buka modal Tambah
            $('#btnAdd').on('click', function() {
                $('#userForm')[0].reset();
                $('#id').val('');
                $('.password-group').show();
                $('#errorMsg').addClass('d-none').text('');
                $('#userModalLabel').text('Tambah User');
                new bootstrap.Modal(document.getElementById('userModal')).show();
            });

            // Submit form (tambah / update)
            $('#userForm').on('submit', function(e) {
                e.preventDefault();

                let id = $('#id').val();
                let url = id ? `/admin/users/${id}` : '{{ route('admin.users.store') }}';
                let method = id ? 'PUT' : 'POST';

                // Saat edit, password boleh kosong
                if (id && !$('#password').val()) {
                    $('#password, #password_confirmation').prop('disabled', true);
                }

                $('#btnSave').prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            bootstrap.Modal.getInstance(document.getElementById('userModal'))
                                .hide();
                            table.ajax.reload(null, false);
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        $('#errorMsg').removeClass('d-none').text(message);
                    },
                    complete: function() {
                        $('#btnSave').prop('disabled', false).text('Simpan');
                        $('#password, #password_confirmation').prop('disabled', false);
                    }
                });
            });

            // Edit
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');

                $.get(`/admin/users/${id}/edit`, function(res) {
                    $('#id').val(res.id);
                    $('#name').val(res.name);
                    $('#email').val(res.email);
                    $('#role').val(res.role);
                    $('#password').val('');
                    $('#password_confirmation').val('');
                    $('.password-group').show();
                    $('#errorMsg').addClass('d-none').text('');
                    $('#userModalLabel').text('Edit User');
                    new bootstrap.Modal(document.getElementById('userModal')).show();
                }).fail(function(xhr) {
                    alert('Gagal mengambil data: ' + (xhr.responseJSON?.message ||
                        'Terjadi kesalahan'));
                });
            });

            // Hapus
            $(document).on('click', '.delete-btn', function() {
                if (!confirm('Yakin ingin menghapus user ini?')) return;

                let id = $(this).data('id');

                $.ajax({
                    url: `/admin/users/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.success) {
                            table.ajax.reload(null, false);
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
