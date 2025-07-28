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
                                    <th width="40%">QR Code Token:</th>
                                    <td>
                                        <code>{{ $student->qr_code_token }}</code>
                                        <form action="{{ route('admin.students.generate-qr', $student) }}" method="POST" class="d-inline ms-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary" 
                                                    onclick="return confirm('Generate ulang QR Code?')"
                                                    title="Generate Ulang QR Code">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </form>
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
                        <div class="qr-code-placeholder bg-white border rounded d-flex align-items-center justify-content-center" 
                             style="height: 200px; width: 200px; margin: 0 auto;">
                            <div class="text-center">
                                <i class="bi bi-qr-code display-1 text-muted"></i>
                                <p class="small text-muted mt-2">QR Code Preview<br>
                                <small>{{ Str::limit($student->qr_code_token, 20) }}</small></p>
                            </div>
                        </div>
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        QR Code ini digunakan untuk absensi siswa
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
@endsection
