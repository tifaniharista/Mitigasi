{{-- resources/views/projects/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 class="border-bottom pb-3">
            <i class="fas fa-plus me-2" style="color: #004882;"></i>
            <span style="color: #004882; font-weight: 500; font-size: 23pt;">Tambah Proyek Baru</span>
        </h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-info-circle me-2"></i> Informasi Proyek
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('projects.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Proyek <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required
                                   placeholder="Masukkan nama proyek">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="developer_id" class="form-label">Developer <span class="text-danger">*</span></label>
                            <select class="form-select @error('developer_id') is-invalid @enderror"
                                    id="developer_id" name="developer_id" required>
                                <option value="">Pilih Developer</option>
                                @foreach($developers as $developer)
                                    <option value="{{ $developer->id }}" {{ old('developer_id') == $developer->id ? 'selected' : '' }}>
                                        {{ $developer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('developer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="opd_id" class="form-label">OPD <span class="text-danger">*</span></label>
                            <select class="form-select @error('opd_id') is-invalid @enderror"
                                    id="opd_id" name="opd_id" required>
                                <option value="">Pilih OPD</option>
                                @foreach($opds as $opd)
                                    <option value="{{ $opd->id }}" {{ old('opd_id') == $opd->id ? 'selected' : '' }}>
                                        {{ $opd->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('opd_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="construction_type" class="form-label">Tipe Konstruksi <span class="text-danger">*</span></label>
                            <select class="form-select @error('construction_type') is-invalid @enderror"
                                    id="construction_type" name="construction_type" required>
                                <option value="">Pilih Tipe</option>
                                @foreach(['Pembangunan', 'Pemeliharaan', 'Pengembangan'] as $type)
                                    <option value="{{ $type }}" {{ old('construction_type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('construction_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                   id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary shadow-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="fas fa-save me-1"></i> Simpan
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
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');

        startDate.addEventListener('change', function() {
            endDate.min = this.value;
            // Also ensure end date is valid if start date is changed to a later date
            if (endDate.value && endDate.value < this.value) {
                endDate.value = this.value;
            }
        });

        // Set initial min for end_date on load
        if(startDate.value) {
            endDate.min = startDate.value;
        }
    });
</script>
@endpush
