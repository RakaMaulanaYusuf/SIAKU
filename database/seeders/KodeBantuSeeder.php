<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KodeBantuSeeder extends Seeder
{
    public function run()
    {
        DB::table('kode_bantu')->insert([
            [
                'company_id' => 1,
                'company_period_id' => 1,      // Disesuaikan dengan company_id di CompaniesSeeder
                'helper_id' => '11',           // Sudah sesuai
                'name' => 'BANK BRI',          // Sudah sesuai
                'status' => 'PIUTANG',         // Sudah sesuai
                'balance' => 1000.00,          // Sudah sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 2,
                'company_period_id' => 3,      // Disesuaikan dengan company_id di CompaniesSeeder
                'helper_id' => '12',           // Sudah sesuai
                'name' => 'BANK MANDIRI',      // Sudah sesuai
                'status' => 'HUTANG',          // Sudah sesuai
                'balance' => 1000.00,          // Sudah sesuai
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}