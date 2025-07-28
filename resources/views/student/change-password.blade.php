@extends('layouts.student')

@section('styles')
<style>
    /* Mobile optimizations for change password */
    @media (max-width: 576px) {
        .input-group .btn {
            min-width: 44px;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .form-control {
            font-size: 16px; /* Prevent zoom on iOS */
        }
    }
    
    .input-group .btn-outline-secondary {
        border-color: #ced4da;
    }
    
    .password-toggle {
        cursor: pointer;
    }
</style>
@endsection

@section('title', 'Ubah Password')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('student.change-password.submit') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" 
                                       placeholder="Masukkan password saat ini" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" name="new_password" 
                                       placeholder="Masukkan password baru" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye" id="new_password_icon"></i>
                                </button>
                            </div>
                            @error('new_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Password minimal 6 karakter
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-check"></i>
                                </span>
                                <input type="password" class="form-control" 
                                       id="new_password_confirmation" name="new_password_confirmation" 
                                       placeholder="Konfirmasi password baru" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                    <i class="fas fa-eye" id="new_password_confirmation_icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-sm-end">
                            <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Tips -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Tips Keamanan Password
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                        <li>Minimal 8 karakter untuk keamanan yang lebih baik</li>
                        <li>Jangan gunakan informasi pribadi seperti nama atau tanggal lahir</li>
                        <li>Jangan bagikan password Anda kepada siapapun</li>
                        <li>Ubah password secara berkala</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

// Real-time password confirmation validation
document.getElementById('new_password_confirmation').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && newPassword !== confirmPassword) {
        this.setCustomValidity('Password tidak cocok');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

// Password strength indicator
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strengthIndicator = document.getElementById('password-strength');
    
    let strength = 0;
    
    // Length check
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    
    // Character variety checks
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    // Update indicator (if exists)
    if (strengthIndicator) {
        let strengthText = '';
        let strengthClass = '';
        
        if (strength < 3) {
            strengthText = 'Lemah';
            strengthClass = 'text-danger';
        } else if (strength < 5) {
            strengthText = 'Sedang';
            strengthClass = 'text-warning';
        } else {
            strengthText = 'Kuat';
            strengthClass = 'text-success';
        }
        
        strengthIndicator.textContent = strengthText;
        strengthIndicator.className = strengthClass;
    }
});
</script>
@endsection
