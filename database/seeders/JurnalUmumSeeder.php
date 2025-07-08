<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurnalUmumSeeder extends Seeder
{
    public function run()
    {
        DB::table('jurnal_umum')->insert([
            // 1. Pembelian Persediaan secara Kredit dari PT. Supplier Sejahtera (V001)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-03', // Tanggal di awal bulan
                'transaction_proof' => 'PO-MB-001',
                'description' => 'Pembelian persediaan secara kredit dari PT. Supplier Sejahtera',
                'account_id' => '1103', // Persediaan Barang Dagang (DEBIT)
                'helper_id' => null, // Persediaan tidak pakai kode bantu klien/vendor
                'debit' => 15000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-03',
                'transaction_proof' => 'PO-MB-001',
                'description' => 'Pembelian persediaan secara kredit dari PT. Supplier Sejahtera',
                'account_id' => '2101', // Utang Usaha (KREDIT)
                'helper_id' => 'V001', // Kode Bantu: PT. Supplier Sejahtera
                'debit' => null,
                'credit' => 15000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 2. Pembayaran Gaji Karyawan Bulan Mei (yang menjadi beban di bulan Juni)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-05',
                'transaction_proof' => 'BYR-GJ-001',
                'description' => 'Pembayaran gaji karyawan bulan Mei',
                'account_id' => '6101', // Beban Gaji Karyawan (DEBIT)
                'helper_id' => null,
                'debit' => 10000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-05',
                'transaction_proof' => 'BYR-GJ-001',
                'description' => 'Pembayaran gaji karyawan bulan Mei',
                'account_id' => '1101', // Kas (KREDIT)
                'helper_id' => null,
                'debit' => null,
                'credit' => 10000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 3. Pendapatan Jasa dari Klien Jaya Abadi (K001) secara kredit
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-10',
                'transaction_proof' => 'INV-JA-001',
                'description' => 'Pendapatan jasa desain grafis kepada Klien Jaya Abadi',
                'account_id' => '1102', // Piutang Usaha (DEBIT)
                'helper_id' => 'K001', // Kode Bantu: Klien Jaya Abadi
                'debit' => 20000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-10',
                'transaction_proof' => 'INV-JA-001',
                'description' => 'Pendapatan jasa desain grafis kepada Klien Jaya Abadi',
                'account_id' => '4102', // Pendapatan Jasa (KREDIT)
                'helper_id' => null,
                'debit' => null,
                'credit' => 20000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 4. Pembayaran Sewa Kantor
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-15',
                'transaction_proof' => 'BYR-SEWA-001',
                'description' => 'Pembayaran sewa kantor bulan Juni',
                'account_id' => '6102', // Beban Sewa Kantor (DEBIT)
                'helper_id' => null,
                'debit' => 7000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-15',
                'transaction_proof' => 'BYR-SEWA-001',
                'description' => 'Pembayaran sewa kantor bulan Juni',
                'account_id' => '1101', // Kas (KREDIT)
                'helper_id' => null,
                'debit' => null,
                'credit' => 7000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 5. Penerimaan Pembayaran dari Klien Jaya Abadi (K001)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-20',
                'transaction_proof' => 'REC-JA-001',
                'description' => 'Penerimaan pembayaran dari Klien Jaya Abadi',
                'account_id' => '1101', // Kas (DEBIT)
                'helper_id' => null,
                'debit' => 15000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-20',
                'transaction_proof' => 'REC-JA-001',
                'description' => 'Penerimaan pembayaran dari Klien Jaya Abadi',
                'account_id' => '1102', // Piutang Usaha (KREDIT)
                'helper_id' => 'K001', // Kode Bantu: Klien Jaya Abadi
                'debit' => null,
                'credit' => 15000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 6. Pembelian Perlengkapan Kantor secara tunai
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-22',
                'transaction_proof' => 'BLI-PLKP-001',
                'description' => 'Pembelian perlengkapan kantor tunai',
                'account_id' => '1103', // Perlengkapan Kantor (DEBIT)
                'helper_id' => null,
                'debit' => 2000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-22',
                'transaction_proof' => 'BLI-PLKP-001',
                'description' => 'Pembelian perlengkapan kantor tunai',
                'account_id' => '1101', // Kas (KREDIT)
                'helper_id' => null,
                'debit' => null,
                'credit' => 2000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // 7. Pembayaran Utang kepada PT. Supplier Sejahtera (V001)
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-25',
                'transaction_proof' => 'BYR-UTG-001',
                'description' => 'Pembayaran utang kepada PT. Supplier Sejahtera',
                'account_id' => '2101', // Utang Usaha (DEBIT)
                'helper_id' => 'V001', // Kode Bantu: PT. Supplier Sejahtera
                'debit' => 10000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-25',
                'transaction_proof' => 'BYR-UTG-001',
                'description' => 'Pembayaran utang kepada PT. Supplier Sejahtera',
                'account_id' => '1101', // Kas (KREDIT)
                'helper_id' => null,
                'debit' => null,
                'credit' => 10000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // 8. Pendapatan Penjualan Tunai
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-28',
                'transaction_proof' => 'PJL-TUNAI-001',
                'description' => 'Pendapatan penjualan tunai',
                'account_id' => '1101', // Kas (DEBIT)
                'helper_id' => null,
                'debit' => 8000000.00,
                'credit' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'company_period_id' => 1,
                'date' => '2025-06-28',
                'transaction_proof' => 'PJL-TUNAI-001',
                'description' => 'Pendapatan penjualan tunai',
                'account_id' => '4101', // Pendapatan Penjualan (KREDIT)
                'helper_id' => null,
                'debit' => null,
                'credit' => 8000000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}