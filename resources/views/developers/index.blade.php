@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1>
        <i class="fas fa-code-branch me-2" style="color: #004882;"></i>
        <span style="color: #004882; font-weight: 500; font-size: 23pt;">Manajemen Developers</span>
    </h1>

    <a href="{{ route('developers.create') }}" class="btn btn-primary shadow-sm"
        style="color: #004882; background-color: transparent; border-color: #004882; border-radius: 8px; padding: 0.60rem 1.2rem; border-width: 1.3px; font-weight: 600;"
        onmouseover="this.style.backgroundColor='#004882'; this.style.color='white';"
        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#004882';">
        <i class="fas fa-plus me-1"></i> Tambah Developer
    </a>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center pt-3 pb-2 border-bottom">
        <h5 class="mb-0" style="color: #004882; font-weight:550; font-size: 16pt;">
            <i class="fas fa-list me-2"></i>Daftar Developer
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('developers.index', ['status' => 'all']) }}"
                class="btn btn-sm btn-outline-secondary {{ request('status') === 'all' || !request('status') ? 'active' : '' }}">
                Semua
            </a>
            <a href="{{ route('developers.index', ['status' => 'active']) }}"
                class="btn btn-sm btn-outline-success {{ request('status') === 'active' ? 'active' : '' }}">
                Aktif
            </a>
            <a href="{{ route('developers.index', ['status' => 'inactive']) }}"
                class="btn btn-sm btn-outline-danger {{ request('status') === 'inactive' ? 'active' : '' }}">
                Nonaktif
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        @if($developers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center text-uppercase text-muted small py-3">#</th>
                            <th class="text-uppercase text-muted small py-3">Nama Developer</th>
                            <th width="15%" class="text-uppercase text-muted small py-3 text-center">Status</th>
                            <th width="15%" class="text-uppercase text-muted small py-3 text-center">Jumlah Proyek</th>
                            <th width="20%" class="text-uppercase text-muted small py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($developers as $developer)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <span class="text-dark fw-medium">{{ $developer->name }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $developer->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                                    {{ $developer->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info rounded-pill px-3 py-2">{{ $developer->projects_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('developers.show', $developer) }}"
                                        class="btn btn-sm btn-info btn-square" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('developers.edit', $developer) }}"
                                        class="btn btn-sm btn-warning btn-square" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger btn-square delete-btn"
                                            data-id="{{ $developer->id }}"
                                            data-name="{{ $developer->name }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $developer->id }}"
                                        action="{{ route('developers.destroy', $developer) }}"
                                        method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-code-branch fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak Ada Developer Ditemukan</h5>
                <p class="text-muted">Mulai dengan menambahkan developer pertama Anda.</p>
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
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const developerId = this.getAttribute('data-id');
            const developerName = this.getAttribute('data-name');

            if (confirm(`Apakah Anda yakin ingin menghapus developer "${developerName}"?`)) {
                document.getElementById(`delete-form-${developerId}`).submit();
            }
        });
    });
});
</script>
@endpush
