@extends('layouts.student')

@section('title', 'Dashboard Siswa')

@section('styles')
<style>
    /* Mobile-first responsive styles */
    .qr-code-container svg {
        max-width: 100%;
        height: auto;
    }
    
    .profile-item {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 0.5rem;
    }
    
    .profile-item:last-child {
        border-bottom: none;
    }
    
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card-header h6 {
        font-weight: 600;
    }
    
    /* Consistent spacing for all sections */
    .row {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }
    
    .row > [class*="col-"] {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    
    /* Better mobile spacing */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
        
        .h2-md {
            font-size: 1.5rem !important;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .btn-lg {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }
        
        /* Ensure consistent margins on mobile */
        .mb-3 {
            margin-bottom: 1rem !important;
        }
        
        .mb-md-4 {
            margin-bottom: 1rem !important;
        }
    }
    
    @media (min-width: 768px) {
        .mb-md-4 {
            margin-bottom: 1.5rem !important;
        }
    }
    
    /* QR Code responsive container */
    .qr-code-container {
        max-width: 100%;
        overflow: hidden;
    }
    
    /* Better button spacing for mobile */
    .gap-2 > * {
        margin-bottom: 0.5rem;
    }
    
    @media (min-width: 576px) {
        .gap-2 > * {
            margin-bottom: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header Info -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="welcome-header text-center text-md-start">
                <h2 class="mb-1 h3 h2-md">Selamat Datang, {{ auth('student')->user()->name }}!</h2>
                <p class="small">{{ date('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    <!-- QR Code Section -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div class="card-body py-3 py-md-4 px-3 px-md-4">
                    <h5 class="card-title mb-3 mb-md-4 text-primary text-center">
                        <i class="fas fa-qrcode me-2"></i>
                        QR Code Absensi Anda
                    </h5>
                    
                    <div class="row">
                        <!-- QR Code Section - Mobile First -->
                        <div class="col-12 col-md-6 text-center mb-4 mb-md-0">
                            <div class="qr-code-container p-3 bg-white rounded border shadow-sm mx-auto" style="display: inline-block; max-width: 220px;">
                                {!! QrCode::size(180)->generate(auth('student')->user()->qr_code) !!}
                            </div>
                            <div class="mt-3">
                                <p class="mb-2 small"><strong>Kode QR:</strong></p>
                                <code class="bg-light p-2 rounded small d-block text-break" style="word-break: break-all; font-size: 0.75rem;">{{ auth('student')->user()->qr_code }}</code>
                            </div>
                            <div class="mt-3 d-flex flex-column flex-sm-row gap-2 justify-content-center">
                                <button class="btn btn-outline-primary btn-sm" onclick="downloadQR()">
                                    <i class="fas fa-download me-2"></i>Download
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="printQR()">
                                    <i class="fas fa-print me-2"></i>Print
                                </button>
                            </div>
                        </div>
                        
                        <!-- Instructions Section -->
                        <div class="col-12 col-md-6">
                            <div class="alert alert-info text-start mt-3 mt-md-0">
                                <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Cara Menggunakan:</h6>
                                <ol class="mb-0 small lh-base">
                                    <li class="mb-2">Tunjukkan QR code ini kepada admin/guru</li>
                                    <li class="mb-2">Admin/guru akan melakukan scan menggunakan scanner</li>
                                    <li class="mb-2">Absensi Anda akan tercatat otomatis</li>
                                    <li>Orang tua akan menerima notifikasi WhatsApp</li>
                                </ol>
                            </div>
                            
                            @if($todayAttendance)
                                <div class="alert alert-success mt-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Sudah Absen Hari Ini:</strong><br>
                                    Status: {{ ucfirst($todayAttendance->status) }}<br>
                                    Waktu: {{ $todayAttendance->attendance_time->format('H:i') }}
                                    @if($todayAttendance->scanned_by)
                                        <br><small>Di-scan oleh: {{ $todayAttendance->scannedBy->name }}</small>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Belum Absen Hari Ini</strong><br>
                                    <small>Pastikan QR code dapat di-scan dengan jelas</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px;">
                <div class="card-body text-center py-3 py-md-4 px-3 px-md-4">
                    <h5 class="card-title mb-3 text-primary">
                        <i class="fas fa-user-circle me-2"></i>
                        Kelola Profil Anda
                    </h5>
                    
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-8 col-lg-6 mb-3">
                            <a href="{{ route('student.profile') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-user-edit me-2"></i>
                                Edit Profil
                            </a>
                            <small class="text-muted d-block mt-2">
                                Kelola informasi profil dan nomor WhatsApp orang tua
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
                            <small>Admin/guru akan melakukan scan untuk mencatat absensi Anda. Pastikan Anda hadir di kelas.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-3 mb-md-4">
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

    <div class="row mb-3 mb-md-4">
        <!-- Attendance Status Section -->
        <div class="col-12 col-lg-8 mb-3 mb-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Status Absensi Hari Ini</h6>
                </div>
                <div class="card-body p-3">
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
                            Admin atau guru akan melakukan scan untuk mencatat absensi Anda.<br>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle me-1"></i>
                                Pastikan Anda hadir di kelas saat waktu absensi.
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div class="col-12 col-lg-4 mb-3 mb-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Profil Siswa</h6>
                </div>
                <div class="card-body p-3">
                    <div class="profile-info">
                        <div class="profile-item mb-2">
                            <label class="small text-muted">Nama:</label>
                            <span class="d-block">{{ auth('student')->user()->name }}</span>
                        </div>
                        <div class="profile-item mb-2">
                            <label class="small text-muted">NIS:</label>
                            <span class="d-block">{{ auth('student')->user()->nis }}</span>
                        </div>
                        <div class="profile-item mb-3">
                            <label class="small text-muted">Kelas:</label>
                            <span class="d-block">{{ auth('student')->user()->schoolClass->name }}</span>
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
    <div class="row mb-3 mb-md-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Absensi Terakhir</h6>
                </div>
                <div class="card-body p-2 p-md-3">
                    @if($recentAttendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th class="small">Tanggal</th>
                                        <th class="small">Status</th>
                                        <th class="small">Waktu</th>
                                        <th class="d-none d-md-table-cell small">Di-scan oleh</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
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
                                        <td class="d-none d-md-table-cell">
                                            @if($attendance->scanned_by)
                                                <small class="text-muted">{{ $attendance->scannedBy->name }}</small>
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

@endsection
@section('scripts')
<script>
function downloadQR() {
    const svg = document.querySelector('.qr-code-container svg');
    if (!svg) {
        alert('QR Code belum dimuat!');
        return;
    }
    
    // Student data
    const studentName = '{{ auth("student")->user()->name }}';
    const studentNIS = '{{ auth("student")->user()->nis }}';
    
    // Convert SVG to Canvas and download
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    
    // Set canvas size
    canvas.width = 400;
    canvas.height = 500;
    
    // Create data URL from SVG
    const svgData = new XMLSerializer().serializeToString(svg);
    const svgBlob = new Blob([svgData], {type: 'image/svg+xml;charset=utf-8'});
    const svgUrl = URL.createObjectURL(svgBlob);
    
    img.onload = function() {
        // White background
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Add title
        ctx.fillStyle = 'black';
        ctx.font = 'bold 24px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('QR Code Siswa', canvas.width / 2, 40);
        
        // Add student info
        ctx.font = '18px Arial';
        ctx.fillText(studentName, canvas.width / 2, 70);
        ctx.font = '14px Arial';
        ctx.fillText(`NIS: ${studentNIS}`, canvas.width / 2, 90);
        
        // Draw QR code
        const qrSize = 200;
        const qrX = (canvas.width - qrSize) / 2;
        const qrY = 120;
        ctx.drawImage(img, qrX, qrY, qrSize, qrSize);
        
        // Add footer
        ctx.font = '12px Arial';
        ctx.fillText('Sistem Absensi Siswa', canvas.width / 2, canvas.height - 40);
        ctx.fillText(new Date().toLocaleDateString('id-ID'), canvas.width / 2, canvas.height - 20);
        
        // Download
        const link = document.createElement('a');
        link.download = `QR_${studentName}_${studentNIS}.png`;
        link.href = canvas.toDataURL();
        link.click();
        
        URL.revokeObjectURL(svgUrl);
    };
    
    img.src = svgUrl;
}

function printQR() {
    const svg = document.querySelector('.qr-code-container svg');
    if (!svg) {
        alert('QR Code belum dimuat!');
        return;
    }
    
    // Student data
    const studentName = '{{ auth("student")->user()->name }}';
    const studentNIS = '{{ auth("student")->user()->nis }}';
    
    // Create print content
    const printContent = `
        <div class="print-area">
            <h1>QR Code Siswa</h1>
            <div class="student-info">
                <strong>${studentName}</strong><br>
                NIS: ${studentNIS}
            </div>
            <div style="margin: 30px 0;">
                ${svg.outerHTML}
            </div>
            <div style="margin-top: 30px; font-size: 12px; color: #666;">
                <p>Sistem Absensi Siswa</p>
                <p>Dicetak pada: ${new Date().toLocaleDateString('id-ID')} ${new Date().toLocaleTimeString('id-ID')}</p>
                <p style="margin-top: 20px; font-size: 10px;">
                    QR Code ini digunakan untuk absensi siswa.<br>
                    Admin/Guru dapat memindai QR ini untuk mencatat kehadiran.
                </p>
            </div>
        </div>
    `;
    
    // Open new window for printing
    const printWindow = window.open('', '_blank', 'width=400,height=600');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print QR Code - ${studentName}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    text-align: center;
                }
                .print-area {
                    max-width: 400px;
                    margin: 0 auto;
                }
                h1 {
                    color: #333;
                    margin-bottom: 10px;
                }
                .student-info {
                    margin-bottom: 20px;
                    font-size: 16px;
                }
                svg {
                    max-width: 200px;
                    height: auto;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }
                @media print {
                    body {
                        margin: 0;
                        padding: 0;
                    }
                }
            </style>
        </head>
        <body>
            ${printContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    // Auto print after content loads
    setTimeout(() => {
        printWindow.print();
        setTimeout(() => printWindow.close(), 1000);
    }, 500);
}
</script>
@endsection
