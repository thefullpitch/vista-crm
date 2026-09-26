@extends('admin.layouts.app')
@section('title', 'Add Shop')
@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.shops.index') }}" class="text-decoration-none text-muted">Add Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add New Shop</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Add New Shop</h2>
    </div>
    <a href="{{ route('admin.shops.index') }}" class="btn btn-light border shadow-sm btn-sm px-3 py-2 fw-medium text-secondary hover-primary">
        <i class="bi bi-arrow-left me-1"></i> Back to Add Shop
    </a>
</div>

<style>
    .hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>

<div class="card shadow mb-4 border-0 rounded-3">
    <div class="card-body p-4">
        <form action="{{ route('admin.shops.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Shop Name <span class="text-danger">*</span></label>
                    <input type="text" name="firm_name" class="form-control @error('firm_name') is-invalid @enderror" value="{{ old('firm_name') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Contact Person <span class="text-danger">*</span></label>
                    <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Mobile <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>State <span class="text-danger">*</span></label>
                    <select name="state_id" class="form-select @error('state_id') is-invalid @enderror" required>
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>City <span class="text-danger">*</span></label>
                    <select name="city_id" class="form-select @error('city_id') is-invalid @enderror" required>
                        <option value="">Select City</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Pincode <span class="text-danger">*</span></label>
                    <select name="pincode_id" class="form-select @error('pincode_id') is-invalid @enderror" required>
                        <option value="">Select Pincode</option>
                    </select>
                </div>
                <div class="col-md-8 mb-3">
                    <label>Address <span class="text-danger">*</span></label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" required>{{ old('address') }}</textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('select[name="state_id"]').on('change', function() {
            var state_id = $(this).val();
            if(state_id) {
                $.ajax({
                    url: "{{ route('admin.ajax.get-cities') }}",
                    type: "GET",
                    data: {state_id: state_id},
                    success:function(data) {
                        $('select[name="city_id"]').empty();
                        $('select[name="city_id"]').append('<option value="">Select City</option>');
                        $.each(data, function(key, value) {
                            $('select[name="city_id"]').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                        });
                        $('select[name="pincode_id"]').empty();
                        $('select[name="pincode_id"]').append('<option value="">Select Pincode</option>');
                    }
                });
            }else{
                $('select[name="city_id"]').empty();
                $('select[name="pincode_id"]').empty();
            }
        });

        $('select[name="city_id"]').on('change', function() {
            var city_id = $(this).val();
            if(city_id) {
                $.ajax({
                    url: "{{ route('admin.ajax.get-pincodes') }}",
                    type: "GET",
                    data: {city_id: city_id},
                    success:function(data) {
                        $('select[name="pincode_id"]').empty();
                        $('select[name="pincode_id"]').append('<option value="">Select Pincode</option>');
                        $.each(data, function(key, value) {
                            $('select[name="pincode_id"]').append('<option value="'+ value.id +'">'+ value.pincode +'</option>');
                        });
                    }
                });
            }else{
                $('select[name="pincode_id"]').empty();
            }
        });
    });
</script>
@endpush

