<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CompaniesSeeder::class,
            CompanyPeriodSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            KodeAkunSeeder::class,
            KodeBantuSeeder::class,
            JurnalUmumSeeder::class
        ]);
    }
}