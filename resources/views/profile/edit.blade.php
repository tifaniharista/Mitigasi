@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 class="border-bottom pb-3 d-inline-block">
            <i class="fas fa-user-edit me-2" style="color: #004882;"></i>
            <span style="color: #004882; font-weight: 500; font-size: 23pt;">Edit Profile</span>
        </h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-info-circle me-2"></i>Informasi Profile
                </h5>
            </div>
            <div class="card-body">
                <!-- Form Edit Profile -->
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $user->name) }}"
                                   placeholder="Masukkan nama lengkap" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $user->email) }}"
                                   placeholder="Masukkan alamat email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($showOpdField)
                            <!-- Tampilkan field OPD jika $showOpdField = true -->
                            <div class="col-md-6 mb-3">
                                <label for="opd_id" class="form-label">OPD / Organisasi <span class="text-danger">*</span></label>
                                <select class="form-select @error('opd_id') is-invalid @enderror"
                                        id="opd_id" name="opd_id" required>
                                    <option value="">Pilih OPD</option>
                                    @foreach($opds as $opd)
                                        <option value="{{ $opd->id }}"
                                            {{ old('opd_id', $user->opd_id) == $opd->id ? 'selected' : '' }}>
                                            {{ $opd->code }} - {{ $opd->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('opd_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <!-- Sembunyikan field OPD, tapi tambahkan hidden input -->
                            <input type="hidden" name="opd_id" value="{{ $user->opd_id }}">

                            <!-- Tampilkan informasi OPD sebagai informasi saja -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">OPD / Organisasi</label>
                                <div class="input-group">
                                    @if($user->opd)
                                        <input type="text" class="form-control"
                                               value="{{ $user->opd->code }} - {{ $user->opd->name }}"
                                               readonly>
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-building text-primary"></i>
                                        </span>
                                    @else
                                        <input type="text" class="form-control"
                                               value="Belum dipilih"
                                               readonly>
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                        </span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    OPD tidak dapat diubah setelah akun diverifikasi dan masuk ke aplikasi.
                                </small>
                            </div>
                        @endif

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ $user->role_label }}" readonly>
                            <small class="text-muted">Role tidak dapat diubah</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Profile
                        </button>
                    </div>
                </form>

                <!-- Form Update Password -->
                <div class="card border-warning mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-key me-2"></i>Update Password
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.update-password') }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                               id="current_password" name="current_password"
                                               placeholder="Masukkan password saat ini" required>
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="current_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                               id="new_password" name="new_password"
                                               placeholder="Masukkan password baru" required>
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="new_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @error('new_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="new_password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control"
                                               id="new_password_confirmation" name="new_password_confirmation"
                                               placeholder="Konfirmasi password baru" required>
                                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="new_password_confirmation">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-1"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Informasi Akun -->
                <div class="card border-info mt-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Informasi Akun
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Bergabung</strong></td>
                                        <td>
                                            <i class="fas fa-calendar-plus me-2 text-muted"></i>
                                            {{ $user->created_at->translatedFormat('d F Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Login Terakhir</strong></td>
                                        <td>
                                            @if($user->last_login_at)
                                                <i class="fas fa-clock me-2 text-muted"></i>
                                                {{ $user->last_login_at->translatedFormat('d F Y H:i') }}
                                                <br><small class="text-muted ms-4">{{ $user->last_login_at->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-times me-2"></i>Belum pernah
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Status</strong></td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>OPD Saat Ini</strong></td>
                                        <td>
                                            @if($user->opd)
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-building me-1"></i>{{ $user->opd->name }}
                                                </span>
                                                @if(!$showOpdField)
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-lock me-1"></i>Tidak dapat diubah
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-building me-1"></i>Belum dipilih
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        font-weight: 500;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #004882;
        box-shadow: 0 0 0 0.2rem rgba(0, 72, 130, 0.25);
    }

    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #004882;
        border-color: #004882;
    }

    .btn-primary:hover {
        background-color: #003366;
        border-color: #003366;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 72, 130, 0.4);
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
        transform: translateY(-2px);
    }

    .input-group .btn-outline-secondary {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-left: none;
    }

    .input-group .form-control {
        border-right: none;
    }

    .input-group .form-control:focus {
        border-right: none;
    }

    .badge {
        font-size: 0.8rem;
        padding: 0.5em 0.75em;
        border-radius: 20px;
    }

    .table-borderless td {
        padding: 0.5rem 0;
        vertical-align: top;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.toggle-password');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Auto-hide alerts setelah 5 detik
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
            }
        });
    });
});
</script>
@endpush
