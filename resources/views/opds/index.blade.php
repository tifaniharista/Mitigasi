@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1>
        <i class="fas fa-building me-2" style="color: #004882;"></i>
        <span class="" style="color: #004882; font-weight: 500; font-size: 23pt;">Manajemen OPD</span>
    </h1>

    @if(auth()->user()->isAdmin())
        <a href="{{ route('opds.create') }}" class="btn btn-primary shadow-sm"
           style="color: #004882; background-color: transparent; border-color: #004882; border-radius: 8px; padding: 0.60rem 1.2rem; border-width: 1.3px; font-weight: 600;"
           onmouseover="this.style.backgroundColor='#004882'; this.style.color='white';"
           onmouseout="this.style.backgroundColor='transparent'; this.style.color='#004882';">
            <i class="fas fa-plus me-2"></i> Tambah OPD
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center pt-3 pb-2 border-bottom">
        <h5 class="mb-0" style="color: #004882; font-weight:550; font-size: 16pt;">
            <i class="fas fa-list me-3"></i>Daftar OPD
        </h5>
        <div class="d-flex gap-2">
            <a href="{{ route('opds.index', ['status' => 'all']) }}"
                class="btn btn-sm btn-outline-secondary {{ request('status') === 'all' || !request('status') ? 'active' : '' }}">
                Semua
            </a>
            <a href="{{ route('opds.index', ['status' => 'active']) }}"
                class="btn btn-sm btn-outline-success {{ request('status') === 'active' ? 'active' : '' }}">
                Aktif
            </a>
            <a href="{{ route('opds.index', ['status' => 'inactive']) }}"
                class="btn btn-sm btn-outline-danger {{ request('status') === 'inactive' ? 'active' : '' }}">
                Nonaktif
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        @if($opds->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center text-uppercase text-muted small py-3">#</th>
                            <th class="text-uppercase text-muted small py-3">Nama</th>
                            <th width="15%" class="text-uppercase text-muted small py-3 text-center">Kode</th>
                            <th width="15%" class="text-uppercase text-muted small py-3 text-center">Jumlah Proyek</th>
                            <th width="15%" class="text-uppercase text-muted small py-3 text-center">Status</th>
                            <th width="20%" class="text-uppercase text-muted small py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($opds as $opd)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <span class="text-dark fw-medium">{{ $opd->name }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary px-3 py-2">{{ $opd->code }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info rounded-pill px-3 py-2">{{ $opd->projects->count() }}</span>
                            </td>
                            <td class="text-center">
                                @if($opd->is_active)
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3 py-2">
                                        <i class="fas fa-times-circle me-1"></i>Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('opds.edit', $opd->id) }}" class="btn btn-sm btn-warning btn-square" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if(auth()->user()->isAdmin())
                                        <button type="button" class="btn btn-sm btn-danger btn-square delete-opd-btn"
                                                title="Hapus"
                                                data-id="{{ $opd->id }}"
                                                data-name="{{ $opd->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-opd-form-{{ $opd->id }}"
                                              action="{{ route('opds.destroy', $opd->id) }}"
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-building fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak Ada OPD Ditemukan</h5>
                <p class="text-muted">Mulai dengan menambahkan OPD pertama Anda.</p>
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
    const deleteButtons = document.querySelectorAll('.delete-opd-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const opdId = this.getAttribute('data-id');
            const opdName = this.getAttribute('data-name');

            if (confirm(`Apakah Anda yakin ingin menghapus OPD "${opdName}"?`)) {
                document.getElementById(`delete-opd-form-${opdId}`).submit();
            }
        });
    });
});
</script>
@endpush
