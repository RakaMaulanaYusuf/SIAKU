<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus admin yang ada jika sudah ada
        User::where('email', 'admin@gmail.com')->delete();
        
        // Admin user
        User::create([
            'name' => 'yusuf',
            'email' => 'yusuf@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Staff users
        User::updateOrCreate(
            ['email' => 'rakamaulanayusuf@gmail.com'],
            [
                'name' => 'Raka Maulana Yusuf',
                'password' => Hash::make('password'),
                'role' => 'staff'
            ]
        );

        User::updateOrCreate(
            ['email' => 'raka@gmail.com'],
            [
                'name' => 'Raka',
                'password' => Hash::make('password'),
                'role' => 'staff'
            ]
        );

        // Viewer user (hanya jika ada company)
        // if (\App\Models\Company::count() > 0) {
        //     User::updateOrCreate(
        //         ['email' => 'majumundur@gmail.com'],
        //         [
        //             'name' => 'PT MAJU MUNDUR',
        //             'password' => Hash::make('password'),
        //             'role' => 'viewer',
        //             'assigned_company_id' => 1,
        //             'assigned_company_period_id' => 1
        //         ]
        //     );
        // }
    }
}