@extends('admin.layouts.app')

@section('title', 'Add Pincode')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.pincodes.index') }}" class="text-decoration-none text-muted">Add Pincode</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New Pincode</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Add New Pincode</h2>
    </div>
    <a href="{{ route('admin.pincodes.index') }}" class="btn btn-light border shadow-sm btn-sm px-3 py-2 fw-medium text-secondary hover-primary">
        <i class="bi bi-arrow-left me-1"></i> Back to Add Pincode
    </a>
</div>

<style>
    .hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>

<div class="card shadow mb-4 border-0 rounded-3">
    <div class="card-body p-4">
                <form action="{{ route('admin.pincodes.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="city_id" class="form-label">City</label>
                        <select name="city_id" id="city_id" class="form-select @error('city_id') is-invalid @enderror" required>
                            <option value="">Select City</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }} ({{ $city->state->name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        @error('city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="pincode" class="form-label">Pincode Number</label>
                        <input type="text" name="pincode" id="pincode" class="form-control @error('pincode') is-invalid @enderror" value="{{ old('pincode') }}" required>
                        @error('pincode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.pincodes.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Save Pincode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

