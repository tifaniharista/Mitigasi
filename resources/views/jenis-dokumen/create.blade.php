@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12 text-center">
        <h1 class="border-bottom pb-3 d-inline-block">
            <i class="fas fa-plus me-2" style="color: #004882;"></i>
            <span style="color: #004882; font-weight: 500; font-size: 23pt;">Tambah Dokumen Baru</span>
        </h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white text-center pt-3 pb-2">
                <h5 class="mb-0" style="color: #004882; font-weight:400; font-size: 16pt;">
                    <i class="fas fa-info-circle me-2"></i>Informasi Dokumen
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('jenis-dokumen.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                            <select class="form-select @error('project_id') is-invalid @enderror"
                                    id="project_id" name="project_id" required>
                                <option value="">Pilih Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }} - {{ $project->developer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tahapan_id" class="form-label">Tahapan <span class="text-danger">*</span></label>
                            <select class="form-select @error('tahapan_id') is-invalid @enderror"
                                    id="tahapan_id" name="tahapan_id" required>
                                <option value="">Pilih Tahapan</option>
                                @foreach($tahapans as $tahapan)
                                    <option value="{{ $tahapan->id }}" {{ old('tahapan_id') == $tahapan->id ? 'selected' : '' }}>
                                        {{ $tahapan->nama_tahapan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tahapan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nama_dokumen" class="form-label">Nama Dokumen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_dokumen') is-invalid @enderror"
                                   id="nama_dokumen" name="nama_dokumen" value="{{ old('nama_dokumen') }}"
                                   placeholder="Masukkan nama dokumen" required>
                            @error('nama_dokumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="versi" class="form-label">Versi</label>
                            <input type="text" class="form-control @error('versi') is-invalid @enderror"
                                   id="versi" name="versi" value="{{ old('versi') }}"
                                   placeholder="Auto-generated" readonly>
                            @error('versi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Versi akan di-generate otomatis</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tanggal_realisasi" class="form-label">Tanggal Realisasi</label>
                            <input type="date" class="form-control @error('tanggal_realisasi') is-invalid @enderror"
                                   id="tanggal_realisasi" name="tanggal_realisasi" value="{{ old('tanggal_realisasi') }}">
                            @error('tanggal_realisasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tanggal_revisi" class="form-label">Tanggal Revisi</label>
                            <input type="date" class="form-control @error('tanggal_revisi') is-invalid @enderror"
                                   id="tanggal_revisi" name="tanggal_revisi" value="{{ old('tanggal_revisi') }}">
                            @error('tanggal_revisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="file_dokumen" class="form-label">File Dokumen</label>
                            <input type="file" class="form-control @error('file_dokumen') is-invalid @enderror"
                                id="file_dokumen" name="file_dokumen"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
                                data-max-size="2048">
                            @error('file_dokumen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG (Maksimal: 2MB)
                            </div>
                            <div id="file_dokumen_error" class="text-danger small mt-1" style="display: none;"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="file_pendukung" class="form-label">File Pendukung</label>
                            <input type="file" class="form-control @error('file_pendukung') is-invalid @enderror"
                                id="file_pendukung" name="file_pendukung"
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip,.rar"
                                data-max-size="2048">
                            @error('file_pendukung')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Format: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, ZIP, RAR (Maksimal: 2MB)
                            </div>
                            <div id="file_pendukung_error" class="text-danger small mt-1" style="display: none;"></div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                      id="keterangan" name="keterangan" rows="3"
                                      placeholder="Masukkan informasi tambahan">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Dokumen Aktif
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top mt-4">
                        <a href="{{ route('jenis-dokumen.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validasi ukuran file
    function validateFileSize(input, errorElement) {
        const maxSize = input.getAttribute('data-max-size'); // dalam KB
        const file = input.files[0];

        if (file) {
            const fileSize = file.size / 1024; // Convert ke KB
            const maxSizeKB = parseInt(maxSize);

            if (fileSize > maxSizeKB) {
                errorElement.textContent = `Ukuran file (${fileSize.toFixed(2)}KB) melebihi batas maksimal ${maxSizeKB}KB (2MB)`;
                errorElement.style.display = 'block';
                input.value = ''; // Clear input
                return false;
            } else {
                errorElement.style.display = 'none';
                return true;
            }
        }
        return true;
    }

    // Validasi format file
    function validateFileFormat(input, errorElement, allowedFormats) {
        const file = input.files[0];

        if (file) {
            const fileName = file.name.toLowerCase();
            const isValidFormat = allowedFormats.some(format => fileName.endsWith(format));

            if (!isValidFormat) {
                errorElement.textContent = `Format file tidak didukung. Format yang diperbolehkan: ${allowedFormats.join(', ')}`;
                errorElement.style.display = 'block';
                input.value = ''; // Clear input
                return false;
            } else {
                errorElement.style.display = 'none';
                return true;
            }
        }
        return true;
    }

    // Setup validasi untuk file dokumen
    const fileDokumenInput = document.getElementById('file_dokumen');
    const fileDokumenError = document.getElementById('file_dokumen_error');
    const dokumenAllowedFormats = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.jpg', '.jpeg', '.png'];

    if (fileDokumenInput) {
        fileDokumenInput.addEventListener('change', function() {
            const isValidFormat = validateFileFormat(this, fileDokumenError, dokumenAllowedFormats);
            if (isValidFormat) {
                validateFileSize(this, fileDokumenError);
            }
        });
    }

    // Setup validasi untuk file pendukung
    const filePendukungInput = document.getElementById('file_pendukung');
    const filePendukungError = document.getElementById('file_pendukung_error');
    const pendukungAllowedFormats = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.jpg', '.jpeg', '.png', '.zip', '.rar'];

    if (filePendukungInput) {
        filePendukungInput.addEventListener('change', function() {
            const isValidFormat = validateFileFormat(this, filePendukungError, pendukungAllowedFormats);
            if (isValidFormat) {
                validateFileSize(this, filePendukungError);
            }
        });
    }

    // Validasi form sebelum submit
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Validasi file dokumen
            if (fileDokumenInput && fileDokumenInput.files.length > 0) {
                const isValidFormat = validateFileFormat(fileDokumenInput, fileDokumenError, dokumenAllowedFormats);
                const isValidSize = validateFileSize(fileDokumenInput, fileDokumenError);
                if (!isValidFormat || !isValidSize) {
                    isValid = false;
                    fileDokumenInput.focus();
                }
            }

            // Validasi file pendukung
            if (filePendukungInput && filePendukungInput.files.length > 0) {
                const isValidFormat = validateFileFormat(filePendukungInput, filePendukungError, pendukungAllowedFormats);
                const isValidSize = validateFileSize(filePendukungInput, filePendukungError);
                if (!isValidFormat || !isValidSize) {
                    isValid = false;
                    if (isValid) filePendukungInput.focus(); // Only focus if first validation passed
                }
            }

            if (!isValid) {
                e.preventDefault();
                alert('Terdapat error dalam upload file. Silakan periksa kembali file yang diupload.');
            }
        });
    }

    // Validasi tanggal (existing code)
    const realisasiDate = document.getElementById('tanggal_realisasi');
    const revisiDate = document.getElementById('tanggal_revisi');

    if (realisasiDate && !realisasiDate.readOnly) {
        realisasiDate.addEventListener('change', function() {
            if (this.value) {
                revisiDate.min = this.value;
            }
        });

        // Set min date for revisi if realisasi already has value
        if (realisasiDate.value) {
            revisiDate.min = realisasiDate.value;
        }
    }
});
</script>
@endpush
