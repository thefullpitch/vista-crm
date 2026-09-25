@extends('admin.layouts.app')
@section('title', 'Compose Notification')
@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm border-top border-primary border-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-broadcast"></i> Compose Broadcast Notification</h5>
            </div>
            <div class="card-body p-4">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('admin.notifications.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Recipient(s) <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select select2 @error('user_id') is-invalid @enderror" required>
                            <option value="all">📢 Broadcast to ALL Active Mechanics</option>
                            <optgroup label="Direct Message Specific Mechanic">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->mobile }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted mt-1 d-block">Choose "Broadcast to ALL" to send a mass notification, or select a specific mechanic.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Notification Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. New Rewards Added!" required maxlength="255">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Message Body <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="5" placeholder="Write your notification message here..." required>{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary me-2 px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4" onclick="return confirm('Are you sure you want to send this notification? If you selected ALL Mechanics, this will broadcast to everyone.')"><i class="bi bi-send-fill"></i> Send Notification</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endpush

