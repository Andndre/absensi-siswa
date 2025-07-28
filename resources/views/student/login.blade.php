<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #34568B;
            --primary-light: #0d6efd;
            --bg-light: #f8f9fa;
        }

        body {
            background-color: var(--bg-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 1rem 0;
        }

        .login-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            border: 1px solid #e9ecef;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .login-header h3 {
            color: #212529;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem;
            font-weight: 500;
            border-radius: 6px;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
        }

        .form-control {
            padding: 0.75rem;
            border-radius: 6px;
            border: 1px solid #ced4da;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 86, 139, 0.25);
        }

        .input-group-text {
            background: #f8f9fa;
            border: 1px solid #ced4da;
            border-right: none;
            color: #6c757d;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: none;
        }

        .alert {
            border-radius: 6px;
            font-size: 0.9rem;
        }

        /* Mobile Optimizations */
        @media (max-width: 576px) {
            body {
                padding: 0.5rem;
            }

            .login-card {
                padding: 1.5rem;
                margin: 0.5rem;
            }

            .login-header i {
                font-size: 2rem;
            }

            .login-header h3 {
                font-size: 1.4rem;
            }

            .form-control {
                padding: 0.65rem;
            }

            .btn-primary {
                padding: 0.65rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-graduation-cap"></i>
                        <h3 class="mb-0">Login Siswa</h3>
                        <p class="text-muted mb-0">Masuk untuk melakukan absensi</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('student.login.submit') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <input type="text" class="form-control @error('nis') is-invalid @enderror" 
                                       id="nis" name="nis" value="{{ old('nis') }}" 
                                       placeholder="Masukkan NIS" required autofocus>
                            </div>
                            @error('nis')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="Masukkan password" required>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-1">Default password: NIS Anda</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
