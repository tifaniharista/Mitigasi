<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks untuk menghindari constraint error
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear data existing terlebih dahulu (optional)
        $this->truncateTables();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Jalankan seeders secara berurutan
        $this->call([
            UserSeeder::class,
            OpdSeeder::class,
            DeveloperSeeder::class,
            TahapanSeeder::class,
            ProjectSeeder::class,
            JenisDokumenSeeder::class,
        ]);

        $this->command->info('All seeders completed successfully! 🎉');
    }

    /**
     * Truncate all tables (optional - hati-hati di production)
     */
    protected function truncateTables(): void
    {
        $tables = [
            'jenis_dokumen',
            'projects',
            'tahapans',
            'developers',
            'opds',
            'users',
        ];

        foreach ($tables as $table) {
            // Gunakan delete() bukan truncate() untuk menghindari foreign key constraint
            DB::table($table)->delete();
            $this->command->info("Cleared table: {$table}");
        }

        // Reset auto increment
        foreach ($tables as $table) {
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
        }
    }
}
