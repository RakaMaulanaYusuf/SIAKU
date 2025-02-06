<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'staff'
        ]);

        User::create([
            'name' => 'Viewer A',
            'email' => 'viewera@example.com',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'assigned_company_id' => 1,
            'active_company_id' => 1
        ]);
    }
}