@extends('admin.layouts.app')
@section('title', 'Reports & Analytics')
@section('content')
<!-- Page Header -->
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small bg-transparent p-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reports & Analytics</li>
            </ol>
        </nav>
        <h2 class="h4 mb-0 fw-bold text-dark"><i class="bi bi-graph-up text-primary me-2"></i>Analytics Dashboard</h2>
    </div>
    <div>
        <a href="{{ route('admin.reports.export.users') }}" class="btn btn-outline-primary btn-sm me-2 fw-medium shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export Mechanics
        </a>
        <a href="{{ route('admin.reports.export.invoices') }}" class="btn btn-outline-success btn-sm fw-medium shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export Invoices
        </a>
    </div>
</div>

<style>
    .page-header { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
</style>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Mechanics</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($totalMechanics) }}</h2>
                    </div>
                    <div class="fs-1 text-white-50"><i class="bi bi-people-fill"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Total Shops</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($totalShops) }}</h2>
                    </div>
                    <div class="fs-1 text-white-50"><i class="bi bi-shop"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-dark-50">Points Awarded</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($totalPointsAwarded) }}</h2>
                    </div>
                    <div class="fs-1 text-dark-50"><i class="bi bi-star-fill"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50">Rewards Redeemed</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($totalRedemptions) }}</h2>
                    </div>
                    <div class="fs-1 text-white-50"><i class="bi bi-gift-fill"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">Invoice Submissions (Last 6 Months)</h6>
            </div>
            <div class="card-body">
                <canvas id="barChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0">Mechanic KYC Status</h6>
            </div>
            <div class="card-body">
                <canvas id="pieChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dummy Data for Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Verified Invoices',
                data: [120, 190, 300, 250, 420, 500],
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });

    // Dummy Data for Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                data: [75, 15, 10],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(255, 99, 132, 0.7)'
                ]
            }]
        }
    });
</script>
@endpush

