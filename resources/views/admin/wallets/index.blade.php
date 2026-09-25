@extends('admin.layouts.app')
@section('title', 'User Wallets')
@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">User Wallets</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-wallet2 text-primary me-2"></i>User Wallets</h2>
    </div>
    
    <div>
        <select id="userTypeFilter" class="form-select form-select-sm border shadow-sm">
            <option value="">All User Types</option>
            @foreach($userTypes as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
    </div>
</div>

<style>
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .table th { background-color: #f8f9fa !important; color: #495057; font-weight: 600; }
</style>

<div class="card shadow mb-4 border-0 rounded-3">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="walletsTable">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Mobile</th>
                        <th>User Type</th>
                        <th>Wallet Balance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        var table = $('#walletsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.wallets.index') }}",
                data: function (d) {
                    d.user_type = $('#userTypeFilter').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'mobile', name: 'mobile'},
                {data: 'user_type', name: 'user_type'},
                {data: 'wallet_balance', name: 'wallet_balance'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        $('#userTypeFilter').change(function(){
            table.draw();
        });
    });
</script>
@endpush

