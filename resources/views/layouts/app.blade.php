<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-color: #34568B;
            --primary-light: #0d6efd;
            --bg-light: #f8f9fa;
            --success-color: #198754;
            --sidebar-width: 250px;
            --sidebar-bg: #212529;
            --sidebar-text: #adb5bd;
            --sidebar-active: #495057;
        }
        
        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .card-stats {
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            z-index: 1000;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            overflow-y: auto;
        }

        .sidebar.show {
            transform: translateX(0);
        }

        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: var(--sidebar-width);
            }
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid var(--sidebar-active);
        }

        .sidebar-brand {
            color: white !important;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .sidebar-brand:hover {
            color: #fff !important;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-section {
            margin-bottom: 1.5rem;
        }

        .nav-section-title {
            color: var(--sidebar-text);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 1rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-nav .nav-link {
            color: var(--sidebar-text);
            padding: 0.75rem 1rem;
            border-radius: 0;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-nav .nav-link:hover {
            color: white;
            background-color: var(--sidebar-active);
            border-left-color: var(--primary-color);
        }

        .sidebar-nav .nav-link.active {
            color: white;
            background-color: var(--primary-color);
            border-left-color: var(--primary-light);
        }

        .sidebar-nav .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 1rem;
            border-top: 1px solid var(--sidebar-active);
        }

        /* Mobile sidebar overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Mobile navbar adjustments */
        @media (max-width: 991px) {
            .navbar {
                margin-left: 0;
            }
        }

        /* Content area adjustments */
        .main-content {
            transition: margin-left 0.3s ease-in-out;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div id="app">
        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        @auth
        <nav class="sidebar" id="sidebar">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <i class="bi bi-calendar-check-fill me-2"></i>
                    ABSENSI SEKOLAH
                </a>
            </div>

            <!-- Sidebar Navigation -->
            <div class="sidebar-nav">
                <!-- Navigasi Utama -->
                <div class="nav-section">
                    <div class="nav-section-title">Navigasi Utama</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                <i class="bi bi-grid-fill"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" 
                               href="{{ route('admin.attendance.index') }}">
                                <i class="bi bi-calendar2-check"></i>
                                Rekap Absensi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.qr-code.*') ? 'active' : '' }}" 
                               href="{{ route('admin.qr-code.today') }}">
                                <i class="bi bi-qr-code"></i>
                                QR Code Harian
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Data Master -->
                <div class="nav-section">
                    <div class="nav-section-title">Data Master</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" 
                               href="{{ route('admin.students.index') }}">
                                <i class="bi bi-people-fill"></i>
                                Manajemen Siswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}" 
                               href="{{ route('admin.classes.index') }}">
                                <i class="bi bi-collection-fill"></i>
                                Manajemen Kelas
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Sistem -->
                <div class="nav-section">
                    <div class="nav-section-title">Sistem</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                               href="#" onclick="alert('Fitur sedang dalam pengembangan')">
                                <i class="bi bi-person-gear"></i>
                                Manajemen Admin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                               href="#" onclick="alert('Fitur sedang dalam pengembangan')">
                                <i class="bi bi-gear-fill"></i>
                                Pengaturan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <a class="nav-link text-danger" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </nav>
        @endauth

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    @auth
                    <!-- Sidebar Toggle Button (Mobile) -->
                    <button class="btn btn-outline-primary d-lg-none me-2" type="button" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    @endauth

                    <a class="navbar-brand d-lg-none" href="{{ url('/') }}">
                        <i class="bi bi-calendar-check-fill me-2"></i>
                        {{ config('app.name', 'Sistem Absensi') }}
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto">
                            @guest
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                                    </a>
                                </li>
                            @endguest
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif

                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        <i class="bi bi-person-circle me-1"></i>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('dashboard') }}">
                                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="py-4" style="background-color: var(--bg-light); min-height: calc(100vh - 76px);">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Sidebar JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const sidebarToggle = document.getElementById('sidebarToggle');

            // Toggle sidebar on mobile
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
            }

            // Close sidebar when clicking overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }

            // Close sidebar on window resize if mobile
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>
