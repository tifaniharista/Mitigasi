<?php

namespace Database\Seeders;

use App\Models\Developer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeveloperSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel developers terlebih dahulu
        DB::table('developers')->truncate();

        // Set developer_id menjadi null di tabel projects untuk memutus relasi
        DB::table('projects')->update(['developer_id' => null]);

        $this->command->info('All developers data has been cleared!');
        $this->command->info('Developer seeder completed - table is now empty.');
    }
}
