@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-file-earmark-excel me-2"></i>Laporan Detail Absensi
                </h1>
                <div>
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                    <button onclick="window.print()" class="btn btn-success">
                        <i class="bi bi-printer me-1"></i>Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-funnel me-2"></i>Periode Laporan
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.attendance.report') }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="class_id" class="form-label">Kelas</label>
                                <select class="form-select" id="class_id" name="class_id">
                                    <option value="">Semua Kelas</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" 
                                                {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block w-100">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card card-stats h-100 shadow-sm border-left-primary">
                <div class="card-body text-center">
                    <div class="text-primary">
                        <i class="bi bi-calendar-range display-4"></i>
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDays }}</div>
                    <div class="text-xs font-weight-bold text-primary text-uppercase">Total Hari</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card card-stats h-100 shadow-sm border-left-success">
                <div class="card-body text-center">
                    <div class="text-success">
                        <i class="bi bi-person-check-fill display-4"></i>
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallStats['hadir'] }}</div>
                    <div class="text-xs font-weight-bold text-success text-uppercase">Total Hadir</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card card-stats h-100 shadow-sm border-left-warning">
                <div class="card-body text-center">
                    <div class="text-warning">
                        <i class="bi bi-people-fill display-4"></i>
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallStats['unique_students'] }}</div>
                    <div class="text-xs font-weight-bold text-warning text-uppercase">Siswa Aktif</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card card-stats h-100 shadow-sm border-left-info">
                <div class="card-body text-center">
                    <div class="text-info">
                        <i class="bi bi-clipboard-data display-4"></i>
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallStats['total_records'] }}</div>
                    <div class="text-xs font-weight-bold text-info text-uppercase">Total Record</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Report Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-table me-2"></i>Rekap Kehadiran Per Siswa
                        <span class="badge bg-light text-dark ms-2">
                            Periode: {{ Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                        </span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle" width="5%">#</th>
                                        <th rowspan="2" class="text-center align-middle" width="15%">Nama Siswa</th>
                                        <th rowspan="2" class="text-center align-middle" width="10%">NIS</th>
                                        <th rowspan="2" class="text-center align-middle" width="10%">Kelas</th>
                                        <th colspan="6" class="text-center">Jumlah Kehadiran</th>
                                        <th rowspan="2" class="text-center align-middle" width="10%">Persentase Hadir</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" width="7%">Hadir</th>
                                        <th class="text-center" width="7%">Terlambat</th>
                                        <th class="text-center" width="7%">Izin</th>
                                        <th class="text-center" width="7%">Sakit</th>
                                        <th class="text-center" width="7%">Alpha</th>
                                        <th class="text-center" width="7%">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                    @php
                                        $attendanceCount = [
                                            'hadir' => $student->attendances->where('status', 'hadir')->count(),
                                            'terlambat' => $student->attendances->where('status', 'terlambat')->count(),
                                            'izin' => $student->attendances->where('status', 'izin')->count(),
                                            'sakit' => $student->attendances->where('status', 'sakit')->count(),
                                            'alpha' => $student->attendances->where('status', 'alpha')->count(),
                                        ];
                                        $totalAttendance = array_sum($attendanceCount);
                                        // Total hadir = hadir + terlambat (keduanya dianggap hadir)
                                        $totalHadir = $attendanceCount['hadir'] + $attendanceCount['terlambat'];
                                        $presentPercentage = $totalDays > 0 ? round(($totalHadir / $totalDays) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $student->name }}</div>
                                        </td>
                                        <td class="text-center">{{ $student->nis }}</td>
                                        <td class="text-center">
                                            @if($student->schoolClass)
                                                <span class="badge bg-secondary">{{ $student->schoolClass->name }}</span>
                                            @else
                                                <span class="badge bg-warning">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success fs-6">{{ $attendanceCount['hadir'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning fs-6">{{ $attendanceCount['terlambat'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info fs-6">{{ $attendanceCount['izin'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary fs-6">{{ $attendanceCount['sakit'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger fs-6">{{ $attendanceCount['alpha'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-dark fs-6">{{ $totalAttendance }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($presentPercentage >= 80)
                                                <span class="badge bg-success fs-6">{{ $presentPercentage }}%</span>
                                            @elseif($presentPercentage >= 60)
                                                <span class="badge bg-warning fs-6">{{ $presentPercentage }}%</span>
                                            @else
                                                <span class="badge bg-danger fs-6">{{ $presentPercentage }}%</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <th colspan="4" class="text-center">TOTAL</th>
                                        <th class="text-center">{{ $overallStats['hadir'] }}</th>
                                        <th class="text-center">{{ $overallStats['terlambat'] ?? 0 }}</th>
                                        <th class="text-center">{{ $overallStats['izin'] }}</th>
                                        <th class="text-center">{{ $overallStats['sakit'] }}</th>
                                        <th class="text-center">{{ $overallStats['alpha'] }}</th>
                                        <th class="text-center">{{ $overallStats['total_records'] }}</th>
                                        <th class="text-center">
                                            @php
                                                $totalPossible = $students->count() * $totalDays;
                                                // Total hadir = hadir + terlambat untuk perhitungan persentase
                                                $totalHadirOverall = $overallStats['hadir'] + ($overallStats['terlambat'] ?? 0);
                                                $overallPercentage = $totalPossible > 0 ? round(($totalHadirOverall / $totalPossible) * 100, 1) : 0;
                                            @endphp
                                            {{ $overallPercentage }}%
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Tidak ada data siswa</h5>
                            <p class="text-muted">Silakan pilih kelas atau periode yang berbeda</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header .badge, .d-print-none {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 12px;
    }
    
    .badge {
        color: #000 !important;
        background-color: transparent !important;
        border: 1px solid #000 !important;
    }
}

.border-left-primary {
    border-left: 4px solid var(--primary-color) !important;
}

.border-left-success {
    border-left: 4px solid #198754 !important;
}

.border-left-warning {
    border-left: 4px solid #ffc107 !important;
}

.border-left-info {
    border-left: 4px solid #0dcaf0 !important;
}

.text-xs {
    font-size: 0.7rem;
}
</style>
@endsection
