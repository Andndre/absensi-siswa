@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Pengaturan Sistem</h1>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6>Ada kesalahan pada form:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="whatsapp-tab" data-bs-toggle="tab" data-bs-target="#whatsapp" type="button" role="tab">
                        <i class="fab fa-whatsapp me-2"></i>WhatsApp
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="school-tab" data-bs-toggle="tab" data-bs-target="#school" type="button" role="tab">
                        <i class="fas fa-school me-2"></i>Sekolah
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
                        <i class="fas fa-cog me-2"></i>Sistem
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="settingsTabContent">
                <!-- WhatsApp Settings -->
                <div class="tab-pane fade show active" id="whatsapp" role="tabpanel">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fab fa-whatsapp text-success me-2"></i>
                                        Pengaturan WhatsApp
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.settings.whatsapp') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="mb-3">
                                            <label for="fonnte_token" class="form-label">Fonnte API Token</label>
                                            <input type="text" class="form-control {{ $errors->has('fonnte_token') ? 'is-invalid' : '' }}" 
                                                   id="fonnte_token" name="fonnte_token" 
                                                   value="{{ old('fonnte_token', $settings['whatsapp']['fonnte_token'] ?? '') }}">
                                            @if($errors->has('fonnte_token'))
                                                <div class="invalid-feedback">{{ $errors->first('fonnte_token') }}</div>
                                            @endif
                                            <div class="form-text">
                                                Token API dari <a href="https://fonnte.com" target="_blank">Fonnte.com</a>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="auto_notification" name="auto_notification" value="1"
                                                       {{ ($settings['whatsapp']['auto_notification'] ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="auto_notification">
                                                    Aktifkan notifikasi otomatis ke orang tua
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="notification_format" class="form-label">Format Pesan Notifikasi</label>
                                            <textarea class="form-control {{ $errors->has('notification_format') ? 'is-invalid' : '' }}" 
                                                      id="notification_format" name="notification_format" rows="6">{{ old('notification_format', $settings['whatsapp']['notification_format'] ?? '') }}</textarea>
                                            @if($errors->has('notification_format'))
                                                <div class="invalid-feedback">{{ $errors->first('notification_format') }}</div>
                                            @endif
                                            <div class="form-text">
                                                Gunakan placeholder: {student_name}, {status}, {time}, {date}, {school_name}
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- School Settings -->
                <div class="tab-pane fade" id="school" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-school text-primary me-2"></i>
                                Informasi Sekolah
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.settings.school') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="school_name" class="form-label">Nama Sekolah</label>
                                            <input type="text" class="form-control" id="school_name" name="name" 
                                                   value="{{ $settings['school']['name'] ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="school_phone" class="form-label">Telepon</label>
                                            <input type="text" class="form-control" id="school_phone" name="phone" 
                                                   value="{{ $settings['school']['phone'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="school_address" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="school_address" name="address" rows="3" required>{{ $settings['school']['address'] ?? '' }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="school_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="school_email" name="email" 
                                           value="{{ $settings['school']['email'] ?? '' }}">
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- System Settings -->
                <div class="tab-pane fade" id="system" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-cog text-secondary me-2"></i>
                                Pengaturan Sistem
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.settings.system') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="app_name" class="form-label">Nama Aplikasi</label>
                                            <input type="text" class="form-control" id="app_name" name="app_name" 
                                                   value="{{ $settings['system']['app_name'] ?? 'Sistem Absensi' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="timezone" class="form-label">Zona Waktu</label>
                                            <select class="form-select" id="timezone" name="timezone" required>
                                                <option value="Asia/Jakarta" {{ ($settings['system']['timezone'] ?? 'Asia/Jakarta') == 'Asia/Jakarta' ? 'selected' : '' }}>WIB (Asia/Jakarta)</option>
                                                <option value="Asia/Makassar" {{ ($settings['system']['timezone'] ?? '') == 'Asia/Makassar' ? 'selected' : '' }}>WITA (Asia/Makassar)</option>
                                                <option value="Asia/Jayapura" {{ ($settings['system']['timezone'] ?? '') == 'Asia/Jayapura' ? 'selected' : '' }}>WIT (Asia/Jayapura)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date_format" class="form-label">Format Tanggal</label>
                                            <select class="form-select" id="date_format" name="date_format" required>
                                                <option value="d/m/Y" {{ ($settings['system']['date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                                <option value="d-m-Y" {{ ($settings['system']['date_format'] ?? '') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY</option>
                                                <option value="Y-m-d" {{ ($settings['system']['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="records_per_page" class="form-label">Data per Halaman</label>
                                            <select class="form-select" id="records_per_page" name="records_per_page" required>
                                                <option value="10" {{ ($settings['system']['records_per_page'] ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ ($settings['system']['records_per_page'] ?? '') == 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ ($settings['system']['records_per_page'] ?? '') == 50 ? 'selected' : '' }}>50</option>
                                                <option value="100" {{ ($settings['system']['records_per_page'] ?? '') == 100 ? 'selected' : '' }}>100</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
