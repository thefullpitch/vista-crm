@extends('admin.layouts.app')
@section('title', 'Redemption Request Details')
@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Redemption Request #{{ $redemption->id }}</h5>
                <a href="{{ route('admin.redemptions.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Requests</a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        <div class="bg-light d-flex align-items-center justify-content-center rounded border mx-auto" style="height: 120px;">
                            <span class="text-muted fs-1"><i class="bi bi-cash-coin"></i></span>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <table class="table table-sm table-borderless">
                            <tr><th width="150">Mechanic:</th><td>{{ $redemption->user->name ?? 'N/A' }} <span class="text-muted">({{ $redemption->user->mobile ?? '' }})</span></td></tr>
                            <tr><th>Points Deducted:</th><td><span class="badge bg-danger fs-6">-{{ $redemption->points_redeemed }}</span></td></tr>
                            <tr><th>Requested On:</th><td>{{ $redemption->created_at->format('d M Y, h:i A') }}</td></tr>
                            <tr><th>Current Status:</th>
                                <td>
                                    @if($redemption->status == 'Approved') <span class="badge bg-primary">Approved</span>
                                    @elseif($redemption->status == 'Shipped') <span class="badge bg-info text-dark">Shipped</span>
                                    @elseif($redemption->status == 'Delivered') <span class="badge bg-success">Delivered</span>
                                    @elseif($redemption->status == 'Rejected') <span class="badge bg-danger">Rejected</span>
                                    @else <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <h6 class="border-bottom pb-2 mb-3">Shipping Details</h6>
                <p class="bg-light p-3 rounded border">{{ $redemption->shipping_address ?? 'No shipping address provided.' }}</p>

                @if($redemption->remarks)
                    <h6 class="border-bottom pb-2 mb-3">Admin Remarks</h6>
                    <p class="text-muted">{{ $redemption->remarks }}</p>
                @endif
                
                @if($redemption->processed_by)
                    <p class="text-muted small">Last processed by: <strong>{{ $redemption->processor->name ?? 'Admin' }}</strong> on {{ $redemption->processed_at }}</p>
                @endif
            </div>
        </div>

        @can('redemptions.process')
        @if($redemption->status != 'Rejected' && $redemption->status != 'Delivered')
        <div class="card border-0 shadow-sm border-top border-info border-3">
            <div class="card-body p-4">
                <h6 class="text-primary mb-3"><i class="bi bi-gear-fill"></i> Process Request Workflow</h6>
                <form action="{{ route('admin.redemptions.status', $redemption->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Update Status to:</label>
                        <select name="status" class="form-select form-select-lg" required id="statusSelect">
                            <option value="">-- Select Next Step --</option>
                            @if($redemption->status == 'Pending')
                                <option value="Approved">Approve Request (Prepare for shipping)</option>
                                <option value="Rejected">Reject Request (Refund points)</option>
                            @endif
                            @if($redemption->status == 'Approved')
                                <option value="Shipped">Mark as Shipped</option>
                                <option value="Rejected">Reject Request (Refund points)</option>
                            @endif
                            @if($redemption->status == 'Shipped')
                                <option value="Delivered">Mark as Delivered (Finalize)</option>
                            @endif
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Remarks / Tracking Info (Optional)</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="e.g. Courier tracking link, or reason for rejection.">{{ $redemption->remarks }}</textarea>
                    </div>
                    
                    <div id="refundWarning" class="alert alert-danger" style="display: none;">
                        <i class="bi bi-exclamation-triangle-fill"></i> <strong>Warning:</strong> Rejecting this request will automatically refund <strong>{{ $redemption->points_redeemed }} points</strong> back to the mechanic's wallet.
                    </div>

                    <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Are you sure you want to update this request?')">Update Workflow</button>
                </form>
            </div>
        </div>
        @endif
        @endcan

    </div>
</div>
@endsection
@push('scripts')
<script>
    $('#statusSelect').change(function() {
        if($(this).val() == 'Rejected') {
            $('#refundWarning').slideDown();
        } else {
            $('#refundWarning').slideUp();
        }
    });
</script>
@endpush

