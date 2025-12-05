@extends('layouts.app')

@section('content')
<div class="row mb-4 border-bottom pb-3">
    <div class="col-12 text-center">
        <h1 class="mb-3" style="color: #003159;">
            <i class="fas fa-chart-line me-2" style="color: #003159;"></i>Dashboard
        </h1>
        <div class="welcome-message" style="max-width: 800px; margin: 0 auto; padding: 0 15px;">
            @auth
                <p class="lead mb-2" style="font-size: 1.25rem; font-weight: 500; color: #003159;">
                    Selamat Datang, <span class="text-primary">{{ auth()->user()->name }}</span>
                    <span class="badge {{ auth()->user()->role_badge_class }} fw-bold fs-6 ms-2">
                        {{ auth()->user()->role_label }}
                    </span>
                </p>

                <div class="role-description" style="background-color: #f8f9fa; border-radius: 8px; padding: 15px;">
                    @if(auth()->user()->isAdmin())
                        <p class="text-muted mb-2 fs-6">
                            <i class="fas fa-crown me-2"></i>
                            Anda memiliki hak akses administratif penuh untuk manajemen sistem.
                        </p>
                    @elseif(auth()->user()->isExecutive())
                        <p class="text-muted mb-2 fs-6">
                            <i class="fas fa-chart-bar me-2"></i>
                            Anda memiliki akses untuk memonitor project dan dokumen secara keseluruhan.
                        </p>
                    @elseif(auth()->user()->isVerificator())
                        <p class="text-muted mb-2 fs-6">
                            <i class="fas fa-check-circle me-2"></i>
                            Anda berwenang untuk melakukan verifikasi dokumen.
                        </p>
                    @elseif(auth()->user()->isUploader())
                        <p class="text-muted mb-2 fs-6">
                            <i class="fas fa-upload me-2"></i>
                            Anda dapat mengunggah dan mengelola dokumen project.
                        </p>
                    @elseif(auth()->user()->isViewer())
                        <p class="text-muted mb-2 fs-6">
                            <i class="fas fa-building me-2"></i>
                            OPD/Organisasi: <strong>{{ auth()->user()->opd->name ?? 'Tidak tersedia' }}</strong>
                        </p>
                        <p class="text-muted mb-0 fs-6">
                            <i class="fas fa-eye me-2"></i>
                            Anda memiliki akses untuk melihat project dan dokumen dari OPD {{ auth()->user()->opd->name ?? 'Anda' }}
                        </p>
                    @endif
                </div>
            @else
                <p class="text-muted lead fw-light fs-5">
                    Selamat Datang di Aplikasi Mitigasi Kabupaten Gresik
                </p>
            @endauth
        </div>
    </div>
</div>

