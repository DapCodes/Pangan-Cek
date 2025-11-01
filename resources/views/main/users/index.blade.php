@extends('layouts.app')

@section('title', 'Manajemen User - PanganCek Admin')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Manajemen User</h5>
                <button class="btn btn-light btn-sm" id="btnAdd" type="button">
                    <i class="bx bx-plus"></i> Tambah User
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="usersTable" style="width:100%">
                    <thead class="table-light">
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
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
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
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Minimal 6 karakter.</div>
                        </div>

                        <div class="mb-3 password-group">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
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
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function () {
            // Inisialisasi DataTable
            let table = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.users.data') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name',       name: 'name' },
                    { data: 'email',      name: 'email' },
                    { data: 'role',       name: 'role' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action',     name: 'action', orderable: false, searchable: false }
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
                    paginate: { first: "Pertama", previous: "Sebelumnya", next: "Selanjutnya", last: "Terakhir" }
                }
            });

            // Buka modal Tambah
            $('#btnAdd').on('click', function () {
                $('#userForm')[0].reset();
                $('#id').val('');
                $('.password-group').show();                  // password wajib saat tambah
                $('#errorMsg').addClass('d-none').text('');
                $('#userModalLabel').text('Tambah User');
                new bootstrap.Modal(document.getElementById('userModal')).show();
            });

            // Submit form (tambah / update)
            $('#userForm').on('submit', function (e) {
                e.preventDefault();

                let id = $('#id').val();
                let url = id ? `/admin/users/${id}` : '{{ route('admin.users.store') }}';
                let method = id ? 'PUT' : 'POST';

                // Saat edit, password boleh kosong -> jangan kirim kalau kosong (biar tidak memicu validasi)
                if (id && !$('#password').val()) {
                    $('#password, #password_confirmation').prop('disabled', true);
                }

                $('#btnSave').prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function (res) {
                        if (res.success) {
                            // Tutup modal & reload tabel
                            bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
                            table.ajax.reload(null, false);
                            alert(res.message);
                        }
                    },
                    error: function (xhr) {
                        let message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        $('#errorMsg').removeClass('d-none').text(message);
                    },
                    complete: function () {
                        $('#btnSave').prop('disabled', false).text('Simpan');
                        $('#password, #password_confirmation').prop('disabled', false);
                    }
                });
            });

            // Edit
            $(document).on('click', '.edit-btn', function () {
                let id = $(this).data('id');

                $.get(`/admin/users/${id}/edit`, function (res) {
                    $('#id').val(res.id);
                    $('#name').val(res.name);
                    $('#email').val(res.email);
                    $('#role').val(res.role);
                    $('#password').val('');
                    $('#password_confirmation').val('');
                    $('.password-group').show();               // tampilkan; user boleh kosongkan
                    $('#errorMsg').addClass('d-none').text('');
                    $('#userModalLabel').text('Edit User');
                    new bootstrap.Modal(document.getElementById('userModal')).show();
                }).fail(function (xhr) {
                    alert('Gagal mengambil data: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
                });
            });

            // Hapus
            $(document).on('click', '.delete-btn', function () {
                if (!confirm('Yakin ingin menghapus user ini?')) return;

                let id = $(this).data('id');

                $.ajax({
                    url: `/admin/users/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },    // kirim CSRF token
                    success: function (res) {
                        if (res.success) {
                            table.ajax.reload(null, false);
                            alert(res.message);
                        }
                    },
                    error: function (xhr) {
                        alert('Gagal menghapus: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
                    }
                });
            });
        });
    </script>
@endpush
