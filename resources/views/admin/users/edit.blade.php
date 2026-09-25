@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted">Users</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center mt-2">
            <div class="avatar me-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=e9ecef&color=0d6efd&size=56" class="rounded-circle shadow-sm" alt="Avatar">
            </div>
            <div>
                <h2 class="h4 mb-1 fw-bold text-dark">{{ $user->name }}</h2>
                <span class="badge bg-{{ $user->status == 'Approved' || $user->status == 'Active' || $user->status == 1 ? 'success' : 'secondary' }} fw-normal">
                    {{ $user->status == 1 || $user->status == 'Active' || $user->status == 'Approved' ? 'Approved' : ($user->status == 0 ? 'Inactive' : $user->status) }}
                </span>
                <span class="text-muted small ms-2"><i class="bi bi-telephone-fill me-1"></i> {{ $user->mobile }}</span>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-light border shadow-sm btn-sm px-3 py-2 fw-medium text-secondary hover-primary">
        <i class="bi bi-arrow-left me-1"></i> Back to Users
    </a>
</div>

<style>
    .hover-primary:hover {
        background-color: #0d6efd !important;
        color: white !important;
        border-color: #0d6efd !important;
    }
    .page-header {
        background: white;
        padding: 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    /* Colorful Toggle Switches */
    .form-switch .form-check-input {
        background-color: #dc3545; /* Red when OFF */
        border-color: #dc3545;
    }
    .form-switch .form-check-input:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }
    .form-switch .form-check-input:checked {
        background-color: #198754; /* Green when ON */
        border-color: #198754;
    }
    .form-switch .form-check-input:checked:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }
</style>

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    /* Additional custom styling to make Select2 look premium */
    .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        min-height: 45px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
        background-color: #0d6efd;
        color: white;
        border: none;
        border-radius: 0.25rem;
        padding: 2px 8px;
        margin-top: 6px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove:hover {
        background: transparent;
        color: #ffcccc;
    }
</style>
@endpush

