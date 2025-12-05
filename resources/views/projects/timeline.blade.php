@extends('layouts.app')

@section('title', 'Project Timeline - ' . $project->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1>
        <i class="fas fa-stream me-2" style="color: #004882;"></i>
        <span style="color: #004882; font-weight: 500; font-size: 23pt;">Project Timeline</span>
    </h1>
    @if($project->is_closed)
        <span class="badge bg-dark p-2 fs-6">
            <i class="fas fa-lock me-1"></i> PROJECT DITUTUP
        </span>
    @endif
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0 text-primary">
                    <i class="fas fa-stream me-2"></i>Timeline Progress
                </h5>
            </div>
            <div class="card-body">
                <!-- Project Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Informasi Project</h6>
                        <p class="mb-2"><strong class="text-dark">Nama:</strong> {{ $project->name }}</p>
                        <p class="mb-2"><strong class="text-dark">Developer:</strong> {{ $project->developer->name ?? '-' }}</p>
                        <p class="mb-2"><strong class="text-dark">OPD:</strong> {{ $project->opd ?? '-' }}</p>

                        <!-- Status Keterlambatan -->
                        @if($project->is_overdue && !$project->is_closed)
                        <div class="mt-3 p-3 border rounded bg-danger bg-opacity-10">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-danger me-2 fa-lg"></i>
                                <div>
                                    <strong class="text-danger">PROJECT TERLAMBAT!</strong><br>
                                    <small class="text-muted">Keterlambatan: <strong>{{ $project->overdue_days }} hari</strong></small>
                                </div>
                            </div>
                        </div>
                        @elseif($project->actual_end_date && !$project->is_overdue && !$project->is_closed)
                        <div class="mt-3 p-3 border rounded bg-success bg-opacity-10">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2 fa-lg"></i>
                                <div>
                                    <strong class="text-success">SELESAI TEPAT WAKTU</strong><br>
                                    <small class="text-muted">Project diselesaikan sesuai jadwal target</small>
                                </div>
                            </div>
                        </div>
                        @elseif($project->end_date->isPast() && !$project->actual_end_date && !$project->is_closed)
                        <div class="mt-3 p-3 border rounded bg-warning bg-opacity-10">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-warning me-2 fa-lg"></i>
                                <div>
                                    <strong class="text-warning">BELUM DILAPORKAN SELESAI</strong><br>
                                    <small class="text-muted">
                                        Project telah melewati batas waktu namun belum ada laporan penyelesaian
                                    </small>
                                </div>
                            </div>
                        </div>
                        @elseif(!$project->is_closed)
                        <div class="mt-3 p-3 border rounded bg-primary bg-opacity-10">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-spinner text-primary me-2 fa-lg"></i>
                                <div>
                                    <strong class="text-primary">PROJECT SEDANG BERJALAN</strong><br>
                                    <small class="text-muted">
                                        Sisa waktu: {{ now()->diffInDays($project->end_date) }} hari
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Progress Keseluruhan</h6>
                        <div class="progress mb-2" style="height: 20px; border-radius: 10px;">
                            <div class="progress-bar
                                @if($project->is_closed) bg-dark
                                @elseif($project->overall_progress >= 80) bg-success
                                @elseif($project->overall_progress >= 50) bg-info
                                @elseif($project->overall_progress >= 25) bg-warning
                                @else bg-danger
                                @endif"
                                 role="progressbar"
                                 style="width: {{ $project->overall_progress }}%; border-radius: 10px;"
                                 aria-valuenow="{{ $project->overall_progress }}"
                                 aria-valuemin="0" aria-valuemax="100">
                                {{ $project->overall_progress }}%
                            </div>
                        </div>
                        <p class="mb-0 text-muted"><small>{{ $project->completed_tahapan_count }} dari {{ $project->total_tahapan_count }} tahapan selesai</small></p>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="timeline">
                    <!-- Event: Project Created -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary">
                            <i class="fas fa-play-circle text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-dark">Project Dimulai</h6>
                                <span class="badge bg-primary">Project Start</span>
                            </div>
                            <div class="timeline-body">
                                <p class="mb-2 text-muted">Project {{ $project->name }} resmi dimulai</p>
                            </div>
                            <div class="timeline-footer">
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $project->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Event: Dokumen per Tahapan -->
                    @php
                        $dokumenByTahapan = $project->dokumenByTahapan();
                    @endphp

                    @foreach($dokumenByTahapan as $tahapanName => $dokumenList)
                    <div class="timeline-item timeline-tahapan-container">
                        <div class="timeline-marker bg-secondary">
                            <i class="fas fa-folder text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <!-- Header Tahapan -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-1 text-dark">
                                        <i class="fas fa-folder me-2"></i>
                                        {{ $tahapanName }}
                                    </h6>
                                    <p class="mb-2 text-muted small">Kumpulan dokumen untuk tahap {{ $tahapanName }}</p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary">
                                        {{ $dokumenList->count() }} Dokumen
                                    </span>
                                    <div class="mt-1">
                                        <small class="text-muted">
                                            Progress:
                                            @php
                                                $verifiedCount = $dokumenList->where('status_verifikasi', 'diterima')->count();
                                                $progressPercentage = $dokumenList->count() > 0 ? round(($verifiedCount / $dokumenList->count()) * 100) : 0;
                                            @endphp
                                            {{ $progressPercentage }}%
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar Mini -->
                            <div class="progress mb-3" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ $progressPercentage }}%"></div>
                            </div>

                            <!-- Daftar Dokumen Ringkas -->
                            <div class="documents-list">
                                @foreach($dokumenList as $dokumen)
                                <div class="document-item border-bottom pb-2 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="text-dark small fw-semibold">
                                                    {{ $dokumen->nama_dokumen }}
                                                </span>
                                                <span class="badge bg-info ms-2">v{{ $dokumen->versi }}</span>
                                                <span class="badge bg-{{ $dokumen->status_verifikasi_label['class'] }} ms-1">
                                                    {{ $dokumen->status_verifikasi_label['label'] }}
                                                </span>
                                            </div>
                                            <div class="small text-muted">
                                                @if($dokumen->tanggal_realisasi)
                                                <span class="me-2">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $dokumen->tanggal_realisasi->format('d M Y') }}
                                                </span>
                                                @endif
                                                @if($dokumen->keterangan)
                                                <span>
                                                    <i class="fas fa-sticky-note me-1"></i>
                                                    {{ Str::limit($dokumen->keterangan, 30) }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <!-- Tombol Download -->
                                            <div class="btn-group btn-group-sm" role="group">
                                                @if($dokumen->file_dokumen)
                                                <a href="{{ route('jenis-dokumen.download-dokumen', $dokumen->id) }}"
                                                   class="btn btn-outline-primary btn-sm"
                                                   title="Download Dokumen Utama">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endif

                                                @if($dokumen->file_pendukung)
                                                <a href="{{ route('jenis-dokumen.download-pendukung', $dokumen->id) }}"
                                                   class="btn btn-outline-secondary btn-sm"
                                                   title="Download File Pendukung">
                                                    <i class="fas fa-paperclip"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Info Verifikator -->
                                    @if($dokumen->isVerified() && $dokumen->verifier)
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-user-check me-1"></i>
                                        Diverifikasi oleh {{ $dokumen->verifier->name }}
                                        @if($dokumen->tanggal_verifikasi)
                                            pada {{ $dokumen->tanggal_verifikasi->format('d M Y') }}
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                            <!-- Footer Tahapan -->
                            <div class="timeline-footer mt-3 pt-2 border-top">
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    Tahapan dimulai: {{ $dokumenList->first()->created_at->format('d M Y H:i') }} WIB
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Event: Project End Date -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-dark">
                            <i class="fas fa-flag-checkered text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-dark">Tanggal Target Selesai</h6>
                                <span class="badge bg-dark">Target Finish</span>
                            </div>
                            <div class="timeline-body">
                                <p class="mb-2 text-muted">Target penyelesaian project</p>
                            </div>
                            <div class="timeline-footer">
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $project->end_date->format('d M Y') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Event: Actual Completion (if exists) -->
                    @if($project->actual_end_date)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-dark">Tanggal Aktual Selesai</h6>
                                <span class="badge bg-success">Actual Finish</span>
                            </div>
                            <div class="timeline-body">
                                <p class="mb-2 text-muted">Project selesai secara aktual</p>
                            </div>
                            <div class="timeline-footer">
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $project->actual_end_date->format('d M Y') }}
                                    @if($project->is_overdue)
                                        <span class="badge bg-warning ms-2">+{{ $project->overdue_days }} hari</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Event: Project Closed (if closed) -->
                    @if($project->is_closed)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-dark">
                            <i class="fas fa-lock text-white"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-dark">Project Ditutup</h6>
                                <span class="badge bg-dark">Project Closed</span>
                            </div>
                            <div class="timeline-body">
                                <p class="mb-2 text-muted">Project telah difinalkan dan ditutup</p>
                                @if($project->closure_reason)
                                    <p class="mb-2 text-muted small">
                                        <strong>Alasan:</strong> {{ $project->closure_reason }}
                                    </p>
                                @endif
                            </div>
                            <div class="timeline-footer">
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $project->closed_at->format('d M Y H:i') }} WIB
                                    @if($project->closedByUser)
                                        oleh {{ $project->closedByUser->name }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Project Summary -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="card-title mb-0 text-primary">
                    <i class="fas fa-chart-pie me-2"></i>Ringkasan Project
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3" style="border-radius: 10px !important;">
                            <h4 class="text-primary mb-1">{{ $project->total_documents_count }}</h4>
                            <small class="text-muted">Total Dokumen</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3" style="border-radius: 10px !important;">
                            <h4 class="text-success mb-1">{{ $project->verified_documents_count }}</h4>
                            <small class="text-muted">Terverifikasi</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3" style="border-radius: 10px !important;">
                            <h4 class="text-warning mb-1">{{ $project->pending_documents_count }}</h4>
                            <small class="text-muted">Menunggu</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3" style="border-radius: 10px !important;">
                            <h4 class="text-danger mb-1">{{ $project->rejected_documents_count }}</h4>
                            <small class="text-muted">Ditolak</small>
                        </div>
                    </div>
                </div>

                <!-- Statistik Timeline -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="text-primary mb-3">Informasi Waktu</h6>
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Durasi Plan:</span>
                            <strong>{{ $project->start_date->diffInDays($project->end_date) }} hari</strong>
                        </div>
                        @if($project->actual_end_date)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Durasi Aktual:</span>
                            <strong>{{ $project->start_date->diffInDays($project->actual_end_date) }} hari</strong>
                        </div>
                        @endif
                        @if($project->is_overdue && !$project->is_closed)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-danger">Keterlambatan:</span>
                            <strong class="text-danger">{{ $project->overdue_days }} hari</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Efisiensi:</span>
                            <strong class="text-warning">
                                {{ number_format((($project->start_date->diffInDays($project->end_date) / $project->start_date->diffInDays($project->actual_end_date)) * 100), 1) }}%
                            </strong>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <span>Status:</span>
                            <strong class="text-{{ $project->is_closed ? 'dark' : ($project->detailed_status == 'Selesai Tepat Waktu' ? 'success' : ($project->is_overdue ? 'danger' : 'primary')) }}">
                                {{ $project->is_closed ? 'DITUTUP' : $project->detailed_status }}
                            </strong>
                        </div>

                        @if($project->is_closed)
                        <div class="d-flex justify-content-between mt-2">
                            <span>Status Final:</span>
                            <strong class="text-dark">
                                <i class="fas fa-lock me-1"></i> DITUTUP
                            </strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Time Display -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body text-center">
        <small class="text-muted">
            <i class="fas fa-clock me-1"></i>
            Waktu saat ini: <span id="currentTime">{{ now()->timezone('Asia/Jakarta')->format('d M Y H:i:s') }} WIB</span>
        </small>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -38px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 3px solid;
}

