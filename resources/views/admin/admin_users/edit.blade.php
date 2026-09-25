@extends('admin.layouts.app')
@section('title', 'Edit Admin User')
@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.admin-users.index') }}" class="text-decoration-none text-muted">Admin User</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Admin User</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Admin User</h2>
    </div>
    <a href="{{ route('admin.admin-users.index') }}" class="btn btn-light border shadow-sm btn-sm px-3 py-2 fw-medium text-secondary hover-primary">
        <i class="bi bi-arrow-left me-1"></i> Back to Admin User
    </a>
</div>

<style>
    .hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow mb-4 border-0 rounded-3">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.admin-users.update', $user->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password (Leave blank to keep current)</label>
                <input type="password" name="password" class="form-control" autocomplete="new-password">
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm-password" class="form-control" autocomplete="new-password">
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="roles[]" class="form-select" multiple required>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ in_array($role, $userRole) ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Zone (Optional)</label>
                <select name="zone_id" id="zone_id" class="form-select">
                    <option value="">Select Zone</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" {{ $user->zone_id == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>States (Optional)</label>
                @php
                    $selectedStates = [];
                    if (is_array($user->state_ids)) {
                        $selectedStates = $user->state_ids;
                    } elseif (is_string($user->state_ids)) {
                        $selectedStates = json_decode($user->state_ids, true) ?? [];
                    }
                @endphp
                <select name="state_ids[]" id="state_ids" class="form-select" multiple>
                    @if(isset($states))
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ in_array($state->id, old('state_ids', $selectedStates)) ? 'selected' : '' }}>{{ $state->name }}</option>
                        @endforeach
                    @endif
                </select>
                <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple states.</small>
            </div>
            <div class="mb-3">
                <label>Accessible User Types (Optional)</label>
                @php
                    $selectedTypes = [];
                    if (is_array($user->user_types)) {
                        $selectedTypes = $user->user_types;
                    } elseif (is_string($user->user_types)) {
                        $selectedTypes = json_decode($user->user_types, true) ?? [];
                    }
                @endphp
                <div>
                    @foreach($userTypes as $type)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="user_types[]" value="{{ $type }}" id="type_{{ Str::slug($type) }}" {{ in_array($type, old('user_types', $selectedTypes)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_{{ Str::slug($type) }}">{{ $type }}</label>
                        </div>
                    @endforeach
                </div>
                <small class="text-muted">If none selected, all user types are accessible.</small>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#zone_id').change(function() {
            var zone_id = $(this).val();
            var stateSelect = $('#state_ids');
            stateSelect.empty();
            
            if(zone_id) {
                $.ajax({
                    url: '{{ route("admin.ajax.get-states") }}',
                    type: 'GET',
                    data: { zone_id: zone_id },
                    success: function(data) {
                        $.each(data, function(key, value) {
                            stateSelect.append('<option value="'+ value.id +'">'+ value.name +'</option>');
                        });
                    }
                });
            }
        });
    });
</script>
@endsection

