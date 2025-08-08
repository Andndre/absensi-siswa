@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-eye me-2"></i>Detail Kelas: {{ $class->name }}
                </h1>
                <div>
                    <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Information -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-info-circle me-2"></i>Informasi Kelas
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Nama Kelas:</th>
                            <td><strong>{{ $class->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>ID Kelas:</th>
                            <td><code>{{ $class->id }}</code></td>
                        </tr>
                        <tr>
                            <th>Dibuat:</th>
                            <td>{{ $class->created_at->format('d F Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Update:</th>
                            <td>{{ $class->updated_at->format('d F Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Siswa:</th>
                            <td>
                                <span class="badge bg-info fs-6">{{ $studentsCount }} siswa</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-people me-2"></i>Daftar Siswa ({{ $studentsCount }})
                    </h6>
                    <a href="{{ route('admin.students.create', ['class_id' => $class->id]) }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Siswa
                    </a>
                </div>
                <div class="card-body">
                    @if($students->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">Belum ada siswa di kelas ini</h5>
                            <p class="text-muted">Klik tombol "Tambah Siswa" untuk menambah siswa baru</p>
                            <a href="{{ route('admin.students.create', ['class_id' => $class->id]) }}" class="btn btn-success">
                                <i class="bi bi-plus-circle me-1"></i>Tambah Siswa Pertama
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">#</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th width="25%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $student->nis }}</strong></td>
                                        <td>{{ $student->name }}</td>
                                        <td>
                                            <a href="{{ route('admin.students.show', $student) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.students.edit', $student) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus siswa {{ $student->name }} ({{ $student->nis }})? Semua data absensi siswa ini akan ikut terhapus!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($students->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $students->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Class Statistics -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-bar-chart me-2"></i>Statistik Absensi Kelas
                    </h6>
                </div>
                <div class="card-body">
                    @if($studentsCount > 0)
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-light text-center">
                                    <div class="card-body">
                                        <h5 class="text-success">{{ $attendanceStats['present'] ?? 0 }}</h5>
                                        <small>Total Hadir</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light text-center">
                                    <div class="card-body">
                                        <h5 class="text-warning">{{ $attendanceStats['late'] ?? 0 }}</h5>
                                        <small>Total Terlambat</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light text-center">
                                    <div class="card-body">
                                        <h5 class="text-danger">{{ $attendanceStats['absent'] ?? 0 }}</h5>
                                        <small>Total Tidak Hadir</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light text-center">
                                    <div class="card-body">
                                        <h5 class="text-primary">{{ $attendanceStats['excused'] ?? 0 }}</h5>
                                        <small>Total Izin/Sakit</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="bi bi-bar-chart" style="font-size: 2rem;"></i>
                            <p class="mt-2">Statistik akan muncul setelah ada siswa dan data absensi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12 text-end">
            <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Yakin ingin menghapus kelas {{ $class->name }}? Tindakan ini tidak dapat dibatalkan!')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i>Hapus Kelas
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
