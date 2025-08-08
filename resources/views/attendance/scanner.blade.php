@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1 text-primary fw-bold">
                        <i class="bi bi-qr-code-scan me-2"></i>QR Scanner Absensi
                    </h2>
                    <p class="text-muted mb-0">Scan QR code siswa untuk mencatat absensi hari ini</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list-ul me-2"></i>Rekap Absensi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Hari Ini -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-present">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $todayStats['hadir'] }}</div>
                    <div class="stat-label">Hadir</div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar bg-success" style="width: {{ $todayStats['hadir'] > 0 ? min(($todayStats['hadir'] / max(array_sum($todayStats), 1)) * 100, 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-late">
                <div class="stat-icon">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $todayStats['terlambat'] }}</div>
                    <div class="stat-label">Terlambat</div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar bg-warning" style="width: {{ $todayStats['terlambat'] > 0 ? min(($todayStats['terlambat'] / max(array_sum($todayStats), 1)) * 100, 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-excused">
                <div class="stat-icon">
                    <i class="bi bi-file-medical"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $todayStats['izin'] + $todayStats['sakit'] }}</div>
                    <div class="stat-label">Izin/Sakit</div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar bg-info" style="width: {{ ($todayStats['izin'] + $todayStats['sakit']) > 0 ? min((($todayStats['izin'] + $todayStats['sakit']) / max(array_sum($todayStats), 1)) * 100, 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card stat-absent">
                <div class="stat-icon">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $todayStats['alpha'] }}</div>
                    <div class="stat-label">Alpha</div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar bg-danger" style="width: {{ $todayStats['alpha'] > 0 ? min(($todayStats['alpha'] / max(array_sum($todayStats), 1)) * 100, 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Scanner Form -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-qr-code-scan me-2"></i>Scanner QR Code Siswa
                    </h5>
                </div>
                <div class="card-body">
                    <form id="scannerForm" onsubmit="return false;">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="qr_code" class="form-label">QR Code Siswa</label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="qr_code" 
                                       name="qr_code" 
                                       placeholder="Fokuskan kursor di sini dan scan QR code siswa..."
                                       autocomplete="off"
                                       autofocus>
                                <div class="form-text">
                                    Posisikan kursor pada input ini, lalu gunakan QR scanner untuk scan QR code siswa
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status Absensi</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="hadir" selected>Hadir</option>
                                    <option value="terlambat">Terlambat</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="alpha">Alpha</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="notes" class="form-label">Catatan (Opsional)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="notes" 
                                       name="notes" 
                                       placeholder="Catatan tambahan...">
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="button" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Proses Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-4">
            <!-- Hasil Scan -->
            <div class="card mb-4" id="resultCard" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0">Hasil Scan</h6>
                </div>
                <div class="card-body" id="resultContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>

            <!-- Panduan Penggunaan -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Panduan Penggunaan
                    </h6>
                </div>
                <div class="card-body">
                    <ol class="mb-0 small">
                        <li>Pastikan kursor berada di input "QR Code Siswa"</li>
                        <li>Pilih status absensi yang sesuai</li>
                        <li>Arahkan QR scanner ke QR code siswa</li>
                        <li>QR code akan otomatis terisi dan diproses</li>
                        <li>Hasil scan akan ditampilkan di panel hasil</li>
                    </ol>
                    
                    <hr>
                    
                    <h6 class="small text-muted mb-2">Shortcut Keyboard:</h6>
                    <ul class="mb-0 small text-muted">
                        <li><kbd>Tab</kbd> - Pindah ke field berikutnya</li>
                        <li><kbd>Enter</kbd> - Proses absensi</li>
                        <li><kbd>Esc</kbd> - Reset form</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audio untuk notifikasi -->
<audio id="successSound" preload="auto">
    <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmEaBS2W3+/DciADKIfU8+ePNAYhd8rx4Ik0BSqC1vPfdSoCJH7M8+iQOwsVYqvm0KNRFAdIquPxtWAaBjWU2O3HdBwDKInX9OOKOQoafsvy4Io4BySF1vPgdysC" type="audio/wav">
</audio>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scannerForm');
    const qrCodeInput = document.getElementById('qr_code');
    const statusSelect = document.getElementById('status');
    const notesInput = document.getElementById('notes');
    const submitBtn = document.getElementById('submitBtn');
    const resultCard = document.getElementById('resultCard');
    const resultContent = document.getElementById('resultContent');
    const successSound = document.getElementById('successSound');
    
    // Debug: Check if all elements exist
    console.log('Form elements check:', {
        form: !!form,
        qrCodeInput: !!qrCodeInput,
        statusSelect: !!statusSelect,
        notesInput: !!notesInput,
        submitBtn: !!submitBtn,
        resultCard: !!resultCard,
        resultContent: !!resultContent
    });
    
    if (!form || !qrCodeInput || !statusSelect || !submitBtn) {
        console.error('Some required form elements are missing!');
        showAlert('Form elements tidak ditemukan. Silakan refresh halaman.', 'danger');
        return;
    }
    
    // Auto-focus on QR input
    qrCodeInput.focus();
    
    // Auto-submit when QR code is entered (assuming QR scanner adds newline)
    qrCodeInput.addEventListener('input', function(e) {
        const value = e.target.value.trim();
        if (value.length > 0 && (value.includes('\n') || value.includes('\r') || value.length > 20)) {
            // Clean the value
            e.target.value = value.replace(/[\n\r]/g, '');
            
            // Auto-submit after short delay
            setTimeout(() => {
                processQrCode();
            }, 100);
        }
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            resetForm();
        }
        if (e.key === 'Enter' && document.activeElement === qrCodeInput) {
            e.preventDefault();
            processQrCode();
        }
    });
    
    // Button click event
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        processQrCode();
    });
    
    // Process QR Code function
    function processQrCode() {
        const qrCode = qrCodeInput.value.trim();
        if (!qrCode) {
            showAlert('Harap scan QR code siswa terlebih dahulu', 'warning');
            qrCodeInput.focus();
            return false;
        }
        
        console.log('Processing QR code with data:', {
            qr_code: qrCode,
            status: statusSelect.value,
            notes: notesInput.value,
            url: '{{ route("admin.scan-student-qr") }}'
        });
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        
        // Send AJAX request using XMLHttpRequest for better control
        const xhr = new XMLHttpRequest();
        const requestUrl = '{{ route("admin.scan-student-qr") }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        
        console.log('Making XMLHttpRequest to:', requestUrl);
        console.log('CSRF Token:', csrfToken);
        
        xhr.open('POST', requestUrl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log('XHR Response:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText
                });
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Proses Absensi';
                
                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        console.log('Success response data:', data);
                        
                        if (data.success) {
                            showResult(data, 'success');
                            successSound.play().catch(() => {});
                            resetForm();
                            updateStats();
                        } else {
                            showResult(data, 'error');
                            qrCodeInput.select();
                        }
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        showAlert('Response tidak valid dari server', 'danger');
                    }
                } else {
                    console.error('HTTP Error:', xhr.status, xhr.statusText);
                    showAlert(`Terjadi kesalahan HTTP ${xhr.status}: ${xhr.statusText}`, 'danger');
                }
                
                qrCodeInput.focus();
            }
        };
        
        xhr.onerror = function() {
            console.error('Network error occurred');
            showAlert('Terjadi kesalahan jaringan', 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Proses Absensi';
            qrCodeInput.focus();
        };
        
        // Prepare form data
        const params = new URLSearchParams();
        params.append('qr_code', qrCode);
        params.append('status', statusSelect.value);
        params.append('notes', notesInput.value);
        params.append('_token', csrfToken);
        
        console.log('Sending POST data:', params.toString());
        xhr.send(params.toString());
        
        return false;
    }

    function showResult(data, type) {
        resultCard.style.display = 'block';
        
        if (type === 'success') {
            resultContent.innerHTML = `
                <div class="alert alert-success mb-3">
                    <i class="bi bi-check-circle me-2"></i>${data.message}
                </div>
                <div class="student-info">
                    <h6 class="mb-2">${data.student.name}</h6>
                    <p class="mb-1 text-muted small">NIS: ${data.student.nis}</p>
                    <p class="mb-1 text-muted small">Kelas: ${data.student.class}</p>
                    <p class="mb-1 text-muted small">Status: <span class="badge bg-success">${data.student.status}</span></p>
                    <p class="mb-0 text-muted small">Waktu: ${data.student.attendance_time}</p>
                </div>
            `;
        } else {
            resultContent.innerHTML = `
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-exclamation-circle me-2"></i>${data.message}
                </div>
                ${data.student ? `
                <div class="student-info">
                    <h6 class="mb-2">${data.student.name}</h6>
                    <p class="mb-1 text-muted small">NIS: ${data.student.nis}</p>
                    <p class="mb-1 text-muted small">Kelas: ${data.student.class}</p>
                    <p class="mb-1 text-muted small">Status Sebelumnya: <span class="badge bg-warning">${data.student.existing_status}</span></p>
                    <p class="mb-0 text-muted small">Waktu Absen: ${data.student.attendance_time}</p>
                </div>
                ` : ''}
            `;
        }
        
        // Auto-hide after 5 seconds for success
        if (type === 'success') {
            setTimeout(() => {
                resultCard.style.display = 'none';
            }, 5000);
        }
    }
    
    function showAlert(message, type) {
        // Create temporary alert
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        form.insertBefore(alert, form.firstChild);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
    
    function resetForm() {
        qrCodeInput.value = '';
        notesInput.value = '';
        statusSelect.value = 'hadir';
        qrCodeInput.focus();
    }
    
    function updateStats() {
        // Reload page to update statistics (simple approach)
        // In production, you might want to use AJAX to update just the stats
        setTimeout(() => {
            location.reload();
        }, 2000);
    }
});
</script>

