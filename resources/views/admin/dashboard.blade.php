@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row justify-content-center">

    <!-- Shop Boys Card -->
    <div class="col-md-4 mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
            <div class="card premium-card premium-bg-1 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <div class="main-title mb-2">Shop Boys</div>
                            <div class="d-flex align-items-baseline">
                                <span class="main-count">{{ number_format($shopBoysCount) }}</span>
                                <span class="ms-2 fs-6 opacity-75">USERS</span>
                            </div>
                        </div>
                        <div class="icon-wrapper shadow-sm">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                    
                    @php 
                        $total1 = $shopBoysCount > 0 ? $shopBoysCount : 1; 
                        $apprPct1 = ($shopBoysApproved / $total1) * 100;
                        $pendPct1 = ($shopBoysPending / $total1) * 100;
                        $suspPct1 = ($shopBoysSuspended / $total1) * 100;
                    @endphp
                    <div class="progress-bar-custom mb-3 shadow-sm">
                        <div class="progress-segment bg-white" style="width: {{ $apprPct1 }}%" title="Approved: {{ $shopBoysApproved }}"></div>
                        <div class="progress-segment bg-warning" style="width: {{ $pendPct1 }}%" title="Pending: {{ $shopBoysPending }}"></div>
                        <div class="progress-segment bg-dark" style="width: {{ $suspPct1 }}%" title="Suspended: {{ $shopBoysSuspended }}"></div>
                    </div>

                    <div class="d-flex justify-content-between text-center pt-1">
                        <div><div class="stat-label">Approved</div><div class="stat-value">{{ $shopBoysApproved }}</div></div>
                        <div><div class="stat-label">Pending</div><div class="stat-value">{{ $shopBoysPending }}</div></div>
                        <div><div class="stat-label">Suspended</div><div class="stat-value">{{ $shopBoysSuspended }}</div></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Installers Card -->
    <div class="col-md-4 mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
            <div class="card premium-card premium-bg-2 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <div class="main-title mb-2">Installers</div>
                            <div class="d-flex align-items-baseline">
                                <span class="main-count">{{ number_format($installersCount) }}</span>
                                <span class="ms-2 fs-6 opacity-75">USERS</span>
                            </div>
                        </div>
                        <div class="icon-wrapper shadow-sm">
                            <i class="bi bi-tools"></i>
                        </div>
                    </div>
                    
                    @php 
                        $total2 = $installersCount > 0 ? $installersCount : 1; 
                        $apprPct2 = ($installersApproved / $total2) * 100;
                        $pendPct2 = ($installersPending / $total2) * 100;
                        $suspPct2 = ($installersSuspended / $total2) * 100;
                    @endphp
                    <div class="progress-bar-custom mb-3 shadow-sm">
                        <div class="progress-segment bg-white" style="width: {{ $apprPct2 }}%" title="Approved: {{ $installersApproved }}"></div>
                        <div class="progress-segment bg-warning" style="width: {{ $pendPct2 }}%" title="Pending: {{ $installersPending }}"></div>
                        <div class="progress-segment bg-dark" style="width: {{ $suspPct2 }}%" title="Suspended: {{ $installersSuspended }}"></div>
                    </div>

                    <div class="d-flex justify-content-between text-center pt-1">
                        <div><div class="stat-label">Approved</div><div class="stat-value">{{ $installersApproved }}</div></div>
                        <div><div class="stat-label">Pending</div><div class="stat-value">{{ $installersPending }}</div></div>
                        <div><div class="stat-label">Suspended</div><div class="stat-value">{{ $installersSuspended }}</div></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Women Entrepreneurs Card -->
    <div class="col-md-4 mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
            <div class="card premium-card premium-bg-3 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <div class="main-title mb-2">Women Entrepreneurs</div>
                            <div class="d-flex align-items-baseline">
                                <span class="main-count">{{ number_format($womenEntrepreneursCount) }}</span>
                                <span class="ms-2 fs-6 opacity-75">USERS</span>
                            </div>
                        </div>
                        <div class="icon-wrapper shadow-sm">
                            <i class="bi bi-briefcase"></i>
                        </div>
                    </div>
                    
                    @php 
                        $total3 = $womenEntrepreneursCount > 0 ? $womenEntrepreneursCount : 1; 
                        $apprPct3 = ($womenEntrepreneursApproved / $total3) * 100;
                        $pendPct3 = ($womenEntrepreneursPending / $total3) * 100;
                        $suspPct3 = ($womenEntrepreneursSuspended / $total3) * 100;
                    @endphp
                    <div class="progress-bar-custom mb-3 shadow-sm">
                        <div class="progress-segment bg-white" style="width: {{ $apprPct3 }}%" title="Approved: {{ $womenEntrepreneursApproved }}"></div>
                        <div class="progress-segment bg-warning" style="width: {{ $pendPct3 }}%" title="Pending: {{ $womenEntrepreneursPending }}"></div>
                        <div class="progress-segment bg-dark" style="width: {{ $suspPct3 }}%" title="Suspended: {{ $womenEntrepreneursSuspended }}"></div>
                    </div>

                    <div class="d-flex justify-content-between text-center pt-1">
                        <div><div class="stat-label">Approved</div><div class="stat-value">{{ $womenEntrepreneursApproved }}</div></div>
                        <div><div class="stat-label">Pending</div><div class="stat-value">{{ $womenEntrepreneursPending }}</div></div>
                        <div><div class="stat-label">Suspended</div><div class="stat-value">{{ $womenEntrepreneursSuspended }}</div></div>
                    </div>
                </div>
            </div>
        </a>
    </div>


