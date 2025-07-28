@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Data Siswa
                </h1>
                <div>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-form me-2"></i>Form Edit Siswa: {{ $student->name }}
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.students.update', $student) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $student->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nis" class="form-label">NIS (Nomor Induk Siswa) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nis') is-invalid @enderror" 
                                       id="nis" name="nis" value="{{ old('nis', $student->nis) }}" required>
                                @error('nis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="school_class_id" class="form-label">Kelas</label>
                                <select class="form-select @error('school_class_id') is-invalid @enderror" 
                                        id="school_class_id" name="school_class_id">
                                    <option value="">Pilih Kelas</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" 
                                                {{ old('school_class_id', $student->school_class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('school_class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="parent_whatsapp_number" class="form-label">No. WhatsApp Orang Tua <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('parent_whatsapp_number') is-invalid @enderror" 
                                       id="parent_whatsapp_number" name="parent_whatsapp_number" 
                                       value="{{ old('parent_whatsapp_number', $student->parent_whatsapp_number) }}" 
                                       placeholder="Contoh: 081234567890" required>
                                @error('parent_whatsapp_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-x-circle me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i>Update Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
