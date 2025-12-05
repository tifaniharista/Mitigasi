@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1>
        <i class="fas fa-users me-2" style="color: #004882;"></i>
        <span style="color: #004882; font-weight: 500; font-size: 23pt;">Manajemen Pengguna</span>
    </h1>

    <div>
        <a href="#" class="btn btn-warning me-2">
            <i class="fas fa-user-clock me-1"></i>Pending Verification
            @if($pendingCount > 0)
                <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm"
           style="color: #004882; background-color: transparent; border-color: #004882; border-radius: 8px; padding: 0.60rem 1.2rem; border-width: 1.3px; font-weight: 600;"
           onmouseover="this.style.backgroundColor='#004882'; this.style.color='white';"
           onmouseout="this.style.backgroundColor='transparent'; this.style.color='#004882';">
            <i class="fas fa-plus me-1"></i> Tambah User
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm" style="margin-bottom: 20pt;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center pt-3 pb-2 border-bottom">
        <h5 class="mb-0" style="color: #004882; font-weight:550; font-size: 16pt;">
            <i class="fas fa-list me-2"></i>Daftar Pengguna
        </h5>
    </div>

    <div class="card-body p-0">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center text-uppercase text-muted small py-3">#</th>
                            <th class="text-uppercase text-muted small py-3">User</th>
                            <th width="10%" class="text-uppercase text-muted small py-3 text-center">Role</th>
                            <th width="15%" class="text-uppercase text-muted small py-3 text-center">Status Verifikasi</th>
                            <th width="10%" class="text-uppercase text-muted small py-3 text-center">Status Akun</th>
                            <th width="15%" class="text-uppercase text-muted small py-3 text-center">Login Terakhir</th>
                            <th width="20%" class="text-uppercase text-muted small py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                         class="rounded-circle me-3" width="40" height="40">
                                    <div>
                                        <h6 class="mb-0 fw-medium">{{ $user->name }}</h6>
                                        <small class="text-muted">{{ $user->email }}</small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-building me-1"></i>{{ $user->opd_name }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $user->role_badge_class }} px-3 py-2">
                                    {{ $user->role_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($user->role === \App\Models\User::ROLE_VIEWER)
                                    @if($user->status_verifikasi === 'pending')
                                        <span class="badge bg-warning px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>Menunggu
                                        </span>
                                    @elseif($user->status_verifikasi === 'approved')
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check me-1"></i>Terverifikasi
                                        </span>
                                    @elseif($user->status_verifikasi === 'rejected')
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times me-1"></i>Ditolak
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary px-3 py-2">Tidak Perlu</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($user->is_active)
                                    <span class="badge bg-success px-3 py-2">Aktif</span>
                                @else
                                    <span class="badge bg-danger px-3 py-2">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($user->last_login_at)
                                    <small class="text-muted">
                                        {{ $user->last_login_at->diffForHumans() }}
                                    </small>
                                @else
                                    <span class="text-muted">Belum pernah</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('users.show', $user) }}"
                                       class="btn btn-sm btn-info btn-square" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Tombol Edit Verifikasi untuk Viewer -->
                                    @if($user->isViewer() && Auth::user()->isAdmin())
                                        <a href="{{ route('users.edit-verification', $user) }}"
                                           class="btn btn-sm btn-warning btn-square" title="Edit Verifikasi">
                                            <i class="fas fa-user-check"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('users.edit', $user) }}"
                                       class="btn btn-sm btn-primary btn-square" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Tombol Verifikasi untuk Viewer yang Pending -->
                                    @if($user->role === \App\Models\User::ROLE_VIEWER && $user->status_verifikasi === 'pending')
                                        <form action="{{ route('users.approve-from-list', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success btn-square"
                                                    title="Setujui User"
                                                    onclick="return confirm('Setujui user {{ $user->name }}? User akan dapat mengakses sistem.')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger btn-square"
                                                title="Tolak User"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectModal{{ $user->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('users.toggle-status', $user) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-square {{ $user->is_active ? 'btn-warning' : 'btn-success' }}"
                                                    title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas {{ $user->is_active ? 'fa-times' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        <button type="button"
                                                class="btn btn-sm btn-danger btn-square delete-user-btn"
                                                title="Hapus"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-user-form-{{ $user->id }}"
                                              action="{{ route('users.destroy', $user) }}"
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>

                                <!-- Modal untuk penolakan dari halaman list -->
                                @if($user->role === \App\Models\User::ROLE_VIEWER && $user->status_verifikasi === 'pending')
                                <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1">
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
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak Ada Pengguna Ditemukan</h5>
                <p class="text-muted">Mulai dengan menambahkan pengguna pertama Anda.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-square {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-user-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const userId = this.getAttribute('data-id');
            const userName = this.getAttribute('data-name');

            if (confirm(`Apakah Anda yakin ingin menghapus user "${userName}"?`)) {
                document.getElementById(`delete-user-form-${userId}`).submit();
            }
        });
    });
});
</script>
@endpush
