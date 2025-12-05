<?php

namespace Database\Seeders;

use App\Models\Tahapan;
use Illuminate\Database\Seeder;

class TahapanSeeder extends Seeder
{
    public function run(): void
    {
        $tahapans = [
            [
                'nama_tahapan' => 'Perencanaan',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'nama_tahapan' => 'Persiapan',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'nama_tahapan' => 'Pelaksanaan',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'nama_tahapan' => 'Pengawasan',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'nama_tahapan' => 'Pelaporan',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'nama_tahapan' => 'Evaluasi',
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($tahapans as $tahapan) {
            Tahapan::firstOrCreate(
                ['nama_tahapan' => $tahapan['nama_tahapan']],
                $tahapan
            );
        }

        $this->command->info('Tahapan seeder completed!');
    }
}
