@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 class="border-bottom pb-3 d-inline-block">
            <i class="fas fa-plus me-2" style="color: #004882;"></i>
            <span style="color: #004882; font-weight: 500; font-size: 23pt;">Tambah Tahapan Baru</span>
        </h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-info-circle me-2"></i>Informasi Tahapan
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tahapans.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nama_tahapan" class="form-label">Nama Tahapan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_tahapan') is-invalid @enderror"
                                   id="nama_tahapan" name="nama_tahapan" value="{{ old('nama_tahapan') }}"
                                   placeholder="Masukkan nama tahapan" required>
                            @error('nama_tahapan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="order" class="form-label">Urutan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror"
                                   id="order" name="order" value="{{ old('order', $lastOrder + 1) }}"
                                   min="0" required>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Urutan tertinggi saat ini: {{ $lastOrder ?? 'N/A' }}</small>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Tahapan Aktif
                                </label>
                            </div>
                            <small class="text-muted">Jika tidak dicentang, tahapan ini tidak akan tersedia untuk dokumen baru.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top mt-4">
                        <a href="{{ route('tahapans.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-info-circle me-2"></i>Informasi Tambahan
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-2"></i>
                    Tahapan digunakan untuk mengelompokkan jenis-jenis dokumen yang harus diunggah dalam sebuah proyek.
                </p>
                <ul class="text-muted small mb-0">
                    <li>Urutan (Order) menentukan posisi tampil di daftar proyek.</li>
                    <li>Tahapan aktif dapat ditambahkan ke proyek baru.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
