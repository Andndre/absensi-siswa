@extends('layouts.student')

@section('title', 'QR Code Saya')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-qrcode me-2"></i>QR Code Absensi Saya</h5>
                </div>
                
                <div class="card-body text-center">
                    @if($student->qr_code)
                        <!-- QR Code Display -->
                        <div class="qr-code-container mb-4">
                            <div class="qr-code-frame mx-auto" style="max-width: 300px;">
                                <div class="border border-3 border-primary p-3 rounded">
                                    {!! QrCode::size(250)->generate($student->qr_code) !!}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Student Info -->
                        <div class="student-info mb-4">
                            <h4 class="text-primary">{{ $student->name }}</h4>
                            <p class="text-muted mb-1">NIS: <strong>{{ $student->nis }}</strong></p>
                            <p class="text-muted mb-1">Kelas: <strong>{{ $student->schoolClass->name }}</strong></p>
                            <p class="text-muted">QR Code: <code>{{ $student->qr_code }}</code></p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <a href="{{ route('student.qr-code.download') }}" class="btn btn-success btn-lg me-2">
                                <i class="fas fa-download me-2"></i>Download QR Code
                            </a>
                            <button type="button" class="btn btn-outline-warning btn-lg" data-bs-toggle="modal" data-bs-target="#regenerateModal">
                                <i class="fas fa-sync-alt me-2"></i>Generate Ulang
                            </button>
                        </div>
                        
                        <!-- Panduan Penggunaan -->
                        <div class="mt-5">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Cara Menggunakan QR Code</h6>
                                <ul class="mb-0 text-start">
                                    <li>Tunjukkan QR Code ini kepada admin/guru saat absensi</li>
                                    <li>Admin/guru akan scan QR Code dengan scanner khusus</li>
                                    <li>Absensi Anda akan tercatat secara otomatis</li>
                                    <li>Simpan atau download QR Code untuk kemudahan akses</li>
                                </ul>
                            </div>
                        </div>
                        
                    @else
                        <!-- No QR Code -->
                        <div class="no-qr-code">
                            <i class="fas fa-qrcode text-muted" style="font-size: 5rem;"></i>
                            <h4 class="text-muted mt-3">QR Code Belum Tersedia</h4>
                            <p class="text-muted">QR Code Anda belum di-generate. Silakan hubungi admin untuk membuat QR Code.</p>
                            
                            <form method="POST" action="{{ route('student.qr-code.regenerate') }}" class="mt-4">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-plus me-2"></i>Generate QR Code
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Regenerate QR Code -->
@if($student->qr_code)
<div class="modal fade" id="regenerateModal" tabindex="-1" aria-labelledby="regenerateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regenerateModalLabel">Generate Ulang QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin generate ulang QR Code?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian:</strong> QR Code lama akan tidak dapat digunakan setelah di-generate ulang.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('student.qr-code.regenerate') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-sync-alt me-2"></i>Generate Ulang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('styles')
<style>
.qr-code-frame {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
}

.qr-code-frame svg {
    display: block;
    margin: 0 auto;
}

.student-info h4 {
    font-weight: 600;
}

.action-buttons .btn {
    min-width: 180px;
}

@media (max-width: 576px) {
    .action-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .action-buttons .btn:last-child {
        margin-bottom: 0;
    }
}
</style>
@endsection
