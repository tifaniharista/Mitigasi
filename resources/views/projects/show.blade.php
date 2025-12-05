@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1>
        <i class="fas fa-eye me-2" style="color: #004882;"></i>
        <span style="color: #004882; font-weight: 500; font-size: 23pt;">Detail Project</span>
    </h1>
    <div class="d-flex gap-2">
        <a href="{{ route('projects.index') }}" class="btn btn-secondary shadow-sm"
           style="color: #004882; background-color: transparent; border-color: #004882; border-radius: 8px; padding: 0.60rem 1.2rem; border-width: 1.3px; font-weight: 600;"
           onmouseover="this.style.backgroundColor='#004882'; this.style.color='white';"
           onmouseout="this.style.backgroundColor='transparent'; this.style.color='#004882';">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>

        {{-- TOMBOL TUTUP PROJECT (HANYA ADMIN) --}}
        @if(auth()->user()->isAdmin())
            @if($project->is_closed)
                <form action="{{ route('projects.reopen', $project) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning shadow-sm"
                            style="border-radius: 8px; padding: 0.60rem 1.2rem; font-weight: 600;"
                            onclick="return confirm('Apakah Anda yakin ingin membuka kembali project ini?')">
                        <i class="fas fa-unlock me-1"></i> Buka Kembali
                    </button>
                </form>
            @else
                <a href="{{ route('projects.close.form', $project) }}" class="btn btn-danger shadow-sm"
                   style="border-radius: 8px; padding: 0.60rem 1.2rem; font-weight: 600;">
                    <i class="fas fa-lock me-1"></i> Tutup Project
                </a>
            @endif
        @endif
    </div>
</div>

