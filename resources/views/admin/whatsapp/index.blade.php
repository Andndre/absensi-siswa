@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-whatsapp me-2"></i>Pengaturan WhatsApp
                </h1>
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

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Test Connection -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-wifi me-2"></i>Test Koneksi WhatsApp
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Test apakah sistem dapat mengirim pesan WhatsApp dengan API Fonnte.</p>
                    
                    <form action="{{ route('admin.whatsapp.test') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor WhatsApp Test</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   placeholder="contoh: 08123456789" required>
                            <div class="form-text">Masukkan nomor WhatsApp untuk test pengiriman</div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-send me-1"></i>Kirim Test Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Manual Notification -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-chat-dots me-2"></i>Kirim Pesan Manual
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Kirim pesan khusus ke orang tua siswa tertentu.</p>
                    
                    <form action="{{ route('admin.whatsapp.manual') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Pilih Siswa</label>
                            <select class="form-select" id="student_id" name="student_id" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach(\App\Models\Student::whereNotNull('parent_whatsapp_number')->orderBy('name')->get() as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->name }} - {{ $student->nis }}
                                        @if($student->schoolClass)
                                            ({{ $student->schoolClass->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Pesan</label>
                            <textarea class="form-control" id="message" name="message" rows="4" 
                                      placeholder="Tulis pesan untuk orang tua..." required maxlength="1000"></textarea>
                            <div class="form-text">Maksimal 1000 karakter</div>
                        </div>
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-send me-1"></i>Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Broadcast Message -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-megaphone me-2"></i>Kirim Pengumuman Broadcast
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Perhatian!</strong> Fitur ini akan mengirim pesan ke semua orang tua siswa. 
                        Gunakan dengan bijak untuk menghindari spam.
                    </div>
                    
                    <form action="{{ route('admin.whatsapp.broadcast') }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin mengirim broadcast ke semua orang tua? Ini tidak bisa dibatalkan.')">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="class_id" class="form-label">Filter Kelas (Opsional)</label>
                                <select class="form-select" id="class_id" name="class_id">
                                    <option value="">Semua Kelas</option>
                                    @foreach(\App\Models\SchoolClass::orderBy('name')->get() as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Kosongkan untuk mengirim ke semua kelas</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah Penerima</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-secondary" id="recipientCount">
                                        {{ \App\Models\Student::whereNotNull('parent_whatsapp_number')->count() }} orang tua
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="broadcast_message" class="form-label">Pesan Pengumuman</label>
                            <textarea class="form-control" id="broadcast_message" name="message" rows="6" 
                                      placeholder="Tulis pengumuman untuk semua orang tua..." required maxlength="1000"></textarea>
                            <div class="form-text">Maksimal 1000 karakter</div>
                        </div>
                        
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-megaphone me-1"></i>Kirim Broadcast
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration Info -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-gear me-2"></i>Informasi Konfigurasi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Nama Sekolah:</strong></td>
                                    <td>{{ \App\Models\Setting::get('school.name', 'SMK Negeri 1') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fonnte Token:</strong></td>
                                    <td>
                                        @if(\App\Models\Setting::get('whatsapp.fonnte_token'))
                                            <span class="badge bg-success">Dikonfigurasi</span>
                                        @else
                                            <span class="badge bg-danger">Belum dikonfigurasi</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Siswa:</strong></td>
                                    <td>{{ \App\Models\Student::count() }} siswa</td>
                                </tr>
                                <tr>
                                    <td><strong>Siswa dengan No. WA Ortu:</strong></td>
                                    <td>{{ \App\Models\Student::whereNotNull('parent_whatsapp_number')->count() }} siswa</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle me-2"></i>Cara Konfigurasi WhatsApp:</h6>
                                <ol class="mb-0">
                                    <li>Daftar akun di <a href="https://fonnte.com" target="_blank">fonnte.com</a></li>
                                    <li>Login ke dashboard Fonnte</li>
                                    <li>Klik menu "Device" dan hubungkan WhatsApp dengan scan QR</li>
                                    <li>Pada daftar device klik tombol "Token" untuk menyalin token Anda</li>
                                    <li>Buka menu <a href="{{ route('admin.settings.index') }}">Pengaturan</a></li>
                                    <li>Pada tab WhatsApp, masukkan token di field "Fonnte API Token"</li>
                                    <li>Klik "Simpan Pengaturan"</li>
                                    <li>Test koneksi menggunakan form di atas</li>
                                </ol>
                                <small class="text-muted mt-2 d-block">
                                    <i class="bi bi-info-circle"></i> Pastikan nomor WhatsApp yang digunakan sudah terdaftar di Fonnte dan dalam keadaan aktif.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update recipient count based on class filter
document.getElementById('class_id').addEventListener('change', function() {
    // You can implement AJAX call here to get dynamic count
    // For now, it shows total count
});
</script>
@endsection
