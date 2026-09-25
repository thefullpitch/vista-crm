@extends('admin.layouts.app')
@section('title', 'View User Profile')

@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted">Users</a></li>
                <li class="breadcrumb-item active" aria-current="page">View Profile</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark">User Profile</h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.index') }}" class="btn btn-light border shadow-sm btn-sm px-3 py-2 fw-medium text-secondary hover-primary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        @if(auth()->guard('admin')->user()->can('users.edit'))
        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary shadow-sm btn-sm px-3 py-2 fw-medium">
            <i class="bi bi-pencil me-1"></i> Edit Profile
        </a>
        @endif
    </div>
</div>

<style>
    .page-header {
        background: white;
        padding: 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .hover-primary:hover {
        background-color: #0d6efd !important;
        color: white !important;
        border-color: #0d6efd !important;
    }
    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-size: 1rem;
        color: #212529;
        font-weight: 500;
    }
    .profile-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
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

<form action="{{ route('admin.users.status', $user->id) }}" method="POST">
    @csrf
    <div class="row">
    @php
        $isSuperAdmin = auth()->guard('admin')->user()->hasRole('Super Admin');
        $kycPoints = 0;
        $pointValue = ($user->user_type == 'Shop Boy') ? 20 : 25;
        
        $basicInfoComplete = !empty($user->name) && !empty($user->mobile) && !empty($user->address) && !empty($user->state_id) && !empty($user->city_id) && !empty($user->pincode_id) && !empty($user->profile_photo) && $user->is_photo_verified;
        if ($basicInfoComplete) $kycPoints += $pointValue;
        
        $aadharComplete = !empty($user->aadhar_number) && $user->is_aadhar_verified;
        if ($aadharComplete) $kycPoints += $pointValue;
        
        $panComplete = !empty($user->pan_number) && $user->is_pan_verified;
        if ($panComplete) $kycPoints += $pointValue;
        
        $bankComplete = !empty($user->bank_account_number) && !empty($user->ifsc_code) && $user->is_bank_verified;
        if ($bankComplete) $kycPoints += $pointValue;
        
        $invoiceComplete = false;
        if ($user->user_type == 'Shop Boy') {
            $invoiceComplete = !empty($user->invoice_sample_file) && $user->is_invoice_verified;
            if ($invoiceComplete) $kycPoints += 20;
        }
        
        $maxPoints = 100;
        $kycPercentage = $kycPoints;
    @endphp
    <!-- Left Column: Profile Card -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
            <div class="card-body p-0 text-center">
                <div class="profile-card py-5 border-bottom">
                    @php
                        $avatarUrl = $user->profile_photo ? asset('storage/'.$user->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0d6efd&color=fff&size=120';
                    @endphp
                    <a href="javascript:void(0)" onclick="openImageModal('{{ $avatarUrl }}')">
                        <img src="{{ $avatarUrl }}" class="rounded-circle shadow-sm mb-3 border border-3 border-white" width="120" height="120" style="object-fit: cover;">
                    </a>
                    <h4 class="mb-1 fw-bold text-dark">{{ $user->name }}</h4>
                    <p class="text-muted mb-2 fw-medium">{{ $user->user_type ?? 'Unassigned Type' }}</p>
                    
                    @php
                        $status = $user->status;
                        if ($status === 1 || $status === '1' || $status === 'Active') $status = 'Approved';
                        if ($status === 0 || $status === '0') $status = 'Inactive';
                        
                        $bg = 'secondary';
                        if($status == 'Approved') $bg = 'success';
                        if($status == 'Rejected') $bg = 'danger';
                        if($status == 'Suspended') $bg = 'dark';
                        if($status == 'Pending') $bg = 'warning text-dark';
                    @endphp
                    <span class="badge bg-{{ $bg }} px-3 py-2 rounded-pill shadow-sm">{{ $status }}</span>
                </div>
                
                <div class="p-4 text-start">
                    <h6 class="fw-bold mb-3 text-secondary text-uppercase small">Account Status Override</h6>
                    
                    <div class="mb-3">
                            <label class="form-label text-muted fw-semibold small">Status</label>
                            <select name="status" class="form-select bg-light mb-2">
                                <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Approved" {{ $status == 'Approved' || $status == 'Active' ? 'selected' : '' }}>Approved</option>
                                <option value="Suspended" {{ $status == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="Rejected" {{ $status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            
                            <label class="form-label text-muted fw-semibold small">Remark <span class="text-danger">*</span></label>
                            <textarea name="status_remark" class="form-control bg-light mb-3" rows="2" placeholder="Required for status update..." required>{{ $user->status_remark }}</textarea>
                            
                            <button class="btn btn-primary w-100 fw-medium" type="submit">Update Status</button>
                        </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Registered On</span>
                        <span class="fw-bold">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-info fw-bold">KYC Score</span>
                        <span class="badge bg-info rounded-pill fs-5 shadow-sm text-dark">{{ $kycPoints }} <i class="bi bi-shield-check fs-6"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Details Tabs/Cards -->
    <div class="col-xl-8 col-lg-7 mb-4">

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-person-badge text-primary me-2"></i> Profile & KYC Progress</h5>
                <div class="progress mb-3 shadow-sm" style="height: 25px; border-radius: 15px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated fw-bold fs-6 {{ $kycPoints == $maxPoints ? 'bg-success' : ($kycPercentage >= 60 ? 'bg-info' : 'bg-warning text-dark') }}" role="progressbar" style="width: {{ $kycPercentage }}%;" aria-valuenow="{{ $kycPoints }}" aria-valuemin="0" aria-valuemax="{{ $maxPoints }}">
                        {{ $kycPoints }} / {{ $maxPoints }} Points
                    </div>
                </div>
                <div class="row g-2 small">
                    <div class="col-md-4 col-6"><i class="bi {{ $basicInfoComplete ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-danger' }}"></i> Basic Info ({{ $pointValue }} pts)</div>
                    <div class="col-md-4 col-6"><i class="bi {{ $aadharComplete ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-danger' }}"></i> Aadhaar Verified ({{ $pointValue }} pts)</div>
                    <div class="col-md-4 col-6"><i class="bi {{ $panComplete ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-danger' }}"></i> PAN Verified ({{ $pointValue }} pts)</div>
                    <div class="col-md-4 col-6"><i class="bi {{ $bankComplete ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-danger' }}"></i> Bank Verified ({{ $pointValue }} pts)</div>
                    @if($user->user_type == 'Shop Boy')
                    <div class="col-md-4 col-6"><i class="bi {{ $invoiceComplete ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-danger' }}"></i> Invoice Sample (20 pts)</div>
                    @endif
                </div>
            </div>
        </div>

        @php
            $missingFlags = [];
            if(empty($user->pan_number)) $missingFlags[] = 'PAN number not found.';
            if(empty($user->aadhar_number)) $missingFlags[] = 'Aadhar number not found.';
            if(empty($user->bank_account_number) || empty($user->ifsc_code)) $missingFlags[] = 'Bank account number with IFSC code not found.';
            if(empty($user->profile_photo)) $missingFlags[] = 'Profile photo not uploaded.';
            if($user->user_type == 'Shop Boy' && empty($user->invoice_sample_file)) $missingFlags[] = 'Invoice sample not uploaded.';
        @endphp
        
        @if(count($missingFlags) > 0)
        <div class="card border-0 shadow-sm rounded-3 mb-4 border-start border-danger border-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5 me-2"></i>
                <h5 class="mb-0 fw-bold text-danger">Action Required / Missing Information</h5>
            </div>
            <div class="card-body p-3">
                <ul class="mb-0 text-danger fw-medium">
                    @foreach($missingFlags as $flag)
                        <li class="mb-1">{{ $flag }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3 mb-4 border-start border-success border-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-info-circle-fill text-success fs-5 me-2"></i>
                <h5 class="mb-0 fw-bold text-success">Important Information</h5>
            </div>
            <div class="card-body p-3">
                <ul class="mb-0 text-success fw-medium">
                    <li class="mb-1">Always check the uploaded photo.</li>
                    <li class="mb-1">Always check the uploaded invoice format.</li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-person-lines-fill text-primary fs-5 me-2"></i>
                <h5 class="mb-0 fw-bold">Contact Information</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Email Address</div>
                        <div class="info-value">{{ $user->email ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Mobile Number</div>
                        <div class="info-value">{{ $user->mobile }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Gender</div>
                        <div class="info-value">{{ $user->gender ?? 'Not Specified' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d M Y') : 'Not Specified' }}</div>
                    </div>

                </div>
            </div>
        </div>

        @if(in_array($user->user_type, ['Shop Boy', 'Installer', 'Women Entrepreneurs']))
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-shop text-warning fs-5 me-2"></i>
                <h5 class="mb-0 fw-bold">Assigned Shops</h5>
            </div>
            <div class="card-body p-4">
                @if($user->shops->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($user->shops as $shop)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $shop->name }}</h6>
                                    <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $shop->pincode->pincode ?? 'N/A' }}, {{ $shop->city->name ?? 'N/A' }}</small>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-muted text-center py-3">No shops assigned.</div>
                @endif
            </div>
        </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3 mb-4" id="verification-section">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-shield-lock-fill text-success fs-5 me-2"></i>
                <h5 class="mb-0 fw-bold">Identity & Banking Verification</h5>
            </div>
            @php
                $aadharEmpty = empty(trim($user->aadhar_number));
                $panEmpty = empty(trim($user->pan_number));
                $bankEmpty = empty(trim($user->bank_account_number)) || empty(trim($user->ifsc_code));
            @endphp
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-label d-flex justify-content-between align-items-center">
                                Aadhar Number
                                <div class="form-check form-switch m-0" title="{{ $aadharEmpty ? 'Cannot verify: Aadhar number is missing' : (($user->is_aadhar_verified && !$isSuperAdmin) ? 'Only Super Admin can change this' : 'Mark as Verified') }}">
                                    <input class="form-check-input" type="checkbox" name="is_aadhar_verified" value="1" {{ $user->is_aadhar_verified ? 'checked' : '' }} {{ ($aadharEmpty || ($user->is_aadhar_verified && !$isSuperAdmin)) ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="info-value">
                                {{ $user->aadhar_number ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label d-flex justify-content-between align-items-center">
                                PAN Number
                                <div class="form-check form-switch m-0" title="{{ $panEmpty ? 'Cannot verify: PAN number is missing' : (($user->is_pan_verified && !$isSuperAdmin) ? 'Only Super Admin can change this' : 'Mark as Verified') }}">
                                    <input class="form-check-input" type="checkbox" name="is_pan_verified" value="1" {{ $user->is_pan_verified ? 'checked' : '' }} {{ ($panEmpty || ($user->is_pan_verified && !$isSuperAdmin)) ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="info-value">
                                {{ $user->pan_number ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label d-flex justify-content-between align-items-center">
                                Bank Account Number
                                <div class="form-check form-switch m-0" title="{{ $bankEmpty ? 'Cannot verify: Bank details are missing' : (($user->is_bank_verified && !$isSuperAdmin) ? 'Only Super Admin can change this' : 'Mark as Verified') }}">
                                    <input class="form-check-input" type="checkbox" name="is_bank_verified" value="1" {{ $user->is_bank_verified ? 'checked' : '' }} {{ ($bankEmpty || ($user->is_bank_verified && !$isSuperAdmin)) ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="info-value">
                                {{ $user->bank_account_number ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">
                                IFSC Code
                            </div>
                            <div class="info-value">
                                {{ $user->ifsc_code ?? 'N/A' }}
                            </div>
                        </div>
                        @if($user->user_type == 'Shop Boy')
                        <div class="col-md-6">
                            <div class="info-label d-flex justify-content-between align-items-center">
                                Invoice Sample
                                <div class="form-check form-switch m-0" title="Mark as Verified">
                                    <input class="form-check-input" type="checkbox" name="is_invoice_verified" id="is_invoice_verified" value="1" {{ old('is_invoice_verified', $user->is_invoice_verified) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="info-value mt-2">
                                @if($user->invoice_sample_file)
                                    @if(in_array(strtolower(pathinfo($user->invoice_sample_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                        <a href="javascript:void(0)" onclick="openImageModal('{{ asset('storage/'.$user->invoice_sample_file) }}')">
                                            <img src="{{ asset('storage/'.$user->invoice_sample_file) }}" alt="Invoice Sample" class="img-thumbnail shadow-sm rounded" style="max-height: 120px; object-fit: cover;">
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/'.$user->invoice_sample_file) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-pdf"></i> View PDF Document</a>
                                    @endif
                                @else
                                    <span class="text-muted fst-italic">Not uploaded</span>
                                @endif
                                @error('is_invoice_verified')
                                    <div class="text-danger small mt-2 fw-bold"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <div class="info-label d-flex justify-content-between align-items-center">
                                Profile Photo Verification
                                <div class="form-check form-switch m-0" title="Mark as Verified">
                                    <input class="form-check-input" type="checkbox" name="is_photo_verified" id="is_photo_verified" value="1" {{ old('is_photo_verified', $user->is_photo_verified) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="info-value mt-2 text-muted small">
                                Profile photo can be viewed in the left panel. Check this switch if the photo is correct and clearly shows the user.
                                @error('is_photo_verified')
                                    <div class="text-danger small mt-2 fw-bold"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-geo-alt-fill text-danger fs-5 me-2"></i>
                <h5 class="mb-0 fw-bold">Location Details</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="info-label">Full Address</div>
                        <div class="info-value">{{ $user->address ?? 'No address provided.' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">State</div>
                        <div class="info-value">{{ $user->state->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Zone</div>
                        <div class="info-value">{{ $user->state->zone->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">City</div>
                        <div class="info-value">{{ $user->city->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Pincode</div>
                        <div class="info-value">{{ $user->pincode->pincode ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</form>
@endsection

@push('scripts')
@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var verificationSection = document.getElementById('verification-section');
        if (verificationSection) {
            verificationSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            // Optionally, add a subtle highlight animation
            verificationSection.classList.add('border-danger');
            setTimeout(() => {
                verificationSection.classList.remove('border-danger');
            }, 3000);
        }
    });
</script>
@endif
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
</script>
@endpush

