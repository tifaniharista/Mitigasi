@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 class="border-bottom pb-3 d-inline-block">
            <i class="fas fa-code-branch me-2" style="color: #004882;"></i>
            <span style="color: #004882; font-weight: 500; font-size: 23pt;">Detail Developer</span>
        </h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-info-circle me-2"></i>Informasi Dasar
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6 mb-3">
                        <p class="text-muted mb-1">Nama Developer:</p>
                        <h4 style="color: #004882;">{{ $developer->name }}</h4>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="text-muted mb-1">Status:</p>
                        <span class="badge {{ $developer->is_active ? 'bg-success' : 'bg-danger' }} fs-6">
                            {{ $developer->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>

                <div class="row text-center mt-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Dibuat Pada:</p>
                        <p class="mb-0">{{ $developer->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Terakhir Diperbarui:</p>
                        <p class="mb-0">{{ $developer->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-project-diagram me-2"></i>Proyek Terkait
                </h5>
            </div>
            <div class="card-body">
                @if($developer->projects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-muted small py-3">Nama Proyek</th>
                                    <th class="text-uppercase text-muted small py-3">OPD</th>
                                    <th class="text-uppercase text-muted small py-3">Tgl Mulai</th>
                                    <th class="text-uppercase text-muted small py-3">Tgl Selesai</th>
                                    <th class="text-uppercase text-muted small py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($developer->projects as $project)
                                <tr>
                                    <td>
                                        <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                            {{ $project->name }}
                                        </a>
                                    </td>
                                    <td>{{ $project->opd->name ?? '-' }}</td>
                                    <td>{{ $project->start_date->format('d M Y') }}</td>
                                    <td>{{ $project->end_date->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $project->end_date->isPast() ? 'bg-info' : 'bg-success' }}">
                                            {{ $project->end_date->isPast() ? 'Selesai' : 'Berjalan' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada proyek yang terkait dengan developer ini.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('developers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
