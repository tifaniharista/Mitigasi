@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-clock me-2"></i>User Menunggu Verifikasi
                    <span class="badge bg-warning ms-2">{{ $pendingUsers->count() }}</span>
                </h5>
                <div>
                    <a href="{{ route('users.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar User
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($pendingUsers->count() > 0)
                    <!-- Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body py-3">
                                    <form action="{{ route('users.bulk-approve') }}" method="POST" id="bulkApproveForm">
                                        @csrf
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                                    <label class="form-check-label fw-bold" for="selectAll">
                                                        Pilih Semua
                                                    </label>
                                                </div>
                                                <small class="text-muted">Pilih user yang ingin disetujui sekaligus</small>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <button type="submit" class="btn btn-success" id="bulkApproveBtn" disabled>
                                                    <i class="fas fa-check-double me-1"></i>Setujui yang Dipilih
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllTable">
                                        </div>
                                    </th>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>OPD</th>
                                    <th>Tanggal Daftar</th>
                                    <th width="200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingUsers as $user)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox"
                                                   name="user_ids[]" value="{{ $user->id }}" form="bulkApproveForm">
                                        </div>
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                                 class="rounded-circle me-3" width="40" height="40">
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->opd_name }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->created_at->translatedFormat('d F Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('users.show', $user) }}"
                                               class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit-verification', $user) }}"
                                               class="btn btn-sm btn-warning" title="Edit Verifikasi">
                                                <i class="fas fa-user-check"></i>
                                            </a>
                                            <form action="{{ route('users.approve', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('Setujui user {{ $user->name }}?')"
                                                        title="Setujui">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal{{ $user->id }}"
                                                    title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>

                                        <!-- Modal untuk penolakan -->
                                        <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak User</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('users.reject', $user) }}" method="POST">
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
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>Tidak ada user yang menunggu verifikasi</h5>
                        <p class="text-muted">Semua user viewer sudah terverifikasi.</p>
                        <a href="{{ route('users.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar User
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const selectAllTable = document.getElementById('selectAllTable');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');

    function updateBulkButton() {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        bulkApproveBtn.disabled = checkedCount === 0;

        if (checkedCount > 0) {
            bulkApproveBtn.innerHTML = `<i class="fas fa-check-double me-1"></i>Setujui ${checkedCount} User`;
        } else {
            bulkApproveBtn.innerHTML = `<i class="fas fa-check-double me-1"></i>Setujui yang Dipilih`;
        }
    }

    function toggleAllCheckboxes(checked) {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        updateBulkButton();
    }

    selectAll?.addEventListener('change', function() {
        toggleAllCheckboxes(this.checked);
    });

    selectAllTable?.addEventListener('change', function() {
        toggleAllCheckboxes(this.checked);
        if (selectAll) selectAll.checked = this.checked;
    });

    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkButton);
    });

    // Konfirmasi bulk approval
    document.getElementById('bulkApproveForm')?.addEventListener('submit', function(e) {
        const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
        if (checkedCount > 0) {
            if (!confirm(`Anda akan menyetujui ${checkedCount} user. Lanjutkan?`)) {
                e.preventDefault();
            }
        } else {
            e.preventDefault();
            alert('Pilih minimal satu user untuk disetujui.');
        }
    });

    // Initial state
    updateBulkButton();
});
</script>
@endpush