@auth
<!-- Statistics Cards untuk Semua Role -->
<div class="row mb-4">
    @if(auth()->user()->isAdmin() || auth()->user()->isExecutive())
    <!-- Admin & Executive Stats -->
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Total Projects</h6>
                        <h3 class="mb-0 text-dark">{{ $totalProjects ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-project-diagram fa-2x" style="color: #2b6bff;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Ongoing Projects</h6>
                        <h3 class="mb-0 text-dark">{{ $ongoingProjects ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-spinner fa-2x" style="color: #f3d700;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Completed Projects</h6>
                        <h3 class="mb-0 text-dark">{{ $completedProjects ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-check-circle fa-2x" style="color: #12c502;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Total Users</h6>
                        <h3 class="mb-0 text-dark">{{ $totalUsers ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-users fa-2x" style="color: #e74c3c;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Documents Card untuk Admin & Executive -->
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545; border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Dokumen Terlambat</h6>
                        <h3 class="mb-0 text-danger">{{ $overdueDocuments ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-exclamation-triangle fa-2x" style="color: #dc3545;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @elseif(auth()->user()->isVerificator())
    <!-- Verificator Stats -->
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Menunggu Verifikasi</h6>
                        <h3 class="mb-0 text-dark">{{ $pendingVerification ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-clock fa-2x" style="color: #f3d700;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Terverifikasi</h6>
                        <h3 class="mb-0 text-dark">{{ $verifiedDocuments ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-check-circle fa-2x" style="color: #12c502;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Ditolak</h6>
                        <h3 class="mb-0 text-dark">{{ $rejectedDocuments ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-times-circle fa-2x" style="color: #e74c3c;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Total Dokumen</h6>
                        <h3 class="mb-0 text-dark">{{ $totalDocuments ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-file-pdf fa-2x" style="color: #2b6bff;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Documents Card untuk Verificator -->
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545; border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Dokumen Terlambat</h6>
                        <h3 class="mb-0 text-danger">{{ $overdueDocuments ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-exclamation-triangle fa-2x" style="color: #dc3545;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @elseif(auth()->user()->isUploader())
    <!-- Uploader Stats -->
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Dokumen Saya</h6>
                        <h3 class="mb-0 text-dark">{{ $myDocuments ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-file-upload fa-2x" style="color: #2b6bff;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Menunggu Verifikasi</h6>
                        <h3 class="mb-0 text-dark">{{ $myPendingDocuments ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-clock fa-2x" style="color: #f3d700;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Terverifikasi</h6>
                        <h3 class="mb-0 text-dark">{{ $myVerifiedDocuments ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-check-circle fa-2x" style="color: #12c502;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Ditolak</h6>
                        <h3 class="mb-0 text-dark">{{ $myRejectedDocuments ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-times-circle fa-2x" style="color: #e74c3c;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Documents Card untuk Uploader -->
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545; border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Dokumen Terlambat</h6>
                        <h3 class="mb-0 text-danger">{{ $overdueDocuments ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-exclamation-triangle fa-2x" style="color: #dc3545;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @elseif(auth()->user()->isViewer())
    <!-- Viewer Stats -->
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Total Project OPD</h6>
                        <h3 class="mb-0 text-dark">{{ $opdProjectsCount ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-project-diagram fa-2x" style="color: #2b6bff;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Project Berjalan</h6>
                        <h3 class="mb-0 text-dark">{{ $ongoingProjectsCount ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-spinner fa-2x" style="color: #f3d700;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Project Selesai</h6>
                        <h3 class="mb-0 text-dark">{{ $completedProjectsCount ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-check-circle fa-2x" style="color: #12c502;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm h-100" style="border-radius: 15px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-1">Dokumen Tersedia</h6>
                        <h3 class="mb-0 text-dark">{{ $documentsCount ?? 0 }}</h3>
                    </div>
                    <div class="icon-shape p-3">
                        <i class="fas fa-file-pdf fa-2x" style="color: #e74c3c;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Overdue Documents Section (Untuk semua role kecuali Viewer) -->
@if(!auth()->user()->isViewer())
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom pt-3 pb-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0" style="color: #004882;">
                    <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                    Dokumen Melewati Batas Waktu
                    <span class="badge bg-danger ms-2">{{ $overdueDocuments ?? 0 }}</span>
                </h5>
                <a href="{{ route('jenis-dokumen.index') }}?filter=overdue" class="text-danger small fw-bold">
                    Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if(isset($overdueDocumentsList) && $overdueDocumentsList->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Nama Dokumen</th>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Project</th>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Tahapan</th>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Target Tanggal</th>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Keterlambatan</th>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Status</th>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdueDocumentsList as $doc)
                                <tr class="@if($doc->overdue_days > 30) table-danger @elseif($doc->overdue_days > 7) table-warning @elseif($doc->overdue_days > 0) bg-warning-subtle @endif">
                                    <td>
                                        <a href="{{ route('jenis-dokumen.show', $doc->id) }}" class="text-decoration-none fw-bold text-dark">
                                            {{ $doc->nama_dokumen }}
                                        </a>
                                        @if($doc->keterangan)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($doc->keterangan, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.show', $doc->project_id) }}" class="text-decoration-none">
                                            {{ $doc->project->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ $doc->tahapan->nama_tahapan ?? 'N/A' }}</td>
                                    <td>
                                        @if($doc->tanggal_realisasi)
                                            {{ \Carbon\Carbon::parse($doc->tanggal_realisasi)->format('d M Y') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($doc->overdue_days > 0)
                                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $doc->overdue_days }} hari
                                            </span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">Tepat waktu</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'diterima' => 'bg-success',
                                                'ditolak' => 'bg-danger',
                                                'menunggu' => 'bg-warning'
                                            ][$doc->status_verifikasi] ?? 'bg-secondary';

                                            $statusLabel = [
                                                'diterima' => 'Terverifikasi',
                                                'ditolak' => 'Ditolak',
                                                'menunggu' => 'Menunggu'
                                            ][$doc->status_verifikasi] ?? 'Unknown';
                                        @endphp
                                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('jenis-dokumen.show', $doc->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(auth()->user()->isAdmin() || auth()->user()->isVerificator())
                                            <button class="btn btn-sm btn-outline-warning"
                                                    title="Verifikasi"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#verifyModal{{ $doc->id }}">
                                                <i class="fas fa-check"></i>
                                            </button>
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
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h5 class="text-success">Tidak Ada Dokumen Terlambat</h5>
                        <p class="text-muted">Semua dokumen masih dalam batas waktu yang ditentukan.</p>
                    </div>
                @endif
            </div>
            @if(isset($overdueDocuments) && $overdueDocuments > 5)
            <div class="card-footer bg-white text-end pb-3 pt-3">
                <a href="{{ route('jenis-dokumen.index') }}?filter=overdue" class="text-danger small fw-bold">
                    Lihat {{ $overdueDocuments - 5 }} dokumen terlambat lainnya <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Charts Section -->
@if(auth()->user()->isAdmin() || auth()->user()->isExecutive())
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h5 class="card-title mb-0 text-center" style="color: #004882;">
                    <i class="fas fa-chart-pie me-2"></i>Status Project
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="projectStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h5 class="card-title mb-0 text-center" style="color: #004882;">
                    <i class="fas fa-chart-bar me-2"></i>Dokumen per Bulan
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="documentsMonthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if(auth()->user()->isViewer())
<!-- Project Timeline Chart untuk Viewer -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h5 class="card-title mb-0 text-center" style="color: #004882;">
                    <i class="fas fa-chart-bar me-2"></i>Timeline Project OPD {{ auth()->user()->opd->name ?? '' }}
                </h5>
            </div>
            <div class="card-body">
                @if(isset($chartData) && !empty($chartData['labels']))
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="projectTimelineChart"></canvas>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data project untuk ditampilkan dalam chart.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Projects Section -->
@if(auth()->user()->canAccessMenu('projects.index'))
<div class="row mt-4" style="margin-bottom: 20pt;">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom pt-3 pb-3">
                <h5 class="card-title mb-0 text-center" style="color: #004882;">
                    <i class="fas fa-history me-2"></i>
                    @if(auth()->user()->isViewer())
                    Project Terbaru OPD {{ auth()->user()->opd->name ?? '' }}
                    @else
                    Project Terbaru
                    @endif
                </h5>
            </div>
            <div class="card-body p-0">
                @if(isset($recentProjects) && $recentProjects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Nama Project</th>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Developer</th>
                                    @if(!auth()->user()->isViewer())
                                    <th class="text-uppercase text-secondary small fw-bold py-3">OPD</th>
                                    @endif
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Jenis Konstruksi</th>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Mulai</th>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Akhir</th>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Status</th>
                                    <th class="text-uppercase text-secondary small fw-bold py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProjects as $project)
                                <tr>
                                    <td>
                                        <a href="{{ route('projects.show', $project) }}" class="text-decoration-none fw-bold text-dark">
                                            {{ $project->name }}
                                        </a>
                                    </td>
                                    <td>{{ $project->developer->name ?? 'N/A' }}</td>
                                    @if(!auth()->user()->isViewer())
                                    <td>{{ $project->opdRelation->name ?? $project->opd }}</td>
                                    @endif
                                    <td>{{ $project->construction_type }}</td>
                                    <td>{{ $project->start_date->format('d M Y') }}</td>
                                    <td>{{ $project->end_date->format('d M Y') }}</td>
                                    <td>
                                        @if($project->end_date->isPast())
                                            <span class="badge bg-success rounded-pill px-3 py-2">Selesai</span>
                                        @else
                                            <span class="badge bg-primary rounded-pill px-3 py-2">Berlangsung</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Lihat
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum Ada Project</h5>
                        <p class="text-muted">
                            @if(auth()->user()->isViewer())
                            Tidak ada project yang tersedia untuk OPD {{ auth()->user()->opd->name ?? 'Anda' }}.
                            @else
                            Belum ada data project terbaru yang tersedia.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
            <div class="card-footer bg-white text-end pb-3 pt-3">
                <a href="{{ route('projects.index') }}" class="text-primary small fw-bold">
                    Lihat Semua Project <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endauth

<style>
.welcome-message .text-muted {
    font-size: 0.95rem;
    line-height: 1.5;
}

.welcome-message .badge {
    font-size: 0.85rem;
    padding: 0.5em 0.8em;
}

.role-description .text-muted {
    color: #495057 !important;
    margin-bottom: 0;
}

.card-stats {
    transition: transform 0.2s ease-in-out;
}

.card-stats:hover {
    transform: translateY(-2px);
}

.quick-action-btn {
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.table-danger {
    --bs-table-bg: rgba(220, 53, 69, 0.05);
}

.table-warning {
    --bs-table-bg: rgba(255, 193, 7, 0.05);
}

.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

@media (max-width: 768px) {
    .welcome-message .lead {
        font-size: 1.1rem;
    }

    .welcome-message .text-muted {
        font-size: 0.9rem;
    }

    .role-description {
        padding: 12px;
    }

    .row.mb-3 .col-md-3 {
        margin-bottom: 1rem !important;
    }
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart untuk Admin/Executive - Status Project
    @if((auth()->user()->isAdmin() || auth()->user()->isExecutive()) && isset($projectStatusData))
    const projectStatusCtx = document.getElementById('projectStatusChart').getContext('2d');
    const projectStatusChart = new Chart(projectStatusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($projectStatusData['labels']) !!},
            datasets: [{
                data: {!! json_encode($projectStatusData['data']) !!},
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    @endif

    // Chart untuk Admin/Executive - Dokumen per Bulan
    @if((auth()->user()->isAdmin() || auth()->user()->isExecutive()) && isset($documentsMonthlyData))
    const documentsMonthlyCtx = document.getElementById('documentsMonthlyChart').getContext('2d');
    const documentsMonthlyChart = new Chart(documentsMonthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($documentsMonthlyData['labels']) !!},
            datasets: [{
                label: 'Dokumen',
                data: {!! json_encode($documentsMonthlyData['data']) !!},
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Dokumen'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Bulan'
                    }
                }
            }
        }
    });
    @endif

    // Chart untuk Viewer - Project Timeline
    @if(auth()->user()->isViewer() && isset($chartData) && !empty($chartData['labels']))
    const viewerCtx = document.getElementById('projectTimelineChart').getContext('2d');
    const projectTimelineChart = new Chart(viewerCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: 'Jumlah Project',
                data: {!! json_encode($chartData['data']) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Distribusi Project Berdasarkan Timeline',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Project'
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Timeline'
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endpush
