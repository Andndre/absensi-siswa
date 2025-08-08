@extends('layouts.student')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Profil Saya</h5>
                </div>
                
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('student.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $student->name) }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nis" class="form-label">NIS</label>
                                <input type="text" class="form-control" id="nis" value="{{ $student->nis }}" readonly>
                                <div class="form-text">NIS tidak dapat diubah</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $student->email) }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="school_class" class="form-label">Kelas</label>
                                <input type="text" class="form-control" id="school_class" value="{{ $student->schoolClass->name }}" readonly>
                                <div class="form-text">Kelas tidak dapat diubah</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="parent_whatsapp_number" class="form-label">Nomor WhatsApp Orang Tua</label>
                            <input type="text" class="form-control" id="parent_whatsapp_number" name="parent_whatsapp_number" 
                                   value="{{ old('parent_whatsapp_number', $student->parent_whatsapp_number) }}" 
                                   placeholder="Contoh: 081234567890">
                            <div class="form-text">Nomor WhatsApp untuk notifikasi absensi kepada orang tua</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status Akun</label>
                            <div>
                                @if($student->is_active)
                                    <span class="badge bg-success fs-6">Aktif</span>
                                @else
                                    <span class="badge bg-danger fs-6">Tidak Aktif</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                            </a>
                            <div>
                                <a href="{{ route('student.change-password') }}" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-key me-2"></i>Ubah Password
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Info Tambahan -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Statistik Absensi Bulan Ini</h6>
                            @php
                                $stats = $student->getAttendanceStatsForMonth();
                            @endphp
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Hadir: {{ $stats['hadir'] ?? 0 }} hari</li>
                                <li><i class="fas fa-clock text-warning me-2"></i>Terlambat: {{ $stats['terlambat'] ?? 0 }} hari</li>
                                <li><i class="fas fa-file-medical text-info me-2"></i>Izin: {{ $stats['izin'] ?? 0 }} hari</li>
                                <li><i class="fas fa-procedures text-secondary me-2"></i>Sakit: {{ $stats['sakit'] ?? 0 }} hari</li>
                                <li><i class="fas fa-times-circle text-danger me-2"></i>Alpha: {{ $stats['alpha'] ?? 0 }} hari</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Informasi Akun</h6>
                            <ul class="list-unstyled">
                                <li><strong>Bergabung:</strong> {{ $student->created_at->format('d F Y') }}</li>
                                @if($student->last_login_at)
                                    <li><strong>Login Terakhir:</strong> {{ $student->last_login_at->format('d F Y H:i') }}</li>
                                @endif
                                <li><strong>QR Code:</strong> 
                                    @if($student->qr_code)
                                        <span class="text-success">Tersedia</span>
                                    @else
                                        <span class="text-muted">Belum dibuat</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
