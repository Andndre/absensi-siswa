@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Kelas
                </h1>
                <div>
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-form me-2"></i>Form Edit Kelas: {{ $class->name }}
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.classes.update', $class) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $class->name) }}" 
                                   placeholder="Contoh: Kelas 10-A, Kelas 11-IPA-1, dll." required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Informasi Kelas</h6>
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <th>Dibuat:</th>
                                                <td>{{ $class->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Terakhir Update:</th>
                                                <td>{{ $class->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Jumlah Siswa:</th>
                                                <td>
                                                    <span class="badge bg-info">{{ $class->students()->count() }} siswa</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Perhatian:</strong> Mengubah nama kelas akan mempengaruhi semua siswa yang terdaftar di kelas ini.
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-x-circle me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i>Update Kelas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
