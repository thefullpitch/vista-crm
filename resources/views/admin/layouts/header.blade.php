<nav class="navbar navbar-expand bg-white topbar sticky-top shadow-sm px-4 py-3 border-bottom border-light">
    <!-- Sidebar Toggle (Mobile & Desktop) -->
    <button type="button" id="sidebarToggle" class="btn btn-link text-dark p-0 me-3 fs-4">
        <i class="bi bi-list"></i>
    </button>
    
    <!-- Page Title -->
    <h5 class="mb-0 text-dark fw-bold d-none d-sm-block">@yield('title', 'Dashboard')</h5>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ms-auto align-items-center">
        
        <!-- Search Dropdown (Mobile) -->
        <li class="nav-item d-sm-none me-2">
            <a class="nav-link text-dark" href="#" role="button">
                <i class="bi bi-search fs-5"></i>
            </a>
        </li>

        <!-- Alerts/Notifications -->
        <li class="nav-item dropdown me-4">
            <a class="nav-link text-dark position-relative" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-25 start-75 translate-middle p-1 bg-danger border border-light rounded-circle">
                    <span class="visually-hidden">New alerts</span>
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="alertsDropdown">
                <li><h6 class="dropdown-header">Alerts Center</h6></li>
                <li><a class="dropdown-item d-flex align-items-center py-2" href="#">
                    <div class="me-3">
                        <div class="icon-circle bg-primary text-white p-2 rounded-circle">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-muted">December 12, 2026</div>
                        <span class="fw-bold">A new monthly report is ready to download!</span>
                    </div>
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center small text-muted py-2" href="#">Show All Alerts</a></li>
            </ul>
        </li>

        <div class="topbar-divider d-none d-sm-block bg-light mx-3" style="width: 1px; height: 30px;"></div>

        <!-- User Information Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center text-dark" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="me-2 d-none d-lg-inline text-muted small fw-bold">{{ Auth::guard('admin')->user()->name ?? 'Admin User' }}</span>
                <img class="img-profile rounded-circle border border-primary border-2 p-1" src="https://ui-avatars.com/api/?name=Admin&background=0d6efd&color=fff" style="width: 35px; height: 35px;" alt="Profile">
            </a>
            <!-- Dropdown - User Information -->
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userDropdown">
                <li>
                    <a class="dropdown-item py-2" href="#">
                        <i class="bi bi-person fa-sm fa-fw me-2 text-muted"></i>
                        Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('admin.settings.index') }}">
                        <i class="bi bi-gear fa-sm fa-fw me-2 text-muted"></i>
                        Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}" class="d-inline m-0 p-0">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger">
                            <i class="bi bi-box-arrow-right fa-sm fa-fw me-2 text-danger"></i>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>

