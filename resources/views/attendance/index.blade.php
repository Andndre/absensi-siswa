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
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card card-stats h-100 shadow-sm border-left-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Absen</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card card-stats h-100 shadow-sm border-left-success">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['hadir'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check-fill text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card card-stats h-100 shadow-sm border-left-warning">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Terlambat</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['terlambat'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card card-stats h-100 shadow-sm border-left-info">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Izin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['izin'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-exclamation text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card card-stats h-100 shadow-sm border-left-secondary">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Sakit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sakit'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-heart-pulse text-secondary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card card-stats h-100 shadow-sm border-left-danger">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpha</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['alpha'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-x-fill text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 mb-3">
            <div class="card card-stats h-100 shadow-sm border-left-secondary">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Belum Absen</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['belum_absen'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-dash text-muted" style="font-size: 2rem;"></i>
                        </div>
                    </div>
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
</style>

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
