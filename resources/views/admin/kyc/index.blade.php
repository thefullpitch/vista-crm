@extends('admin.layouts.app')
@section('title', 'KYC Management')
@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">KYC Management</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-shield-check text-primary me-2"></i>KYC Directory</h2>
    </div>
</div>

<style>
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .table th { background-color: #f8f9fa !important; color: #495057; font-weight: 600; }
</style>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle border" id="dataTable">
                <thead class="table-light text-secondary">
                    <tr>
                        <th width="50">No</th>
                        <th>User Name</th>
                        <th>User Type</th>
                        <th>Aadhar No</th>
                        <th>PAN No</th>
                        <th>Bank A/C</th>
                        <th>Status</th>
                        <th width="80">Action</th>
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
    $(document).ready(function() {
        $('#dataTable').DataTable({
            processing: true, 
            serverSide: true,
            ajax: "{{ route('admin.kyc.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'user_name', name: 'name'},
                {data: 'user_type', name: 'user_type'},
                {data: 'aadhar_number', name: 'aadhar_number', render: function(data) { return data ? data : '-'; }},
                {data: 'pan_number', name: 'pan_number', render: function(data) { return data ? data : '-'; }},
                {data: 'bank_account_number', name: 'bank_account_number', render: function(data) { return data ? data : '-'; }},
                {data: 'kyc_status', name: 'kyc_status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });
</script>
@endpush

