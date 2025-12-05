<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Projects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #004882;
            padding-bottom: 8px;
        }
        .header h1 {
            color: #004882;
            margin: 0;
            font-size: 16px;
        }
        .header .subtitle {
            color: #666;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }
        th {
            background-color: #004882;
            color: white;
            padding: 6px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 9px;
        }
        td {
            padding: 4px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 9px;
        }
        .project-name {
            font-weight: bold;
            color: #004882;
        }
        .progress-bar {
            background-color: #e0e0e0;
            border-radius: 2px;
            height: 12px;
            margin: 1px 0;
        }
        .progress-fill {
            background-color: #004882;
            height: 100%;
            border-radius: 2px;
            text-align: center;
            color: white;
            font-size: 8px;
            line-height: 12px;
        }
        .summary {
            margin-top: 15px;
            padding: 8px;
            background-color: #f8f9fa;
            border-left: 3px solid #004882;
            font-size: 10px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }
        .badge-dark { background-color: #343a40; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        .overdue-info {
            background-color: #fff3cd;
            border-left: 2px solid #ffc107;
            padding: 2px;
            margin: 2px 0;
            font-size: 8px;
        }
        .closure-info {
            background-color: #f8f9fa;
            border-left: 2px solid #343a40;
            padding: 2px;
            margin: 2px 0;
            font-size: 8px;
        }
        .row-highlight {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA PROJECT</h1>
        <div class="subtitle">
            @if($tahun)
                Tahun {{ $tahun }}
            @else
                Semua Project
            @endif
        </div>
        <div>Dibuat pada: {{ $exportDate }} | Oleh: {{ $exportedBy }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="12%">Nama Proyek</th>
                <th width="10%">Developer</th>
                <th width="8%">OPD</th>
                <th width="7%">Tipe Konstruksi</th>
                <th width="6%">Tanggal Mulai</th>
                <th width="6%">Tanggal Selesai (Target)</th>
                <th width="6%">Tanggal Aktual Selesai</th>
                <th width="5%">Durasi Plan (Hari)</th>
                <th width="5%">Durasi Aktual (Hari)</th>
                <th width="5%">Keterlambatan (Hari)</th>
                <th width="6%">Status Penutupan</th>
                <th width="6%">Tanggal Penutupan</th>
                <th width="7%">Penutup Oleh</th>
                <th width="7%">Progress</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $project)
            @php
                $progressByTahapan = $project->getProgressByTahapan();
                $status = $project->detailed_status;

                // Hitung durasi aktual
                $durasiAktual = 0;
                if ($project->actual_end_date) {
                    $durasiAktual = $project->start_date->diffInDays($project->actual_end_date);
                } elseif ($project->end_date->isPast()) {
                    $durasiAktual = $project->start_date->diffInDays($project->end_date);
                } else {
                    $durasiAktual = $project->start_date->diffInDays(now());
                }
            @endphp
            <tr @if($project->is_closed) class="row-highlight" @endif>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="project-name">{{ $project->name }}</td>
                <td>{{ $project->developer->name ?? '-' }}</td>
                <td>{{ $project->opd }}</td>
                <td>{{ $project->construction_type }}</td>
                <td>{{ $project->start_date->format('d/m/Y') }}</td>
                <td>{{ $project->end_date->format('d/m/Y') }}</td>
                <td>
                    {{ $project->actual_end_date ? $project->actual_end_date->format('d/m/Y') : '-' }}
                    @if($project->is_overdue)
                        <div class="overdue-info">
                            +{{ $project->overdue_days }} hari
                        </div>
                    @endif
                </td>
                <td class="text-center">{{ $project->start_date->diffInDays($project->end_date) }}</td>
                <td class="text-center">{{ $durasiAktual }}</td>
                <td class="text-center">
                    @if($project->overdue_days > 0)
                        <span class="badge badge-danger">{{ $project->overdue_days }} hari</span>
                    @else
                        <span class="badge badge-success">Tepat waktu</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($project->is_closed)
                        <span class="badge badge-dark">DITUTUP</span>
                    @else
                        <span class="badge badge-success">AKTIF</span>
                    @endif
                </td>
                <td>
                    @if($project->closed_at)
                        {{ $project->closed_at->format('d/m/Y H:i') }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $project->closedByUser ? $project->closedByUser->name : '-' }}</td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $project->overall_progress }}%">
                            {{ $project->overall_progress }}%
                        </div>
                    </div>
                    <small>{{ $project->completed_tahapan_count }}/{{ $project->total_tahapan_count }} Tahapan</small>
                </td>
                <td class="text-center">
                    @if($project->is_closed)
                        <span class="badge badge-dark">DITUTUP</span>
                    @elseif($status == 'Selesai Tepat Waktu')
                        <span class="badge badge-success">{{ $status }}</span>
                    @elseif($status == 'Selesai Terlambat')
                        <span class="badge badge-danger">{{ $status }}</span>
                    @elseif($status == 'Terlambat')
                        <span class="badge badge-danger">{{ $status }}</span>
                    @elseif($status == 'Berjalan')
                        <span class="badge badge-info">{{ $status }}</span>
                    @else
                        <span class="badge badge-warning">{{ $status }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($projects->count() > 0)
    <div class="summary">
        <h4>Ringkasan</h4>
        <p>Total Project: <strong>{{ $projects->count() }}</strong></p>
        <p>Project Aktif: <strong>{{ $projects->where('is_closed', false)->count() }}</strong></p>
        <p>Project Ditutup: <strong>{{ $projects->where('is_closed', true)->count() }}</strong></p>
        <p>Project Selesai Tepat Waktu: <strong>{{ $projects->filter(fn($p) => $p->detailed_status == 'Selesai Tepat Waktu')->count() }}</strong></p>
        <p>Project Selesai Terlambat: <strong>{{ $projects->filter(fn($p) => $p->detailed_status == 'Selesai Terlambat')->count() }}</strong></p>
        <p>Project Berjalan: <strong>{{ $projects->filter(fn($p) => $p->detailed_status == 'Berjalan' && !$p->is_closed)->count() }}</strong></p>
        <p>Project Terlambat: <strong>{{ $projects->filter(fn($p) => $p->detailed_status == 'Terlambat' && !$p->is_closed)->count() }}</strong></p>
        <p>Project Akan Dimulai: <strong>{{ $projects->filter(fn($p) => $p->detailed_status == 'Akan Dimulai' && !$p->is_closed)->count() }}</strong></p>
        <p>Rata-rata Progress: <strong>{{ round($projects->avg('overall_progress')) }}%</strong></p>
        <p>Total Keterlambatan: <strong>{{ $projects->sum('overdue_days') }} hari</strong></p>
    </div>
    @endif
</body>
</html>
