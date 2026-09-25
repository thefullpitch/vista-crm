@php
    $adminUser = auth()->guard('admin')->user();
    $allowedUserTypes = is_array($adminUser->user_types) ? $adminUser->user_types : (is_string($adminUser->user_types) ? json_decode($adminUser->user_types, true) ?? [] : []);
    $isSuperAdmin = $adminUser->hasRole('Super Admin');
    
    if (!function_exists('canViewUserType')) {
        function canViewUserType($type, $allowed, $isSuper) {
            if ($isSuper) return true;
            if (empty($allowed)) return true;
            return in_array($type, $allowed);
        }
    }
@endphp
<nav id="sidebar" class="premium-sidebar shadow-lg">
    <div class="sidebar-header d-flex align-items-center justify-content-between p-3 border-bottom border-light border-opacity-10">
        <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none d-flex align-items-center">
            <div class="logo-icon bg-gradient-primary rounded-circle p-2 me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                <i class="bi bi-gem fs-5 text-white"></i>
            </div>
            <span class="fs-4 fw-bolder mb-0 logo-text tracking-wide text-gradient">Vista CRM</span>
        </a>
        <button type="button" class="btn btn-sm btn-link text-white d-md-none sidebar-close-btn transition-all" id="sidebarCloseBtn">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
    </div>

    <div class="sidebar-body p-3 overflow-y-auto custom-scrollbar" style="height: calc(100vh - 76px);">
        <ul class="list-unstyled components mb-5">
            <li class="nav-label text-uppercase text-light opacity-50 small fw-bold mb-2 px-3 mt-2 letter-spacing-1">Core</li>
            
            <li class="nav-item mb-2">
                <a href="{{ route('admin.dashboard') }}" class="nav-link rounded-3 px-3 py-2 text-white transition-all {{ request()->routeIs('admin.dashboard') ? 'active bg-gradient-primary shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-speedometer2 nav-icon {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-primary' }} me-2"></i>
                        <span class="nav-text fw-medium">Dashboard</span>
                    </div>
                </a>
            </li>
            
            <div class="sidebar-divider my-3"></div>
            
            <li class="nav-label text-uppercase text-light opacity-50 small fw-bold mb-2 px-3 letter-spacing-1">Modules</li>

            @can('users.view')
            <li class="nav-item mb-2">
                <a href="#appUsersSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}" class="nav-link rounded-3 px-3 py-2 text-white d-flex justify-content-between align-items-center transition-all {{ request()->routeIs('admin.users.*') ? 'bg-white bg-opacity-10 shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people nav-icon text-info me-2"></i>
                        <span class="nav-text fw-medium">App Users</span>
                    </div>
                    <i class="bi bi-chevron-down nav-arrow transition-transform"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 mt-2 mb-2 {{ request()->routeIs('admin.users.*') ? 'show' : '' }}" id="appUsersSubmenu">
                    @if(canViewUserType('Shop Boy', $allowedUserTypes, $isSuperAdmin))
                    <li class="mb-1"><a href="{{ route('admin.users.index', ['user_type' => 'Shop Boy']) }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request('user_type') == 'Shop Boy' ? 'active' : '' }}"><i class="bi bi-person-badge me-2 fs-6 text-primary"></i> Shop Boy</a></li>
                    @endif
                    @if(canViewUserType('Installer', $allowedUserTypes, $isSuperAdmin))
                    <li class="mb-1"><a href="{{ route('admin.users.index', ['user_type' => 'Installer']) }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request('user_type') == 'Installer' ? 'active' : '' }}"><i class="bi bi-person-gear me-2 fs-6 text-info"></i> Installer</a></li>
                    @endif
                    @if(canViewUserType('Women Entrepreneurs', $allowedUserTypes, $isSuperAdmin))
                    <li class="mb-1"><a href="{{ route('admin.users.index', ['user_type' => 'Women Entrepreneurs']) }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request('user_type') == 'Women Entrepreneurs' ? 'active' : '' }}"><i class="bi bi-person-heart me-2 fs-6 text-danger"></i> Women Entrepreneurs</a></li>
                    @endif
                </ul>
            </li>
            @endcan
            
            @canany(['admin_users.view', 'roles.view'])
            <li class="nav-item mb-2">
                <a href="#accessControlSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.admin-users.*', 'admin.roles.*') ? 'true' : 'false' }}" class="nav-link rounded-3 px-3 py-2 text-white d-flex justify-content-between align-items-center transition-all {{ request()->routeIs('admin.admin-users.*', 'admin.roles.*') ? 'bg-white bg-opacity-10 shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-lock nav-icon text-warning me-2"></i>
                        <span class="nav-text fw-medium">Access Control</span>
                    </div>
                    <i class="bi bi-chevron-down nav-arrow transition-transform"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 mt-2 mb-2 {{ request()->routeIs('admin.admin-users.*', 'admin.roles.*') ? 'show' : '' }}" id="accessControlSubmenu">
                    @can('admin_users.view')<li class="mb-1"><a href="{{ route('admin.admin-users.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}"><i class="bi bi-shield-check me-2 fs-6 text-success"></i> Admins</a></li>@endcan
                    @can('roles.view')<li class="mb-1"><a href="{{ route('admin.roles.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"><i class="bi bi-key me-2 fs-6 text-warning"></i> Roles & Permissions</a></li>@endcan
                </ul>
            </li>
            @endcanany

            @canany(['locations.view', 'zones.view', 'states.view', 'cities.view', 'pincodes.view'])
            <li class="nav-item mb-2">
                <a href="#locationSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.zones.*', 'admin.states.*', 'admin.cities.*', 'admin.pincodes.*') ? 'true' : 'false' }}" class="nav-link rounded-3 px-3 py-2 text-white d-flex justify-content-between align-items-center transition-all {{ request()->routeIs('admin.zones.*', 'admin.states.*', 'admin.cities.*', 'admin.pincodes.*') ? 'bg-white bg-opacity-10 shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-geo-alt nav-icon me-2" style="color: #fd7e14;"></i>
                        <span class="nav-text fw-medium">Locations</span>
                    </div>
                    <i class="bi bi-chevron-down nav-arrow transition-transform"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 mt-2 mb-2 {{ request()->routeIs('admin.zones.*', 'admin.states.*', 'admin.cities.*', 'admin.pincodes.*') ? 'show' : '' }}" id="locationSubmenu">
                    @can('zones.view')<li class="mb-1"><a href="{{ route('admin.zones.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.zones.*') ? 'active' : '' }}"><i class="bi bi-map me-2 fs-6 text-primary"></i> Zones</a></li>@endcan
                    @can('states.view')<li class="mb-1"><a href="{{ route('admin.states.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.states.*') ? 'active' : '' }}"><i class="bi bi-geo me-2 fs-6 text-info"></i> States</a></li>@endcan
                    @can('cities.view')<li class="mb-1"><a href="{{ route('admin.cities.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}"><i class="bi bi-building me-2 fs-6 text-success"></i> Cities</a></li>@endcan
                    @can('pincodes.view')<li class="mb-1"><a href="{{ route('admin.pincodes.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.pincodes.*') ? 'active' : '' }}"><i class="bi bi-pin-map me-2 fs-6 text-warning"></i> Pincodes</a></li>@endcan
                </ul>
            </li>
            @endcanany

            @canany(['categories.view', 'subcategories.view', 'products.view'])
            <li class="nav-item mb-2">
                <a href="#productSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.categories.*', 'admin.subcategories.*', 'admin.products.*') ? 'true' : 'false' }}" class="nav-link rounded-3 px-3 py-2 text-white d-flex justify-content-between align-items-center transition-all {{ request()->routeIs('admin.categories.*', 'admin.subcategories.*', 'admin.products.*') ? 'bg-white bg-opacity-10 shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box nav-icon text-info me-2"></i>
                        <span class="nav-text fw-medium">Product Mgmt</span>
                    </div>
                    <i class="bi bi-chevron-down nav-arrow transition-transform"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 mt-2 mb-2 {{ request()->routeIs('admin.categories.*', 'admin.subcategories.*', 'admin.products.*') ? 'show' : '' }}" id="productSubmenu">
                    @can('categories.view')<li class="mb-1"><a href="{{ route('admin.categories.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="bi bi-tags me-2 fs-6 text-primary"></i> Categories</a></li>@endcan
                    @can('subcategories.view')<li class="mb-1"><a href="{{ route('admin.subcategories.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}"><i class="bi bi-diagram-3 me-2 fs-6 text-info"></i> Subcategories</a></li>@endcan
                    @can('products.view')<li class="mb-1"><a href="{{ route('admin.products.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><i class="bi bi-boxes me-2 fs-6 text-success"></i> Products</a></li>@endcan
                </ul>
            </li>
            @endcanany

            @can('shops.view')
            <li class="nav-item mb-2">
                <a href="{{ route('admin.shops.index') }}" class="nav-link rounded-3 px-3 py-2 text-white transition-all {{ request()->routeIs('admin.shops.*') ? 'active bg-gradient-primary shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shop nav-icon {{ request()->routeIs('admin.shops.*') ? 'text-white' : 'text-danger' }} me-2"></i>
                        <span class="nav-text fw-medium">Network (Shops)</span>
                    </div>
                </a>
            </li>
            @endcan

            @canany(['invoice.view', 'installations.view', 'leads.view'])
            <li class="nav-item mb-2">
                <a href="#salesSubmissionsSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.invoices.*', 'admin.installations.*', 'admin.leads.*') ? 'true' : 'false' }}" class="nav-link rounded-3 px-3 py-2 text-white d-flex justify-content-between align-items-center transition-all {{ request()->routeIs('admin.invoices.*', 'admin.installations.*', 'admin.leads.*') ? 'bg-white bg-opacity-10 shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-check nav-icon text-success me-2"></i>
                        <span class="nav-text fw-medium">Sales Submissions</span>
                    </div>
                    <i class="bi bi-chevron-down nav-arrow transition-transform"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 mt-2 mb-2 {{ request()->routeIs('admin.invoices.*', 'admin.installations.*', 'admin.leads.*') ? 'show' : '' }}" id="salesSubmissionsSubmenu">
                    @if(canViewUserType('Shop Boy', $allowedUserTypes, $isSuperAdmin))
                    @can('invoice.view')<li class="mb-1"><a href="{{ route('admin.invoices.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}"><i class="bi bi-receipt-cutoff me-2 fs-6 text-primary"></i> Shop Boys</a></li>@endcan
                    @endif
                    @if(canViewUserType('Installer', $allowedUserTypes, $isSuperAdmin))
                    @can('installations.view')<li class="mb-1"><a href="{{ route('admin.installations.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.installations.*') ? 'active' : '' }}"><i class="bi bi-tools me-2 fs-6 text-info"></i> Installer</a></li>@endcan
                    @endif
                    @if(canViewUserType('Women Entrepreneurs', $allowedUserTypes, $isSuperAdmin))
                    @can('leads.view')<li class="mb-1"><a href="{{ route('admin.leads.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}"><i class="bi bi-funnel me-2 fs-6 text-danger"></i> Women Entrepreneurs</a></li>@endcan
                    @endif
                </ul>
            </li>
            @endcanany

            @canany(['wallets.view', 'redemptions.view'])
            <li class="nav-item mb-2">
                <a href="#rewardsSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('admin.redemptions.*', 'admin.wallets.*') ? 'true' : 'false' }}" class="nav-link rounded-3 px-3 py-2 text-white d-flex justify-content-between align-items-center transition-all {{ request()->routeIs('admin.redemptions.*', 'admin.wallets.*') ? 'bg-white bg-opacity-10 shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-gift nav-icon text-warning me-2"></i>
                        <span class="nav-text fw-medium">Rewards</span>
                    </div>
                    <i class="bi bi-chevron-down nav-arrow transition-transform"></i>
                </a>
                <ul class="collapse list-unstyled ps-4 mt-2 mb-2 {{ request()->routeIs('admin.redemptions.*', 'admin.wallets.*') ? 'show' : '' }}" id="rewardsSubmenu">
                    @can('wallets.view')<li class="mb-1"><a href="{{ route('admin.wallets.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}"><i class="bi bi-wallet2 me-2 fs-6 text-success"></i> User Wallets</a></li>@endcan
                    @can('redemptions.view')<li class="mb-1"><a href="{{ route('admin.redemptions.index') }}" class="submenu-link rounded-3 px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.redemptions.*') ? 'active' : '' }}"><i class="bi bi-award me-2 fs-6 text-warning"></i> Redemptions</a></li>@endcan
                </ul>
            </li>
            @endcanany

            @can('notifications.view')
            <li class="nav-item mb-2">
                <a href="{{ route('admin.notifications.index') }}" class="nav-link rounded-3 px-3 py-2 text-white transition-all {{ request()->routeIs('admin.notifications.*') ? 'active bg-gradient-primary shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-bell nav-icon {{ request()->routeIs('admin.notifications.*') ? 'text-white' : 'text-danger' }} me-2"></i>
                        <span class="nav-text fw-medium">Notifications</span>
                    </div>
                </a>
            </li>
            @endcan

            @can('cms.view')
            <li class="nav-item mb-2">
                <a href="{{ route('admin.cms-pages.index') }}" class="nav-link rounded-3 px-3 py-2 text-white transition-all {{ request()->routeIs('admin.cms-pages.*') ? 'active bg-gradient-primary shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text nav-icon {{ request()->routeIs('admin.cms-pages.*') ? 'text-white' : 'text-primary' }} me-2"></i>
                        <span class="nav-text fw-medium">CMS</span>
                    </div>
                </a>
            </li>
            @endcan

            @can('reports.view')
            <li class="nav-item mb-2">
                <a href="{{ route('admin.reports.index') }}" class="nav-link rounded-3 px-3 py-2 text-white transition-all {{ request()->routeIs('admin.reports.*') ? 'active bg-gradient-primary shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-graph-up nav-icon {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-info' }} me-2"></i>
                        <span class="nav-text fw-medium">Reports</span>
                    </div>
                </a>
            </li>
            @endcan

            <div class="sidebar-divider my-3"></div>
            
            <li class="nav-label text-uppercase text-light opacity-50 small fw-bold mb-2 px-3 letter-spacing-1">System</li>

            @can('settings.view')
            <li class="nav-item mb-2">
                <a href="{{ route('admin.settings.index') }}" class="nav-link rounded-3 px-3 py-2 text-white transition-all {{ request()->routeIs('admin.settings.*') ? 'active bg-gradient-primary shadow-sm' : 'hover-bg-light' }}">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-gear nav-icon {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-light' }} me-2"></i>
                        <span class="nav-text fw-medium">Settings</span>
                    </div>
                </a>
            </li>
            @endcan
        </ul>
    </div>
</nav>

<style>
    :root {
        --sidebar-bg: #111827;
        --sidebar-accent: #3b82f6;
        --sidebar-accent-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        --sidebar-text: #f3f4f6;
        --sidebar-text-muted: #9ca3af;
        --sidebar-hover-bg: rgba(255, 255, 255, 0.05);
    }
    
    .premium-sidebar {
        background: var(--sidebar-bg);
        color: var(--sidebar-text);
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .bg-gradient-primary {
        background: var(--sidebar-accent-gradient) !important;
    }

    .text-gradient {
        background: linear-gradient(to right, #ffffff, #9ca3af);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .sidebar-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.1), transparent);
    }

    .letter-spacing-1 {
        letter-spacing: 1px;
    }
    
    .transition-all {
        transition: all 0.3s ease;
    }
    
    .transition-transform {
        transition: transform 0.3s ease;
    }
    
    .hover-bg-light:hover {
        background-color: var(--sidebar-hover-bg);
        transform: translateX(4px);
    }

    .nav-item .nav-link {
        font-size: 0.95rem;
    }

    .nav-item .nav-link[aria-expanded="true"] .nav-arrow {
        transform: rotate(180deg);
    }

    .submenu-link {
        color: var(--sidebar-text-muted);
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.9rem;
        position: relative;
    }
    
    .submenu-link:hover {
        color: white;
        background-color: var(--sidebar-hover-bg);
        transform: translateX(4px);
    }

    .submenu-link.active {
        color: white;
        background-color: rgba(59, 130, 246, 0.15);
        font-weight: 500;
        border-left: 3px solid var(--sidebar-accent);
    }

    /* Custom scrollbar for sidebar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.2);
    }
</style>
