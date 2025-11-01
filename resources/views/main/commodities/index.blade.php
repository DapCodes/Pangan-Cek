@extends('layouts.app')

@section('title', 'Manajemen Komoditas - PanganCek Admin')

{{-- Tambahkan CSS DataTables --}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Manajemen Komoditas</h5>
                <button class="btn btn-light btn-sm" id="btnAdd" type="button">
                    <i class="bx bx-plus"></i> Tambah Komoditas
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="commoditiesTable" style="width:100%">
                    <thead class="table-light">
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
                            <input type="text" class="form-control" id="name" name="name" required>
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
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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
                console.log('Tombol Tambah diklik'); // Debug
                $('#commodityForm')[0].reset();
                $('#id').val('');
                $('#errorMsg').addClass('d-none').text('');
                $('#commodityModalLabel').text('Tambah Komoditas');

                // Gunakan Bootstrap 5 modal API
                var modal = new bootstrap.Modal(document.getElementById('commodityModal'));
                modal.show();
            });

            // Submit Form
            $('#commodityForm').on('submit', function(e) {
                e.preventDefault();

                let id = $('#id').val();
                let url = id ? `/admin/commodities/${id}` : '{{ route('admin.commodities.store') }}';
                let method = id ? 'PUT' : 'POST';

                // Disable tombol submit
                $('#btnSave').prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            // Tutup modal
                            var modal = bootstrap.Modal.getInstance(document.getElementById(
                                'commodityModal'));
                            modal.hide();

                            // Reload tabel
                            table.ajax.reload();

                            // Tampilkan notifikasi sukses (opsional)
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        $('#errorMsg').removeClass('d-none').text(message);
                    },
                    complete: function() {
                        // Enable kembali tombol submit
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

                    // Tampilkan modal
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
