<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KodeBantuSeeder extends Seeder
{
    public function run()
    {
        DB::table('kode_bantu')->insert([
            // Kode Bantu untuk PIUTANG (terkait akun 1102: Piutang Usaha)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'helper_id' => 'K001', // Kode untuk Klien A
                'name' => 'Klien Jaya Abadi',
                'status' => 'PIUTANG',
                'balance' => 15000000.00, // Saldo awal piutang dari Klien Jaya Abadi
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'helper_id' => 'K002', // Kode untuk Klien B
                'name' => 'PT. Bintang Terang',
                'status' => 'PIUTANG',
                'balance' => 10000000.00, // Saldo awal piutang dari PT. Bintang Terang
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Total saldo awal PIUTANG = 15.000.000 + 10.000.000 = 25.000.000 (sesuai akun 1102)

            // Kode Bantu untuk HUTANG (terkait akun 2101: Utang Usaha)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'helper_id' => 'V001', // Kode untuk Vendor X
                'name' => 'PT. Supplier Sejahtera',
                'status' => 'HUTANG',
                'balance' => 20000000.00, // Saldo awal hutang ke Vendor X
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'helper_id' => 'V002', // Kode untuk Vendor Y
                'name' => 'CV. Jaya Furnitur',
                'status' => 'HUTANG',
                'balance' => 10000000.00, // Saldo awal hutang ke Vendor Y
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Total saldo awal HUTANG = 20.000.000 + 10.000.000 = 30.000.000 (sesuai akun 2101)

            // Contoh untuk perusahaan lain, sesuaikan jika Anda punya company_id 2 & period_id 3
            [
                'company_id' => 2,
                'company_period_id' => 3, // Disesuaikan dengan company_id di CompaniesSeeder
                'helper_id' => 'C001-B',
                'name' => 'Client Company B',
                'status' => 'PIUTANG',
                'balance' => 5000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}