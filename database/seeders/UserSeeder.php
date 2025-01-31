<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Ponidi',
            'email' => 'ponidiAdmin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Create customer user
        $customer = User::create([
            'name' => 'Eri Pras',
            'email' => 'eriprasCustomer@gmail.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer'
        ]);

        // Assign roles
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();

        $admin->roles()->attach($adminRole);
        $customer->roles()->attach($customerRole);
    }
}