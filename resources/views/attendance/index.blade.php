@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-calendar2-check me-2"></i>Rekap Absensi
                </h1>
                <div>
                    <a href="{{ route('admin.attendance.report') }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i>Laporan Detail
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
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
                    <form method="GET" action="{{ route('admin.attendance.index') }}">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="date" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="date" name="date" 
                                       value="{{ request('date', $selectedDate) }}">
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
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
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label for="search" class="form-label">Cari Siswa</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Nama atau NIS..." value="{{ request('search') }}">
                            </div>
                            @php
                                $defaultPerPage = \App\Models\Setting::get('system.records_per_page', 10);
                            @endphp
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label for="per_page" class="form-label">Per Halaman</label>
                                <select class="form-select" id="per_page" name="per_page">
                                    <option value="10" {{ request('per_page', $defaultPerPage) == '10' ? 'selected' : '' }}>10</option>
                                    <option value="15" {{ request('per_page', $defaultPerPage) == '15' ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page', $defaultPerPage) == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page', $defaultPerPage) == '50' ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page', $defaultPerPage) == '100' ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <div class="col-lg-1 col-md-6 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block w-100">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="stat-card stat-present">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $stats['hadir'] ?? 0 }}</div>
                            <div class="stat-label">Hadir</div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar bg-success" style="width: {{ $stats['total'] > 0 ? min((($stats['hadir'] ?? 0) / $stats['total']) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="stat-card stat-late">
                        <div class="stat-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $stats['terlambat'] ?? 0 }}</div>
                            <div class="stat-label">Terlambat</div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['total'] > 0 ? min((($stats['terlambat'] ?? 0) / $stats['total']) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="stat-card stat-excused">
                        <div class="stat-icon">
                            <i class="bi bi-file-medical"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ ($stats['izin'] ?? 0) + ($stats['sakit'] ?? 0) }}</div>
                            <div class="stat-label">Izin/Sakit</div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar bg-info" style="width: {{ $stats['total'] > 0 ? min(((($stats['izin'] ?? 0) + ($stats['sakit'] ?? 0)) / $stats['total']) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="stat-card stat-absent">
                        <div class="stat-icon">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $stats['alpha'] ?? 0 }}</div>
                            <div class="stat-label">Alpha</div>
                        </div>
                        <div class="stat-progress">
                            <div class="progress-bar bg-danger" style="width: {{ $stats['total'] > 0 ? min((($stats['alpha'] ?? 0) / $stats['total']) * 100, 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Ringkasan Absensi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <h3 class="text-primary mb-1">{{ $stats['total'] }}</h3>
                                <small class="text-muted">Total Absen</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 class="text-success mb-1">{{ $stats['total'] > 0 ? round((($stats['hadir'] ?? 0) / $stats['total']) * 100, 1) : 0 }}%</h3>
                            <small class="text-muted">Tingkat Kehadiran</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-2">
                            <h4 class="text-muted mb-1">{{ $stats['belum_absen'] }}</h4>
                            <small class="text-muted">Belum Absen</small>
                        </div>
                        <div class="col-6 mb-2">
                            <h4 class="text-info mb-1">{{ ($stats['izin'] ?? 0) + ($stats['sakit'] ?? 0) }}</h4>
                            <small class="text-muted">Total Izin+Sakit</small>
                        </div>
                    </div>
                    
                    <!-- Progress Bar Kehadiran -->
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Progress Kehadiran</small>
                            <small class="text-muted">{{ $stats['total'] > 0 ? round((($stats['hadir'] ?? 0) / $stats['total']) * 100, 1) : 0 }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['total'] > 0 ? (($stats['hadir'] ?? 0) / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Status -->
    @if(request('date') || request('class_id') || request('status') || request('search'))
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0" style="background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-1">
                            <i class="bi bi-funnel me-2"></i>Filter Aktif:
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            @if(request('date'))
                                <span class="badge bg-primary">📅 {{ \Carbon\Carbon::parse(request('date'))->format('d F Y') }}</span>
                            @endif
                            @if(request('class_id'))
                                @php $selectedClass = $classes->find(request('class_id')) @endphp
                                <span class="badge bg-info">🏫 {{ $selectedClass->name ?? 'Kelas' }}</span>
                            @endif
                            @if(request('status'))
                                <span class="badge bg-warning">📊 {{ ucfirst(request('status')) }}</span>
                            @endif
                            @if(request('search'))
                                <span class="badge bg-secondary">🔍 "{{ request('search') }}"</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset Filter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Manual Attendance Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Absensi Manual
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.attendance.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label for="student_id" class="form-label">Pilih Siswa <span class="text-danger">*</span></label>
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <option value="">-- Pilih Siswa --</option>
                                    @php
                                        $classesWithStudents = \App\Models\SchoolClass::with('students')->orderBy('name')->get();
                                    @endphp
                                    @foreach($classesWithStudents as $class)
                                        @if($class->students->count() > 0)
                                            <optgroup label="{{ $class->name }}">
                                                @foreach($class->students->sortBy('name') as $student)
                                                    <option value="{{ $student->id }}">
                                                        {{ $student->name }} ({{ $student->nis }})
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                    @php
                                        $studentsWithoutClass = \App\Models\Student::whereNull('school_class_id')->orderBy('name')->get();
                                    @endphp
                                    @if($studentsWithoutClass->count() > 0)
                                        <optgroup label="Tanpa Kelas">
                                            @foreach($studentsWithoutClass as $student)
                                                <option value="{{ $student->id }}">
                                                    {{ $student->name }} ({{ $student->nis }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label for="manual_date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="manual_date" name="attendance_date" 
                                       value="{{ request('date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label for="manual_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="manual_status" name="status" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="hadir">🟢 Hadir</option>
                                    <option value="terlambat">🟡 Terlambat</option>
                                    <option value="izin">🔵 Izin</option>
                                    <option value="sakit">⚪ Sakit</option>
                                    <option value="alpha">🔴 Alpha</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-6 mb-3">
                                <label for="manual_notes" class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="manual_notes" name="notes" 
                                       placeholder="Keterangan (opsional)">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i>Tambah Absensi
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Form ini untuk menambah data absensi secara manual. Waktu akan otomatis menggunakan waktu saat ini.
                                </small>
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
                        <i class="bi bi-table me-2"></i>Data Absensi
                        <span class="badge bg-light text-dark ms-2">{{ $attendances->total() }} record</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($attendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Siswa</th>
                                        <th width="15%">Kelas</th>
                                        <th width="15%">Tanggal</th>
                                        <th width="15%">Waktu</th>
                                        <th width="10%">Status</th>
                                        <th width="20%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendances as $index => $attendance)
                                    <tr>
                                        <td>{{ $attendances->firstItem() + $index }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $attendance->student->name }}</strong>
                                            </div>
                                            <small class="text-muted">NIS: {{ $attendance->student->nis }}</small>
                                        </td>
                                        <td>
                                            @if($attendance->student->schoolClass)
                                                <span class="badge bg-secondary">{{ $attendance->student->schoolClass->name }}</span>
                                            @else
                                                <span class="badge bg-warning">Tidak ada kelas</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $attendance->attendance_time->format('d/m/Y') }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $attendance->attendance_time->format('H:i:s') }}</strong>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.attendance.update', $attendance) }}" method="POST" class="d-inline status-form">
                                                @csrf
                                                @method('PUT')
                                                <select class="form-select form-select-sm status-select" name="status" style="width: 130px;">
                                                    <option value="hadir" {{ $attendance->status == 'hadir' ? 'selected' : '' }} class="text-success">
                                                        🟢 Hadir
                                                    </option>
                                                    <option value="terlambat" {{ $attendance->status == 'terlambat' ? 'selected' : '' }} class="text-warning">
                                                        🟡 Terlambat
                                                    </option>
                                                    <option value="izin" {{ $attendance->status == 'izin' ? 'selected' : '' }} class="text-info">
                                                        🔵 Izin
                                                    </option>
                                                    <option value="sakit" {{ $attendance->status == 'sakit' ? 'selected' : '' }} class="text-secondary">
                                                        ⚪ Sakit
                                                    </option>
                                                    <option value="alpha" {{ $attendance->status == 'alpha' ? 'selected' : '' }} class="text-danger">
                                                        🔴 Alpha
                                                    </option>
                                                </select>
                                            </form>
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
                        
                        <!-- Pagination -->
                        <div class="row mt-3">
                            <div class="col-lg-8 col-md-12 mb-2">
                                <small class="text-muted">
                                    Menampilkan {{ $attendances->firstItem() }} sampai {{ $attendances->lastItem() }} 
                                    dari {{ $attendances->total() }} data
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
                                        {{ $attendances->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h5 class="text-muted mt-3">Tidak ada data absensi</h5>
                            <p class="text-muted">Silakan ubah filter untuk melihat data yang berbeda</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Statistics Cards - Same as Scanner */
.stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border: none;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
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

/* Total Card */
.stat-total {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
}

.stat-total .stat-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.stat-total::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
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

/* Sick Card */
.stat-sick {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
}

.stat-sick .stat-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.stat-sick::before {
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

/* Not Present Card */
.stat-not-present {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
}

.stat-not-present .stat-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.stat-not-present::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* Percentage Card */
.stat-percentage {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: white;
}

.stat-percentage .stat-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.stat-percentage::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* Cards and Form Improvements */
.card {
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    border-radius: 12px;
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
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
    font-weight: 600;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
}

.btn-outline-primary {
    border: 2px solid #3b82f6;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-outline-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
}

/* Status select styling */
.status-select {
    padding: 4px 8px;
    font-size: 0.875rem;
    border-radius: 4px;
}

.status-select option {
    padding: 4px 8px;
}

.status-select option[value="hadir"] {
    color: #198754;
}

.status-select option[value="terlambat"] {
    color: #ffc107;
}

.status-select option[value="izin"] {
    color: #0dcaf0;
}

.status-select option[value="sakit"] {
    color: #6c757d;
}

.status-select option[value="alpha"] {
    color: #dc3545;
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

.border-left-danger {
    border-left: 4px solid #dc3545 !important;
}

.border-left-secondary {
    border-left: 4px solid #6c757d !important;
}

.text-xs {
    font-size: 0.7rem;
}

/* Badge styling */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 6px;
    padding: 4px 8px;
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

/* Select2 Custom Styling */
.select2-container .select2-selection--single {
    height: 38px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
}

.select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    line-height: 34px;
    padding-left: 12px;
}

.select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
    height: 34px;
    right: 12px;
}

.select2-container--bootstrap-5.select2-container--focus .select2-selection {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.select2-dropdown {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
}

.select2-search--dropdown .select2-search__field {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 8px 12px;
}
</style>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset ke halaman pertama saat mengubah per_page
    window.location.href = url.toString();
}

// Auto submit form ketika filter berubah (opsional - untuk UX yang lebih baik)
document.addEventListener('DOMContentLoaded', function() {
    const autoSubmitSelects = ['date', 'class_id', 'status', 'per_page'];
    
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

// Initialize Select2 for student dropdown
$(document).ready(function() {
    $('#student_id').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Pilih Siswa --',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Tidak ada siswa yang ditemukan";
            },
            searching: function() {
                return "Mencari...";
            },
            loadingMore: function() {
                return "Memuat lebih banyak...";
            }
        }
    });
});

// Add event listeners to all status selects
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const form = this.closest('form');
        if (form) {
            const oldStatus = this.options[this.selectedIndex].text;
            if (confirm('Ubah status menjadi ' + oldStatus + '?')) {
                form.submit();
            } else {
                // Reset to previous value if cancelled
                this.value = this.getAttribute('data-original-value');
            }
        }
    });
    // Store original value for cancel action
    select.setAttribute('data-original-value', select.value);
});
</script>
@endsection
