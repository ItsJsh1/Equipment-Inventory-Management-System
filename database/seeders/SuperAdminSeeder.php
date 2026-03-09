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
        // Create Super Admin user
        $superAdmin = User::create([
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'middlename' => null,
            'email' => 'superadmin@eims.local',
            'employee_id' => 'SA-001',
            'contact_number' => null,
            'password' => Hash::make('password123'),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $superAdmin->assignRole('super_admin');
    }
}
