<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'email_verified_at' => Carbon::now(),
                'last_login_at' => Carbon::now()->subDays(2),
            ],
            [
                'name' => 'Executive User',
                'email' => 'executive@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_EXECUTIVE,
                'is_active' => true,
                'email_verified_at' => Carbon::now(),
                'last_login_at' => Carbon::now()->subDays(5),
            ],
            [
                'name' => 'Verificator User',
                'email' => 'verificator@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_VERIFICATOR,
                'is_active' => true,
                'email_verified_at' => Carbon::now(),
                'last_login_at' => Carbon::now()->subDays(1),
            ],
            [
                'name' => 'Uploader User',
                'email' => 'uploader@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_UPLOADER,
                'is_active' => true,
                'email_verified_at' => Carbon::now(),
                'last_login_at' => Carbon::now()->subHours(12),
            ],
            // TAMBAHKAN USER VIEWER
            [
                'name' => 'Viewer User',
                'email' => 'viewer@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_VIEWER,
                'is_active' => true,
                'status_verifikasi' => User::STATUS_APPROVED,
                'email_verified_at' => Carbon::now(),
                'last_login_at' => Carbon::now()->subHours(6),
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $this->command->info('User seeder completed!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Executive: executive@example.com / password');
        $this->command->info('Verificator: verificator@example.com / password');
        $this->command->info('Uploader: uploader@example.com / password');
        $this->command->info('Viewer: viewer@example.com / password');
    }
}
