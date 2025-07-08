<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KodeAkunSeeder extends Seeder
{
    public function run()
    {
        DB::table('kode_akun')->insert([
            // ASET (Pos Saldo Normal: DEBIT)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '1101',
                'name' => 'Kas',
                'helper_table' => '1101', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'DEBIT',
                'report_type' => 'NERACA',
                'debit' => 75000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '1102',
                'name' => 'Piutang Usaha',
                'helper_table' => '1102', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'DEBIT',
                'report_type' => 'NERACA',
                'debit' => 25000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '1103',
                'name' => 'Persediaan Barang Dagang',
                'helper_table' => '1103', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'DEBIT',
                'report_type' => 'NERACA',
                'debit' => 40000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '1201',
                'name' => 'Peralatan Kantor',
                'helper_table' => '1201', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'DEBIT',
                'report_type' => 'NERACA',
                'debit' => 50000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '1202',
                'name' => 'Akumulasi Penyusutan Peralatan',
                'helper_table' => '1202', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'CREDIT',
                'report_type' => 'NERACA',
                'debit' => null,
                'credit' => 10000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // LIABILITAS (Pos Saldo Normal: KREDIT)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '2101',
                'name' => 'Utang Usaha',
                'helper_table' => '2101', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'CREDIT',
                'report_type' => 'NERACA',
                'debit' => null,
                'credit' => 30000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '2102',
                'name' => 'Utang Gaji',
                'helper_table' => '2102', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'CREDIT',
                'report_type' => 'NERACA',
                'debit' => null,
                'credit' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '2103',
                'name' => 'Pendapatan Diterima Dimuka',
                'helper_table' => '2103', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'CREDIT',
                'report_type' => 'NERACA',
                'debit' => null,
                'credit' => 5000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // EKUITAS (Pos Saldo Normal: KREDIT)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '3101',
                'name' => 'Modal Pemilik',
                'helper_table' => '3101', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'CREDIT',
                'report_type' => 'NERACA',
                'debit' => null,
                'credit' => 145000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // PENDAPATAN (Pos Saldo Normal: KREDIT)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '4101',
                'name' => 'Pendapatan Penjualan',
                'helper_table' => '4101', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'CREDIT',
                'report_type' => 'LABARUGI',
                'debit' => null,
                'credit' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '4102',
                'name' => 'Pendapatan Jasa',
                'helper_table' => '4102', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'CREDIT',
                'report_type' => 'LABARUGI',
                'debit' => null,
                'credit' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // HARGA POKOK PENJUALAN (Pos Saldo Normal: DEBIT)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '5001',
                'name' => 'Harga Pokok Penjualan',
                'helper_table' => '5001', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'DEBIT',
                'report_type' => 'LABARUGI',
                'debit' => 0.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // BEBAN (Pos Saldo Normal: DEBIT)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '6101',
                'name' => 'Beban Gaji Karyawan',
                'helper_table' => '6101', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'DEBIT',
                'report_type' => 'LABARUGI',
                'debit' => 0.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '6102',
                'name' => 'Beban Sewa Kantor',
                'helper_table' => '6102', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'DEBIT',
                'report_type' => 'LABARUGI',
                'debit' => 0.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '6103',
                'name' => 'Beban Listrik, Air & Telepon',
                'helper_table' => '6103', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'DEBIT',
                'report_type' => 'LABARUGI',
                'debit' => 0.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'account_id' => '6104',
                'name' => 'Beban Penyusutan Peralatan',
                'helper_table' => '6104', // <--- DIISI DENGAN ACCOUNT_ID SENDIRI SESUAI PERMINTAAN ANDA
                'balance_type' => 'DEBIT',
                'report_type' => 'LABARUGI',
                'debit' => 0.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}