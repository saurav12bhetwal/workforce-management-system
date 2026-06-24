<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('Admin');

        // Create Manager
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
        ]);
        $manager->assignRole('Manager');

        // Create Employee
        $employee = User::create([
            'name' => 'Employee User',
            'email' => 'employee@test.com',
            'password' => bcrypt('password'),
        ]);
        $employee->assignRole('Employee');
    }
}