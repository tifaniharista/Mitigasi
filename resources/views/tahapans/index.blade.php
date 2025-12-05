@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1>
        <i class="fas fa-list-ol me-2" style="color: #004882;"></i>
        <span style="color: #004882; font-weight: 500; font-size: 23pt;">Manajemen Tahapan</span>
    </h1>

    @if(auth()->user()->isAdmin())
        <a href="{{ route('tahapans.create') }}" class="btn btn-primary shadow-sm"
           style="color: #004882; background-color: transparent; border-color: #004882; border-radius: 8px; padding: 0.60rem 1.2rem; border-width: 1.3px; font-weight: 600;"
           onmouseover="this.style.backgroundColor='#004882'; this.style.color='white';"
           onmouseout="this.style.backgroundColor='transparent'; this.style.color='#004882';">
            <i class="fas fa-plus me-2"></i> Tambah Tahapan
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center pt-3 pb-2 border-bottom">
        <h5 class="mb-0" style="color: #004882; font-weight:550; font-size: 16pt;">
            <i class="fas fa-list me-3"></i>Daftar Tahapan
        </h5>

        <div class="d-flex gap-2">
            <a href="{{ route('tahapans.index') }}?status=all"
               class="btn btn-sm btn-outline-secondary {{ request('status') == 'all' || !request('status') ? 'active' : '' }}">
                Semua
            </a>
            <a href="{{ route('tahapans.index') }}?status=active"
               class="btn btn-sm btn-outline-success {{ request('status') == 'active' ? 'active' : '' }}">
                Aktif
            </a>
            <a href="{{ route('tahapans.index') }}?status=inactive"
               class="btn btn-sm btn-outline-danger {{ request('status') == 'inactive' ? 'active' : '' }}">
                Nonaktif
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        @if($tahapans->count() > 0)
            @if(auth()->user()->isAdmin())
            <div class="alert alert-info d-flex align-items-center m-3 shadow-sm">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    Anda dapat mengubah urutan tahapan dengan menyeretnya. Urutan ini memengaruhi tampilannya dalam proyek.
                </div>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tahapanTable">
                    <thead class="table-light">
                        <tr>
                            <th width="10%" class="text-center text-uppercase text-muted small py-3">Urutan</th>
                            <th class="text-uppercase text-muted small py-3">Nama Tahapan</th>
                            <th width="15%" class="text-uppercase text-muted small py-3 text-center">Jumlah Dokumen</th>
                            <th width="15%" class="text-uppercase text-muted small py-3 text-center">Status</th>
                            <th width="15%" class="text-uppercase text-muted small py-3 text-center">Dibuat</th>
                            <th width="20%" class="text-uppercase text-muted small py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="sortable">
                        @foreach($tahapans as $tahapan)
                        <tr data-id="{{ $tahapan->id }}" class="{{ $tahapan->is_active ? '' : 'table-warning' }}">
                            <td class="text-center">
                                @if(auth()->user()->isAdmin())
                                    <i class="fas fa-arrows-alt handle text-muted" style="cursor: move;"></i>
                                    <span class="badge bg-secondary ms-1 px-3 py-2">{{ $tahapan->order }}</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">{{ $tahapan->order }}</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-dark">{{ $tahapan->nama_tahapan }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info px-3 py-2">{{ $tahapan->jenisDokumen->count() }}</span>
                            </td>
                            <td class="text-center">
                                @if($tahapan->is_active)
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger px-3 py-2">
                                        <i class="fas fa-times-circle me-1"></i>Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <small class="text-muted">{{ $tahapan->created_at->format('d M Y') }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('tahapans.edit', $tahapan->id) }}"
                                           class="btn btn-sm btn-warning btn-square" title="Ubah">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if(auth()->user()->isAdmin())
                                        <form action="{{ route('tahapans.toggle-status', $tahapan->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="btn btn-sm btn-square {{ $tahapan->is_active ? 'btn-danger' : 'btn-success' }}"
                                                    title="{{ $tahapan->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                    onclick="return confirm('Apakah Anda yakin ingin {{ $tahapan->is_active ? 'menonaktifkan' : 'mengaktifkan' }} tahapan ini?')">
                                                <i class="fas {{ $tahapan->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if(auth()->user()->isAdmin())
                                        <button type="button"
                                                class="btn btn-sm btn-danger btn-square delete-tahapan-btn"
                                                title="Hapus"
                                                data-id="{{ $tahapan->id }}"
                                                data-name="{{ $tahapan->nama_tahapan }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-tahapan-form-{{ $tahapan->id }}"
                                              action="{{ route('tahapans.destroy', $tahapan->id) }}"
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
                <i class="fas fa-list-ol fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak Ada Tahapan Ditemukan</h5>
                <p class="text-muted">Mulai dengan menambahkan tahapan pertama Anda.</p>
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

    /* Override untuk tombol toggle agar ikonnya tetap di tengah */
    .btn-square i.fas {
        margin: 0 !important;
    }

    /* Style untuk Drag & Drop */
    .handle {
        cursor: move;
        padding: 5px;
    }
    .handle:hover {
        color: #0d6efd !important;
    }
    .sortable-ghost {
        opacity: 0.6;
        background-color: #f8f9fa;
        border: 2px dashed #dee2e6;
    }
    .sortable-chosen {
        background-color: #e3f2fd !important;
        transform: rotate(1deg);
    }
    .sortable-drag {
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .table-warning {
        opacity: 0.7;
    }
</style>
@endpush

@if(auth()->user()->isAdmin())
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sortable = document.getElementById('sortable');

        if (sortable) {
            new Sortable(sortable, {
                handle: '.handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                animation: 150,

                onEnd: function(evt) {
                    const tahapans = [];
                    document.querySelectorAll('#sortable tr').forEach((row, index) => {
                        tahapans.push({
                            id: row.getAttribute('data-id'),
                            order: index + 1
                        });
                    });

                    // Update order via AJAX
                    fetch('{{ route("tahapans.update-order") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ tahapans: tahapans })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update order numbers in UI
                            document.querySelectorAll('#sortable tr').forEach((row, index) => {
                                const badge = row.querySelector('.badge.bg-secondary');
                                if (badge) {
                                    badge.textContent = index + 1;
                                }

                                // Update iteration number (kolom #)
                                const iterationCell = row.querySelector('td:nth-child(2)');
                                if (iterationCell) {
                                    iterationCell.textContent = index + 1;
                                }
                            });

                            // Show success message
                            showAlert('Urutan Tahapan berhasil diperbarui!', 'success');
                        } else {
                            throw new Error(data.message || 'Gagal memperbarui urutan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Gagal memperbarui urutan tahapan: ' + error.message, 'danger');

                        // Reload page to reset order
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    });
                }
            });
        }

        // Delete button functionality
        const deleteButtons = document.querySelectorAll('.delete-tahapan-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const tahapanId = this.getAttribute('data-id');
                const tahapanName = this.getAttribute('data-name');

                if (confirm(`Apakah Anda yakin ingin menghapus tahapan "${tahapanName}"?`)) {
                    document.getElementById(`delete-tahapan-form-${tahapanId}`).submit();
                }
            });
        });

        function showAlert(message, type) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert.alert-dismissible');
            existingAlerts.forEach(alert => alert.remove());

            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show m-3`;
            alert.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            // Insert after the filter section
            const cardBody = document.querySelector('.card-body');
            const tableResponsive = document.querySelector('.table-responsive');
            if (tableResponsive) {
                cardBody.insertBefore(alert, tableResponsive);
            } else {
                cardBody.appendChild(alert);
            }

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 5000);
        }
    });
</script>
@endpush
@endif
