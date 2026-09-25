@extends('admin.layouts.app')
@section('title', 'KYC Verification')
@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center bg-white p-4 shadow-sm rounded-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.kyc.index') }}" class="text-decoration-none text-muted">KYC Management</a></li>
                <li class="breadcrumb-item active" aria-current="page">Verification</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark">Review KYC: {{ $user->name }}</h2>
    </div>
    <a href="{{ route('admin.kyc.index') }}" class="btn btn-light border shadow-sm btn-sm px-3 py-2 fw-medium text-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <i class="bi bi-shield-lock-fill text-primary fs-5 me-2"></i>
                <h5 class="mb-0 fw-bold">Submitted Documents</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Aadhar -->
                    <div class="col-md-6 border-end">
                        <h6 class="text-secondary fw-bold mb-3">Aadhar Details</h6>
                        <div class="mb-2"><span class="text-muted small">Aadhar Number:</span><br><strong>{{ $user->aadhar_number ?? 'Not Provided' }}</strong></div>
                        <div class="mt-3">
                            @if($user->aadhar_file)
                                @if(in_array(strtolower(pathinfo($user->aadhar_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                    <a href="javascript:void(0)" onclick="openImageModal('{{ asset('storage/'.$user->aadhar_file) }}')">
                                        <img src="{{ asset('storage/'.$user->aadhar_file) }}" class="img-thumbnail shadow-sm" style="height: 150px; object-fit: cover; width: 100%;">
                                    </a>
                                @else
                                    <a href="{{ asset('storage/'.$user->aadhar_file) }}" target="_blank" class="btn btn-light border w-100 py-3"><i class="bi bi-file-pdf text-danger fs-3 d-block mb-2"></i> View Aadhar PDF</a>
                                @endif
                            @else
                                <div class="bg-light border text-center text-muted p-4 rounded">No Document Uploaded</div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- PAN -->
                    <div class="col-md-6">
                        <h6 class="text-secondary fw-bold mb-3">PAN Details</h6>
                        <div class="mb-2"><span class="text-muted small">PAN Number:</span><br><strong>{{ $user->pan_number ?? 'Not Provided' }}</strong></div>
                        <div class="mt-3">
                            @if($user->pan_file)
                                @if(in_array(strtolower(pathinfo($user->pan_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                    <a href="javascript:void(0)" onclick="openImageModal('{{ asset('storage/'.$user->pan_file) }}')">
                                        <img src="{{ asset('storage/'.$user->pan_file) }}" class="img-thumbnail shadow-sm" style="height: 150px; object-fit: cover; width: 100%;">
                                    </a>
                                @else
                                    <a href="{{ asset('storage/'.$user->pan_file) }}" target="_blank" class="btn btn-light border w-100 py-3"><i class="bi bi-file-pdf text-danger fs-3 d-block mb-2"></i> View PAN PDF</a>
                                @endif
                            @else
                                <div class="bg-light border text-center text-muted p-4 rounded">No Document Uploaded</div>
                            @endif
                        </div>
                    </div>

                    <!-- Bank & Invoice -->
                    <div class="col-12 mt-4 pt-3 border-top">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h6 class="text-secondary fw-bold mb-3">Banking Details</h6>
                                <div class="mb-2"><span class="text-muted small">Account Number:</span><br><strong>{{ $user->bank_account_number ?? 'Not Provided' }}</strong></div>
                                <div class="mt-3">
                                    @if($user->bank_passbook_file)
                                        @if(in_array(strtolower(pathinfo($user->bank_passbook_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                            <a href="javascript:void(0)" onclick="openImageModal('{{ asset('storage/'.$user->bank_passbook_file) }}')">
                                                <img src="{{ asset('storage/'.$user->bank_passbook_file) }}" class="img-thumbnail shadow-sm" style="height: 150px; object-fit: cover; width: 100%;">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/'.$user->bank_passbook_file) }}" target="_blank" class="btn btn-light border w-100 py-3"><i class="bi bi-file-pdf text-danger fs-3 d-block mb-2"></i> View Passbook PDF</a>
                                        @endif
                                    @else
                                        <div class="bg-light border text-center text-muted p-4 rounded">No Passbook Uploaded</div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-secondary fw-bold mb-3">Invoice Sample</h6>
                                <div class="mb-2"><span class="text-muted small">Reference document:</span><br><strong>For product invoice verification</strong></div>
                                <div class="mt-3">
                                    @if($user->invoice_sample_file)
                                        @if(in_array(strtolower(pathinfo($user->invoice_sample_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                            <a href="javascript:void(0)" onclick="openImageModal('{{ asset('storage/'.$user->invoice_sample_file) }}')">
                                                <img src="{{ asset('storage/'.$user->invoice_sample_file) }}" class="img-thumbnail shadow-sm" style="height: 150px; object-fit: cover; width: 100%;">
                                            </a>
                                        @else
                                            <a href="{{ asset('storage/'.$user->invoice_sample_file) }}" target="_blank" class="btn btn-light border w-100 py-3"><i class="bi bi-file-pdf text-danger fs-3 d-block mb-2"></i> View Invoice PDF</a>
                                        @endif
                                    @else
                                        <div class="bg-light border text-center text-muted p-4 rounded">No Sample Uploaded</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4 text-center">
                <img src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=0d6efd&color=fff&size=100' }}" class="rounded-circle shadow-sm mb-3 border border-3 border-white" width="100" height="100" style="object-fit: cover;">
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-3">{{ $user->user_type ?? 'User' }}</p>
                <div class="d-grid">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-secondary btn-sm" target="_blank">View Full Profile</a>
                </div>
            </div>
        </div>

        @can('kyc.verify')
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold">Verification Decision</h6>
            </div>
            <div class="card-body p-4 bg-light rounded-bottom">
                @if($user->kyc_status == 'Rejected' && $user->kyc_review_details)
                    <div class="alert alert-danger mb-4 small shadow-sm">
                        <strong>Previous Rejection Reason:</strong><br>
                        {{ $user->kyc_review_details }}
                    </div>
                @endif
                <form action="{{ route('admin.kyc.status', $user->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">KYC Status</label>
                        <select name="kyc_status" class="form-select form-select-lg shadow-sm" id="kycStatusSelect">
                            <option value="Pending" {{ $user->kyc_status == 'Pending' ? 'selected' : '' }}>Pending (Under Review)</option>
                            <option value="Approved" {{ $user->kyc_status == 'Approved' || $user->kyc_status == 'Verified' ? 'selected' : '' }}>Approved (Verified)</option>
                            <option value="Rejected" {{ $user->kyc_status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="mb-4" id="kycReviewDetailsBox">
                        <label class="form-label fw-bold">Review Details / Reason</label>
                        <textarea name="kyc_review_details" class="form-control shadow-sm" rows="3" placeholder="Enter reason if rejecting...">{{ old('kyc_review_details', $user->kyc_review_details) }}</textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">Save Decision</button>
                    </div>
                </form>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection

@push('scripts')
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

