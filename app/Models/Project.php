<?php
// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'developer_id',
        'opd',
        'construction_type',
        'start_date',
        'end_date',
        'actual_end_date',
        'extension_reason',
        'extension_days',
        'is_closed',
        'closed_by',
        'closed_at',
        'closure_reason'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_end_date' => 'date',
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
    ];

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function opdRelation()
    {
        return $this->belongsTo(Opd::class, 'opd', 'name');
    }

    public function jenisDokumen()
    {
        return $this->hasMany(JenisDokumen::class);
    }

    public function dokumenByTahapan()
    {
        return $this->jenisDokumen()
            ->with('tahapan')
            ->get()
            ->groupBy(function($dokumen) {
                return $dokumen->tahapan->nama_tahapan;
            });
    }

    // Hanya dokumen yang sudah terverifikasi
    public function dokumenByTahapanVerified()
    {
        return $this->jenisDokumen()->verified()->with('tahapan')->get()->groupBy('tahapan.nama_tahapan');
    }

    // Hitung total dokumen
    public function getTotalDocumentsCountAttribute()
    {
        return $this->jenisDokumen()->count();
    }

    public function getVerifiedDocumentsCountAttribute()
    {
        return $this->jenisDokumen()->where('status_verifikasi', 'diterima')->count();
    }

    public function getIsOverdueAttribute()
    {
        if ($this->actual_end_date) {
            return $this->actual_end_date->gt($this->end_date);
        }

        return now()->gt($this->end_date);
    }

    public function getOverdueDaysAttribute()
    {
        if (!$this->is_overdue) {
            return 0;
        }

        $endDate = $this->actual_end_date ?: now();
        return $this->end_date->diffInDays($endDate);
    }

    public function canAddDocuments()
    {
        return !$this->is_closed;
    }

    public function canBeEdited()
    {
        return !$this->is_closed;
    }

    public function getDetailedStatusAttribute()
    {
        if ($this->actual_end_date) {
            if ($this->is_overdue) {
                return 'Selesai Terlambat';
            }
            return 'Selesai Tepat Waktu';
        }

        if ($this->end_date->isPast()) {
            return 'Terlambat';
        } elseif ($this->start_date->isFuture()) {
            return 'Akan Dimulai';
        } else {
            return 'Berjalan';
        }
    }

    public function extendProject($newEndDate, $reason)
    {
        $this->update([
            'actual_end_date' => $newEndDate,
            'extension_reason' => $reason,
            'extension_days' => $this->end_date->diffInDays($newEndDate)
        ]);
    }

    public function markAsCompleted($actualEndDate = null)
    {
        $completionDate = $actualEndDate ?: now();

        $this->update([
            'actual_end_date' => $completionDate,
            'extension_reason' => $this->is_overdue ? $this->extension_reason : null,
            'extension_days' => $this->is_overdue ? $this->overdue_days : 0
        ]);
    }

    // Method untuk menutup project
    public function closeProject($closedByUserId, $reason = null)
    {
        $this->update([
            'is_closed' => true,
            'closed_by' => $closedByUserId,
            'closed_at' => now(),
            'closure_reason' => $reason,
            'actual_end_date' => $this->actual_end_date ?: now()
        ]);
    }

    // Method untuk membuka kembali project
    public function reopenProject()
    {
        $this->update([
            'is_closed' => false,
            'closed_by' => null,
            'closed_at' => null,
            'closure_reason' => null
        ]);
    }

    // Accessor untuk mendapatkan user yang menutup project
    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // Method untuk mendapatkan status project dengan penutupan
    public function getFullStatusAttribute()
    {
        if ($this->is_closed) {
            return 'Ditutup';
        }

        return $this->detailed_status;
    }

    // Method untuk menghitung dokumen pending
    public function getPendingDocumentsCountAttribute()
    {
        return $this->jenisDokumen()->where('status_verifikasi', 'menunggu')->count();
    }

    // Method untuk menghitung dokumen ditolak
    public function getRejectedDocumentsCountAttribute()
    {
        return $this->jenisDokumen()->where('status_verifikasi', 'ditolak')->count();
    }

    // Method untuk mendapatkan progress dokumen berdasarkan tahapan
    public function getProgressByTahapan()
    {
        $tahapans = Tahapan::where('is_active', true)->ordered()->get();
        $progress = [];

        foreach ($tahapans as $tahapan) {
            $totalDokumen = $this->jenisDokumen()
                ->where('tahapan_id', $tahapan->id)
                ->count();

            $dokumenTerverifikasi = $this->jenisDokumen()
                ->where('tahapan_id', $tahapan->id)
                ->where('status_verifikasi', 'diterima')
                ->count();

            $progress[$tahapan->nama_tahapan] = [
                'tahapan' => $tahapan,
                'total_dokumen' => $totalDokumen,
                'dokumen_terverifikasi' => $dokumenTerverifikasi,
                'progress_percentage' => $totalDokumen > 0 ? round(($dokumenTerverifikasi / $totalDokumen) * 100) : 0,
                'is_completed' => $totalDokumen > 0 && $dokumenTerverifikasi == $totalDokumen
            ];
        }

        return $progress;
    }

    // Method untuk mendapatkan overall progress
    public function getOverallProgressAttribute()
    {
        $totalTahapan = Tahapan::where('is_active', true)->count();
        $progressByTahapan = $this->getProgressByTahapan();

        $completedTahapan = collect($progressByTahapan)->filter(function($item) {
            return $item['is_completed'];
        })->count();

        return $totalTahapan > 0 ? round(($completedTahapan / $totalTahapan) * 100) : 0;
    }

    // Method untuk mendapatkan jumlah tahapan yang sudah selesai
    public function getCompletedTahapanCountAttribute()
    {
        $progressByTahapan = $this->getProgressByTahapan();
        return collect($progressByTahapan)->filter(function($item) {
            return $item['is_completed'];
        })->count();
    }

    // Method untuk mendapatkan total tahapan aktif
    public function getTotalTahapanCountAttribute()
    {
        return Tahapan::where('is_active', true)->count();
    }
}
