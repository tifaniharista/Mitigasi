<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Opd;
use App\Models\Developer;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $opds = Opd::all();
        $developers = Developer::all();

        $projects = [
            [
                'name' => 'Pembangunan Gedung Sekolah Dasar Negeri 01',
                'developer_id' => $developers->random()->id,
                'opd' => $opds->where('code', 'DISDIK')->first()->name,
                'construction_type' => 'Gedung Pendidikan',
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(6),
            ],
            [
                'name' => 'Rehabilitasi Jalan Utama Kota',
                'developer_id' => $developers->random()->id,
                'opd' => $opds->where('code', 'DPUPR')->first()->name,
                'construction_type' => 'Jalan dan Jembatan',
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addMonths(8),
            ],
            [
                'name' => 'Pembangunan Puskesmas Pembantu',
                'developer_id' => $developers->random()->id,
                'opd' => $opds->where('code', 'DINKES')->first()->name,
                'construction_type' => 'Gedung Kesehatan',
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->addMonths(4),
            ],
            [
                'name' => 'Peningkatan Drainase Perkotaan',
                'developer_id' => $developers->random()->id,
                'opd' => $opds->where('code', 'DPUPR')->first()->name,
                'construction_type' => 'Drainase',
                'start_date' => Carbon::now()->subMonths(4),
                'end_date' => Carbon::now()->addMonths(2),
            ],
            [
                'name' => 'Revitalisasi Taman Kota',
                'developer_id' => $developers->random()->id,
                'opd' => $opds->where('code', 'DLH')->first()->name,
                'construction_type' => 'Lanskap dan Taman',
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addMonths(3),
            ],
        ];

        foreach ($projects as $project) {
            Project::firstOrCreate(
                ['name' => $project['name']],
                $project
            );
        }

        $this->command->info('Project seeder completed!');
    }
}
