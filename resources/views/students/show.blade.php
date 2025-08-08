@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-person-circle me-2"></i>Detail Siswa
                </h1>
                <div>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                    <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Student Info -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-info-circle me-2"></i>Informasi Siswa
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Nama Lengkap:</th>
                                    <td><strong>{{ $student->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>NIS:</th>
                                    <td><span class="badge bg-secondary fs-6">{{ $student->nis }}</span></td>
                                </tr>
                                <tr>
                                    <th>QR Code:</th>
                                    <td>
                                        @if($student->qr_code)
                                            <code class="text-primary">{{ $student->qr_code }}</code>
                                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyQrCode(event)" title="Copy QR Code">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">Belum di-generate</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kelas:</th>
                                    <td>
                                        @if($student->schoolClass)
                                            <span class="badge bg-info fs-6">{{ $student->schoolClass->name }}</span>
                                        @else
                                            <span class="badge bg-warning fs-6">Belum ada kelas</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>No. WhatsApp Ortu:</th>
                                    <td>{{ $student->parent_whatsapp_number }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Status:</th>
                                    <td>
                                        @if($student->is_active)
                                            <span class="badge bg-success fs-6">Aktif</span>
                                        @else
                                            <span class="badge bg-danger fs-6">Tidak Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Terdaftar:</th>
                                    <td>{{ $student->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Update:</th>
                                    <td>{{ $student->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Visualization -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-qr-code me-2"></i>QR Code Siswa
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="border rounded p-3 mb-3" style="background: #f8f9fa;">
                        <div class="qr-container bg-white border rounded d-flex align-items-center justify-content-center" 
                             style="height: 200px; width: 200px; margin: 0 auto;" id="qrContainer">
                            <!-- QR Code akan dimuat di sini -->
                        </div>
                    </div>
                    <div class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        QR Code ini digunakan untuk absensi siswa
                    </div>
                    
                    <!-- Action buttons untuk QR -->
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-success btn-sm" onclick="downloadQr()">
                            <i class="bi bi-download me-1"></i>Download
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="printQr()">
                            <i class="bi bi-printer me-1"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Absensi Terakhir
                    </h6>
                </div>
                <div class="card-body">
                    @if($student->attendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($student->attendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->attendance_time->format('d/m/Y') }}</td>
                                        <td><strong>{{ $attendance->attendance_time->format('H:i:s') }}</strong></td>
                                        <td>
                                            @switch($attendance->status)
                                                @case('Hadir')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>{{ $attendance->status }}
                                                    </span>
                                                    @break
                                                @case('Izin')
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $attendance->status }}
                                                    </span>
                                                    @break
                                                @case('Sakit')
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-heart-pulse me-1"></i>{{ $attendance->status }}
                                                    </span>
                                                    @break
                                                @case('Alpha')
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i>{{ $attendance->status }}
                                                    </span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $attendance->created_at->diffForHumans() }}
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.attendance.index', ['search' => $student->name]) }}" 
                               class="btn btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Lihat Semua Riwayat
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clock-history display-4 text-muted"></i>
                            <h6 class="text-muted mt-3">Belum ada riwayat absensi</h6>
                            <p class="text-muted">Siswa ini belum pernah melakukan absensi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentStudentData = {
    id: '{{ $student->id }}',
    name: '{{ $student->name }}',
    nis: '{{ $student->nis }}',
    qrCode: '{{ $student->qr_code }}'
};

// Generate QR Code when page loads
document.addEventListener('DOMContentLoaded', function() {
    generateQrCode('{{ $student->qr_code }}');
});

// Generate QR Code using API
function generateQrCode(qrCode) {
    const container = document.getElementById('qrContainer');
    
    // Show loading
    container.innerHTML = `
        <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    // Generate QR using QR Server API
    const qrSize = 180;
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=${qrSize}x${qrSize}&data=${encodeURIComponent(qrCode)}&format=svg&margin=10`;
    
    // Create SVG element
    fetch(qrUrl)
        .then(response => response.text())
        .then(svgContent => {
            container.innerHTML = svgContent;
        })
        .catch(error => {
            console.error('Error generating QR code:', error);
            container.innerHTML = `
                <div class="text-danger">
                    <i class="bi bi-exclamation-triangle display-4"></i>
                    <p class="mt-2">Gagal memuat QR Code</p>
                    <small>Silakan refresh halaman</small>
                </div>
            `;
        });
}

// Copy QR Code to clipboard
function copyQrCode(event) {
    const qrCodeText = currentStudentData.qrCode;
    
    // Fallback function for older browsers
    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        
        // Avoid scrolling to bottom
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";
        textArea.style.opacity = "0";
        
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const successful = document.execCommand('copy');
            document.body.removeChild(textArea);
            return successful;
        } catch (err) {
            document.body.removeChild(textArea);
            return false;
        }
    }
    
    // Try modern clipboard API first
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(qrCodeText).then(function() {
            showCopySuccess(event.target);
        }).catch(function(err) {
            console.error('Clipboard API failed: ', err);
            // Fallback to execCommand
            if (fallbackCopyTextToClipboard(qrCodeText)) {
                showCopySuccess(event.target);
            } else {
                alert('Gagal menyalin QR Code. Silakan copy manual: ' + qrCodeText);
            }
        });
    } else {
        // Use fallback for older browsers or non-secure context
        if (fallbackCopyTextToClipboard(qrCodeText)) {
            showCopySuccess(event.target);
        } else {
            alert('Gagal menyalin QR Code. Silakan copy manual: ' + qrCodeText);
        }
    }
}

// Show copy success feedback
function showCopySuccess(button) {
    const btn = button.closest('button');
    const originalHtml = btn.innerHTML;
    const originalClasses = btn.className;
    
    // Show success state
    btn.innerHTML = '<i class="bi bi-check"></i> Tersalin!';
    btn.className = 'btn btn-sm btn-success ms-2';
    
    // Reset after 2 seconds
    setTimeout(function() {
        btn.innerHTML = originalHtml;
        btn.className = originalClasses;
    }, 2000);
}

// Download QR Code
function downloadQr() {
    const qrSvg = document.querySelector('#qrContainer svg');
    if (!qrSvg) {
        alert('QR Code belum dimuat!');
        return;
    }
    
    // Convert SVG to Canvas and download
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    
    // Set canvas size
    canvas.width = 400;
    canvas.height = 500;
    
    // Create data URL from SVG
    const svgData = new XMLSerializer().serializeToString(qrSvg);
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
        ctx.fillText(currentStudentData.name, canvas.width / 2, 70);
        ctx.font = '14px Arial';
        ctx.fillText(`NIS: ${currentStudentData.nis}`, canvas.width / 2, 90);
        
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
        link.download = `QR_${currentStudentData.name}_${currentStudentData.nis}.png`;
        link.href = canvas.toDataURL();
        link.click();
        
        URL.revokeObjectURL(svgUrl);
    };
    
    img.src = svgUrl;
}

// Print QR Code
function printQr() {
    const qrSvg = document.querySelector('#qrContainer svg');
    if (!qrSvg) {
        alert('QR Code belum dimuat!');
        return;
    }
    
    // Create print content
    const printContent = `
        <div class="print-area">
            <h1>QR Code Siswa</h1>
            <div class="student-info">
                <strong>${currentStudentData.name}</strong><br>
                NIS: ${currentStudentData.nis}
            </div>
            <div style="margin: 30px 0;">
                ${qrSvg.outerHTML}
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
            <title>Print QR Code - ${currentStudentData.name}</title>
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
