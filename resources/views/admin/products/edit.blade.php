@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none text-muted">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Product</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark">Edit Product</h2>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn btn-light border shadow-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<style>
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Subcategory <span class="text-danger">*</span></label>
                    <select name="subcategory_id" id="subcategory_id" class="form-select" required>
                        <option value="">Select Subcategory</option>
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" {{ old('subcategory_id', $product->subcategory_id) == $subcategory->id ? 'selected' : '' }}>{{ $subcategory->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Reward Points <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="reward_points" class="form-control" value="{{ old('reward_points', $product->reward_points ?? 0) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="Active" {{ old('status', $product->status) == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $product->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary px-4 fw-bold">Update Product</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#category_id').change(function() {
        var category_id = $(this).val();
        if (category_id) {
            $.ajax({
                type: "GET",
                url: "{{ route('admin.ajax.get-subcategories') }}?category_id=" + category_id,
                success: function(res) {
                    if (res) {
                        $("#subcategory_id").empty();
                        $("#subcategory_id").append('<option value="">Select Subcategory</option>');
                        $.each(res, function(key, value) {
                            $("#subcategory_id").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    } else {
                        $("#subcategory_id").empty();
                    }
                }
            });
        } else {
            $("#subcategory_id").empty();
            $("#subcategory_id").append('<option value="">Select Subcategory</option>');
        }
    });

    // Trigger on load if old category_id changed but form had error
    var oldCategoryId = "{{ old('category_id') }}";
    var oldSubcategoryId = "{{ old('subcategory_id') }}";
    var currentCategoryId = "{{ $product->category_id }}";
    
    if(oldCategoryId && oldCategoryId != currentCategoryId) {
        $.ajax({
            type: "GET",
            url: "{{ route('admin.ajax.get-subcategories') }}?category_id=" + oldCategoryId,
            success: function(res) {
                if (res) {
                    $("#subcategory_id").empty();
                    $("#subcategory_id").append('<option value="">Select Subcategory</option>');
                    $.each(res, function(key, value) {
                        var selected = (value.id == oldSubcategoryId) ? 'selected' : '';
                        $("#subcategory_id").append('<option value="' + value.id + '" '+selected+'>' + value.name + '</option>');
                    });
                }
            }
        });
    }
});
</script>
@endpush
