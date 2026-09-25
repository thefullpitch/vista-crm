<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - Vista CRM</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Admin CSS -->
    <link href="{{ asset('admin_assets/css/style.css') }}" rel="stylesheet">
    
    <style>
        /* Premium Sidebar & Layout Styles */
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', 'Roboto', sans-serif;
            overflow-x: hidden;
        }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #1e222d;
            color: #fff;
            transition: all 0.3s;
            position: fixed;
            height: 100vh;
            z-index: 1040;
        }
        #sidebar.active {
            margin-left: -260px;
        }
        .nav-link {
            transition: all 0.2s ease;
            opacity: 0.85;
            font-size: 0.95rem;
        }
        .nav-link:hover {
            opacity: 1;
            background-color: rgba(255,255,255,0.05);
            transform: translateX(3px);
        }
        .nav-link.active {
            opacity: 1;
            box-shadow: 0 4px 6px -1px rgba(13, 110, 253, 0.4);
        }
        .nav-icon {
            font-size: 1.1rem;
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .nav-arrow {
            font-size: 0.8rem;
            transition: transform 0.3s;
        }
        .nav-link[aria-expanded="true"] .nav-arrow {
            transform: rotate(180deg);
        }
        #content {
            width: calc(100% - 260px);
            margin-left: 260px;
            min-height: 100vh;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }
        #content.active {
            width: 100%;
            margin-left: 0;
        }
        .topbar {
            z-index: 1030;
        }
        /* Custom Scrollbar for Sidebar */
        .sidebar-body::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-body::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1); 
        }
        .sidebar-body::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2); 
            border-radius: 10px;
        }
        .sidebar-body::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.4); 
        }
        
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -260px;
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        @include('admin.layouts.sidebar')

        <!-- Page Content -->
        <div id="content" class="bg-light">
            <!-- Header/Topbar -->
            @include('admin.layouts.header')
            
            <!-- Main Content -->
            <div class="container-fluid px-4 pt-4 pb-5">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="mt-auto">
                @include('admin.layouts.footer')
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5.3 Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function () {
            // Sidebar Toggle Logic
            $('#sidebarToggle, #sidebarCloseBtn').on('click', function () {
                $('#sidebar').toggleClass('active');
                $('#content').toggleClass('active');
            });
            
            // Close sidebar on mobile when clicking outside
            $(document).on('click', function (e) {
                if ($(window).width() <= 768) {
                    if (!$(e.target).closest('#sidebar, #sidebarToggle').length) {
                        $('#sidebar').removeClass('active');
                    }
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>

