<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZAR Logistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4ade80;
            --primary-dark: #22c55e;
            --primary-light: #86efac;
            --secondary-color: #000000;
            --white: #ffffff;
            --gray-light: #f3f4f6;
            --gray-medium: #9ca3af;
            --gray-dark: #4b5563;
            --primary-gradient: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            --sidebar-width: 260px;
            --navbar-height: 70px;
        }

        body {
            background-color: var(--gray-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            background: var(--white);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }
        .card-header {
            background: var(--primary-gradient);
            color: var(--white);
            border-radius: 12px 12px 0 0 !important;
            border: none;
            padding: 1.5rem;
        }
        .card-header h6 {
            margin: 0;
            font-weight: 600;
        }
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
        }
        .btn-secondary {
            background: var(--gray-medium);
            border: none;
            border-radius: 8px;
            color: var(--white);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: var(--gray-dark);
            transform: translateY(-1px);
        }
        .form-control {
            border: 2px solid var(--gray-medium);
            border-radius: 8px;
            padding: 0.75rem;
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(74, 222, 128, 0.25);
        }
        .progress {
            height: 10px;
            border-radius: 5px;
        }
        .table-hover tbody tr:hover {
            background-color: var(--primary-light);
            cursor: pointer;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        .badge {
            font-size: 0.75em;
            border-radius: 20px;
            padding: 0.5rem 1rem;
        }
        .btn-group .btn {
            border-radius: 6px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Sidebar -->
        <nav class="sidebar bg-white text-dark p-3" style="width: var(--sidebar-width); min-height: 100vh;">
            <div class="sidebar-header mb-4">
                <a class="navbar-brand text-dark" href="/" style="font-size: 1.7rem; color: var(--primary-color) !important;">ZAR Logistics</a>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a class="nav-link text-dark" href="{{ route('driver-performance.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2" style="color: var(--primary-color);"></i> Driver Performance
                    </a>
                </li>
                <li class="nav-item mb-2 dropdown">
                    <a class="nav-link dropdown-toggle text-dark active" href="#" id="routeManagementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--primary-color) !important;">
                        <i class="fas fa-route me-2" style="color: var(--primary-color);"></i> Route Management
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="routeManagementDropdown">
                        <li><a class="dropdown-item" href="{{ route('route-management.index') }}#routes">
                            <i class="fas fa-list me-2" style="color: var(--primary-color);"></i> Routes
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('route-management.index') }}#gps">
                            <i class="fas fa-map-marker-alt me-2" style="color: var(--primary-color);"></i> GPS Tracking
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('route-management.create') }}">
                            <i class="fas fa-plus me-2" style="color: var(--primary-color);"></i> Create Route
                        </a></li>
                    </ul>
                </li>
                <!-- Add more sidebar links here -->
            </ul>
        </nav>
        <!-- Main Content -->
        <div class="flex-grow-1" style="background-color: var(--gray-light);">
            <div class="container-fluid py-4">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
    <style>
        .sidebar {
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            border-right: 1px solid var(--gray-light);
        }
        .sidebar .nav-link {
            font-size: 1.1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 0.25rem 0;
            color: var(--gray-dark);
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--primary-gradient);
            color: var(--white) !important;
            box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3);
        }
        .dropdown-menu {
            background: var(--white);
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-radius: 8px;
            margin-top: 0.5rem;
        }
        .dropdown-item {
            color: var(--gray-dark);
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        .dropdown-item:hover,
        .dropdown-item.active {
            background: var(--primary-light);
            color: var(--secondary-color);
        }
        .dropdown-toggle::after {
            color: var(--white);
        }
        #map {
            border: 2px solid var(--primary-light);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fix dropdown toggle for route management in sidebar
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownToggle = document.getElementById('routeManagementDropdown');
            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    var dropdownMenu = this.nextElementSibling;
                    if (dropdownMenu) {
                        dropdownMenu.classList.toggle('show');
                    }
                });
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    var isClickInside = dropdownToggle.contains(e.target) || (dropdownToggle.nextElementSibling && dropdownToggle.nextElementSibling.contains(e.target));
                    if (!isClickInside) {
                        var dropdownMenu = dropdownToggle.nextElementSibling;
                        if (dropdownMenu) {
                            dropdownMenu.classList.remove('show');
                        }
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
