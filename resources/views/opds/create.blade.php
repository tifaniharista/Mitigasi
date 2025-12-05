@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 class="border-bottom pb-3 d-inline-block">
            <i class="fas fa-plus me-2" style="color: #004882;"></i>
            <span style="color: #004882; font-weight: 500; font-size: 23pt;">Tambah OPD Baru</span>
        </h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-info-circle me-2"></i>Informasi OPD
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('opds.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama OPD <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}"
                               placeholder="Nama Organisasi Perangkat Daerah" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Kode OPD <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                               id="code" name="code" value="{{ old('code') }}"
                               placeholder="Kode unik (misalnya: DISKOMINFO)" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi (Opsional)</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="2"
                                  placeholder="Deskripsi singkat mengenai tugas dan fungsi">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                OPD Aktif
                            </label>
                        </div>
                        <small class="text-muted">OPD aktif akan muncul dalam daftar untuk proyek baru.</small>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top mt-4">
                        <a href="{{ route('opds.index') }}" class="btn btn-secondary">
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
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const codeInput = document.getElementById('code');

        nameInput.addEventListener('blur', function() {
            if (!codeInput.value) {
                const name = this.value.trim();
                const code = name.toUpperCase()
                    .replace(/\s+/g, '_')
                    .replace(/[^A-Z0-9_]/g, '')
                    .substring(0, 20);
                codeInput.value = code;
            }
        });
    });
</script>
@endpush