</div>

<!-- Charts and Data Tables Section -->
<div class="row">
    <!-- Chart: Total Users by Zone -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-uppercase mb-0 text-secondary">Total Users by Zone</h6>
                <form action="{{ route('admin.dashboard') }}" method="GET" id="userFilterForm">
                    <input type="hidden" name="month" value="{{ request('month') }}">
                    <select name="user_type" class="form-select form-select-sm" onchange="document.getElementById('userFilterForm').submit();">
                        <option value="">All Users</option>
                        <option value="Shop Boy" {{ request('user_type') == 'Shop Boy' ? 'selected' : '' }}>Shop Boy</option>
                        <option value="Installer" {{ request('user_type') == 'Installer' ? 'selected' : '' }}>Installer</option>
                        <option value="Women Entrepreneurs" {{ request('user_type') == 'Women Entrepreneurs' ? 'selected' : '' }}>Women Entrepreneurs</option>
                    </select>
                </form>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="usersChart" height="250" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart: Total Sales by Zone -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-uppercase mb-0 text-secondary">Total Sales by Zone</h6>
                <form action="{{ route('admin.dashboard') }}" method="GET" id="monthFilterForm">
                    <input type="hidden" name="user_type" value="{{ request('user_type') }}">
                    <select name="month" class="form-select form-select-sm" onchange="document.getElementById('monthFilterForm').submit();">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="250" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <!-- Chart: Total Shops by Zone -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="fw-bold text-uppercase mb-0 text-secondary">Total Shops by Zone</h6>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="shopsChart" height="300" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>


@endsection

@push('styles')
<style>
    .premium-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        color: white;
        position: relative;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        z-index: 1;
    }
    
    .premium-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: inherit;
        z-index: -1;
        transition: all 0.4s ease;
        opacity: 0;
        filter: brightness(1.2);
    }

    .premium-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }
    
    .premium-card:hover::before {
        opacity: 1;
    }

    .premium-bg-1 {
        background: linear-gradient(135deg, #0284c7 0%, #2563eb 40%, #db2777 100%);
    }

    .premium-bg-2 {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4f46e5 100%);
    }

    .premium-bg-3 {
        background: linear-gradient(135deg, #7c3aed 0%, #9333ea 40%, #e11d48 100%);
    }

    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #ffffff;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .stat-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #ffffff;
    }
    
    .main-title {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 700;
        opacity: 0.9;
    }
    
    .main-count {
        font-size: 3rem;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -1px;
    }
    
    .icon-wrapper {
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        backdrop-filter: blur(5px);
    }
    
    .progress-bar-custom {
        border-radius: 10px;
        background-color: rgba(255,255,255,0.2);
        height: 8px;
        overflow: hidden;
        display: flex;
    }
    
    .progress-segment {
        height: 100%;
        transition: width 0.6s ease;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('usersChart').getContext('2d');
        const usersChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($zoneLabels ?? []) !!},
                datasets: [{
                    label: 'Total Users',
                    data: {!! json_encode($zoneData ?? []) !!},
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#E7E9ED',
                        '#8A2BE2'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                },
                cutout: '70%'
            }
        });

        const ctxSales = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctxSales, {
            type: 'bar',
            data: {
                labels: {!! json_encode($zoneSalesLabels ?? []) !!},
                datasets: [{
                    label: 'Total Sales (₹)',
                    data: {!! json_encode($zoneSalesData ?? []) !!},
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] },
                        ticks: {
                            callback: function(value, index, values) {
                                return '₹' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        const ctxShops = document.getElementById('shopsChart').getContext('2d');
        const shopsChart = new Chart(ctxShops, {
            type: 'polarArea',
            data: {
                labels: {!! json_encode($zoneShopLabels ?? []) !!},
                datasets: [{
                    label: 'Total Shops',
                    data: {!! json_encode($zoneShopData ?? []) !!},
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