<!-- Alert jika project ditutup -->
@if($project->is_closed)
<div class="alert alert-warning mb-4">
    <div class="d-flex align-items-center">
        <i class="fas fa-lock fa-lg me-3"></i>
        <div>
            <h6 class="mb-1 fw-bold">PROJECT SUDAH DITUTUP</h6>
            <p class="mb-0 small">
                Project ini sudah ditutup pada {{ $project->closed_at->format('d M Y H:i') }}
                oleh {{ $project->closedByUser->name ?? 'Admin' }}.
                Penambahan, pengeditan, dan penghapusan dokumen tidak dapat dilakukan.
            </p>
            @if($project->closure_reason)
                <p class="mb-0 mt-1 small">
                    <strong>Alasan Penutupan:</strong> {{ $project->closure_reason }}
                </p>
            @endif
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0" style="color: #004882; font-weight:550; font-size: 15pt;">
                    <i class="fas fa-info-circle me-2"></i> Informasi Project
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="35%" class="text-muted">Nama Project</th>
                                <td class="fw-semibold">{{ $project->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Developer</th>
                                <td class="fw-semibold">{{ $project->developer->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">OPD</th>
                                <td class="fw-semibold">{{ $project->opd ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="35%" class="text-muted">Tipe Pembangunan</th>
                                <td class="fw-semibold">{{ $project->construction_type ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tanggal Mulai</th>
                                <td class="fw-semibold">{{ $project->start_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tanggal Selesai</th>
                                <td class="fw-semibold">{{ $project->end_date->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Durasi Project</h6>
                            <p class="text-muted mb-0">
                                Total: {{ $project->start_date->diffInDays($project->end_date) }} hari
                                ({{ $project->start_date->format('d M Y') }} - {{ $project->end_date->format('d M Y') }})
                            </p>
                        </div>
                        <div class="col-md-6">
                            {{-- WARNING KETERLAMBATAN --}}
                            @if($project->is_overdue)
                                <div class="alert alert-danger border-start border-5 border-danger py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle fa-2x me-3 text-danger"></i>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-danger">PROJECT TERLAMBAT!</h6>
                                            <p class="mb-0 small">Project telah melebihi batas waktu:</p>
                                            <p class="mb-0 small">
                                            <strong>Batas Waktu:</strong> {{ $project->end_date->format('d M Y') }}<br>
                                            <strong>Akumulasi Keterlambatan:</strong> <span class="badge bg-danger">+{{ $project->overdue_days }} hari</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @elseif($project->actual_end_date && !$project->is_overdue)
                                <div class="alert alert-success border-start border-5 border-success py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-success">PROJECT TEPAT WAKTU</h6>
                                            <p class="mb-0 small">Project selesai sesuai jadwal.</p>
                                            <p class="mb-0 small">
                                                <strong>Selesai pada:</strong> {{ $project->actual_end_date->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @elseif($project->end_date->isPast() && !$project->actual_end_date)
                                <div class="alert alert-warning border-start border-5 border-warning py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock fa-2x me-3 text-warning"></i>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-warning">BELUM DILAPORKAN SELESAI</h6>
                                            <p class="mb-0 small">Batas waktu project telah lewat.</p>
                                            <p class="mb-0 small">
                                                <strong>Batas Waktu:</strong> {{ $project->end_date->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-primary border-start border-5 border-primary py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-spinner fa-2x me-3 text-primary"></i>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-primary">PROJECT SEDANG BERJALAN</h6>
                                            <p class="mb-0 small">Masih dalam rentang waktu project.</p>
                                            <p class="mb-0 small">
                                                <strong>Sisa Waktu:</strong> {{ now()->diffInDays($project->end_date) }} hari
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #004882;">
                    <div class="card-body text-center p-3">
                        <h4 class="text-primary mb-1">{{ $project->total_documents_count }}</h4>
                        <small class="text-muted">Total Dokumen</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #28a745;">
                    <div class="card-body text-center p-3">
                        <h4 class="text-success mb-1">{{ $project->verified_documents_count }}</h4>
                        <small class="text-muted">Terverifikasi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #ffc107;">
                    <div class="card-body text-center p-3">
                        <h4 class="text-warning mb-1">{{ $project->pending_documents_count }}</h4>
                        <small class="text-muted">Menunggu</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm" style="border-left: 4px solid #dc3545;">
                    <div class="card-body text-center p-3">
                        <h4 class="text-danger mb-1">{{ $project->rejected_documents_count }}</h4>
                        <small class="text-muted">Ditolak</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="card border-0 shadow-sm" style="margin-bottom: 20pt;">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: #004882; font-weight:550; font-size: 15pt;">
                    <i class="fas fa-file-pdf me-2"></i> Dokumen Project
                </h5>

                {{-- TOMBOL TAMBAH DOKUMEN HANYA JIKA PROJECT BELUM DITUTUP --}}
                @if((auth()->user()->isAdmin() || auth()->user()->isUploader()) && !$project->is_closed)
                    <a href="{{ route('jenis-dokumen.create') }}?project_id={{ $project->id }}"
                       class="btn btn-primary btn-sm"
                       style="color: white; background-color: #004882; border-color: #004882; border-radius: 6px; font-weight: 500;">
                        <i class="fas fa-plus me-1"></i>Tambah Dokumen
                    </a>
                @elseif((auth()->user()->isAdmin() || auth()->user()->isUploader()) && $project->is_closed)
                    <button class="btn btn-secondary btn-sm" disabled
                        style="border-radius: 6px; font-weight: 500; cursor: not-allowed;"
                        title="Project sudah ditutup. Tidak dapat menambah dokumen.">
                        <i class="fas fa-ban me-1"></i>Project Ditutup
                    </button>
                @endif
            </div>
            <div class="card-body">
                @php
                    $dokumenByTahapan = $project->dokumenByTahapan();
                @endphp

                @if($dokumenByTahapan->count() > 0)
                    @foreach($dokumenByTahapan as $tahapanName => $dokumenList)
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-folder me-2"></i> {{ $tahapanName }}
                            <span class="badge bg-primary">{{ $dokumenList->count() }} dokumen</span>
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="25%">Nama Dokumen</th>
                                        <th width="8%">Versi</th>
                                        <th width="15%">Status</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dokumenList as $dokumen)
                                    <tr>
                                        <td>
                                            <strong class="text-dark">{{ $dokumen->nama_dokumen }}</strong>
                                            @if($dokumen->keterangan)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($dokumen->keterangan, 50) }}</small>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-info">v{{ $dokumen->versi }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $dokumen->status_verifikasi_label['class'] }}">
                                                {{ $dokumen->status_verifikasi_label['label'] }}
                                            </span>
                                            @if($dokumen->catatan_verifikasi)
                                                <br>
                                                <small class="text-muted" title="{{ $dokumen->catatan_verifikasi }}">
                                                    {{ Str::limit($dokumen->catatan_verifikasi, 30) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons-spaced" role="group">
                                                <a href="{{ route('jenis-dokumen.show', $dokumen->id) }}"
                                                   class="btn btn-info btn-sm"
                                                   title="Lihat Detail"
                                                   style="border-radius: 6px;">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                {{-- Hanya tampilkan tombol edit jika project BELUM ditutup --}}
                                                @if((auth()->user()->isAdmin() || auth()->user()->isUploader()) && $dokumen->isPending() && !$project->is_closed)
                                                    <a href="{{ route('jenis-dokumen.edit', $dokumen->id) }}"
                                                       class="btn btn-warning btn-sm"
                                                       title="Edit"
                                                       style="border-radius: 6px;">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif

                                                {{-- Hanya tampilkan tombol hapus jika project BELUM ditutup --}}
                                                @if(auth()->user()->isAdmin() && $dokumen->isPending() && !$project->is_closed)
                                                    <form action="{{ route('jenis-dokumen.destroy', $dokumen->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-danger btn-sm"
                                                                title="Hapus"
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')"
                                                                style="border-radius: 6px;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Tombol verifikasi tetap aktif meskipun project ditutup --}}
                                                @if((auth()->user()->isAdmin() || auth()->user()->isVerificator()) && !$project->is_closed)
                                                    <button class="btn btn-sm
                                                        @if($dokumen->isVerified()) btn-success
                                                        @elseif($dokumen->isRejected()) btn-danger
                                                        @else btn-warning @endif"
                                                        title="@if($dokumen->isPending()) Verifikasi @else Update Verifikasi @endif"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#verifyModal{{ $dokumen->id }}"
                                                        style="border-radius: 6px;">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Verification Modal -->
                                            @if((auth()->user()->isAdmin() || auth()->user()->isVerificator()) && !$project->is_closed)
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
                                                        <form action="{{ route('jenis-dokumen.verify', $dokumen->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body">
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
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-pdf fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada dokumen ditemukan untuk project ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #004882;
    }
    .action-buttons-spaced .btn,
    .action-buttons-spaced .d-inline {
        margin-right: 5px;
        margin-bottom: 3px;
    }
    .action-buttons-spaced .btn:last-child,
    .action-buttons-spaced .d-inline:last-child,
    .action-buttons-spaced .d-inline:last-child .btn {
        margin-right: 0;
    }
    .card {
        border-radius: 10px;
    }
    .alert {
        border-radius: 10px;
        margin-bottom: 0;
    }
    .alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #721c24;
    }
    .alert-success {
        background-color: rgba(40, 167, 69, 0.1);
        color: #155724;
    }
    .alert-warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: #856404;
    }
    .alert-primary {
        background-color: rgba(0, 72, 130, 0.1);
        color: #004882;
    }
    .badge.bg-danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
    }
</style>
@endpush
