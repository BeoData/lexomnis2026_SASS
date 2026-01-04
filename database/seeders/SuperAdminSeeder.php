<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@lexomnis.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('superadmin123'), // Change in production!
            ]
        );

        $this->command->info('Super Admin created:');
        $this->command->info('Email: superadmin@lexomnis.com');
        $this->command->info('Password: superadmin123');
        $this->command->warn('⚠️  Please change the password in production!');
    }
}
