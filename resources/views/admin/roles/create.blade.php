@extends('admin.layouts.app')
@section('title', 'Create Role')
@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}" class="text-decoration-none text-muted">Role</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Role</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Create Role</h2>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light border shadow-sm btn-sm px-3 py-2 fw-medium text-secondary hover-primary">
        <i class="bi bi-arrow-left me-1"></i> Back to Role
    </a>
</div>

<style>
    .hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>

<div class="card shadow mb-4 border-0 rounded-3">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="fw-bold mb-3">Permissions</label>
                <div class="row g-3">
                    @foreach($groupedPermissions as $group => $perms)
                        @php
                            $groupName = str_replace('_', ' ', $group);
                            if(strtolower($group) == 'invoice') $groupName = 'Shop Boy';
                            elseif(strtolower($group) == 'installations') $groupName = 'Installer';
                            elseif(strtolower($group) == 'leads') $groupName = 'Women Entrepreneurs';
                        @endphp
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm bg-light">
                                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                                    <h6 class="fw-bold text-primary mb-0">{{ $groupName }}</h6>
                                </div>
                                <div class="card-body">
                                    @foreach($perms as $value)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="permission[]" value="{{ $value->name }}" id="perm_{{ $value->id }}">
                                            <label class="form-check-label" for="perm_{{ $value->id }}">
                                                @php
                                                    $permLabel = explode('.', $value->name)[1] ?? $value->name;
                                                    if ($permLabel === 'view') $permLabel = 'Show Menu / View';
                                                @endphp
                                                {{ ucfirst($permLabel) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

