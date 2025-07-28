@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-qr-code me-2"></i>QR Code Absensi Hari Ini
                </h1>
                <div>
                    <span class="badge bg-info fs-6">{{ $today->format('d F Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($qrCode)
        <!-- QR Code Display -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="bi bi-qr-code me-2"></i>QR Code Aktif
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <!-- QR Code akan di-generate via JavaScript library atau external service -->
                        <div id="qrcode" class="mb-3"></div>
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted">Berlaku dari</small>
                                <div class="fw-bold">{{ Carbon\Carbon::parse($qrCode->valid_from)->format('H:i') }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Berlaku sampai</small>
                                <div class="fw-bold">{{ Carbon\Carbon::parse($qrCode->valid_until)->format('H:i') }}</div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            @if($qrCode->isValidNow())
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-check-circle me-1"></i>Sedang Aktif
                                </span>
                            @else
                                <span class="badge bg-warning fs-6">
                                    <i class="bi bi-clock me-1"></i>Belum/Sudah Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="bi bi-bar-chart me-2"></i>Statistik Penggunaan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="card bg-light text-center">
                                    <div class="card-body py-3">
                                        <h4 class="text-primary">{{ $stats['total_scans'] ?? 0 }}</h4>
                                        <small>Total Scan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light text-center">
                                    <div class="card-body py-3">
                                        <h4 class="text-success">{{ $stats['present'] ?? 0 }}</h4>
                                        <small>Hadir</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <small class="text-muted">Refresh Count:</small>
                                        <strong>{{ $qrCode->refresh_count }}</strong>
                                        <br>
                                        @if($stats['last_scan'] ?? null)
                                            <small class="text-muted">Scan Terakhir:</small>
                                            <strong>{{ $stats['last_scan']->attendance_time->format('H:i:s') }}</strong>
                                            oleh <strong>{{ $stats['last_scan']->student->name }}</strong>
                                        @else
                                            <small class="text-muted">Belum ada yang scan</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="m-0 font-weight-bold">
                            <i class="bi bi-gear me-2"></i>Pengaturan QR Code
                        </h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.qr-code.generate') }}" class="row g-3">
                            @csrf
                            <div class="col-md-3">
                                <label for="valid_from" class="form-label">Berlaku Dari</label>
                                <input type="time" class="form-control" id="valid_from" name="valid_from" 
                                       value="{{ Carbon\Carbon::parse($qrCode->valid_from)->format('H:i') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="valid_until" class="form-label">Berlaku Sampai</label>
                                <input type="time" class="form-control" id="valid_until" name="valid_until" 
                                       value="{{ Carbon\Carbon::parse($qrCode->valid_until)->format('H:i') }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-warning me-2">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh QR Code
                                </button>
                                <form method="POST" action="{{ route('admin.qr-code.deactivate') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Yakin ingin menonaktifkan QR Code?')">
                                        <i class="bi bi-stop-circle me-1"></i>Nonaktifkan
                                    </button>
                                </form>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No QR Code Today -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm text-center">
                    <div class="card-body py-5">
                        <i class="bi bi-qr-code display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Belum Ada QR Code Hari Ini</h4>
                        <p class="text-muted">Buat QR code baru untuk memulai absensi hari ini</p>
                        
                        <form method="POST" action="{{ route('admin.qr-code.generate') }}" class="mt-4">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label for="valid_from" class="form-label">Berlaku Dari</label>
                                            <input type="time" class="form-control" id="valid_from" name="valid_from" value="{{ $defaultStartTime }}">
                                        </div>
                                        <div class="col-6">
                                            <label for="valid_until" class="form-label">Berlaku Sampai</label>
                                            <input type="time" class="form-control" id="valid_until" name="valid_until" value="{{ $defaultEndTime }}">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg mt-3">
                                        <i class="bi bi-plus-circle me-2"></i>Generate QR Code
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Include QR Code Helper -->
<script src="{{ asset('js/qrcode-helper.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($qrCode)
        // Generate QR Code using local helper
        const qrCodeContainer = document.getElementById('qrcode');
        const qrUrl = '{{ $qrCode->getQrCodeUrl() }}';
        
        // Clear container
        qrCodeContainer.innerHTML = '';
        
        // Try to create image
        const img = document.createElement('img');
        img.src = window.QRCodeHelper.generateQRCode(qrUrl, 200);
        img.alt = 'QR Code untuk Absensi';
        img.className = 'img-fluid border rounded';
        img.style.maxWidth = '200px';
        img.style.height = 'auto';
        
        // Add loading placeholder
        qrCodeContainer.innerHTML = '<div class="text-center"><i class="bi bi-hourglass-split"></i> Loading QR Code...</div>';
        
        img.onload = function() {
            qrCodeContainer.innerHTML = '';
            qrCodeContainer.appendChild(img);
        };
        
        // Fallback jika image tidak load
        img.onerror = function() {
            qrCodeContainer.innerHTML = window.QRCodeHelper.generateQRText(qrUrl);
        };
        
        // Auto refresh QR display setiap 30 detik untuk memastikan tetap sinkron
        setInterval(function() {
            if (img.complete && img.naturalHeight !== 0) {
                // Refresh timestamp untuk mencegah cache
                img.src = window.QRCodeHelper.generateQRCode(qrUrl, 200) + '&t=' + Date.now();
            }
        }, 30000);
    @endif
});
</script>
@endsection
