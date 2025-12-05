@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 class="border-bottom pb-3 d-inline-block">
            <i class="fas fa-building me-2" style="color: #004882;"></i>
            <span style="color: #004882; font-weight: 500; font-size: 23pt;">Detail OPD</span>
        </h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-info-circle me-2"></i>Informasi Umum
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6 mb-3">
                        <p class="text-muted mb-1">Nama OPD:</p>
                        <h4 style="color: #004882;">{{ $opd->name }}</h4>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="text-muted mb-1">Kode OPD:</p>
                        <span class="badge bg-secondary fs-6">{{ $opd->code }}</span>
                    </div>
                </div>

                <div class="row text-center mt-3">
                    <div class="col-md-4 mb-3">
                        <p class="text-muted mb-1">Status:</p>
                        <span class="badge {{ $opd->is_active ? 'bg-success' : 'bg-danger' }} fs-6">
                            {{ $opd->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="text-muted mb-1">Total Proyek:</p>
                        <span class="badge bg-info fs-6">{{ $opd->projects->count() }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="text-muted mb-1">Dibuat Pada:</p>
                        <p class="mb-0">{{ $opd->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                @if($opd->description)
                <div class="mt-4 pt-3 border-top text-center">
                    <p class="text-muted mb-1">Deskripsi OPD:</p>
                    <p class="mb-0">{{ $opd->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-project-diagram me-2"></i>Proyek Terkait
                </h5>
            </div>
            <div class="card-body">
                @if($opd->projects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-muted small py-3">Nama Proyek</th>
                                    <th class="text-uppercase text-muted small py-3">Developer</th>
                                    <th class="text-uppercase text-muted small py-3">Tipe</th>
                                    <th class="text-uppercase text-muted small py-3">Tanggal Akhir</th>
                                    <th class="text-uppercase text-muted small py-3 text-center">Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($opd->projects as $project)
                                <tr>
                                    <td>
                                        <a href="{{ route('projects.show', $project->id) }}" class="text-decoration-none">
                                            {{ $project->name }}
                                        </a>
                                    </td>
                                    <td>{{ $project->developer->name }}</td>
                                    <td>{{ $project->construction_type }}</td>
                                    <td>{{ $project->end_date->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $project->total_documents_count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada proyek yang terkait dengan OPD ini.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('opds.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
