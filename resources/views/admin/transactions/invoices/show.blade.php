@extends('admin.layouts.app')
@section('title', 'Shop Boy Details')
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
                <h5 class="mb-0">Shop Boy Verification</h5>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th width="200">User</th><td>{{ $invoice->user->name ?? 'N/A' }}</td></tr>
                    <tr><th>Shop Name</th><td>{{ $invoice->shop->name ?? 'N/A' }}</td></tr>
                    <tr><th>Invoice Number</th><td>{{ $invoice->invoice_number }}</td></tr>
                    <tr><th>Invoice Date</th><td>{{ $invoice->invoice_date }}</td></tr>
                    <tr><th>Invoice Amount</th><td>₹{{ number_format($invoice->amount, 2) }}</td></tr>
                    <tr><th>Customer Name</th><td>{{ $invoice->customer_name ?? 'N/A' }}</td></tr>
                    <tr><th>Customer Phone</th><td>{{ $invoice->customer_phone ?? 'N/A' }}</td></tr>
                    <tr>
                        <td colspan="2" class="p-0">
                            <table class="table table-sm table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Product Name</th>
                                        <th class="text-end pe-3">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoice->items as $item)
                                        <tr>
                                            <td class="ps-3">{{ $item->product_name }}</td>
                                            <td class="text-end pe-3">₹{{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                    @empty
                                        @if($invoice->product_name)
                                            <tr>
                                                <td class="ps-3">{{ $invoice->product_name }}</td>
                                                <td class="text-end pe-3">₹{{ number_format($invoice->amount, 2) }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">No products found for this invoice.</td>
                                            </tr>
                                        @endif
                                    @endforelse
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr><th>Current Status</th>
                        <td>
                            @if($invoice->status == 'Verified')
                                <span class="badge bg-success">Verified</span> ({{ $invoice->points_earned }} Points Earned)
                            @elseif($invoice->status == 'Rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @if($invoice->rejection_reason)
                        <tr><th>Rejection Reason</th><td class="text-danger">{{ $invoice->rejection_reason }}</td></tr>
                    @endif
                    @if($invoice->verification_remark)
                        <tr><th>Verification Remark</th><td>{{ $invoice->verification_remark }}</td></tr>
                    @endif
                    @if($invoice->verified_by)
                        <tr><th>Verified By</th><td>{{ $invoice->verifier->name ?? 'N/A' }} at {{ $invoice->verified_at }}</td></tr>
                    @endif
                    <tr>
                        <th>Verification Documents</th>
                        <td>
                            <div class="row">
                                <div class="col-md-6 border-end">
                                    <h6 class="text-muted mb-2">Current Invoice Document</h6>
                                    @if($invoice->document_file)
                                        <a href="{{ asset('storage/'.$invoice->document_file) }}" target="_blank">
                                            <img src="{{ asset('storage/'.$invoice->document_file) }}" class="img-fluid rounded border shadow-sm" style="max-height: 150px; object-fit: contain;">
                                        </a>
                                    @else
                                        <span class="text-muted">No Document Provided</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">User's Sample Invoice (Reference)</h6>
                                    @if($invoice->user && $invoice->user->invoice_sample_file)
                                        <a href="{{ asset('storage/'.$invoice->user->invoice_sample_file) }}" target="_blank">
                                            <img src="{{ asset('storage/'.$invoice->user->invoice_sample_file) }}" class="img-fluid rounded border shadow-sm" style="max-height: 150px; object-fit: contain;">
                                        </a>
                                    @else
                                        <span class="text-muted">No Sample Invoice Available</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                
                @if($invoice->status == 'Pending')
                    @can('invoice.verify')
                    <div class="mt-4 p-4 bg-light rounded border border-info">
                        <h6 class="text-primary mb-3"><i class="bi bi-shield-check"></i> Admin Verification Action</h6>
                        <form action="{{ route('admin.invoices.status', $invoice->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Verification Decision <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required id="statusSelect">
                                    <option value="">-- Select Decision --</option>
                                    <option value="Verified">Approve</option>
                                    <option value="Rejected">Reject Submission</option>
                                </select>
                            </div>
                            
                            <input type="hidden" name="points_earned" value="0">

                            <div class="mb-3" id="rejectionReasonDiv" style="display: none;">
                                <label class="form-label fw-bold">Reason for Rejection <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason" class="form-control" rows="2" placeholder="e.g. Image blurry, invalid shop, etc."></textarea>
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
    var invoiceAmount = {{ $invoice->amount ?? 0 }};
    var pointsEarnAmount = {{ $points_earn_amount ?? 100 }};
    var pointsEarnReward = {{ $points_earn_reward ?? 1 }};

    $('#statusSelect').change(function() {
        if($(this).val() == 'Verified') {
            $('#rejectionReasonDiv').slideUp();
            $('textarea[name="rejection_reason"]').prop('required', false);
            // Auto calculate points based on global settings
            if (pointsEarnAmount > 0) {
                var earnedPoints = Math.floor(invoiceAmount / pointsEarnAmount) * pointsEarnReward;
                $('input[name="points_earned"]').val(earnedPoints);
            }
        } else if($(this).val() == 'Rejected') {
            $('#rejectionReasonDiv').slideDown();
            $('textarea[name="rejection_reason"]').prop('required', true);
        } else {
            $('#rejectionReasonDiv').slideUp();
            $('textarea[name="rejection_reason"]').prop('required', false);
        }
    });
</script>
@endpush

