<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Sistem Absensi Siswa</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #34568B;
            --primary-light: #0d6efd;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --bg-light: #f8f9fa;
            --text-muted: #6c757d;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 0;
        }

        /* Mobile First - Navbar */
        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }

        /* Welcome Header - Mobile First */
        .welcome-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        .welcome-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        /* Stat Cards - Mobile First */
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid;
            transition: box-shadow 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .stat-present { border-left-color: var(--success-color); }
        .stat-late { border-left-color: var(--warning-color); }
        .stat-absent { border-left-color: var(--danger-color); }
        .stat-excused { border-left-color: var(--info-color); }

        .stat-icon {
            font-size: 2rem;
            margin-right: 1rem;
            min-width: 50px;
            text-align: center;
        }

        .stat-present .stat-icon { color: var(--success-color); }
        .stat-late .stat-icon { color: var(--warning-color); }
        .stat-absent .stat-icon { color: var(--danger-color); }
        .stat-excused .stat-icon { color: var(--info-color); }

        .stat-info h3 {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
            line-height: 1;
        }

        .stat-info p {
            margin: 0;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.9rem;
        }

        /* Cards - Simple Design */
        .card {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }

        .card-header {
            border-radius: 8px 8px 0 0 !important;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .card-header.bg-primary {
            background-color: var(--primary-color) !important;
        }

        .card-header.bg-info {
            background-color: var(--info-color) !important;
        }

        .card-header.bg-secondary {
            background-color: var(--text-muted) !important;
        }

        /* Quick QR Scan Button */
        .quick-scan-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border: 2px solid transparent;
            border-radius: 12px;
            color: white;
            padding: 1.25rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 86, 139, 0.3);
            position: relative;
            overflow: hidden;
        }

        .quick-scan-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .quick-scan-btn:hover::before {
            left: 100%;
        }

        .quick-scan-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 86, 139, 0.4);
            border-color: rgba(255,255,255,0.3);
        }

        .quick-scan-btn:disabled {
            background: linear-gradient(135deg, var(--text-muted) 0%, #adb5bd 100%);
            cursor: not-allowed;
            transform: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .quick-scan-btn:disabled::before {
            display: none;
        }

        /* Warning variant for late scanning */
        .quick-scan-btn.btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .quick-scan-btn.btn-warning:hover {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        .quick-scan-btn i {
            font-size: 1.5rem;
            margin-right: 0.75rem;
            animation: pulse-icon 2s infinite;
        }

        .quick-scan-btn:disabled i {
            animation: none;
        }

        @keyframes pulse-icon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* QR Scan Card */
        .qr-scan-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .qr-scan-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(52, 86, 139, 0.1);
        }

        /* QR Scan Section */
        .qr-icon {
            font-size: 3rem;
            color: var(--primary-color);
        }

        .qr-scan-active .qr-icon {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        /* Profile Section */
        .profile-info {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .profile-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .profile-item:last-child {
            border-bottom: none;
        }

        .profile-item label {
            font-weight: 600;
            color: #495057;
            margin: 0;
            font-size: 0.9rem;
        }

        .profile-item span {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Table */
        .table {
            border-radius: 8px;
            overflow: hidden;
            font-size: 0.9rem;
        }

        .table th {
            background-color: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
            font-size: 0.85rem;
            padding: 0.75rem;
        }

        .table td {
            border: none;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
            padding: 0.75rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Badges */
        .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.65rem;
            border-radius: 4px;
            font-weight: 500;
        }

        /* Buttons - Simple Design */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
            border-width: 1px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        /* Mobile First Responsive */
        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .welcome-header {
                padding: 1.25rem;
                margin-bottom: 1.25rem;
            }

            .welcome-header h2 {
                font-size: 1.3rem;
            }

            .quick-scan-btn {
                padding: 1rem 1.5rem;
                font-size: 1rem;
            }

            .quick-scan-btn i {
                font-size: 1.25rem;
            }

            .stat-card {
                text-align: center;
                flex-direction: column;
                padding: 1rem;
            }

            .stat-icon {
                margin-right: 0;
                margin-bottom: 0.5rem;
                font-size: 1.75rem;
            }

            .stat-info h3 {
                font-size: 1.5rem;
            }

            .profile-item {
                flex-direction: column;
                text-align: center;
                gap: 0.25rem;
                padding: 0.5rem 0;
            }

            .card-body {
                padding: 1rem;
            }

            .table-responsive {
                font-size: 0.8rem;
            }

            .btn-lg {
                padding: 0.65rem 1.25rem;
                font-size: 0.95rem;
            }
        }

        @media (min-width: 577px) and (max-width: 768px) {
            .welcome-header {
                padding: 1.5rem;
            }

            .welcome-header h2 {
                font-size: 1.4rem;
            }
        }

        @media (min-width: 769px) {
            .welcome-header {
                padding: 2rem;
                text-align: left;
            }

            .welcome-header h2 {
                font-size: 1.75rem;
            }

            .stat-card {
                margin-bottom: 0;
            }
        }

        /* Alert Animations */
        .alert {
            border-radius: 6px;
            border: 1px solid transparent;
            font-size: 0.9rem;
        }

        /* Loading Spinner */
        .spinner-border {
            width: 2.5rem;
            height: 2.5rem;
        }

        /* Modal Adjustments */
        .modal-content {
            border-radius: 8px;
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.25rem;
        }

        .modal-body {
            padding: 1.25rem;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                <i class="fas fa-graduation-cap me-2"></i>
                Sistem Absensi
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.profile*') ? 'active' : '' }}" href="{{ route('student.profile') }}">
                            <i class="fas fa-user-edit me-1"></i>Profil
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i>{{ auth('student')->user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('student.change-password') }}">
                                    <i class="fas fa-key me-2"></i>Ubah Password
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        @if(session('success'))
            <div class="container-fluid">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container-fluid">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @yield('scripts')
</body>
</html>
