<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    public function run(): void
    {
        $opds = [
            [
                'name' => 'Dinas Pekerjaan Umum dan Penataan Ruang',
                'code' => 'DPUPR',
                'description' => 'Dinas yang menangani pekerjaan umum dan penataan ruang',
                'is_active' => true,
            ],
            [
                'name' => 'Dinas Kesehatan',
                'code' => 'DINKES',
                'description' => 'Dinas yang menangani bidang kesehatan',
                'is_active' => true,
            ],
            [
                'name' => 'Dinas Pendidikan',
                'code' => 'DISDIK',
                'description' => 'Dinas yang menangani bidang pendidikan',
                'is_active' => true,
            ],
            [
                'name' => 'Dinas Perhubungan',
                'code' => 'DISHUB',
                'description' => 'Dinas yang menangani bidang perhubungan',
                'is_active' => true,
            ],
            [
                'name' => 'Dinas Lingkungan Hidup',
                'code' => 'DLH',
                'description' => 'Dinas yang menangani bidang lingkungan hidup',
                'is_active' => true,
            ],
        ];

        foreach ($opds as $opd) {
            Opd::firstOrCreate(
                ['code' => $opd['code']],
                $opd
            );
        }

        $this->command->info('OPD seeder completed!');
    }
}
