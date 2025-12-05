<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $projects;
    protected $tahun;

    public function __construct($projects, $tahun = null)
    {
        $this->projects = $projects;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return $this->projects->load(['developer', 'jenisDokumen.tahapan', 'closedByUser']);
    }

    public function headings(): array
    {
        $headings = [
            'No',
            'Nama Proyek',
            'Developer',
            'OPD',
            'Tipe Konstruksi',
            'Tanggal Mulai',
            'Tanggal Selesai (Target)',
            'Tanggal Aktual Selesai',
            'Durasi Plan (Hari)',
            'Durasi Aktual (Hari)',
            'Keterlambatan (Hari)',
            'Status Penutupan',
            'Tanggal Penutupan',
            'Penutup Oleh',
            'Total Dokumen',
            'Dokumen Diterima',
            'Dokumen Ditolak',
            'Dokumen Menunggu',
            'Progress Overall (%)'
        ];

        // Tambahkan kolom untuk setiap tahapan
        $tahapans = \App\Models\Tahapan::where('is_active', true)->ordered()->get();
        foreach ($tahapans as $tahapan) {
            $headings[] = "Progress {$tahapan->nama_tahapan} (%)";
            $headings[] = "Dokumen {$tahapan->nama_tahapan} (Diterima/Total)";
        }

        $headings[] = 'Status';
        $headings[] = 'Status Detail';
        $headings[] = 'Tahapan Selesai';

        return $headings;
    }

    public function map($project): array
    {
        $progressByTahapan = $project->getProgressByTahapan();

        // Hitung durasi aktual
        $durasiAktual = 0;
        if ($project->actual_end_date) {
            $durasiAktual = $project->start_date->diffInDays($project->actual_end_date);
        } elseif ($project->end_date->isPast()) {
            $durasiAktual = $project->start_date->diffInDays($project->end_date);
        } else {
            $durasiAktual = $project->start_date->diffInDays(now());
        }

        $row = [
            $project->id,
            $project->name,
            $project->developer->name ?? '-',
            $project->opd,
            $project->construction_type,
            $project->start_date->format('d/m/Y'),
            $project->end_date->format('d/m/Y'),
            $project->actual_end_date ? $project->actual_end_date->format('d/m/Y') : '-',
            $project->start_date->diffInDays($project->end_date),
            $durasiAktual,
            $project->overdue_days,
            $project->is_closed ? 'Ditutup' : 'Aktif',
            $project->closed_at ? $project->closed_at->format('d/m/Y H:i') : '-',
            $project->closedByUser ? $project->closedByUser->name : '-',
            $project->total_documents_count,
            $project->verified_documents_count,
            $project->rejected_documents_count,
            $project->pending_documents_count,
            $project->overall_progress
        ];

        // Tambahkan data progress per tahapan
        $tahapans = \App\Models\Tahapan::where('is_active', true)->ordered()->get();
        foreach ($tahapans as $tahapan) {
            $progress = $progressByTahapan[$tahapan->nama_tahapan] ?? null;
            $row[] = $progress ? $progress['progress_percentage'] : 0;
            $row[] = $progress ? "{$progress['dokumen_terverifikasi']}/{$progress['total_dokumen']}" : "0/0";
        }

        $row[] = $this->getStatusText($project);
        $row[] = $project->detailed_status;
        $row[] = "{$project->completed_tahapan_count}/{$project->total_tahapan_count}";

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk header
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '004882']]
            ],
            // Style untuk data
            'A2:Z1000' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]
        ];
    }

    public function title(): string
    {
        return $this->tahun ? "Projects Tahun {$this->tahun}" : 'Semua Projects';
    }

    private function getStatusText($project)
    {
        if ($project->is_closed) {
            return 'Ditutup';
        } elseif ($project->end_date->isPast()) {
            return 'Selesai';
        } elseif ($project->start_date->isFuture()) {
            return 'Akan Dimulai';
        } else {
            return 'Berjalan';
        }
    }
}