<div class="card shadow mb-4 border-0 rounded-3">
    @php
        $kycPoints = 0;
        $basicInfoComplete = !empty($user->name) && !empty($user->mobile) && !empty($user->address) && !empty($user->state_id) && !empty($user->city_id) && !empty($user->pincode_id) && !empty($user->profile_photo) && $user->is_photo_verified;
        if ($basicInfoComplete) $kycPoints += 20;
        
        $aadharComplete = !empty($user->aadhar_number) && $user->is_aadhar_verified;
        if ($aadharComplete) $kycPoints += 20;
        
        $panComplete = !empty($user->pan_number) && $user->is_pan_verified;
        if ($panComplete) $kycPoints += 20;
        
        $bankComplete = !empty($user->bank_account_number) && !empty($user->ifsc_code) && $user->is_bank_verified;
        if ($bankComplete) $kycPoints += 20;
        
        $invoiceComplete = !empty($user->invoice_sample_file) && $user->is_invoice_verified;
        if ($invoiceComplete) $kycPoints += 20;
    @endphp
    <div class="card-body p-4">
        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h5 class="text-primary border-bottom pb-2 mb-3">Profile Photo</h5>
            <div class="row mb-4 align-items-center">
                <div class="col-auto">
                    @php
                        $avatarUrl = $user->profile_photo ? asset('storage/'.$user->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=e9ecef&color=0d6efd&size=100';
                    @endphp
                    <a href="javascript:void(0)" onclick="openImageModal('{{ $avatarUrl }}')">
                        <img src="{{ $avatarUrl }}" class="rounded-circle shadow-sm border" width="100" height="100" style="object-fit: cover;" alt="Profile Photo">
                    </a>
                </div>
                <div class="col">
                    <label class="form-label">Upload New Photo</label>
                    <input type="file" name="profile_photo" class="form-control" accept="image/*">
                    <small class="text-muted">Recommended size: 200x200px. Formats: JPG, PNG, WEBP.</small>
                </div>
            </div>

            <h5 class="text-primary border-bottom pb-2 mb-3">Basic Information</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Mobile <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">User Type</label>
                    <select name="user_type" id="user_type" class="form-select">
                        <option value="">Select Type</option>
                        <option value="Shop Boy" {{ $user->user_type == 'Shop Boy' ? 'selected' : '' }}>Shop Boy</option>
                        <option value="Installer" {{ $user->user_type == 'Installer' ? 'selected' : '' }}>Installer</option>
                        <option value="Women Entrepreneurs" {{ $user->user_type == 'Women Entrepreneurs' ? 'selected' : '' }}>Women Entrepreneurs</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ $user->gender == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="{{ old('dob', $user->dob) }}">
                </div>
            </div>

            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Location & Address</h5>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">State</label>
                    <select name="state_id" id="state_id" class="form-select">
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ $user->state_id == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">City</label>
                    <select name="city_id" id="city_id" class="form-select">
                        <option value="">Select City</option>
                        <option value="{{ $user->city_id }}" selected>{{ $user->city->name ?? '' }}</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Pincode</label>
                    <select name="pincode_id" id="pincode_id" class="form-select">
                        <option value="">Select Pincode</option>
                        <option value="{{ $user->pincode_id }}" selected>{{ $user->pincode->pincode ?? '' }}</option>
                    </select>
                </div>
            </div>

            <div class="row mt-4" id="assigned-shops-section" style="display: none;">
                <div class="col-md-12 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <label class="form-label text-primary fw-bold mb-0">Assigned Shops</label>
                            <small class="text-muted d-block" id="shop-limit-text"></small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="show_all_shops">
                            <label class="form-check-label small text-muted" for="show_all_shops">Include shops from other pincodes</label>
                        </div>
                    </div>
                    <select name="shop_ids[]" id="shop_ids" class="form-select" multiple>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ $user->shops->contains($shop->id) ? 'selected' : '' }}>
                                {{ $shop->name }} - {{ $shop->owner->name ?? 'No Owner' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Identity & Banking Details</h5>
            @php
                $isSuperAdmin = auth()->guard('admin')->user()->hasRole('Super Admin');
            @endphp
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label text-muted fw-semibold mb-0">Aadhar Number</label>
                        <div class="form-check form-switch m-0" title="{{ ($user->is_aadhar_verified && !$isSuperAdmin) ? 'Only Super Admin can change this' : 'Mark as Verified' }}">
                            <input class="form-check-input" type="checkbox" name="is_aadhar_verified" value="1" {{ $user->is_aadhar_verified ? 'checked' : '' }} {{ ($user->is_aadhar_verified && !$isSuperAdmin) ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-fingerprint text-secondary"></i></span>
                        <input type="text" name="aadhar_number" class="form-control border-start-0 ps-0" placeholder="12-digit Aadhar number" value="{{ old('aadhar_number', $user->aadhar_number) }}">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label text-muted fw-semibold mb-0">PAN Number</label>
                        <div class="form-check form-switch m-0" title="{{ ($user->is_pan_verified && !$isSuperAdmin) ? 'Only Super Admin can change this' : 'Mark as Verified' }}">
                            <input class="form-check-input" type="checkbox" name="is_pan_verified" value="1" {{ $user->is_pan_verified ? 'checked' : '' }} {{ ($user->is_pan_verified && !$isSuperAdmin) ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-card-text text-secondary"></i></span>
                        <input type="text" name="pan_number" class="form-control border-start-0 ps-0" placeholder="10-character PAN number" value="{{ old('pan_number', $user->pan_number) }}">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label text-muted fw-semibold mb-0">Bank Account Number</label>
                        <div class="form-check form-switch m-0" title="{{ ($user->is_bank_verified && !$isSuperAdmin) ? 'Only Super Admin can change this' : 'Mark as Verified' }}">
                            <input class="form-check-input" type="checkbox" name="is_bank_verified" value="1" {{ $user->is_bank_verified ? 'checked' : '' }} {{ ($user->is_bank_verified && !$isSuperAdmin) ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-bank text-secondary"></i></span>
                        <input type="text" name="bank_account_number" class="form-control border-start-0 ps-0" placeholder="Enter account number" value="{{ old('bank_account_number', $user->bank_account_number) }}">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label text-muted fw-semibold mb-1 d-block" style="height: 24px;">IFSC Code</label>
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-upc-scan text-secondary"></i></span>
                        <input type="text" name="ifsc_code" class="form-control border-start-0 ps-0" placeholder="Enter IFSC code" value="{{ old('ifsc_code', $user->ifsc_code) }}">
                    </div>
                </div>
            </div>

            <h6 class="text-secondary mb-3 mt-2">Upload Documents</h6>
            <div class="row">

                <div class="col-md-3 mb-3" id="invoice_sample_section" style="display: {{ $user->user_type == 'Shop Boy' ? 'block' : 'none' }};">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label text-muted small mb-0">Invoice Sample</label>
                        <div class="form-check form-switch m-0" title="Mark as Verified">
                            <input class="form-check-input" type="checkbox" name="is_invoice_verified" value="1" {{ $user->is_invoice_verified ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="input-group shadow-sm rounded">
                        <span class="input-group-text bg-light text-primary"><i class="bi bi-cloud-upload"></i></span>
                        <input type="file" name="invoice_sample_file" class="form-control">
                    </div>
                    @if($user->invoice_sample_file)
                        <div class="mt-2">
                            @if(in_array(strtolower(pathinfo($user->invoice_sample_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                <a href="javascript:void(0)" onclick="openImageModal('{{ asset('storage/'.$user->invoice_sample_file) }}')">
                                    <img src="{{ asset('storage/'.$user->invoice_sample_file) }}" alt="Invoice Sample" class="img-thumbnail shadow-sm rounded" style="max-height: 100px; object-fit: cover;">
                                </a>
                            @else
                                <a href="{{ asset('storage/'.$user->invoice_sample_file) }}" target="_blank" class="btn btn-sm btn-light border"><i class="bi bi-file-pdf text-danger"></i> View Document</a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="col-md-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label text-muted small mb-0">Profile Photo</label>
                        <div class="form-check form-switch m-0" title="Mark as Verified">
                            <input class="form-check-input" type="checkbox" name="is_photo_verified" value="1" {{ $user->is_photo_verified ? 'checked' : '' }}>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">Verify the uploaded profile photo shown above.</small>
                </div>
            </div>

            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Account & Status</h5>
            <div class="row">
                <div class="col-md-4 mb-3 d-none">
                    <label class="form-label text-primary fw-bold">Base Reward Points <i class="bi bi-star-fill text-warning"></i></label>
                    <input type="number" step="0.01" name="wallet_balance" class="form-control border-primary" value="{{ old('wallet_balance', $user->wallet_balance) }}">
                    <small class="text-muted d-block mt-1">Total Points: <strong>{{ number_format(($user->wallet_balance ?? 0) + $kycPoints, 0) }}</strong> (includes {{ $kycPoints }} KYC Score)</small>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Account Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="Pending" {{ $user->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ $user->status == 'Approved' || $user->status == 'Active' || $user->status == 1 ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ $user->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="Suspended" {{ $user->status == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Remark <span class="text-danger">*</span></label>
                    <textarea name="status_remark" class="form-control" rows="2" required placeholder="Enter reason for status">{{ old('status_remark', $user->status_remark) }}</textarea>
                </div>


                <div class="col-md-12 mb-4">
                    <label class="form-label">Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                    <input type="password" name="password" class="form-control">
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

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Image Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center pt-0">
        <img src="" id="modalPreviewImage" class="img-fluid rounded shadow-lg" style="max-height: 85vh;">
      </div>
    </div>
  </div>
</div>

<script>
function openImageModal(url) {
    document.getElementById('modalPreviewImage').src = url;
    var myModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
    myModal.show();
}

$(document).ready(function() {
    // Initialize Select2 for Shops
    $('#shop_ids').select2({
        theme: 'bootstrap-5',
        placeholder: "Search and select shops...",
        allowClear: true,
        width: '100%'
    });

    var oldCity = "{{ old('city_id', $user->city_id) }}";
    var oldPincode = "{{ old('pincode_id', $user->pincode_id) }}";

    function loadCities(state_id, selected_id = null) {
        $('#city_id').html('<option value="">Loading...</option>');
        $.get("{{ route('admin.ajax.get-cities') }}", { state_id: state_id }, function(data) {
            var options = '<option value="">Select City</option>';
            $.each(data, function(index, city) {
                options += '<option value="'+city.id+'" '+(selected_id == city.id ? 'selected' : '')+'>'+city.name+'</option>';
            });
            $('#city_id').html(options);
            if(selected_id) { $('#city_id').trigger('change'); }
        });
    }

    function loadPincodes(city_id, selected_id = null) {
        $('#pincode_id').html('<option value="">Loading...</option>');
        $.get("{{ route('admin.ajax.get-pincodes') }}", { city_id: city_id }, function(data) {
            var options = '<option value="">Select Pincode</option>';
            $.each(data, function(index, pincode) {
                options += '<option value="'+pincode.id+'" '+(selected_id == pincode.id ? 'selected' : '')+'>'+pincode.pincode+'</option>';
            });
            $('#pincode_id').html(options);
        });
    }

    $('#state_id').change(function() {
        var state_id = $(this).val();
        if(state_id) {
            loadCities(state_id);
            $('#pincode_id').html('<option value="">Select Pincode</option>');
        } else {
            $('#city_id').html('<option value="">Select City</option>');
            $('#pincode_id').html('<option value="">Select Pincode</option>');
        }
    });

    $('#city_id').change(function() {
        var city_id = $(this).val();
        if(city_id) {
            loadPincodes(city_id);
        } else {
            $('#pincode_id').html('<option value="">Select Pincode</option>');
        }
    });

    function loadShops(pincode_id, selected_ids = []) {
        var $shopSelect = $('#shop_ids');
        var showAll = $('#show_all_shops').is(':checked');
        $shopSelect.html('<option value="">Loading...</option>');
        $.get("{{ route('admin.ajax.get-shops') }}", { pincode_id: pincode_id, all: showAll }, function(data) {
            var options = '';
            $.each(data, function(index, shop) {
                var isSelected = selected_ids.includes(shop.id.toString()) ? 'selected' : '';
                var shopPincode = shop.pincode ? shop.pincode.pincode : 'No Pincode';
                var displayText = shop.name + ' - ' + (shop.owner ? shop.owner.name : 'No Owner');
                if (showAll) {
                    displayText += ' (' + shopPincode + ')';
                }
                options += '<option value="'+shop.id+'" '+isSelected+'>'+displayText+'</option>';
            });
            if(options === '') options = '<option value="" disabled>No shops found</option>';
            $shopSelect.html(options);
            $shopSelect.trigger('change');
        });
    }

    $('#pincode_id').change(function() {
        var pincode_id = $(this).val();
        var user_type = $('#user_type').val();
        var selected_ids = $('#shop_ids').val() || [];
        if(pincode_id && ['Shop Boy', 'Installer', 'Women Entrepreneurs'].includes(user_type)) {
            loadShops(pincode_id, selected_ids);
        } else if (!pincode_id && $('#show_all_shops').is(':checked') && ['Shop Boy', 'Installer', 'Women Entrepreneurs'].includes(user_type)) {
            loadShops(null, selected_ids);
        } else {
            $('#shop_ids').html('');
        }
    });

    $('#show_all_shops').change(function() {
        var pincode_id = $('#pincode_id').val();
        var selected_ids = $('#shop_ids').val() || [];
        loadShops(pincode_id, selected_ids);
    });

    function toggleShopsSection() {
        var user_type = $('#user_type').val();
        var pincode_id = $('#pincode_id').val();
        
        if (['Shop Boy', 'Installer', 'Women Entrepreneurs'].includes(user_type)) {
            $('#assigned-shops-section').show();
            
            var limitText = 'Must select at least 1 shop.';
            if (user_type === 'Shop Boy') {
                limitText = 'Shop Boy must belong to exactly 1 shop.';
                $('#invoice_sample_section').show();
            } else {
                $('#invoice_sample_section').hide();
            }
            if (user_type === 'Installer') limitText = 'Installer can belong to a maximum of 5 shops.';
            if (user_type === 'Women Entrepreneurs') limitText = 'Women Entrepreneurs can belong to a maximum of 3 shops.';
            $('#shop-limit-text').text(limitText);

        } else {
            $('#assigned-shops-section').hide();
            $('#invoice_sample_section').hide();
        }
    }

    $('#user_type').change(function() {
        toggleShopsSection();
        var pincode_id = $('#pincode_id').val();
        if(pincode_id && ['Shop Boy', 'Installer', 'Women Entrepreneurs'].includes($(this).val())) {
             loadShops(pincode_id, {!! json_encode($user->shops->pluck('id')) !!}.map(String));
        }
    });

    $('form').submit(function(e) {
        var user_type = $('#user_type').val();
        if (['Shop Boy', 'Installer', 'Women Entrepreneurs'].includes(user_type)) {
            var selectedShops = $('#shop_ids').val() || [];
            if (selectedShops.length < 1) {
                alert('Please select at least 1 shop.');
                e.preventDefault();
                return false;
            }
            if (user_type === 'Shop Boy' && selectedShops.length > 1) {
                alert('Shop Boy can only belong to 1 shop.');
                e.preventDefault();
                return false;
            }
            if (user_type === 'Installer' && selectedShops.length > 5) {
                alert('Installer can belong to a maximum of 5 shops.');
                e.preventDefault();
                return false;
            }
            if (user_type === 'Women Entrepreneurs' && selectedShops.length > 3) {
                alert('Women Entrepreneurs can belong to a maximum of 3 shops.');
                e.preventDefault();
                return false;
            }
        }
    });

    toggleShopsSection();

    // Initialize dropdowns on page load if values exist
    var currentState = $('#state_id').val();
    if(currentState) {
        loadCities(currentState, oldCity);
        if(oldCity) {
            setTimeout(function() { 
                loadPincodes(oldCity, oldPincode); 
            }, 500);
        }
    }
});
</script>
@endpush