<style>
/* Modern Statistics Cards */
.stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: none;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.stat-card .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 16px;
    position: relative;
    z-index: 2;
}

.stat-card .stat-content {
    position: relative;
    z-index: 2;
}

.stat-card .stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 4px;
}

.stat-card .stat-label {
    font-size: 0.875rem;
    font-weight: 500;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-card .stat-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: rgba(0, 0, 0, 0.1);
}

.stat-card .progress-bar {
    height: 100%;
    border-radius: 0;
    transition: width 0.6s ease;
}

/* Present Card */
.stat-present {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.stat-present .stat-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.stat-present::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* Late Card */
.stat-late {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.stat-late .stat-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.stat-late::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* Excused Card */
.stat-excused {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.stat-excused .stat-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.stat-excused::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* Absent Card */
.stat-absent {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.stat-absent .stat-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.stat-absent::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* Scanner Form Improvements */
.card {
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

#qr_code {
    font-family: 'Courier New', monospace;
    font-size: 1.1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

#qr_code:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-select, .form-control {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-select:focus, .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border: none;
    border-radius: 8px;
    padding: 12px 24px;
    font-weight: 600;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
}

.student-info h6 {
    color: var(--bs-primary);
    font-weight: 600;
}

kbd {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 0.75rem;
    color: #374151;
    font-weight: 600;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.alert {
    border-radius: 8px;
    border: none;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stat-card {
        padding: 20px;
        margin-bottom: 16px;
    }
    
    .stat-card .stat-number {
        font-size: 2rem;
    }
    
    .stat-card .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
        margin-bottom: 12px;
    }
}
</style>
@endsection
