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
                                        <th width="25%">Nama Siswa</th>
                                        <th width="15%">NIS</th>
                                        <th width="15%">Kelas</th>
                                        <th width="20%">No. WhatsApp Ortu</th>
                                        <th width="20%">Aksi</th>
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
