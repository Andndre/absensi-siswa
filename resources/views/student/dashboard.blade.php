@extends('layouts.student')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="container-fluid">
    <!-- Header Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-header">
                <h2 class="mb-1">Selamat Datang, {{ auth('student')->user()->name }}!</h2>
                <p>{{ date('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px;">
                <div class="card-body text-center py-4">
                    <h5 class="card-title mb-3 text-primary">
                        <i class="fas fa-user-circle me-2"></i>
                        Kelola Profil & QR Code Anda
                    </h5>
                    
                    <div class="row justify-content-center">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('student.qr-code.show') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-qrcode me-2"></i>
                                Lihat QR Code Saya
                            </a>
                            <small class="text-muted d-block mt-2">
                                Tampilkan QR code untuk di-scan oleh admin/guru
                            </small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('student.profile.show') }}" class="btn btn-outline-primary btn-lg w-100">
                                <i class="fas fa-user-edit me-2"></i>
                                Edit Profil
                            </a>
                            <small class="text-muted d-block mt-2">
                                Kelola informasi profil dan data pribadi
                            </small>
                        </div>
                    </div>
                    
                    @if($todayAttendance)
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Absensi Hari Ini:</strong> {{ ucfirst($todayAttendance->status) }} 
                            pada {{ $todayAttendance->attendance_time->format('H:i') }}
                            @if($todayAttendance->scanned_by)
                                <br><small>Di-scan oleh: {{ $todayAttendance->scannedBy->name }}</small>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Belum Absen Hari Ini</strong><br>
                            <small>Silakan tunjukkan QR code Anda kepada admin/guru untuk melakukan absensi</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card stat-present">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['hadir'] }}</h3>
                    <p>Hadir</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card stat-late">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['terlambat'] }}</h3>
                    <p>Terlambat</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card stat-excused">
                <div class="stat-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['izin'] }}</h3>
                    <p>Izin</p>
                </div>
            </div>
        </div>
				<div class="col-6 col-md-3 mb-3">
						<div class="stat-card stat-sick">
					<div class="stat-icon">
							<i class="fas fa-procedures"></i>
					</div>
					<div class="stat-info">
							<h3>{{ $stats['sakit'] }}</h3>
							<p>Sakit</p>
					</div>
						</div>
				</div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card stat-absent">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stats['alpha'] }}</h3>
                    <p>Alpha</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Attendance Status Section -->
        <div class="col-12 col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Status Absensi Hari Ini</h5>
                </div>
                <div class="card-body">
                    @if($todayAttendance)
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Absensi Berhasil!</strong><br>
                            Status: <strong>{{ ucfirst($todayAttendance->status) }}</strong><br>
                            Waktu: {{ $todayAttendance->attendance_time->format('H:i:s') }}<br>
                            @if($todayAttendance->scanned_by)
                                Di-scan oleh: {{ $todayAttendance->scannedBy->name }}<br>
                            @endif
                            @if($todayAttendance->scan_method)
                                Metode: {{ $todayAttendance->scan_method === 'student_qr' ? 'Scan QR Code Siswa' : $todayAttendance->scan_method }}
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Belum Melakukan Absensi</strong><br>
                            Silakan tunjukkan QR code Anda kepada admin atau guru untuk melakukan absensi.<br>
                            <small class="text-muted mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Pastikan QR code Anda dapat terlihat dengan jelas saat di-scan.
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profil Siswa</h5>
                </div>
                <div class="card-body">
                    <div class="profile-info">
                        <div class="profile-item">
                            <label>Nama:</label>
                            <span>{{ auth('student')->user()->name }}</span>
                        </div>
                        <div class="profile-item">
                            <label>NIS:</label>
                            <span>{{ auth('student')->user()->nis }}</span>
                        </div>
                        <div class="profile-item">
                            <label>Kelas:</label>
                            <span>{{ auth('student')->user()->schoolClass->name }}</span>
                        </div>
                        <div class="profile-item">
                            <label>Email:</label>
                            <span>{{ auth('student')->user()->email }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('student.change-password') }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Absensi Terakhir</h5>
                </div>
                <div class="card-body">
                    @if($recentAttendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Waktu</th>
                                        <th class="d-none d-sm-table-cell">QR Code</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->attendance_time->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $attendance->status === 'hadir' ? 'success' : 
                                                ($attendance->status === 'terlambat' ? 'warning' : 
                                                ($attendance->status === 'izin' ? 'info' : 
                                                ($attendance->status === 'sakit' ? 'secondary' : 'danger'))) }}">
                                                {{ ucfirst($attendance->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $attendance->attendance_time->format('H:i') }}</td>
                                        <td class="d-none d-sm-table-cell">
                                            @if($attendance->dailyQrCode)
                                                <small class="text-muted">{{ substr($attendance->dailyQrCode->qr_code, 0, 8) }}...</small>
                                            @else
                                                <small class="text-muted">Manual</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-calendar-times fa-2x mb-3"></i>
                            <p>Belum ada riwayat absensi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrScanModal" tabindex="-1" aria-labelledby="qrScanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrScanModalLabel">Scan QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qr-reader" style="width: 100%;"></div>
                <div id="qr-result" style="display: none;">
                    <div class="alert alert-success">
                        <h5>QR Code Terdeteksi!</h5>
                        <p id="qr-result-text"></p>
                        <button type="button" class="btn btn-primary" onclick="submitAttendance()">
                            Konfirmasi Absensi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let scannedQrCode = null;

function startQrScan() {
    $('#qrScanModal').modal('show');
    
    html5QrCode = new Html5Qrcode("qr-reader");
    
    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        },
        (decodedText, decodedResult) => {
            console.log(`QR Code detected: ${decodedText}`);
            scannedQrCode = decodedText;
            
            // Stop scanning
            html5QrCode.stop().then(() => {
                // Show result
                document.getElementById('qr-result-text').textContent = 'QR Code: ' + decodedText;
                document.getElementById('qr-reader').style.display = 'none';
                document.getElementById('qr-result').style.display = 'block';
            });
        },
        (errorMessage) => {
            // Handle scan error
            console.log(`QR Code scan error: ${errorMessage}`);
        }
    ).catch(err => {
        console.log(`Unable to start scanning: ${err}`);
        alert('Tidak dapat mengakses kamera. Pastikan browser memiliki izin kamera.');
    });
}

function submitAttendance() {
    if (!scannedQrCode) {
        alert('QR Code tidak terdeteksi');
        return;
    }
    
    // Show loading
    document.getElementById('qr-result').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memproses absensi...</p>
        </div>
    `;
    
    // Submit to server
    fetch('{{ route("student.scan-qr") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            qr_code: scannedQrCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#qrScanModal').modal('hide');
            alert('Absensi berhasil dicatat dengan status: ' + data.status);
            location.reload();
        } else {
            let errorMessage = 'Error: ' + data.message;
            
            // Add debug info if available
            if (data.debug) {
                console.log('Debug Info:', data.debug);
                errorMessage += '\n\nDebug Info:\n' + JSON.stringify(data.debug, null, 2);
            }
            
            alert(errorMessage);
            $('#qrScanModal').modal('hide');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses absensi');
        $('#qrScanModal').modal('hide');
    });
}

// Clean up on modal close
$('#qrScanModal').on('hidden.bs.modal', function () {
    if (html5QrCode) {
        html5QrCode.stop().catch(err => console.log(err));
        html5QrCode = null;
    }
    scannedQrCode = null;
    document.getElementById('qr-reader').style.display = 'block';
    document.getElementById('qr-result').style.display = 'none';
});
</script>
@endsection