.timeline-header {
    margin-bottom: 10px;
}

.timeline-body {
    margin-bottom: 10px;
}

.timeline-footer {
    border-top: 1px solid #e9ecef;
    padding-top: 10px;
}

.document-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.documents-list {
    max-height: 400px;
    overflow-y: auto;
}

.card {
    border-radius: 10px;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.4rem;
    font-size: 0.75rem;
}

/* Warna untuk timeline marker */
.bg-primary { border-left-color: #007bff; }
.bg-success { border-left-color: #28a745; }
.bg-warning { border-left-color: #ffc107; }
.bg-danger { border-left-color: #dc3545; }
.bg-info { border-left-color: #17a2b8; }
.bg-dark { border-left-color: #343a40; }
.bg-secondary { border-left-color: #6c757d; }

/* Status indicator untuk project ditutup */
.badge.bg-dark {
    background: linear-gradient(135deg, #343a40, #212529);
    color: white;
}
</style>

<script>
// Real-time clock update
function updateCurrentTime() {
    const now = new Date();
    const options = {
        timeZone: 'Asia/Jakarta',
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    };

    const formatter = new Intl.DateTimeFormat('id-ID', options);
    const formattedDate = formatter.format(now).replace(/\./g, ':');

    document.getElementById('currentTime').textContent = formattedDate + ' WIB';
}

// Update time every second
setInterval(updateCurrentTime, 1000);

// Initial call
updateCurrentTime();

// Tampilkan pesan jika project ditutup
document.addEventListener('DOMContentLoaded', function() {
    @if($project->is_closed)
    console.log('Project ini telah ditutup. Status: FINAL');
    @endif
});
</script>
@endsection
