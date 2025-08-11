<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if superadmin already exists
        $existingSuperAdmin = User::where('role', 'superadmin')->first();
        
        if (!$existingSuperAdmin) {
            User::create([
                'name' => 'Super Administrator',
                'email' => 'admin@academy.com',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('SuperAdmin account created successfully!');
            $this->command->info('Email: admin@academy.com');
            $this->command->info('Password: password123');
        } else {
            $this->command->info('SuperAdmin account already exists.');
        }
    }
}
