@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 class="border-bottom pb-3 d-inline-block">
            <i class="fas fa-user-check me-2" style="color: #004882;"></i>
            <span style="color: #004882; font-weight: 500; font-size: 23pt;">Edit Status Verifikasi</span>
        </h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-info-circle me-2"></i>Informasi User
                </h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Informasi User -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                 class="rounded-circle me-3" width="60" height="60">
                            <div>
                                <h6 class="mb-1">{{ $user->name }}</h6>
                                <small class="text-muted">{{ $user->email }}</small><br>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Role:</strong></td>
                                <td>
                                    <span class="badge {{ $user->role_badge_class }}">
                                        {{ $user->role_label }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status Saat Ini:</strong></td>
                                <td>
                                    <span class="badge {{ $user->status_verifikasi_badge_class }}">
                                        {{ $user->status_verifikasi_label }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <form action="{{ route('users.update-verification', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label for="status_verifikasi" class="form-label">Status Verifikasi <span class="text-danger">*</span></label>
                        <select class="form-select @error('status_verifikasi') is-invalid @enderror"
                                id="status_verifikasi" name="status_verifikasi" required>
                            <option value="">Pilih Status Verifikasi</option>
                            @foreach($user->verification_status_options as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('status_verifikasi', $user->status_verifikasi) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status_verifikasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Pilih status verifikasi untuk user ini.
                        </div>
                    </div>

                    <!-- Field Alasan Penolakan (hanya tampil ketika status rejected dipilih) -->
                    <div class="mb-4" id="rejection_reason_field" style="display: none;">
                        <label for="rejection_reason" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('rejection_reason') is-invalid @enderror"
                                  id="rejection_reason" name="rejection_reason"
                                  rows="4" placeholder="Berikan alasan penolakan yang jelas dan informatif...">{{ old('rejection_reason', $user->rejection_reason) }}</textarea>
                        @error('rejection_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Alasan penolakan akan ditampilkan kepada user ketika mereka login.
                        </div>
                    </div>

                    @if($user->isRejected() && $user->rejection_reason)
                    <!-- Tampilkan alasan penolakan saat ini -->
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-circle me-2"></i>Alasan Penolakan Saat Ini
                        </h6>
                        <p class="mb-2">{{ $user->rejection_reason }}</p>
                        <small class="text-muted">
                            Ditolak pada: {{ $user->rejected_at_formatted }}
                        </small>
                    </div>
                    @endif

                    <!-- Informasi Status -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Informasi Status Verifikasi:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong class="text-warning">Menunggu Verifikasi:</strong> User dapat login tetapi hanya melihat halaman menunggu verifikasi</li>
                            <li><strong class="text-success">Terverifikasi:</strong> User dapat mengakses semua fitur yang diizinkan untuk role Viewer</li>
                            <li><strong class="text-danger">Ditolak:</strong> User dapat login tetapi hanya melihat halaman penolakan dengan alasan</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top mt-4">
                        <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
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
    const statusSelect = document.getElementById('status_verifikasi');
    const reasonField = document.getElementById('rejection_reason_field');
    const reasonTextarea = document.getElementById('rejection_reason');

    function toggleRejectionReason() {
        if (statusSelect.value === 'rejected') {
            reasonField.style.display = 'block';
            reasonTextarea.required = true;
        } else {
            reasonField.style.display = 'none';
            reasonTextarea.required = false;
        }
    }

    // Initial state
    toggleRejectionReason();

    // Listen for changes
    statusSelect.addEventListener('change', toggleRejectionReason);

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (statusSelect.value === 'rejected' && !reasonTextarea.value.trim()) {
            e.preventDefault();
            alert('Harap isi alasan penolakan untuk status Ditolak.');
            reasonTextarea.focus();
        }
    });
});
</script>
@endpush
