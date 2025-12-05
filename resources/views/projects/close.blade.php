@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1>
        <i class="fas fa-lock me-2" style="color: #004882;"></i>
        <span style="color: #004882; font-weight: 500; font-size: 23pt;">Tutup Project</span>
    </h1>
    <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center">
                <h5 class="mb-0 text-danger fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Konfirmasi Penutupan Project
                </h5>
            </div>
            <div class="card-body">
                <!-- Warning Alert -->
                <div class="alert alert-danger border-start border-5 border-danger mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                        <div>
                            <h6 class="mb-1 fw-bold">PERINGATAN!</h6>
                            <p class="mb-0">Tindakan ini akan mengunci project dan mencegah perubahan lebih lanjut.</p>
                            <p class="mb-0"><strong>Setelah project ditutup, hanya Administrator yang bisa membuka kembali.</strong></p>
                        </div>
                    </div>
                </div>

                <!-- Project Information -->
                <div class="card border mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Project
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nama Project:</strong><br>{{ $project->name }}</p>
                                <p><strong>Developer:</strong><br>{{ $project->developer->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tanggal Mulai:</strong><br>{{ $project->start_date->format('d M Y') }}</p>
                                <p><strong>Tanggal Selesai:</strong><br>{{ $project->end_date->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <p><strong>Progress:</strong></p>
                                <div class="progress mb-2" style="height: 25px;">
                                    <div class="progress-bar
                                        @if($project->overall_progress >= 80) bg-success
                                        @elseif($project->overall_progress >= 50) bg-info
                                        @elseif($project->overall_progress >= 25) bg-warning
                                        @else bg-danger
                                        @endif"
                                        style="width: {{ $project->overall_progress }}%">
                                        {{ $project->overall_progress }}%
                                    </div>
                                </div>
                                <small class="text-muted">{{ $project->completed_tahapan_count }} dari {{ $project->total_tahapan_count }} tahapan selesai</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Closure Form -->
                <form action="{{ route('projects.close', $project) }}" method="POST">
                    @csrf

                    <!-- Confirmation Checkbox -->
                    <div class="mb-4 p-3 border rounded bg-light">
                        <div class="form-check">
                            <input class="form-check-input @error('confirm_closure') is-invalid @enderror"
                                   type="checkbox" id="confirm_closure" name="confirm_closure" value="1"
                                   {{ old('confirm_closure') ? 'checked' : '' }}>
                            <label class="form-check-label" for="confirm_closure">
                                Saya memahami bahwa dengan menutup project ini:
                                <ul class="mt-2 mb-0">
                                    <li>Project akan dikunci dan tidak bisa diubah</li>
                                    <li>Tidak ada dokumen baru yang bisa ditambahkan</li>
                                    <li>Dokumen yang ada tidak bisa diubah atau diverifikasi ulang</li>
                                    <li>Hanya Administrator yang bisa membuka kembali project</li>
                                </ul>
                            </label>
                            @error('confirm_closure')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-lock me-1"></i> Tutup Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .progress {
        border-radius: 12px;
        overflow: hidden;
    }
    .progress-bar {
        font-weight: 600;
        line-height: 25px;
    }
    .card {
        border-radius: 10px;
    }
    .alert-danger h6,
    .alert-danger p,
    .alert-danger strong {
        color: inherit;
    }
</style>
@endpush
