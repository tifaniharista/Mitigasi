@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 class="border-bottom pb-3 d-inline-block">
            <i class="fas fa-eye me-2" style="color: #004882;"></i>
            <span style="color: #004882; font-weight: 500; font-size: 23pt;">Detail Tahapan</span>
        </h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-info-circle me-2"></i>Informasi Tahapan
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <p class="text-muted mb-1">Nama Tahapan:</p>
                        <h4 style="color: #004882;">{{ $tahapan->nama_tahapan }}</h4>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="text-muted mb-1">Urutan:</p>
                        <span class="badge bg-secondary fs-6">{{ $tahapan->order }}</span>
                    </div>
                    <div class="col-md-4 mb-3">
                        <p class="text-muted mb-1">Status:</p>
                        <span class="badge {{ $tahapan->is_active ? 'bg-success' : 'bg-danger' }} fs-6">
                            {{ $tahapan->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>

                <div class="row text-center mt-3">
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Total Dokumen:</p>
                        <span class="badge bg-info fs-6">{{ $tahapan->jenisDokumen->count() }}</span>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1">Dibuat Pada:</p>
                        <p class="mb-0">{{ $tahapan->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-file-alt me-2"></i>Dokumen Terkait
                </h5>
            </div>
            <div class="card-body">
                @if($tahapan->jenisDokumen->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-muted small py-3">Nama Dokumen</th>
                                    <th class="text-uppercase text-muted small py-3">Proyek</th>
                                    <th class="text-uppercase text-muted small py-3">Versi</th>
                                    <th class="text-uppercase text-muted small py-3 text-center">Status Verifikasi</th>
                                    <th class="text-uppercase text-muted small py-3">Tanggal Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tahapan->jenisDokumen as $dokumen)
                                <tr>
                                    <td>
                                        <a href="{{ route('jenis-dokumen.show', $dokumen->id) }}" class="text-decoration-none">
                                            {{ $dokumen->nama_dokumen }}
                                        </a>
                                    </td>
                                    <td>{{ $dokumen->project->name }}</td>
                                    <td>
                                        <span class="badge bg-info">v{{ $dokumen->versi }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $dokumen->status_verifikasi_label['class'] }}">
                                            {{ $dokumen->status_verifikasi_label['label'] }}
                                        </span>
                                    </td>
                                    <td>{{ $dokumen->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada dokumen yang terkait dengan tahapan ini.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('tahapans.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
