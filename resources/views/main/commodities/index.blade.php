@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manajemen Komoditas</h5>
            <button class="btn btn-light btn-sm" id="btnAdd">
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
<div class="modal fade" id="commodityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="commodityForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Komoditas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" id="id" name="id">

                <div class="mb-3">
                    <label>Nama Komoditas</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label>Satuan</label>
                    <select class="form-select" id="unit" name="unit">
                        <option value="kg">kg</option>
                        <option value="liter">liter</option>
                        <option value="butir">butir</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Kategori</label>
                    <select class="form-select" id="category" name="category">
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
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" id="btnSave" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function() {
    let table = $('#commoditiesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.commodities.data") }}',
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'unit', name: 'unit'},
            {data: 'category', name: 'category'},
            {data: 'created_at', name: 'created_at'},
            {data: 'updated_at', name: 'updated_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    $('#btnAdd').click(function() {
        $('#commodityForm')[0].reset();
        $('#id').val('');
        $('#errorMsg').addClass('d-none');
        $('.modal-title').text('Tambah Komoditas');
        $('#commodityModal').modal('show');
    });

    $('#commodityForm').submit(function(e) {
        e.preventDefault();
        let id = $('#id').val();
        let url = id ? `/admin/commodities/${id}` : '{{ route("admin.commodities.store") }}`;
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(res) {
                if(res.success){
                    $('#commodityModal').modal('hide');
                    table.ajax.reload();
                }
            },
            error: function(xhr){
                $('#errorMsg').removeClass('d-none').text(xhr.responseJSON.message);
            }
        });
    });

    $(document).on('click', '.edit-btn', function(){
        let id = $(this).data('id');
        $.get(`/admin/commodities/${id}/edit`, function(res){
            $('#id').val(res.id);
            $('#name').val(res.name);
            $('#unit').val(res.unit);
            $('#category').val(res.category);
            $('#errorMsg').addClass('d-none');
            $('.modal-title').text('Edit Komoditas');
            $('#commodityModal').modal('show');
        });
    });

    $(document).on('click', '.delete-btn', function(){
        if(!confirm('Yakin ingin menghapus komoditas ini?')) return;
        let id = $(this).data('id');
        $.ajax({
            url: `/admin/commodities/${id}`,
            method: 'DELETE',
            data: {_token: '{{ csrf_token() }}'},
            success: function(res){
                if(res.success){ table.ajax.reload(); }
            }
        });
    });
});
</script>
@endpush
