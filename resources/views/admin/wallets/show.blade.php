@extends('admin.layouts.app')
@section('title', 'Manage Wallet')
@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.wallets.index') }}" class="text-decoration-none text-muted">User Wallets</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manage</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-wallet2 text-primary me-2"></i>Wallet Ledger: {{ $user->name }}</h2>
    </div>
    <a href="{{ route('admin.wallets.index') }}" class="btn btn-light border shadow-sm btn-sm px-3 py-2 fw-medium text-secondary hover-primary">
        <i class="bi bi-arrow-left me-1"></i> Back to Wallets
    </a>
</div>

<style>
    .hover-primary:hover { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .table th { background-color: #f8f9fa !important; color: #495057; font-weight: 600; }
</style>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow border-0 rounded-3 h-100 bg-primary text-white">
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <h6 class="text-white-50 text-uppercase fw-bold tracking-wide">Current Balance</h6>
                <h1 class="display-4 fw-bold mb-0">{{ number_format($user->wallet_balance) }} <span class="fs-4">pts</span></h1>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow border-0 rounded-3 h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                <h5 class="fw-bold mb-3">Manual Adjustments</h5>
                <p class="text-muted small mb-4">Use these controls to manually add bonus points or deduct points from the user's wallet. All manual adjustments are logged in the transaction ledger below.</p>
                
                <div class="d-flex gap-3">
                    <button class="btn btn-success fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPointsModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Points
                    </button>
                    <button class="btn btn-danger fw-bold px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#deductPointsModal">
                        <i class="bi bi-dash-circle me-1"></i> Deduct Points
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4 border-0 rounded-3">
    <div class="card-header bg-white py-3 border-bottom-0">
        <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history text-secondary me-2"></i>Transaction Ledger</h5>
    </div>
    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="transactionsTable">
                <thead class="bg-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Balance After</th>
                        <th>Description</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Invoice Wise Points Breakup -->
<div class="card shadow mb-4 border-0 rounded-3">
    <div class="card-header bg-white py-3 border-bottom-0">
        <h5 class="mb-0 fw-bold"><i class="bi bi-receipt text-secondary me-2"></i>Invoice Wise Points & Breakup</h5>
    </div>
    <div class="card-body p-4 pt-0 mt-3">
        @if($invoices->count() > 0)
            <div class="accordion" id="invoiceAccordion">
                @foreach($invoices as $index => $invoice)
                    <div class="accordion-item mb-3 border rounded shadow-sm">
                        <h2 class="accordion-header" id="heading{{ $invoice->id }}">
                            <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }} bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $invoice->id }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $invoice->id }}">
                                <strong>Invoice #{{ $invoice->id }}</strong> 
                                <span class="mx-3 text-muted">|</span> 
                                <span class="text-secondary"><i class="bi bi-calendar"></i> {{ $invoice->created_at->format('M d, Y') }}</span>
                                <span class="mx-3 text-muted">|</span> 
                                <span class="text-success fw-bold"><i class="bi bi-star-fill"></i> {{ number_format($invoice->points_earned) }} Points</span>
                            </button>
                        </h2>
                        <div id="collapse{{ $invoice->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $invoice->id }}" data-bs-parent="#invoiceAccordion">
                            <div class="accordion-body">
                                <div class="row mb-3 bg-light p-3 rounded">
                                    <div class="col-md-4"><strong>Customer Name:</strong> <br>{{ $invoice->customer_name ?? 'N/A' }}</div>
                                    <div class="col-md-4"><strong>Customer Mobile:</strong> <br>{{ $invoice->customer_mobile ?? 'N/A' }}</div>
                                    <div class="col-md-4"><strong>Invoice Amount:</strong> <br>₹{{ number_format($invoice->amount, 2) }}</div>
                                </div>
                                
                                @if($invoice->items->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($invoice->items as $item)
                                                <tr>
                                                    <td>{{ $item->product_name ?? 'Unknown Product' }}</td>
                                                    <td>₹{{ number_format($item->amount, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No product breakup available for this invoice.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-light border text-center text-muted">
                <i class="bi bi-info-circle me-1"></i> No verified invoices found for this user.
            </div>
        @endif
    </div>
</div>

<!-- Add Points Modal -->
<div class="modal fade" id="addPointsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('admin.wallets.add-points', $user->id) }}" method="POST" class="modal-content border-0 shadow-lg">
      @csrf
      <div class="modal-header border-bottom-0 bg-success text-white rounded-top">
        <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Add Points to Wallet</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-3">
            <label class="form-label fw-bold">Amount to Add <span class="text-danger">*</span></label>
            <input type="number" name="amount" class="form-control form-control-lg text-success fw-bold" placeholder="e.g. 100" min="1" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Reason / Description <span class="text-danger">*</span></label>
            <input type="text" name="description" class="form-control" placeholder="e.g. Festival Bonus" required>
        </div>
      </div>
      <div class="modal-footer border-top-0 p-4 pt-0">
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success px-4 fw-bold">Confirm Credit</button>
      </div>
    </form>
  </div>
</div>

<!-- Deduct Points Modal -->
<div class="modal fade" id="deductPointsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('admin.wallets.deduct-points', $user->id) }}" method="POST" class="modal-content border-0 shadow-lg">
      @csrf
      <div class="modal-header border-bottom-0 bg-danger text-white rounded-top">
        <h5 class="modal-title fw-bold"><i class="bi bi-dash-circle me-2"></i>Deduct Points from Wallet</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-3">
            <label class="form-label fw-bold">Amount to Deduct <span class="text-danger">*</span></label>
            <input type="number" name="amount" class="form-control form-control-lg text-danger fw-bold" placeholder="e.g. 50" min="1" required>
            <small class="text-muted d-block mt-1">Cannot exceed current balance of {{ number_format($user->wallet_balance) }}.</small>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Reason / Description <span class="text-danger">*</span></label>
            <input type="text" name="description" class="form-control" placeholder="e.g. Penalty or Manual Adjustment" required>
        </div>
      </div>
      <div class="modal-footer border-top-0 p-4 pt-0">
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger px-4 fw-bold">Confirm Debit</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#transactionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.wallets.show', $user->id) }}",
            columns: [
                {data: 'created_at', name: 'created_at'},
                {data: 'transaction_type', name: 'transaction_type'},
                {data: 'amount', name: 'amount'},
                {data: 'balance_after', name: 'balance_after'},
                {data: 'description', name: 'description'},
                {data: 'reference_type', name: 'reference_type'}
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush

