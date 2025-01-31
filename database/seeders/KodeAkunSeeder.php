<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KodeAkunSeeder extends Seeder
{
    public function run()
    {
        DB::table('kode_akun')->insert([
            [
                'company_id' => 1,          // Sesuaikan dengan company_id di CompaniesSeeder
                'account_id' => '11',              // Sudah sesuai
                'name' => 'Kas',
                'helper_table' => '11',
                'balance_type' => 'DEBIT',         // Diubah dari DEBET ke DEBIT
                'report_type' => 'NERACA',         // Sudah sesuai
                'debit' => 1000.00,
                'credit' => null,                  // Diubah dari 0.00 ke null
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'account_id' => '12',
                'name' => 'Utang Dagang',
                'helper_table' => '12',
                'balance_type' => 'CREDIT',        // Diubah dari KREDIT ke CREDIT
                'report_type' => 'NERACA',
                'debit' => null,                   // Diubah dari 0.00 ke null
                'credit' => 1000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 2,
                'account_id' => '11',
                'name' => 'Modal Pemilik',
                'helper_table' => '11',
                'balance_type' => 'CREDIT',        // Diubah dari KREDIT ke CREDIT
                'report_type' => 'LABARUGI',       // Diubah dari LABA_RUGI ke LABARUGI
                'debit' => null,                   // Diubah dari 0.00 ke null
                'credit' => 1000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 2,
                'account_id' => '12',
                'name' => 'Pembelian',
                'helper_table' => '12',
                'balance_type' => 'DEBIT',         // Diubah dari DEBET ke DEBIT
                'report_type' => 'NERACA',
                'debit' => 1000.00,
                'credit' => null,                  // Diubah dari 0.00 ke null
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}