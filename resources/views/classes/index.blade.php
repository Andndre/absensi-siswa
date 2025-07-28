@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-collection-fill me-2"></i>Manajemen Kelas
                </h1>
                <div>
                    <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Kelas
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
                        <i class="bi bi-search me-2"></i>Pencarian
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.classes.index') }}">
                        <div class="row">
                            <div class="col-md-10 mb-3">
                                <label for="search" class="form-label">Cari Kelas</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Nama kelas..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2 mb-3">
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

    <!-- Data Cards -->
    <div class="row">
        @if($classes->count() > 0)
            @foreach($classes as $class)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-left-primary">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Kelas
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $class->name }}
                                </div>
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-people-fill me-1"></i>
                                    {{ $class->students_count }} siswa
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-collection-fill fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('admin.classes.show', $class) }}" 
                               class="btn btn-sm btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.classes.edit', $class) }}" 
                               class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($class->students_count == 0)
                                <form action="{{ route('admin.classes.destroy', $class) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus kelas {{ $class->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        title="Tidak dapat dihapus karena masih ada siswa" disabled>
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-collection display-1 text-muted"></i>
                        <h5 class="text-muted mt-3">Belum ada data kelas</h5>
                        <p class="text-muted">Klik tombol "Tambah Kelas" untuk menambah kelas baru</p>
                        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Kelas Pertama
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($classes->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            Menampilkan {{ $classes->firstItem() }} sampai {{ $classes->lastItem() }} 
                            dari {{ $classes->total() }} kelas
                        </small>
                    </div>
                    <div>
                        {{ $classes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.border-left-primary {
    border-left: 4px solid var(--primary-color) !important;
}

.text-xs {
    font-size: 0.7rem;
}

.fa-2x {
    font-size: 2rem;
}
</style>
@endsection
