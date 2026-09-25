@extends('admin.layouts.app')
@section('title', 'Shop Details')
@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.shops.index') }}" class="text-decoration-none text-muted">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-info-circle text-primary me-2"></i>Shop Details: {{ $shop->name }}</h2>
    </div>
    <a href="{{ route('admin.shops.index') }}" class="btn btn-light border shadow-sm btn-sm px-3 py-2 fw-medium text-secondary hover-primary">
        <i class="bi bi-arrow-left me-1"></i> Back to Shop
    </a>
</div>

<style>
    .hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>

<div class="card shadow mb-4 border-0 rounded-3">
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th width="150">Shop Name</th><td>{{ $shop->name }}</td></tr>
                    <tr><th>Contact Person</th><td>{{ $shop->owner->name ?? 'N/A' }}</td></tr>
                    <tr><th>Mobile</th><td>{{ $shop->contact_number }}</td></tr>
                    <tr><th>Email</th><td>{{ $shop->owner->email ?? 'N/A' }}</td></tr>
                    <tr><th>Status</th>
                        <td>
                            @if($shop->status == 'Active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th width="150">State</th><td>{{ $shop->state->name ?? 'N/A' }}</td></tr>
                    <tr><th>City</th><td>{{ $shop->city->name ?? 'N/A' }}</td></tr>
                    <tr><th>Pincode</th><td>{{ $shop->pincode->pincode ?? 'N/A' }}</td></tr>
                    <tr><th>Address</th><td>{{ $shop->address }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

