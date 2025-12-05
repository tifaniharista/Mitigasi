@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1>
        <i class="fas fa-eye me-2" style="color: #004882;"></i>
        <span style="color: #004882; font-weight: 500; font-size: 23pt;">Detail Dokumen</span>
    </h1>
    <div>
        <!-- Tombol Verify/Update Verification -->
        @if(auth()->user()->isAdmin() || auth()->user()->isVerificator())
            <button class="btn
                @if($jenisDokuman->isVerified()) btn-success
                @elseif($jenisDokuman->isRejected()) btn-danger
                @else btn-warning @endif shadow-sm"
                title="@if($jenisDokuman->isPending()) Verifikasi @else Update Verifikasi @endif"
                data-bs-toggle="modal" data-bs-target="#verifyModal"
                style="color: white; border-radius: 8px; padding: 0.60rem 1.2rem; font-weight: 600;">
                <i class="fas fa-check me-1"></i>
                @if($jenisDokuman->isPending()) Verifikasi
                @else Update Verifikasi
                @endif
            </button>
        @endif

        <a href="{{ route('jenis-dokumen.index') }}" class="btn btn-secondary shadow-sm"
           style="color: #004882; background-color: transparent; border-color: #004882; border-radius: 8px; padding: 0.60rem 1.2rem; border-width: 1.3px; font-weight: 600;"
           onmouseover="this.style.backgroundColor='#004882'; this.style.color='white';"
           onmouseout="this.style.backgroundColor='transparent'; this.style.color='#004882';">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<!-- Verify Modal untuk halaman show -->
@if(auth()->user()->isAdmin() || auth()->user()->isVerificator())
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">
                    @if($jenisDokuman->isPending())
                        <i class="fas fa-check-circle me-2"></i>Verifikasi Dokumen
                    @else
                        <i class="fas fa-edit me-2"></i>Update Verifikasi Dokumen
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('jenis-dokumen.verify', $jenisDokuman->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <!-- Info Status Saat Ini -->
                    @if(!$jenisDokuman->isPending())
                    <div class="alert alert-info">
                        <strong>Status Saat Ini:</strong>
                        {{ $jenisDokuman->status_verifikasi_label['label'] }}
                        @if($jenisDokuman->tanggal_verifikasi)
                            <br><small>Pada: {{ $jenisDokuman->tanggal_verifikasi->format('d M Y H:i') }}</small>
                        @endif
                        @if($jenisDokuman->verifier)
                            <br><small>Oleh: {{ $jenisDokuman->verifier->name }}</small>
                        @endif
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Status Verifikasi</label>
                        <select name="status_verifikasi" class="form-select" required>
                            <option value="diterima" {{ $jenisDokuman->isVerified() ? 'selected' : '' }}>Diterima</option>
                            <option value="ditolak" {{ $jenisDokuman->isRejected() ? 'selected' : '' }}>Ditolak</option>
                            <option value="menunggu" {{ $jenisDokuman->isPending() ? 'selected' : '' }}>Menunggu</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Verifikasi</label>
                        <textarea name="catatan_verifikasi" class="form-control" rows="3" placeholder="Masukkan catatan verifikasi...">{{ old('catatan_verifikasi', $jenisDokuman->catatan_verifikasi) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        @if($jenisDokuman->isPending())
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

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <h5 class="card-title mb-0 text-primary">
            <i class="fas fa-info-circle me-2"></i>Informasi Dokumen
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%" class="text-muted">Project</th>
                        <td class="fw-semibold">
                            <strong class="text-dark">{{ $jenisDokuman->project->name }}</strong>
                            <br>
                            <small class="text-muted">Developer: {{ $jenisDokuman->project->developer->name }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tahapan</th>
                        <td class="fw-semibold">{{ $jenisDokuman->tahapan->nama_tahapan }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Nama Dokumen</th>
                        <td class="fw-semibold">{{ $jenisDokuman->nama_dokumen }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Versi</th>
                        <td><span class="badge bg-info">v{{ $jenisDokuman->versi }}</span></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted">Tanggal Realisasi</th>
                        <td class="fw-semibold">
                            @if($jenisDokuman->tanggal_realisasi)
                                {{ \App\Helpers\DateHelper::indonesianDateTime($jenisDokuman->tanggal_realisasi, false) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tanggal Revisi</th>
                        <td class="fw-semibold">
                            @if($jenisDokuman->tanggal_revisi)
                                {{ \App\Helpers\DateHelper::indonesianDateTime($jenisDokuman->tanggal_revisi, false) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status Aktif</th>
                        <td>
                            <span class="badge @if($jenisDokuman->is_active) bg-success @else bg-secondary @endif">
                                {{ $jenisDokuman->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status Verifikasi</th>
                        <td>
                            <span class="badge bg-{{ $jenisDokuman->status_verifikasi_label['class'] }}">
                                {{ $jenisDokuman->status_verifikasi_label['label'] }}
                            </span>
                            @if($jenisDokuman->catatan_verifikasi)
                                <br>
                                <small class="text-muted">{{ $jenisDokuman->catatan_verifikasi }}</small>
                            @endif
                            @if($jenisDokuman->tanggal_verifikasi)
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-check"></i>
                                    {{ $jenisDokuman->tanggal_verifikasi->format('d M Y H:i') }}
                                </small>
                            @endif
                            @if($jenisDokuman->verifier)
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i>
                                    Oleh: {{ $jenisDokuman->verifier->name }}
                                </small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">File Dokumen</th>
                        <td>
                            @if($jenisDokuman->file_dokumen)
                                <div class="mt-1">
                                    <a href="{{ route('jenis-dokumen.download-dokumen', $jenisDokuman->id) }}"
                                       class="btn btn-outline-secondary btn-sm" title="Download File">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                                <br>
                                <small class="text-muted">{{ $jenisDokuman->nama_file_dokumen }}</small>
                            @else
                                <span class="text-muted">Tidak ada file</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">File Pendukung</th>
                        <td>
                            @if($jenisDokuman->file_pendukung)
                                <div class="mt-1">
                                    <a href="{{ route('jenis-dokumen.download-pendukung', $jenisDokuman->id) }}"
                                       class="btn btn-outline-secondary btn-sm" title="Download File">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                                <br>
                                <small class="text-muted">{{ $jenisDokuman->nama_file_pendukung }}</small>
                            @else
                                <span class="text-muted">Tidak ada file</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if($jenisDokuman->keterangan)
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="text-primary mb-3">Keterangan</h6>
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <p class="mb-0">{{ $jenisDokuman->keterangan }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row mt-4 pt-3 border-top">
            <div class="col-md-6">
                <small class="text-muted">
                    <i class="fas fa-calendar-plus"></i> Dibuat: {{ $jenisDokuman->created_at->format('d M Y H:i') }} WIB
                </small>
            </div>
            <div class="col-md-6 text-end">
                <small class="text-muted">
                    <i class="fas fa-calendar-check"></i> Diupdate: {{ $jenisDokuman->updated_at->format('d M Y H:i') }} WIB
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 10px;
    }
    .table th {
        background-color: transparent;
    }
</style>
@endpush

@push('scripts')
<script>
// Handler untuk verifikasi di halaman show
document.addEventListener('DOMContentLoaded', function() {
    const verifyForm = document.querySelector('#verifyModal form');
    if (verifyForm) {
        verifyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            // Show loading notification
            const loadingId = showNotification('Memproses verifikasi...', 'warning', 0);

            // Dapatkan modal instance
            const modalElement = document.getElementById('verifyModal');
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
        });
    }
});
</script>
@endpush
