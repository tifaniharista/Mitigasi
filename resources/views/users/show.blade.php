@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Profile Card -->
        <div class="card">
            <div class="card-header text-white" style="background-color: #004882">
                <h5 class="card-title mb-0">Profil User</h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                     class="rounded-circle mb-3" width="120" height="120">
                <h4>{{ $user->name }}</h4>
                <span class="badge {{ $user->role_badge_class }} mb-2">{{ $user->role_label }}</span>

                <div class="mt-3">
                    @if($user->is_active)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif

                    @if($user->last_login_at && $user->last_login_at->gt(now()->subMinutes(15)))
                        <span class="badge bg-info">Online</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">Statistik</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary">{{ $user->verified_documents_count }}</h4>
                            <small class="text-muted">Dokumen Diverifikasi</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">
                            {{ $user->created_at->diffInDays(now()) }}
                        </h4>
                        <small class="text-muted">Hari Bergabung</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">Informasi Akun</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>OPD</strong></td>
                        <td>{{ $user->opd_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Bergabung</strong></td>
                        <td>{{ $user->created_at->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Login Terakhir</strong></td>
                        <td>
                            @if($user->last_login_at)
                                {{ $user->last_login_at->translatedFormat('d F Y H:i') }}
                                <br><small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">Belum pernah</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Email Terverifikasi</strong></td>
                        <td>
                            @if($user->email_verified_at)
                                <span class="badge bg-success">Ya</span>
                            @else
                                <span class="badge bg-warning">Belum</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #004882">
                <h5 class="card-title mb-0">Detail User</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Nama Lengkap</strong></td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Role</strong></td>
                                <td>
                                    <span class="badge {{ $user->role_badge_class }}">
                                        {{ $user->role_label }}
                                    </span>
                                </td>
                            </tr>
                            @if($user->role === \App\Models\User::ROLE_VIEWER)
                            <tr>
                                <td><strong>Status Verifikasi</strong></td>
                                <td>
                                    @if($user->status_verifikasi === 'pending')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Menunggu Verifikasi
                                        </span>
                                    @elseif($user->status_verifikasi === 'approved')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Terverifikasi
                                        </span>
                                    @elseif($user->status_verifikasi === 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Ditolak
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Status Akun</strong></td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>OPD</strong></td>
                                <td>{{ $user->opd_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>ID User</strong></td>
                                <td><code>{{ $user->id }}</code></td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Tambahkan tombol edit verifikasi untuk admin --}}
                @if($user->isViewer() && Auth::user()->isAdmin())
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #004882">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-user-check me-2"></i>Manajemen Verifikasi
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6>Status Verifikasi Saat Ini</h6>
                                        <p class="text-muted mb-3">
                                            @if($user->isPending())
                                                User sedang menunggu verifikasi administrator.
                                            @elseif($user->isVerified())
                                                User sudah terverifikasi dan dapat mengakses sistem.
                                            @elseif($user->isRejected())
                                                User ditolak dan tidak dapat mengakses sistem.
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="badge {{ $user->status_verifikasi_badge_class }} fs-6">
                                            {{ $user->status_verifikasi_label }}
                                        </span>
                                    </div>
                                </div>

                                @if($user->isRejected() && $user->rejection_reason)
                                <div class="mt-3 p-3 bg-light rounded">
                                    <h6 class="mb-2">
                                        <i class="fas fa-comment-exclamation me-2"></i>Alasan Penolakan:
                                    </h6>
                                    <p class="mb-2">{{ $user->rejection_reason }}</p>
                                    <small class="text-muted">
                                        Ditolak pada: {{ $user->rejected_at_formatted }}
                                    </small>
                                </div>
                                @endif

                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Klik "Edit Status" untuk mengubah status verifikasi user.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Tombol verifikasi untuk viewer yang pending (existing code) --}}
                @if($user->id !== Auth::id() && $user->role === \App\Models\User::ROLE_VIEWER && $user->status_verifikasi === 'pending')
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="card-title mb-0">Verifikasi User</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Setujui User</h6>
                                        <p class="small text-muted">
                                            Setujui user untuk memberikan akses ke sistem.
                                        </p>
                                        <form action="{{ route('users.approve-from-list', $user) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check me-1"></i>Setujui User
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Tolak User</h6>
                                        <p class="small text-muted">
                                            Tolak user dan nonaktifkan akunnya.
                                        </p>
                                        <button type="button" class="btn btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectModal">
                                            <i class="fas fa-times me-1"></i>Tolak User
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal untuk penolakan -->
                <div class="modal fade" id="rejectModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tolak User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('users.reject-from-list', $user) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="modal-body">
                                    <p>Anda akan menolak user: <strong>{{ $user->name }}</strong></p>
                                    <div class="mb-3">
                                        <label for="rejection_reason" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="rejection_reason"
                                                name="rejection_reason" rows="3"
                                                placeholder="Berikan alasan penolakan..." required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Tolak User</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Tombol Kembali dipindahkan ke sini --}}
                <div class="d-flex justify-content-start pt-3 border-top mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar User
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
setInterval(function() {
    fetch('{{ route("users.stats", $user) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update online status if needed
                const onlineBadge = document.querySelector('.badge.bg-info');
                if (data.data.is_online && !onlineBadge) {
                    // Add online badge
                    const statusDiv = document.querySelector('.card-body.text-center .mt-3');
                    if (statusDiv) {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-info';
                        badge.textContent = 'Online';
                        statusDiv.appendChild(badge);
                    }
                }
            }
        });
}, 30000);
</script>
@endpush
