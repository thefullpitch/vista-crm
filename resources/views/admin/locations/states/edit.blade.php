@extends('admin.layouts.app')

@section('title', 'Edit State')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.states.index') }}" class="text-decoration-none text-muted">State</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit State</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Edit State</h2>
    </div>
    <a href="{{ route('admin.states.index') }}" class="btn btn-light border shadow-sm btn-sm px-3 py-2 fw-medium text-secondary hover-primary">
        <i class="bi bi-arrow-left me-1"></i> Back to State
    </a>
</div>

<style>
    .hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>

<div class="card shadow mb-4 border-0 rounded-3">
    <div class="card-body p-4">
                <form action="{{ route('admin.states.update', $state->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">State Name</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $state->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.states.index') }}" class="btn btn-secondary">Cancel</a>
                        <hr>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Save Changes</button>
            </div></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

