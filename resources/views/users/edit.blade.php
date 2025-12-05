@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 class="border-bottom pb-3 d-inline-block">
            <i class="fas fa-edit me-2" style="color: #004882;"></i>
            <span style="color: #004882; font-weight: 500; font-size: 23pt;">Edit User: {{ $user->name }}</span>
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
                <form action="{{ route('users.update', $user) }}" method="POST">
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

                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror"
                                    id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                @foreach($roles as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ old('role', $user->role) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="opd_id" class="form-label">OPD</label>
                            <select class="form-select @error('opd_id') is-invalid @enderror"
                                    id="opd_id" name="opd_id">
                                <option value="">Pilih OPD (Opsional)</option>
                                @foreach($opds as $opd)
                                    <option value="{{ $opd->id }}" {{ old('opd_id', $user->opd_id) == $opd->id ? 'selected' : '' }}>
                                        {{ $opd->code }} - {{ $opd->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('opd_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                OPD hanya diperlukan untuk role Viewer. Untuk role lain bisa dikosongkan.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Akun</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                    {{ $user->is_active ? 'checked' : '' }} {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Akun Aktif
                                    @if($user->id === Auth::id())
                                        <small class="text-muted d-block">(Tidak dapat menonaktifkan akun sendiri)</small>
                                    @endif
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top mt-4">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update User
                            </button>
                        </div>
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
                        <form action="{{ route('users.update-password', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="new_password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="new_password" name="password"
                                           placeholder="Masukkan password baru (kosongkan jika tidak ingin mengubah)">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation"
                                           placeholder="Konfirmasi password baru">
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

                <!-- Informasi Tambahan -->
                <div class="card border-info mt-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Informasi Tambahan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>ID User:</strong></td>
                                        <td><code>{{ $user->id }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Dibuat:</strong></td>
                                        <td>{{ $user->created_at->translatedFormat('d F Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Terakhir Diupdate:</strong></td>
                                        <td>{{ $user->updated_at->translatedFormat('d F Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Login Terakhir:</strong></td>
                                        <td>
                                            @if($user->last_login_at)
                                                {{ $user->last_login_at->translatedFormat('d F Y H:i') }}
                                            @else
                                                <span class="text-muted">Belum pernah</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status Verifikasi:</strong></td>
                                        <td>
                                            @if($user->role === \App\Models\User::ROLE_VIEWER)
                                                <span class="badge {{ $user->status_verifikasi_badge_class }}">
                                                    {{ $user->status_verifikasi_label }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Perlu</span>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const opdSelect = document.getElementById('opd_id');
    const opdFormText = opdSelect.parentElement.querySelector('.form-text');

    function toggleOpdField() {
        const selectedRole = roleSelect.value;

        if (selectedRole === 'viewer') {
            opdSelect.required = false; // Tetap opsional meski untuk viewer
            opdFormText.style.display = 'block';
            opdFormText.innerHTML = 'OPD sangat disarankan untuk role Viewer agar user dapat mengakses data OPD terkait.';
        } else {
            opdSelect.required = false;
            opdFormText.style.display = 'block';
            opdFormText.innerHTML = 'OPD hanya diperlukan untuk role Viewer. Untuk role lain bisa dikosongkan.';
        }
    }

    // Initial state
    toggleOpdField();

    // Listen for role changes
    roleSelect.addEventListener('change', toggleOpdField);

    // Form validation untuk OPD
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const selectedRole = roleSelect.value;
        const selectedOpd = opdSelect.value;

        // Validasi tambahan: jika role viewer dan OPD kosong, tampilkan warning
        if (selectedRole === 'viewer' && !selectedOpd) {
            if (!confirm('Role Viewer dipilih tetapi OPD belum dipilih. User mungkin tidak dapat mengakses data tertentu. Lanjutkan?')) {
                e.preventDefault();
                opdSelect.focus();
            }
        }
    });
});
</script>

<style>
.form-text {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.card {
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
}

.form-control, .form-select {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #004882;
    box-shadow: 0 0 0 0.2rem rgba(0, 72, 130, 0.25);
}

.invalid-feedback {
    display: block;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
}
</style>
@endpush
