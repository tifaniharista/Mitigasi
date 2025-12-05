@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1>
        <i class="fas fa-file-pdf me-2" style="color: #004882;"></i>
        <span style="color: #004882; font-weight: 500; font-size: 23pt;">Manajemen Dokumen</span>
    </h1>

    @if(auth()->user()->isAdmin() || auth()->user()->isUploader())
        <a href="{{ route('jenis-dokumen.create') }}" class="btn btn-primary shadow-sm"
           style="color: #004882; background-color: transparent; border-color: #004882; border-radius: 8px; padding: 0.60rem 1.2rem; border-width: 1.3px; font-weight: 600;"
           onmouseover="this.style.backgroundColor='#004882'; this.style.color='white';"
           onmouseout="this.style.backgroundColor='transparent'; this.style.color='#004882';">
            <i class="fas fa-plus me-1"></i> Tambah Dokumen
        </a>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center pt-3 pb-2 border-bottom">
        <h5 class="mb-0" style="color: #004882; font-weight:550; font-size: 16pt;">
            <i class="fas fa-list me-2"></i>Daftar Dokumen
        </h5>
    </div>

    <div class="card-body p-0">
        @if($jenisDokumen->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="documentsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center text-uppercase text-muted small py-3">#</th>
                            <th class="text-uppercase text-muted small py-3">Nama Dokumen</th>
                            <th width="15%" class="text-uppercase text-muted small py-3">Project</th>
                            <th width="15%" class="text-uppercase text-muted small py-3">Tahapan</th>
                            <th width="10%" class="text-uppercase text-muted small py-3 text-center">Versi</th>
                            <th width="15%" class="text-uppercase text-muted small py-3 text-center">Status</th>
                            <th width="20%" class="text-uppercase text-muted small py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jenisDokumen as $dokumen)
                        <tr data-document-id="{{ $dokumen->id }}" class="document-row">
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <strong class="text-dark">{{ $dokumen->nama_dokumen }}</strong>
                                @if($dokumen->keterangan)
                                    <br>
                                    <small class="text-muted">{{ Str::limit($dokumen->keterangan, 50) }}</small>
                                @endif
                            </td>
                            <td>{{ $dokumen->project->name }}</td>
                            <td>{{ $dokumen->tahapan->nama_tahapan }}</td>
                            <td class="text-center">
                                <span class="badge bg-info px-3 py-2">v{{ $dokumen->versi }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $dokumen->status_verifikasi_label['class'] }} px-3 py-2 mb-1">
                                    {{ $dokumen->status_verifikasi_label['label'] }}
                                </span>
                                @if($dokumen->catatan_verifikasi)
                                    <br>
                                    <small class="text-muted" title="{{ $dokumen->catatan_verifikasi }}">
                                        {{ Str::limit($dokumen->catatan_verifikasi, 30) }}
                                    </small>
                                @endif
                                @if($dokumen->tanggal_verifikasi)
                                    <br>
                                    <small class="text-muted">
                                        {{ $dokumen->tanggal_verifikasi->format('d M Y') }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <!-- View Button untuk semua role -->
                                    <a href="{{ route('jenis-dokumen.show', $dokumen->id) }}"
                                       class="btn btn-sm btn-info btn-square shadow-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Edit Button hanya untuk Admin dan Uploader, dan hanya jika dokumen statusnya MENUNGGU -->
                                    @if((auth()->user()->isAdmin() || auth()->user()->isUploader()) && $dokumen->isPending())
                                        <a href="{{ route('jenis-dokumen.edit', $dokumen->id) }}"
                                           class="btn btn-sm btn-warning btn-square shadow-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    <!-- Delete Button hanya untuk Admin - BISA HAPUS DOKUMEN TERVERIFIKASI -->
                                    @if(auth()->user()->isAdmin())
                                        <button type="button"
                                                class="btn btn-sm btn-danger btn-square shadow-sm delete-document-btn"
                                                title="Hapus"
                                                data-id="{{ $dokumen->id }}"
                                                data-name="{{ $dokumen->nama_dokumen }}"
                                                @if($dokumen->isVerified())
                                                data-verified="true"
                                                @endif>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif

                                    <!-- Verify/Re-verify Button untuk Admin dan Verifikator -->
                                    @if(auth()->user()->isAdmin() || auth()->user()->isVerificator())
                                        <button class="btn btn-sm btn-square shadow-sm
                                            @if($dokumen->isVerified()) btn-success
                                            @elseif($dokumen->isRejected()) btn-danger
                                            @else btn-warning @endif"
                                            title="@if($dokumen->isPending()) Verifikasi @else Update Verifikasi @endif"
                                            data-bs-toggle="modal" data-bs-target="#verifyModal{{ $dokumen->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Verify Modal -->
                                @if(auth()->user()->isAdmin() || auth()->user()->isVerificator())
                                <div class="modal fade" id="verifyModal{{ $dokumen->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-primary">
                                                    @if($dokumen->isPending())
                                                        <i class="fas fa-check-circle me-2"></i>Verifikasi Dokumen
                                                    @else
                                                        <i class="fas fa-edit me-2"></i>Update Verifikasi Dokumen
                                                    @endif
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('jenis-dokumen.verify', $dokumen->id) }}" method="POST"
                                                  onsubmit="handleVerificationSubmit(event, {{ $dokumen->id }})">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-body">
                                                    <!-- Info Status Saat Ini -->
                                                    @if(!$dokumen->isPending())
                                                    <div class="alert alert-info">
                                                        <strong>Status Saat Ini:</strong>
                                                        {{ $dokumen->status_verifikasi_label['label'] }}
                                                        @if($dokumen->tanggal_verifikasi)
                                                            <br><small>Pada: {{ $dokumen->tanggal_verifikasi->format('d M Y H:i') }}</small>
                                                        @endif
                                                        @if($dokumen->verifier)
                                                            <br><small>Oleh: {{ $dokumen->verifier->name }}</small>
                                                        @endif
                                                    </div>
                                                    @endif

                                                    <div class="mb-3">
                                                        <label class="form-label">Status Verifikasi</label>
                                                        <select name="status_verifikasi" class="form-select" required>
                                                            <option value="diterima" {{ $dokumen->isVerified() ? 'selected' : '' }}>Diterima</option>
                                                            <option value="ditolak" {{ $dokumen->isRejected() ? 'selected' : '' }}>Ditolak</option>
                                                            <option value="menunggu" {{ $dokumen->isPending() ? 'selected' : '' }}>Menunggu</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Catatan Verifikasi</label>
                                                        <textarea name="catatan_verifikasi" class="form-control" rows="3" placeholder="Masukkan catatan verifikasi...">{{ old('catatan_verifikasi', $dokumen->catatan_verifikasi) }}</textarea>
                                                        <div class="form-text">Catatan sebelumnya akan diganti dengan yang baru.</div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        @if($dokumen->isPending())
                                                            Simpan Verifikasi
                                                        @else
                                                            Update Verifikasi
                                                        @endif
                                                    </button>
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
                <i class="fas fa-file-pdf fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak Ada Dokumen Ditemukan</h5>
                <p class="text-muted">Mulai dengan menambahkan dokumen pertama Anda.</p>
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
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #004882;
    }
    .badge {
        font-size: 0.75em;
    }
    .card {
        border-radius: 10px;
    }
    .document-row:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    // Notification handler untuk verifikasi
    function handleVerificationSubmit(event, documentId) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        // Show loading notification
        const loadingId = showNotification('Memproses verifikasi...', 'warning', 0);

        // Dapatkan modal instance untuk ditutup nanti
        const modalElement = document.getElementById('verifyModal' + documentId);
        const modal = bootstrap.Modal.getInstance(modalElement);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Hide loading notification
            notificationSystem.hide(loadingId);

            if (data.success) {
                showNotification(data.message, 'success');
                // Close modal
                if (modal) {
                    modal.hide();
                }
                // Reload page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            notificationSystem.hide(loadingId);
            console.error('Error:', error);

            // Jika error, coba submit form secara normal (non-AJAX)
            showNotification('Menggunakan metode standar...', 'info');
            form.submit();
        });
    }

    // Delete document handler
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-document-btn');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const documentId = this.getAttribute('data-id');
                const documentName = this.getAttribute('data-name');
                const isVerified = this.getAttribute('data-verified') === 'true';

                // Pesan konfirmasi berbeda untuk dokumen terverifikasi
                let confirmMessage = `Apakah Anda yakin ingin menghapus dokumen "${documentName}"?`;

                if (isVerified) {
                    confirmMessage = `PERINGATAN: Dokumen "${documentName}" sudah diverifikasi. Apakah Anda yakin ingin menghapusnya? Tindakan ini tidak dapat dibatalkan.`;
                }

                if (confirm(confirmMessage)) {
                    // Show loading notification
                    const loadingId = showNotification('Menghapus dokumen...', 'warning', 0);

                    // Buat form untuk delete
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/jenis-dokumen/${documentId}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        notificationSystem.hide(loadingId);

                        if (data.success) {
                            showNotification(data.message, 'success');
                            // Redirect ke halaman index setelah 1 detik
                            setTimeout(() => {
                                window.location.href = '{{ route("jenis-dokumen.index") }}';
                            }, 1000);
                        } else {
                            showNotification(data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        notificationSystem.hide(loadingId);
                        console.error('Error:', error);

                        // Jika AJAX gagal, submit form secara normal (akan redirect ke index)
                        showNotification('Menggunakan metode standar...', 'info');
                        form.submit();
                    })
                    .finally(() => {
                        // Hapus form dari DOM
                        document.body.removeChild(form);
                    });
                }
            });
        });
    });

    function exportToPDF() {
        const tahun = document.getElementById('tahun') ? document.getElementById('tahun').value : '';
        let url = '{{ route("projects.export.pdf") }}';

        if (tahun) {
            url += '?tahun=' + tahun;
        }

        window.location.href = url;
    }

    function exportToExcel() {
        const tahun = document.getElementById('tahun') ? document.getElementById('tahun').value : '';
        let url = '{{ route("projects.export.excel") }}';

        if (tahun) {
            url += '?tahun=' + tahun;
        }

        window.location.href = url;
    }
</script>
@endpush
