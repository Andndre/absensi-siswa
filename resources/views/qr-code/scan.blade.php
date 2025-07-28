<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Absensi - Sistem Absensi Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .scan-container {
            max-width: 400px;
            margin: 50px auto;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background: linear-gradient(135deg, #34568B, #2C4A7A);
        }
        .logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        .btn-scan {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 10px;
            padding: 12px 0;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-scan:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="scan-container">
            <div class="card">
                <div class="card-header text-white text-center py-4">
                    <div class="logo">
                        <i class="bi bi-qr-code-scan text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="mb-0">Absensi Siswa</h4>
                    <small>Scan QR Code untuk Absensi</small>
                </div>
                <div class="card-body p-4">
                    <!-- QR Code Info -->
                    <div class="text-center mb-4">
                        <div class="alert alert-info">
                            <i class="bi bi-calendar3 me-2"></i>
                            <strong>{{ $qrCode->date->format('d F Y') }}</strong>
                            <br>
                            <small>
                                Berlaku: {{ Carbon\Carbon::parse($qrCode->valid_from)->format('H:i') }} - 
                                {{ Carbon\Carbon::parse($qrCode->valid_until)->format('H:i') }}
                            </small>
                        </div>
                        
                        @if($qrCode->isValidNow())
                            <span class="badge bg-success fs-6">
                                <i class="bi bi-check-circle me-1"></i>QR Code Aktif
                            </span>
                        @else
                            <span class="badge bg-danger fs-6">
                                <i class="bi bi-x-circle me-1"></i>QR Code Tidak Aktif
                            </span>
                        @endif
                    </div>

                    @if($qrCode->isValidNow())
                        <!-- Scan Form -->
                        <form id="attendanceForm">
                            <div class="mb-3">
                                <label for="nis" class="form-label">
                                    <i class="bi bi-person-badge me-2"></i>Nomor Induk Siswa (NIS)
                                </label>
                                <input type="text" class="form-control form-control-lg" id="nis" name="nis" 
                                       placeholder="Masukkan NIS Anda" required autofocus>
                            </div>
                            
                            <button type="submit" class="btn btn-scan btn-lg w-100 text-white">
                                <i class="bi bi-check-circle me-2"></i>
                                <span id="btnText">Scan Absensi</span>
                                <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none"></span>
                            </button>
                        </form>
                        
                        <!-- Result Container -->
                        <div id="resultContainer" class="mt-3 d-none"></div>
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>QR Code Tidak Aktif</strong>
                            <br>
                            <small>Silakan hubungi admin atau tunggu waktu yang tepat</small>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Info -->
            <div class="text-center mt-3 text-white">
                <small>
                    <i class="bi bi-info-circle me-1"></i>
                    Pastikan NIS yang dimasukkan benar dan sesuai dengan data sekolah
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('attendanceForm');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const resultContainer = document.getElementById('resultContainer');
        const nisInput = document.getElementById('nis');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show loading
                btnText.textContent = 'Memproses...';
                btnSpinner.classList.remove('d-none');
                form.querySelector('button').disabled = true;
                
                // Prepare data
                const formData = new FormData();
                formData.append('nis', nisInput.value);
                formData.append('_token', '{{ csrf_token() }}');
                
                // Send request
                fetch('{{ route("attendance.scan.process", $qrCode->qr_token) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResult('success', data.message, data.data);
                        form.style.display = 'none';
                    } else {
                        showResult('error', data.message);
                        resetButton();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showResult('error', 'Terjadi kesalahan. Silakan coba lagi.');
                    resetButton();
                });
            });
        }
        
        function showResult(type, message, data = null) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'bi-check-circle' : 'bi-x-circle';
            
            let content = `
                <div class="alert ${alertClass}">
                    <i class="${icon} me-2"></i>
                    <strong>${message}</strong>
            `;
            
            if (data) {
                content += `
                    <hr>
                    <div class="row text-start">
                        <div class="col-6"><small><strong>Nama:</strong></small></div>
                        <div class="col-6"><small>${data.student}</small></div>
                        <div class="col-6"><small><strong>NIS:</strong></small></div>
                        <div class="col-6"><small>${data.nis}</small></div>
                        <div class="col-6"><small><strong>Kelas:</strong></small></div>
                        <div class="col-6"><small>${data.class}</small></div>
                        <div class="col-6"><small><strong>Waktu:</strong></small></div>
                        <div class="col-6"><small>${data.time}</small></div>
                        <div class="col-6"><small><strong>Status:</strong></small></div>
                        <div class="col-6"><small><span class="badge bg-info">${data.status}</span></small></div>
                    </div>
                `;
            }
            
            content += '</div>';
            
            resultContainer.innerHTML = content;
            resultContainer.classList.remove('d-none');
        }
        
        function resetButton() {
            btnText.textContent = 'Scan Absensi';
            btnSpinner.classList.add('d-none');
            form.querySelector('button').disabled = false;
            nisInput.value = '';
            nisInput.focus();
        }
    });
    </script>
</body>
</html>
