<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesSeeder extends Seeder
{
    public function run()
    {
        DB::table('companies')->insert([
            [
                'name' => 'PT MAJU MUNDUR',
                'type' => 'Perdagangan Umum',
                'status' => 'Aktif',
                'period_month' => 'Januari',
                'period_year' => 2025,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT JAYA ABADI',
                'type' => 'Perdagangan',
                'status' => 'Aktif',
                'period_month' => 'Februari',
                'period_year' => 2024,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT MBOH RA URUS',
                'type' => 'Marketing',
                'status' => 'Nonaktif',
                'period_month' => 'Maret',
                'period_year' => 2023,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
