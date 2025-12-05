<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisDokumen extends Model
{
    use HasFactory;

    protected $table = 'jenis_dokumen';

    protected $fillable = [
        'project_id',
        'tahapan_id',
        'nama_dokumen',
        'versi',
        'tanggal_realisasi',
        'tanggal_revisi',
        'file_dokumen',
        'file_pendukung',
        'keterangan',
        'is_active',
        'status_verifikasi',
        'catatan_verifikasi',
        'tanggal_verifikasi',
        'verified_by'
    ];

    protected $casts = [
        'tanggal_realisasi' => 'date',
        'tanggal_revisi' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'is_active' => 'boolean'
    ];

    protected $appends = [
        'nama_file_dokumen',
        'nama_file_pendukung',
        'status_verifikasi_label',
        'can_edit'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tahapan()
    {
        return $this->belongsTo(Tahapan::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scope untuk dokumen terverifikasi
    public function scopeVerified($query)
    {
        return $query->where('status_verifikasi', 'diterima');
    }

    // Scope untuk dokumen menunggu verifikasi
    public function scopePending($query)
    {
        return $query->where('status_verifikasi', 'menunggu');
    }

    // Scope untuk dokumen ditolak
    public function scopeRejected($query)
    {
        return $query->where('status_verifikasi', 'ditolak');
    }

    // Scope untuk dokumen yang bisa diedit
    public function scopeEditable($query)
    {
        return $query->where('status_verifikasi', '!=', 'diterima');
    }

    public function scopeOverdue($query)
    {
        return $query->where(function($q) {
            $q->where(function($sub) {
                // Jika ada tanggal_realisasi, cek apakah sudah lewat dari sekarang
                $sub->whereNotNull('tanggal_realisasi')
                    ->where('tanggal_realisasi', '<', now()->format('Y-m-d'));
            })->orWhere(function($sub) {
                // Atau jika ada tanggal_revisi, cek apakah sudah lewat dari sekarang
                $sub->whereNotNull('tanggal_revisi')
                    ->where('tanggal_revisi', '<', now()->format('Y-m-d'));
            });
        })->where('status_verifikasi', '!=', 'diterima'); // Dokumen yang belum terverifikasi
    }

    public function scopeOverdueWithTargetDate($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('tanggal_realisasi')
            ->where('tanggal_realisasi', '<', now()->format('Y-m-d'));
        })->where('status_verifikasi', '!=', 'diterima');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latestVersion = static::where('project_id', $model->project_id)
                ->where('nama_dokumen', $model->nama_dokumen)
                ->max('versi');

            if ($latestVersion) {
                $model->versi = (float)$latestVersion + 0.1;
            } else {
                $model->versi = '1.0';
            }
        });
    }

    public function getNamaFileDokumenAttribute()
    {
        if ($this->file_dokumen) {
            return basename($this->file_dokumen);
        }
        return null;
    }

    public function getNamaFilePendukungAttribute()
    {
        if ($this->file_pendukung) {
            return basename($this->file_pendukung);
        }
        return null;
    }

    public function isVerified()
    {
        return $this->status_verifikasi === 'diterima';
    }

    public function isPending()
    {
        return $this->status_verifikasi === 'menunggu';
    }

    public function isRejected()
    {
        return $this->status_verifikasi === 'ditolak';
    }

    public function getStatusVerifikasiLabelAttribute()
    {
        $statuses = [
            'menunggu' => ['label' => 'Menunggu', 'class' => 'warning'],
            'diterima' => ['label' => 'Terverifikasi', 'class' => 'success'],
            'ditolak' => ['label' => 'Ditolak', 'class' => 'danger']
        ];

        return $statuses[$this->status_verifikasi] ?? $statuses['menunggu'];
    }

    public function getCanEditAttribute()
    {
        // Dokumen tidak bisa diedit jika statusnya sudah diverifikasi (diterima)
        return !$this->isVerified();
    }

    // Method untuk mengecek apakah file dokumen bisa diubah
    public function canEditFileDokumen()
    {
        return !$this->isVerified() && !$this->file_dokumen;
    }

    // Method untuk mengecek apakah file pendukung bisa diubah
    public function canEditFilePendukung()
    {
        return !$this->isVerified() && !$this->file_pendukung;
    }
}
