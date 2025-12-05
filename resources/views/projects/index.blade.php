@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1>
        <i class="fas fa-project-diagram me-2" style="color: #004882;"></i>
        <span style="color: #004882; font-weight: 500; font-size: 23pt;">Manajemen Project</span>
    </h1>

    <div class="d-flex gap-2">
        <!-- Export Buttons - HANYA untuk ADMIN -->
        @if(auth()->user()->isAdmin() && $projects->count() > 0)
        <div class="dropdown">
            <button class="btn btn-success shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                style="border-radius: 8px; padding: 0.60rem 1.2rem; border-width: 1.3px; font-weight: 600;"
                onmouseover="this.style.backgroundColor='#218838'; this.style.color='white';"
                onmouseout="this.style.backgroundColor='#28a745'; this.style.color='white';">
                <i class="fas fa-download me-1"></i> Export Data
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item export-link" href="{{ route('projects.export.excel') }}{{ request('tahun') ? '?tahun=' . request('tahun') : '' }}">
                        <i class="fas fa-file-excel text-success me-2"></i> Export Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item export-link" href="{{ route('projects.export.pdf') }}{{ request('tahun') ? '?tahun=' . request('tahun') : '' }}">
                        <i class="fas fa-file-pdf text-danger me-2"></i> Export PDF
                    </a>
                </li>
            </ul>
        </div>
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isUploader())
            <a href="{{ route('projects.create') }}" class="btn btn-primary shadow-sm"
                style="color: #004882; background-color: transparent; border-color: #004882; border-radius: 8px; padding: 0.60rem 1.2rem; border-width: 1.3px; font-weight: 600;"
                onmouseover="this.style.backgroundColor='#004882'; this.style.color='white';"
                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#004882';">
                <i class="fas fa-plus me-1"></i> Tambah Project
            </a>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('export_success'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="fas fa-file-download me-2"></i>
    {{ session('export_success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h6 class="mb-0" style="color: #004882; font-weight:550;">
            <i class="fas fa-filter me-2"></i>Filter Pencarian
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('projects.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="tahun" class="form-label">Filter Berdasarkan Tahun</label>
                <select class="form-select" id="tahun" name="tahun">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> Cari
                </button>
                @if(request('tahun'))
                    <a href="{{ route('projects.index') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Info Hasil Pencarian -->
@if(request('tahun'))
<div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
        <i class="fas fa-info-circle me-2"></i>
        Menampilkan project untuk tahun <strong>{{ request('tahun') }}</strong>
        @if($projects->count() > 0)
            - Ditemukan {{ $projects->count() }} project
        @endif
    </div>
    <div>
        @if(auth()->user()->isAdmin() && $projects->count() > 0)
        <a href="{{ route('projects.export.excel') }}?tahun={{ request('tahun') }}" class="btn btn-sm btn-success me-2 export-link">
            <i class="fas fa-file-excel me-1"></i> Export Excel
        </a>
        <a href="{{ route('projects.export.pdf') }}?tahun={{ request('tahun') }}" class="btn btn-sm btn-danger export-link">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
        @endif
        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-info ms-2">
            Tampilkan Semua Project
        </a>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center pt-3 pb-2 border-bottom">
        <h5 class="mb-0" style="color: #004882; font-weight:550; font-size: 16pt;">
            <i class="fas fa-list me-2"></i>Daftar Project
            @if(request('tahun'))
                <small class="text-muted fs-6">(Tahun {{ request('tahun') }})</small>
            @endif
        </h5>
    </div>

    <div class="card-body p-0">
        @if($projects->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="projectsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center text-uppercase text-muted small py-3">#</th>
                            <th class="text-uppercase text-muted small py-3">Nama Proyek</th>
                            <th width="12%" class="text-uppercase text-muted small py-3">Developer</th>
                            <th width="12%" class="text-uppercase text-muted small py-3">OPD</th>
                            <th width="12%" class="text-uppercase text-muted small py-3">Tipe Konstruksi</th>
                            <th width="10%" class="text-uppercase text-muted small py-3">Tanggal Mulai</th>
                            <th width="10%" class="text-uppercase text-muted small py-3">Tanggal Selesai</th>
                            <th width="8%" class="text-uppercase text-muted small py-3 text-center">Progress</th>
                            <th width="10%" class="text-uppercase text-muted small py-3 text-center">Status</th>
                            <th width="16%" class="text-uppercase text-muted small py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                        @php
                            $progressByTahapan = $project->getProgressByTahapan();
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <strong class="text-dark">{{ $project->name }}</strong>
                                @if($project->start_date->year == request('tahun') || $project->end_date->year == request('tahun'))
                                    <span class="badge bg-info ms-1">Tahun {{ request('tahun') }}</span>
                                @endif
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-file me-1"></i>
                                    {{ $project->verified_documents_count }}/{{ $project->total_documents_count }} Dokumen
                                </small>
                                @if($project->is_closed)
                                    <br>
                                    <small class="text-dark">
                                        <i class="fas fa-lock me-1"></i>
                                        Project Ditutup
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="fw-medium">{{ $project->developer->name ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="fw-medium">{{ $project->opd ?? '-' }}</span>
                            </td>
                            <td>{{ $project->construction_type ?? '-' }}</td>
                            <td>
                                <span class="fw-medium">{{ $project->start_date->format('d M Y') }}</span>
                                <br>
                                <small class="text-muted">({{ $project->start_date->format('Y') }})</small>
                            </td>
                            <td>
                                <span class="fw-medium">{{ $project->end_date->format('d M Y') }}</span>
                                <br>
                                <small class="text-muted">({{ $project->end_date->format('Y') }})</small>
                            </td>
                            <td class="text-center">
                                <div class="progress" style="height: 20px; width: 80px; margin: 0 auto;">
                                    <div class="progress-bar
                                        @if($project->overall_progress >= 80) bg-success
                                        @elseif($project->overall_progress >= 50) bg-info
                                        @elseif($project->overall_progress >= 25) bg-warning
                                        @else bg-danger
                                        @endif"
                                        role="progressbar"
                                        style="width: {{ $project->overall_progress }}%"
                                        aria-valuenow="{{ $project->overall_progress }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                        {{ $project->overall_progress }}%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    {{ $project->completed_tahapan_count }}/{{ $project->total_tahapan_count }} Tahapan
                                </small>
                            </td>
                            <td class="text-center">
                                {{-- STATUS PENUTUPAN DAN KETERLAMBATAN --}}
                                @if($project->is_closed)
                                    <div class="mb-2">
                                        <span class="badge bg-dark p-2">
                                            <i class="fas fa-flag-checkered me-1"></i>
                                            DITUTUP
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted fw-bold">
                                            Final
                                        </small>
                                    </div>
                                @elseif($project->is_overdue)
                                    <div class="mb-2">
                                        <span class="badge bg-danger p-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            TERLAMBAT
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-danger fw-bold">
                                            +{{ $project->overdue_days }} hari
                                        </small>
                                    </div>
                                @elseif($project->actual_end_date && !$project->is_overdue)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        TEPAT WAKTU
                                    </span>
                                @elseif($project->end_date->isPast() && !$project->actual_end_date)
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>
                                        BELUM SELESAI
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="fas fa-spinner me-1"></i>
                                        BERJALAN
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('projects.show', $project->id) }}"
                                       class="btn btn-sm btn-info btn-square shadow-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isUploader())
                                        @if(!$project->is_closed)
                                            <a href="{{ route('projects.edit', $project->id) }}"
                                               class="btn btn-sm btn-warning btn-square shadow-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(auth()->user()->isAdmin())
                                            <button type="button"
                                                    class="btn btn-sm btn-danger btn-square shadow-sm delete-project-btn"
                                                    title="Hapus"
                                                    data-id="{{ $project->id }}"
                                                    data-name="{{ $project->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-project-form-{{ $project->id }}"
                                                  action="{{ route('projects.destroy', $project->id) }}"
                                                  method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    @endif
                                    <a href="{{ route('projects.timeline', $project) }}"
                                       class="btn btn-sm btn-primary btn-square shadow-sm" title="Timeline">
                                        <i class="fas fa-stream"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                @if(request('tahun'))
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak Ada Project Ditemukan</h5>
                    <p class="text-muted">Tidak ada project untuk tahun {{ request('tahun') }}</p>
                    <a href="{{ route('projects.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i> Lihat Semua Project
                    </a>
                @else
                    <i class="fas fa-project-diagram fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak Ada Project Ditemukan</h5>
                    <p class="text-muted">Mulai dengan menambahkan project pertama Anda.</p>
                    @if(auth()->user()->isAdmin() || auth()->user()->isUploader())
                        <a href="{{ route('projects.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Tambah Project Pertama
                        </a>
                    @endif
                @endif
            </div>
        @endif
    </div>

    <!-- Pagination atau Summary -->
    @if($projects->count() > 0)
    <div class="card-footer bg-white border-top">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small class="text-muted">
                    Menampilkan {{ $projects->count() }} project
                    @if(request('tahun'))
                        untuk tahun {{ request('tahun') }}
                    @endif
                </small>
            </div>
            <div class="col-md-6 text-end">
                @if(!request('tahun'))
                    <small class="text-muted">
                        Total: {{ $projects->count() }} project
                    </small>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Export Loading Modal -->
<div class="modal fade" id="exportLoadingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h6 class="text-primary">Mempersiapkan Export...</h6>
                <p class="text-muted small mb-0">Harap tunggu sebentar</p>
            </div>
        </div>
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
        font-size: 0.7em;
    }
    .badge.bg-dark {
        background: linear-gradient(135deg, #343a40, #212529);
        color: white;
    }
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-bar {
        font-size: 0.7rem;
        font-weight: 600;
    }
    .export-btn {
        transition: all 0.3s ease;
    }
    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-project-btn');
    const exportLinks = document.querySelectorAll('.export-link');
    const exportModal = new bootstrap.Modal(document.getElementById('exportLoadingModal'));

    // Handle delete buttons
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const projectId = this.getAttribute('data-id');
            const projectName = this.getAttribute('data-name');

            if (confirm(`Apakah Anda yakin ingin menghapus project "${projectName}"?`)) {
                document.getElementById(`delete-project-form-${projectId}`).submit();
            }
        });
    });

    // Handle export links with loading indicator
    exportLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            exportModal.show();

            // Hide modal after 3 seconds (fallback)
            setTimeout(() => {
                exportModal.hide();
            }, 3000);
        });
    });

    // Auto-submit form ketika tahun berubah (opsional)
    const tahunSelect = document.getElementById('tahun');
    if (tahunSelect) {
        tahunSelect.addEventListener('change', function() {
            if (this.value) {
                this.form.submit();
            }
        });
    }

    // Add hover effects to export buttons
    const exportBtns = document.querySelectorAll('.btn-success, .btn-danger');
    exportBtns.forEach(btn => {
        btn.classList.add('export-btn');
    });
});

// Show notification function
function showNotification(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' :
                      type === 'error' ? 'alert-danger' : 'alert-info';

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush
