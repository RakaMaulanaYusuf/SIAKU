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
            'name' => 'Ponidi',
            'email' => 'ponidi@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'staff'
        ]);

        User::create([
            'name' => 'PT MAJU MUNDUR',
            'email' => 'majumundur@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'active_company_id' => 1,
            'assigned_company_id' => 1
        ]);
    }
}