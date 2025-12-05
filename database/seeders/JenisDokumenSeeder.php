<?php

namespace Database\Seeders;

use App\Models\JenisDokumen;
use App\Models\Project;
use App\Models\Tahapan;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class JenisDokumenSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();
        $tahapans = Tahapan::all();

        $dokumenTypes = [
            'Surat Permohonan',
            'Dokumen Perencanaan',
            'Gambar Teknik',
            'Rencana Anggaran Biaya',
            'Studi Kelayakan',
            'Analisis Dampak Lingkungan',
            'Laporan Progress',
            'Berita Acara',
            'Dokumen Pengawasan',
            'Laporan Akhir',
        ];

        foreach ($projects as $project) {
            foreach ($tahapans as $tahapan) {
                // Buat 1-3 dokumen per tahapan per project
                $numberOfDocuments = rand(1, 3);

                for ($i = 0; $i < $numberOfDocuments; $i++) {
                    $dokumenName = $dokumenTypes[array_rand($dokumenTypes)] . ' ' . ($i + 1);

                    $statusOptions = ['menunggu', 'diterima', 'ditolak'];
                    $statusVerifikasi = $statusOptions[array_rand($statusOptions)];

                    JenisDokumen::create([
                        'project_id' => $project->id,
                        'tahapan_id' => $tahapan->id,
                        'nama_dokumen' => $dokumenName,
                        'versi' => '1.0',
                        'tanggal_realisasi' => Carbon::now()->subDays(rand(1, 30)),
                        'tanggal_revisi' => rand(0, 1) ? Carbon::now()->subDays(rand(1, 15)) : null,
                        'keterangan' => 'Dokumen ' . $dokumenName . ' untuk project ' . $project->name,
                        'is_active' => true,
                        'status_verifikasi' => $statusVerifikasi,
                        'catatan_verifikasi' => $statusVerifikasi !== 'menunggu' ? 'Dokumen telah ' . $statusVerifikasi : null,
                        'tanggal_verifikasi' => $statusVerifikasi !== 'menunggu' ? Carbon::now()->subDays(rand(1, 10)) : null,
                        'verified_by' => $statusVerifikasi !== 'menunggu' ? 1 : null, // Admin sebagai verifier
                    ]);
                }
            }
        }

        $this->command->info('Jenis Dokumen seeder completed!');
    }
}
