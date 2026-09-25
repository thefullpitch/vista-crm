@extends('admin.layouts.app')
@section('title', 'Manage Shops')
@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Shops</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-list-ul text-primary me-2"></i>Shops</h2>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->guard('admin')->user()->hasRole('Super Admin'))
        <button type="button" class="btn btn-danger d-none" id="bulkDeleteBtn"><i class="bi bi-trash"></i> Bulk Delete</button>
        @endif
        @can('shops.create')
        <button type="button" class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#importModal"><i class="bi bi-upload"></i> Import</button>
        <a href="{{ route('admin.shops.export') }}" class="btn btn-success"><i class="bi bi-download"></i> Export</a>
        <a href="{{ route('admin.shops.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Shop</a>
        @endcan
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.shops.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Shops</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Upload CSV File</label>
                        <input type="file" name="csv_file" class="form-control" required accept=".csv">
                        <small class="text-muted d-block mt-2">Download <a href="{{ route('admin.shops.download-sample') }}">sample format</a></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .table th { background-color: #f8f9fa !important; color: #495057; font-weight: 600; }
</style>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable">
                <thead>
                    <tr>
                        @if(auth()->guard('admin')->user()->hasRole('Super Admin'))
                        <th width="30px"><input type="checkbox" id="selectAll"></th>
                        @endif
                        <th width="50px">No</th>
                        <th>Shop Name</th>
                        <th>Contact Person</th>
                        <th>Mobile</th>
                        <th>Zone</th>
                        <th>State</th>
                        <th>Status</th>
                        <th width="120px">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush
@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $('#dataTable').DataTable({
        processing: true, serverSide: true,
        ajax: "{{ route('admin.shops.index') }}",
        columns: [
            @if(auth()->guard('admin')->user()->hasRole('Super Admin'))
            {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false},
            @endif
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'contact_person', name: 'owner.name'},
            {data: 'contact_number', name: 'contact_number'},
            {data: 'zone_name', name: 'zone_name', orderable: false, searchable: false},
            {data: 'state_name', name: 'state.name'},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    // Handle Select All checkbox
    $('#selectAll').on('click', function() {
        $('.shop-checkbox').prop('checked', this.checked);
        toggleBulkDeleteButton();
    });

    // Handle individual checkboxes
    $('#dataTable').on('change', '.shop-checkbox', function() {
        if ($('.shop-checkbox:checked').length == $('.shop-checkbox').length) {
            $('#selectAll').prop('checked', true);
        } else {
            $('#selectAll').prop('checked', false);
        }
        toggleBulkDeleteButton();
    });

    function toggleBulkDeleteButton() {
        if ($('.shop-checkbox:checked').length > 0) {
            $('#bulkDeleteBtn').removeClass('d-none');
        } else {
            $('#bulkDeleteBtn').addClass('d-none');
        }
    }

    // Handle Bulk Delete Action
    $('#bulkDeleteBtn').on('click', function() {
        if (confirm('WARNING: Are you sure you want to delete selected shops? This will also delete their associated users permanently!')) {
            var selectedIds = [];
            $('.shop-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length > 0) {
                $.ajax({
                    url: "{{ route('admin.shops.bulk-delete') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#dataTable').DataTable().ajax.reload();
                            $('#selectAll').prop('checked', false);
                            toggleBulkDeleteButton();
                            alert(response.message);
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred while deleting.');
                    }
                });
            }
        }
    });
</script>
@endpush

