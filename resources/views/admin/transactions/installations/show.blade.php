@extends('admin.layouts.app')
@section('title', 'Installation Details')
@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Installation Verification</h5>
                <a href="{{ route('admin.installations.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th width="200">Installer</th><td>{{ $installation->user->name ?? 'N/A' }}</td></tr>
                    <tr><th>Customer Name</th><td>{{ $installation->customer_name }}</td></tr>
                    <tr><th>Customer Mobile</th><td>{{ $installation->customer_mobile }}</td></tr>
                    

                    


                    <tr><th colspan="2" class="bg-light"><strong>Invoice Details</strong></th></tr>
                    <tr><th>Invoice Number</th><td>{{ $installation->invoice_number ?? 'N/A' }}</td></tr>
                    <tr><th>Invoice Amount</th><td>₹{{ $installation->invoice_amount ? number_format($installation->invoice_amount, 2) : 'N/A' }}</td></tr>
                    <tr>
                        <th>Invoice Image</th>
                        <td>
                            @if($installation->invoice_image)
                                <a href="{{ asset('storage/'.$installation->invoice_image) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$installation->invoice_image) }}" class="img-fluid rounded border" style="max-height: 400px; object-fit: contain;">
                                </a>
                            @else
                                <span class="text-muted">No Invoice Uploaded</span>
                            @endif
                        </td>
                    </tr>

                    <tr><th colspan="2" class="bg-light"><strong>Verification Details</strong></th></tr>

                    <tr><th>Current Status</th>
                        <td>
                            @if($installation->status == 'Verified')
                                <span class="badge bg-success">Verified</span> ({{ $installation->points_earned }} Points Earned)
                            @elseif($installation->status == 'Rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @if($installation->rejection_reason)
                        <tr><th>Rejection Reason</th><td class="text-danger">{{ $installation->rejection_reason }}</td></tr>
                    @endif
                    @if($installation->verification_remark)
                        <tr><th>Verification Remark</th><td>{{ $installation->verification_remark }}</td></tr>
                    @endif
                    @if($installation->verified_by)
                        <tr><th>Verified By</th><td>{{ $installation->verifier->name ?? 'N/A' }} at {{ $installation->verified_at }}</td></tr>
                    @endif
                    <tr>
                        <th>Installation Photo(s)</th>
                        <td>
                            @if(!empty($installation->installation_photo) && is_array($installation->installation_photo) && count($installation->installation_photo) > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($installation->installation_photo as $photo)
                                        <a href="{{ asset('storage/'.$photo) }}" target="_blank">
                                            <img src="{{ asset('storage/'.$photo) }}" class="img-fluid rounded border" style="max-height: 200px; object-fit: contain;">
                                        </a>
                                    @endforeach
                                </div>
                            @elseif(!empty($installation->installation_photo) && is_string($installation->installation_photo))
                                <a href="{{ asset('storage/'.$installation->installation_photo) }}" target="_blank">
                                    <img src="{{ asset('storage/'.$installation->installation_photo) }}" class="img-fluid rounded border" style="max-height: 400px; object-fit: contain;">
                                </a>
                            @else
                                <span class="text-muted">No Photo Uploaded</span>
                            @endif
                        </td>
                    </tr>
                </table>
                
                @if(!empty($installation->products_data) && is_array($installation->products_data) && count($installation->products_data) > 0)
                    <div class="mt-4">
                        <h6 class="fw-bold mb-3">Additional Products</h6>
                        <table class="table table-bordered table-striped shadow-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Installation Photo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($installation->products_data as $product)
                                    <tr>
                                        <td class="align-middle">{{ $product['product_name'] ?? 'N/A' }}</td>
                                        <td>
                                            @if(!empty($product['installation_photo']))
                                                <a href="{{ asset('storage/'.$product['installation_photo']) }}" target="_blank">
                                                    <img src="{{ asset('storage/'.$product['installation_photo']) }}" class="img-fluid rounded border" style="max-height: 120px; object-fit: contain;">
                                                </a>
                                            @else
                                                <span class="text-muted">No Photo</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                
                @if($installation->status == 'Pending')
                    @can('installations.verify')
                    <div class="mt-4 p-4 bg-light rounded border border-info">
                        <h6 class="text-primary mb-3"><i class="bi bi-shield-check"></i> Admin Verification Action</h6>
                        <form action="{{ route('admin.installations.status', $installation->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Verification Decision <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required id="statusSelect">
                                    <option value="">-- Select Decision --</option>
                                    <option value="Verified">Approve & Assign Points</option>
                                    <option value="Rejected">Reject Submission</option>
                                </select>
                            </div>
                            
                            <div class="mb-3" id="pointsDiv" style="display: none;">
                                <label class="form-label fw-bold">Points to Award <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="points_earned" class="form-control form-control-lg text-success fw-bold" step="0.01" min="0" placeholder="e.g. 20">
                                    <span class="input-group-text">Points</span>
                                </div>
                                <small class="text-muted">These points will be immediately credited to the mechanic's wallet.</small>
                            </div>

                            <div class="mb-3" id="rejectionReasonDiv" style="display: none;">
                                <label class="form-label fw-bold">Reason for Rejection <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason" class="form-control" rows="2" placeholder="e.g. Serial number invalid, photo unclear, etc."></textarea>
                            </div>

                            <div class="mb-3" id="verificationRemarkDiv">
                                <label class="form-label fw-bold">Verification Remark (Optional)</label>
                                <textarea name="verification_remark" class="form-control" rows="2" placeholder="Add any internal notes or remarks..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mt-2" onclick="return confirm('Are you sure you want to submit this decision? This action cannot be undone.')">Submit Verification</button>
                        </form>
                    </div>
                    @endcan
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    var installationAmount = {{ $installation->amount ?? 0 }};
    var pointsEarnAmount = {{ $points_earn_amount ?? 100 }};
    var pointsEarnReward = {{ $points_earn_reward ?? 1 }};

    $('#statusSelect').change(function() {
        if($(this).val() == 'Verified') {
            $('#pointsDiv').slideDown();
            $('#rejectionReasonDiv').slideUp();
            $('input[name="points_earned"]').prop('required', true);
            $('textarea[name="rejection_reason"]').prop('required', false);
            
            // Auto calculate points based on global settings
            if (pointsEarnAmount > 0) {
                var earnedPoints = Math.floor(installationAmount / pointsEarnAmount) * pointsEarnReward;
                $('input[name="points_earned"]').val(earnedPoints);
            }
        } else if($(this).val() == 'Rejected') {
            $('#pointsDiv').slideUp();
            $('#rejectionReasonDiv').slideDown();
            $('input[name="points_earned"]').prop('required', false);
            $('textarea[name="rejection_reason"]').prop('required', true);
        } else {
            $('#pointsDiv').slideUp();
            $('#rejectionReasonDiv').slideUp();
            $('input[name="points_earned"]').prop('required', false);
            $('textarea[name="rejection_reason"]').prop('required', false);
        }
    });
</script>
@endpush

