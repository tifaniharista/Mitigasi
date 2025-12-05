@extends('layouts.app')

@section('title', 'Project Timeline - Semua Project')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
    <h1>
        <i class="fas fa-stream me-2" style="color: #004882;"></i>
        <span style="color: #004882; font-weight: 500; font-size: 23pt;">Daftar Project Timeline</span>
    </h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom text-center">
        <i class="fas fa-info-circle me-2" style="color: #004882;"></i>
        <span style="color: #004882; font-weight: 500; font-size: 14pt;">Ringkasan</span>
    </div>
    <div class="card-body">
        @if($projects->count() > 0)
        <div class="mt-2">
            <div class="row">
                @foreach($projects as $project)
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-project-diagram me-2"></i>
                                        {{ Str::limit($project->name, 30) }}
                                    </h6>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if($project->is_closed)
                                    <span class="badge bg-dark">
                                        <i class="fas fa-lock me-1"></i> Ditutup
                                    </span>
                                    @endif
                                    <span class="badge bg-primary">{{ $project->overall_progress }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Warning sederhana untuk project yang ditutup -->
                            @if($project->is_closed)
                            <div class="alert alert-dark mb-3 py-2 text-center">
                                <i class="fas fa-lock me-2"></i>
                                <strong>Project sudah ditutup</strong>
                            </div>
                            @endif

                            @foreach($project->getProgressByTahapan() as $progress)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-dark">{{ $progress['tahapan']->nama_tahapan }}</span>
                                    <span class="small {{ $progress['is_completed'] ? 'text-success' : 'text-warning' }}">
                                        {{ $progress['progress_percentage'] }}%
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 10px;">
                                    <div class="progress-bar {{ $progress['is_completed'] ? 'bg-success' : 'bg-warning' }}"
                                         role="progressbar"
                                         style="width: {{ $progress['progress_percentage'] }}%; border-radius: 10px;"
                                         aria-valuenow="{{ $progress['progress_percentage'] }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted">
                                    {{ $progress['dokumen_terverifikasi'] }}/{{ $progress['total_dokumen'] }} dokumen
                                    @if($progress['is_completed'])
                                        <i class="fas fa-check text-success ms-1"></i>
                                    @endif
                                </small>
                            </div>
                            @endforeach
                        </div>
                        <div class="card-footer bg-white text-center border-top">
                            <a href="{{ route('projects.timeline', $project) }}"
                               class="btn btn-primary btn-sm"
                               style="color: white; background-color: #004882; border-color: #004882; border-radius: 6px; font-weight: 500;">
                                <i class="fas fa-stream me-1"></i>Lihat Timeline
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-project-diagram fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Tidak Ada Project Ditemukan</h5>
        </div>
        @endif
    </div>
</div>

<!-- Current Time Display -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body text-center">
        <small class="text-muted">
            <i class="fas fa-clock me-1"></i>
            Waktu saat ini: <span id="currentTimeGlobal">{{ now()->timezone('Asia/Jakarta')->format('d M Y H:i:s') }} WIB</span>
        </small>
    </div>
</div>

<style>
.progress {
    border-radius: 10px;
}
.card {
    border-radius: 10px;
}
.badge.bg-dark {
    background-color: #343a40;
    color: white;
}
.alert-dark {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    color: #343a40;
}
</style>

<script>
// Real-time clock update for global timeline
function updateCurrentTimeGlobal() {
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

    document.getElementById('currentTimeGlobal').textContent = formattedDate + ' WIB';
}

// Update time every second
setInterval(updateCurrentTimeGlobal, 1000);

// Initial call
updateCurrentTimeGlobal();
</script>
@endsection
