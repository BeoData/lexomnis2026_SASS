<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        // Note: Do NOT use Hash::make() here - the User model has 'password' => 'hashed' cast
        // which automatically hashes the password when setting it
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@lexomnis.com'],
            [
                'name' => 'Super Administrator',
                'password' => 'superadmin123', // Change in production!
                'role' => 'superadmin',
            ]
        );

        // Ensure existing superadmin has the correct role
        if ($superAdmin->role !== 'superadmin') {
            $superAdmin->update(['role' => 'superadmin']);
        }

        $this->command->info('Super Admin created:');
        $this->command->info('Email: superadmin@lexomnis.com');
        $this->command->info('Password: superadmin123');
        $this->command->warn('⚠️  Please change the password in production!');
    }
}
