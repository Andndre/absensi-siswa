@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-people-fill me-2"></i>Manajemen Siswa
                </h1>
                <div>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-funnel me-2"></i>Filter Data
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.students.index') }}">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="class_id" class="form-label">Kelas</label>
                                <select class="form-select" id="class_id" name="class_id">
                                    <option value="">Semua Kelas</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" 
                                                {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label for="search" class="form-label">Cari Siswa</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Nama atau NIS..." value="{{ request('search') }}">
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label for="per_page" class="form-label">Per Halaman</label>
                                @php
                                    $defaultPerPage = \App\Models\Setting::get('system.records_per_page', 10);
                                @endphp
                                <select class="form-select" id="per_page" name="per_page">
                                    <option value="10" {{ request('per_page', $defaultPerPage) == '10' ? 'selected' : '' }}>10</option>
                                    <option value="15" {{ request('per_page', $defaultPerPage) == '15' ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page', $defaultPerPage) == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page', $defaultPerPage) == '50' ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page', $defaultPerPage) == '100' ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block w-100">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-table me-2"></i>Data Siswa
                        <span class="badge bg-light text-dark ms-2">{{ $students->total() }} siswa</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Nama Siswa</th>
                                        <th width="15%">NIS</th>
                                        <th width="15%">Kelas</th>
                                        <th width="20%">No. WhatsApp Ortu</th>
                                        <th width="25%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                    <tr>
                                        <td>{{ $students->firstItem() + $index }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $student->name }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $student->nis }}</span>
                                        </td>
                                        <td>
                                            @if($student->schoolClass)
                                                <span class="badge bg-info">{{ $student->schoolClass->name }}</span>
                                            @else
                                                <span class="badge bg-warning">Belum ada kelas</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $student->parent_whatsapp_number }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        title="QR Code" 
                                                        onclick="showQrModal('{{ $student->id }}', '{{ $student->name }}', '{{ $student->nis }}', '{{ $student->qr_code }}')">
                                                    <i class="bi bi-qr-code"></i>
                                                </button>
                                                <a href="{{ route('admin.students.show', $student) }}" 
                                                   class="btn btn-sm btn-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.students.edit', $student) }}" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.students.destroy', $student) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Yakin ingin menghapus siswa ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="row mt-3">
                            <div class="col-lg-8 col-md-12 mb-2">
                                <small class="text-muted">
                                    Menampilkan {{ $students->firstItem() }} sampai {{ $students->lastItem() }} 
                                    dari {{ $students->total() }} data
                                    ({{ request('per_page', $defaultPerPage) }} per halaman)
                                </small>
                            </div>
                            <div class="col-lg-4 col-md-12">
                                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-end">
                                    <div class="mb-2 mb-lg-0 me-lg-3">
                                        <select class="form-select form-select-sm" onchange="changePerPage(this.value)" style="min-width: 140px;">
                                            <option value="10" {{ request('per_page', $defaultPerPage) == '10' ? 'selected' : '' }}>10 per halaman</option>
                                            <option value="15" {{ request('per_page', $defaultPerPage) == '15' ? 'selected' : '' }}>15 per halaman</option>
                                            <option value="25" {{ request('per_page', $defaultPerPage) == '25' ? 'selected' : '' }}>25 per halaman</option>
                                            <option value="50" {{ request('per_page', $defaultPerPage) == '50' ? 'selected' : '' }}>50 per halaman</option>
                                            <option value="100" {{ request('per_page', $defaultPerPage) == '100' ? 'selected' : '' }}>100 per halaman</option>
                                        </select>
                                    </div>
                                    <div>
                                        {{ $students->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-person-plus display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Belum ada data siswa</h5>
                            <p class="text-muted">Klik tombol "Tambah Siswa" untuk menambah siswa baru</p>
                            <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Siswa Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="qrModalLabel">
                    <i class="bi bi-qr-code me-2"></i>QR Code Siswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <h6 class="text-primary" id="studentName"></h6>
                    <small class="text-muted">NIS: <span id="studentNis"></span></small>
                </div>
                
                <div class="qr-container mb-4" id="qrContainer">
                    <!-- QR Code akan dimuat di sini -->
                </div>
                
                <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%);">
                    <small>
                        <i class="bi bi-info-circle me-1"></i>
                        QR Code ini digunakan untuk absensi siswa. Admin/Guru dapat memindai QR ini untuk mencatat kehadiran.
                    </small>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-success" onclick="downloadQr()">
                    <i class="bi bi-download me-1"></i>Download
                </button>
                <button type="button" class="btn btn-primary" onclick="printQr()">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Responsive pagination styles */
@media (max-width: 768px) {
    .pagination {
        justify-content: center;
        margin-top: 1rem;
    }
    
    .table-responsive {
        border: none;
    }
    
    .form-select-sm {
        font-size: 0.875rem;
    }
}

/* Custom pagination styles */
.pagination .page-link {
    border-radius: 0.375rem;
    margin: 0 2px;
    border: 1px solid #dee2e6;
    color: #6c757d;
}

.pagination .page-item.active .page-link {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    color: #0d6efd;
}

/* QR Modal Styles */
.qr-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 30px;
    border-radius: 15px;
    border: 2px dashed #dee2e6;
    margin: 20px auto;
    max-width: 300px;
}

.qr-container svg {
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.modal-content {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.modal-header {
    border-bottom: none;
    padding: 20px 30px;
}

.modal-body {
    padding: 30px;
}

.modal-footer {
    border-top: 1px solid #f1f3f4;
    padding: 20px 30px;
    background-color: #f8f9fa;
}

/* Action buttons styling */
.btn-group .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    min-width: 32px;
}

@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
    }
    
    .btn-group .btn-sm {
        flex: 1;
        min-width: 30px;
        margin-bottom: 2px;
    }
}

/* Print styles */
@media print {
    body * {
        visibility: hidden;
    }
    
    .print-area,
    .print-area * {
        visibility: visible;
    }
    
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        text-align: center;
        padding: 20px;
    }
    
    .print-area h1 {
        font-size: 24px;
        margin-bottom: 10px;
        color: #000 !important;
    }
    
    .print-area .student-info {
        font-size: 16px;
        margin-bottom: 20px;
        color: #000 !important;
    }
    
    .print-area svg {
        max-width: 200px;
        height: auto;
    }
}
</style>

<script>
let currentStudentData = {};

function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset ke halaman pertama saat mengubah per_page
    window.location.href = url.toString();
}

// Show QR Modal
function showQrModal(studentId, studentName, studentNis, qrCode) {
    currentStudentData = {
        id: studentId,
        name: studentName,
        nis: studentNis,
        qrCode: qrCode
    };
    
    // Update modal content
    document.getElementById('studentName').textContent = studentName;
    document.getElementById('studentNis').textContent = studentNis;
    
    // Generate QR Code
    generateQrCode(qrCode);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('qrModal'));
    modal.show();
}

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
    const qrSize = 200;
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
                    <small>Silakan coba lagi</small>
                </div>
            `;
        });
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

// Auto submit form ketika filter berubah (opsional - untuk UX yang lebih baik)
document.addEventListener('DOMContentLoaded', function() {
    const autoSubmitSelects = ['class_id', 'per_page'];
    
    autoSubmitSelects.forEach(function(selectId) {
        const element = document.getElementById(selectId);
        if (element) {
            element.addEventListener('change', function() {
                if (selectId === 'per_page') {
                    changePerPage(this.value);
                } else {
                    this.form.submit();
                }
            });
        }
    });
});
</script>
@endsection
