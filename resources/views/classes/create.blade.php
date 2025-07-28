@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Kelas Baru
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
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-form me-2"></i>Form Tambah Kelas
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.classes.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="Contoh: Kelas 10-A, Kelas 11-IPA-1, dll." required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Masukkan nama kelas yang unik dan mudah dikenali.</div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Tips penamaan kelas:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Gunakan format yang konsisten (contoh: Kelas 10-A, Kelas 10-B)</li>
                                <li>Sertakan tingkat dan jurusan jika ada (contoh: Kelas 11-IPA-1)</li>
                                <li>Hindari karakter khusus yang dapat menimbulkan masalah</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-x-circle me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan Kelas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
