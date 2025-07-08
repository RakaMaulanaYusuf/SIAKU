<?php

namespace App\Http\Controllers;

use App\Models\KodeAkun;
use App\Models\KodeBantu;
use App\Models\JurnalUmum;
use App\Models\Company;
use App\Models\CompanyPeriod;
use App\Models\AktivaLancar;
use App\Models\AktivaTetap;
use App\Models\Kewajiban;
use App\Models\Ekuitas;
use App\Models\Pendapatan;
use App\Models\HPP;
use App\Models\BiayaOperasional;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PdfController extends Controller
{
    /**
     * 1. PDF Kode Akun
     */
    public function downloadKodeAkunPDF()
    {
        try {
            // Ambil data perusahaan aktif berdasarkan ID
            $company = Company::find(auth()->user()->active_company_id);

            $accounts = KodeAkun::where('company_id', auth()->user()->active_company_id)
                ->orderBy('account_id')
                ->get();

            $totalDebit = $accounts->sum('debit');
            $totalCredit = $accounts->sum('credit');

            $data = [
                'title' => 'DAFTAR KODE AKUN',
                'companyName' => $company ? $company->name : 'Nama Perusahaan Tidak Ditemukan',
                'date' => now()->format('d F Y'),
                'accounts' => $accounts,
                'totalDebit' => $totalDebit,
                'totalCredit' => $totalCredit
            ];

            $pdf = PDF::loadView('pdf.kode-akun', $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('Daftar_Kode_Akun_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * 2. PDF Kode Bantu
     */
    public function downloadKodeBantuPDF()
    {
        try {
            $company = Company::find(auth()->user()->active_company_id);
            $kodeBantu = KodeBantu::where('company_id', auth()->user()->active_company_id)
                ->orderBy('helper_id')
                ->get();

            $totalBalance = $kodeBantu->sum('balance');

            $data = [
                'title' => 'DAFTAR KODE BANTU',
                'companyName' => $company ? $company->name : 'Nama Perusahaan Tidak Ditemukan',
                'date' => now()->format('d F Y'),
                'kodeBantu' => $kodeBantu,
                'totalBalance' => $totalBalance,
                'totalRecords' => $kodeBantu->count()
            ];

            $pdf = PDF::loadView('pdf.kode-bantu', $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('Daftar_Kode_Bantu_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * 3. PDF Jurnal Umum
     */
    public function downloadJurnalUmumPDF(Request $request)
    {
        try {
            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;

            $company = Company::find($company_id);
            $companyPeriod = CompanyPeriod::find($period_id);

            $journals = JurnalUmum::with(['account', 'helper'])
                ->where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->orderBy('date')
                ->orderBy('transaction_proof')
                ->get()
                ->map(function($journal) {
                    return [
                        'date' => $journal->date->format('Y-m-d'),
                        'transaction_proof' => $journal->transaction_proof,
                        'description' => $journal->description,
                        'account_id' => $journal->account_id,
                        'account_name' => $journal->account->name,
                        'helper_name' => $journal->helper?->name,
                        'debit' => $journal->debit,
                        'credit' => $journal->credit,
                    ];
                });

            $totalDebit = $journals->sum('debit');
            $totalCredit = $journals->sum('credit');

            $data = [
                'title' => 'LAPORAN JURNAL UMUM',
                'companyName' => $company ? $company->name : 'Nama Perusahaan Tidak Ditemukan',
                'periodName' => $companyPeriod ? $companyPeriod->name : 'Periode Tidak Ditemukan',
                'date' => now()->format('d F Y'),
                'journals' => $journals,
                'totalDebit' => $totalDebit,
                'totalCredit' => $totalCredit,
                'isBalanced' => abs($totalDebit - $totalCredit) < 0.01,
            ];

            $pdf = PDF::loadView('pdf.jurnal-umum', $data);
            $pdf->setPaper('A4', 'landscape'); // Jurnal umum biasanya lebih baik dalam landscape

            return $pdf->download('Jurnal_Umum_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF Jurnal Umum: ' . $e->getMessage());
        }
    }

    /**
     * 4. PDF Buku Besar
     */
    // App\Http\Controllers\PdfController.php

    public function downloadBukuBesarPDF(Request $request)
    {
        try {
            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;

            if (!$company_id || !$period_id) {
                return back()->with('error', 'Silakan pilih perusahaan dan periode terlebih dahulu.');
            }

            $company = Company::find($company_id);
            $companyPeriod = CompanyPeriod::find($period_id);

            // Ambil account_id dari request (jika ada)
            $selected_account_id = $request->query('account_id');

            $query = KodeAkun::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->orderBy('account_id');

            // Jika ada selected_account_id, filter hanya akun tersebut
            if ($selected_account_id) {
                $query->where('account_id', $selected_account_id);
            }

            $accounts = $query->get();

            $bukuBesarData = [];
            $bukuBesarController = new \App\Http\Controllers\BukuBesarController();

            foreach ($accounts as $account) {
                $transactions = $bukuBesarController->getAccountTransactions($company_id, $period_id, $account->account_id);

                // Hanya tambahkan akun jika ada transaksi atau saldo awal yang signifikan
                // Ini opsional, bisa dihilangkan jika ingin menampilkan semua akun meskipun kosong transaksinya
                if ($transactions->isNotEmpty() || ($account->debit > 0 || $account->credit > 0)) {
                    $bukuBesarData[] = [
                        'account_id' => $account->account_id,
                        'account_name' => $account->name,
                        'balance_type' => $account->balance_type,
                        'initial_debit' => $account->debit,
                        'initial_credit' => $account->credit,
                        'transactions' => $transactions
                    ];
                }
            }

            // Jika tidak ada data buku besar setelah filter (misal account_id tidak valid atau tidak ada transaksi)
            if (empty($bukuBesarData) && $selected_account_id) {
                return back()->with('error', 'Tidak ada data transaksi untuk akun yang dipilih.');
            } elseif (empty($bukuBesarData) && !$selected_account_id) {
                return back()->with('error', 'Tidak ada data buku besar untuk periode ini.');
            }


            $data = [
                'title' => 'LAPORAN BUKU BESAR',
                'companyName' => $company ? $company->name : 'Nama Perusahaan Tidak Ditemukan',
                'periodName' => $companyPeriod ? $companyPeriod->name : 'Periode Tidak Ditemukan',
                'date' => now()->format('d F Y'), // Tanggal cetak laporan
                'bukuBesarData' => $bukuBesarData
            ];

            $pdf = PDF::loadView('pdf.buku-besar', $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('Buku_Besar_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Gagal generate PDF Buku Besar: ' . $e->getMessage());
            return back()->with('error', 'Gagal generate PDF Buku Besar: ' . $e->getMessage());
        }
    }

    /**
     * 5. PDF Buku Besar Pembantu
     */
    // App\Http\Controllers\PdfController.php

    public function downloadBukuBesarPembantuPDF(Request $request)
    {
        try {
            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;

            if (!$company_id || !$period_id) {
                return back()->with('error', 'Silakan pilih perusahaan dan periode terlebih dahulu.');
            }

            $company = Company::find($company_id);
            $companyPeriod = CompanyPeriod::find($period_id);

            // Ambil account_id dari request (jika ada)
            $selected_helper_id = $request->query('helper_id');

            // Get all helper accounts that have transactions in the general journal
            $query = KodeBantu::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->orderBy('helper_id');

            // Jika ada selected_account_id, filter hanya akun tersebut
            if ($selected_helper_id) {
                $query->where('helper_id', $selected_helper_id);
            }

            $helperAccounts = $query->get();

            $bukuBesarPembantuData = [];
            // Gunakan fully qualified namespace di sini:
            $bukuBesarPembantuController = new \App\Http\Controllers\BukuBesarPembantuController();

            foreach ($helperAccounts as $helper) {
                // Pastikan getHelperTransactions ini mengembalikan array/collection of arrays,
                // bukan Eloquent models, karena nanti di Blade akan diakses dengan notasi array.
                $transactions = $bukuBesarPembantuController->getHelperTransactions($company_id, $period_id, $helper->helper_id);
                
                // Tambahkan data ke $bukuBesarPembantuData hanya jika ada transaksi atau saldo awal
                if ($transactions->isNotEmpty() || ($helper->balance > 0)) {
                    $bukuBesarPembantuData[] = [
                        'helper_id' => $helper->helper_id,
                        'helper_name' => $helper->name,
                        'status' => $helper->status, // 'PIUTANG' or 'HUTANG'
                        'initial_balance' => $helper->balance,
                        'transactions' => $transactions
                    ];
                }
            }

            // Tambahkan penanganan jika tidak ada data sama sekali setelah filter
            if (empty($bukuBesarPembantuData) && $selected_helper_id) {
                return back()->with('error', 'Tidak ada data Buku Besar Pembantu untuk periode ini.');
            } elseif (empty($bukuBesarPembantuData) && !$selected_helper_id){
                return back()->with('error', 'Tidak ada data buku besar untuk periode ini.');
            }

            $data = [
                'title' => 'LAPORAN BUKU BESAR PEMBANTU',
                'companyName' => $company ? $company->name : 'Nama Perusahaan Tidak Ditemukan',
                'periodName' => $companyPeriod ? $companyPeriod->name : 'Periode Tidak Ditemukan',
                'date' => now()->format('d F Y'),
                'bukuBesarPembantuData' => $bukuBesarPembantuData
            ];

            $pdf = PDF::loadView('pdf.buku-besar-pembantu', $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('Buku_Besar_Pembantu_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Gagal generate PDF Buku Besar Pembantu: ' . $e->getMessage());
            return back()->with('error', 'Gagal generate PDF Buku Besar Pembantu: ' . $e->getMessage());
        }
    }

    /**
     * 6. PDF Laba Rugi
     */
    // App\Http\Controllers\PdfController.php
    public function downloadLabaRugiPDF(Request $request)
    {
        try {
            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;

            if (!$company_id || !$period_id) {
                return back()->with('error', 'Silakan pilih perusahaan dan periode terlebih dahulu.');
            }

            $company = Company::find($company_id);
            $companyPeriod = CompanyPeriod::find($period_id);
            
            // Pastikan ini adalah fully qualified namespace
            $labaRugiController = new \App\Http\Controllers\LabaRugiController(); 

            $pendapatan = Pendapatan::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->with('account')
                ->get()
                ->map(function($item) use ($labaRugiController) {
                    // Pastikan getBukuBesarBalance ada di LabaRugiController dan public
                    $balance = $labaRugiController->getBukuBesarBalance($item->account_id);
                    return [
                        'account_id' => $item->account_id,
                        'name' => $item->name,
                        'amount' => $balance, // Data balance disimpan di 'amount'
                    ];
                });
            
            $hpp = HPP::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->with('account')
                ->get()
                ->map(function($item) use ($labaRugiController) {
                    $balance = $labaRugiController->getBukuBesarBalance($item->account_id);
                    return [
                        'account_id' => $item->account_id,
                        'name' => $item->name,
                        'amount' => $balance,
                    ];
                });

            $biaya = BiayaOperasional::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->with('account')
                ->get()
                ->map(function($item) use ($labaRugiController) {
                    $balance = $labaRugiController->getBukuBesarBalance($item->account_id);
                    return [
                        'account_id' => $item->account_id,
                        'name' => $item->name,
                        'amount' => $balance,
                    ];
                });

            $totalPendapatan = $pendapatan->sum('amount');
            $totalHPP = $hpp->sum('amount');
            $totalBiayaOperasional = $biaya->sum('amount');

            $labaKotor = $totalPendapatan - $totalHPP;
            $labaBersih = $labaKotor - $totalBiayaOperasional;

            $data = [
                'title' => 'LAPORAN LABA RUGI',
                'companyName' => $company ? $company->name : 'Nama Perusahaan Tidak Ditemukan',
                'periodName' => $companyPeriod ? $companyPeriod->name : 'Periode Tidak Ditemukan',
                'date' => now()->format('d F Y'), // Tanggal cetak laporan
                'pendapatan' => $pendapatan, // Nama variabel di controller
                'hpp' => $hpp,               // Nama variabel di controller
                'biaya' => $biaya,           // Nama variabel di controller
                'totalPendapatan' => $totalPendapatan,
                'totalHPP' => $totalHPP,
                'totalBiayaOperasional' => $totalBiayaOperasional,
                'labaKotor' => $labaKotor,
                'labaBersih' => $labaBersih,
            ];

            $pdf = PDF::loadView('pdf.laba-rugi', $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('Laporan_Laba_Rugi_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Gagal generate PDF Laba Rugi: ' . $e->getMessage());
            return back()->with('error', 'Gagal generate PDF Laba Rugi: ' . $e->getMessage());
        }
    }

    /**
     * 7. PDF Neraca
     */
    // App\Http\Controllers\PdfController.php
    public function downloadNeracaPDF(Request $request)
    {
        try {
            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;

            if (!$company_id || !$period_id) {
                return back()->with('error', 'Silakan pilih perusahaan dan periode terlebih dahulu.');
            }

            $company = Company::find($company_id);
            $companyPeriod = CompanyPeriod::find($period_id);
            
            // Pastikan fully qualified namespace di sini:
            $neracaController = new \App\Http\Controllers\NeracaController(); 
            $labaRugiController = new \App\Http\Controllers\LabaRugiController(); 

            $aktivaLancar = AktivaLancar::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->with('account')
                ->get()
                ->map(function($item) use ($neracaController) {
                    // Pastikan getBukuBesarBalance di NeracaController adalah public
                    $balance = $neracaController->getBukuBesarBalance($item->account_id);
                    return [
                        'account_id' => $item->account_id,
                        'name' => $item->name,
                        'amount' => $balance,
                    ];
                });

            $aktivaTetap = AktivaTetap::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->with('account')
                ->get()
                ->map(function($item) use ($neracaController) {
                    // Pastikan getBukuBesarBalance di NeracaController adalah public
                    $balance = $neracaController->getBukuBesarBalance($item->account_id);
                    return [
                        'account_id' => $item->account_id,
                        'name' => $item->name,
                        'amount' => $balance,
                    ];
                });
            
            $kewajiban = Kewajiban::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->with('account')
                ->get()
                ->map(function($item) use ($neracaController) {
                    // Pastikan getBukuBesarBalance di NeracaController adalah public
                    $balance = $neracaController->getBukuBesarBalance($item->account_id);
                    return [
                        'account_id' => $item->account_id,
                        'name' => $item->name,
                        'amount' => $balance,
                    ];
                });

            $ekuitas = Ekuitas::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->with('account')
                ->get()
                ->map(function($item) use ($neracaController) {
                    // Pastikan getBukuBesarBalance di NeracaController adalah public
                    $balance = $neracaController->getBukuBesarBalance($item->account_id);
                    return [
                        'account_id' => $item->account_id,
                        'name' => $item->name,
                        'amount' => $balance,
                    ];
                });

            $totalAktivaLancar = $aktivaLancar->sum('amount');
            $totalAktivaTetap = $aktivaTetap->sum('amount');
            $totalAktiva = $totalAktivaLancar + $totalAktivaTetap;

            $totalKewajiban = $kewajiban->sum('amount');
            $totalEkuitas = $ekuitas->sum('amount');
            $totalKewajibanDanEkuitas = $totalKewajiban + $totalEkuitas;
            
            // Calculate Retained Earnings from the Laba Rugi report
            // Pastikan getBukuBesarBalance di LabaRugiController adalah public
            $pendapatan_lr = Pendapatan::where('company_id', $company_id)->where('company_period_id', $period_id)->get()->sum(function($item) use ($labaRugiController) {
                return $labaRugiController->getBukuBesarBalance($item->account_id);
            });
            $hpp_lr = HPP::where('company_id', $company_id)->where('company_period_id', $period_id)->get()->sum(function($item) use ($labaRugiController) {
                return $labaRugiController->getBukuBesarBalance($item->account_id);
            });
            $biaya_lr = BiayaOperasional::where('company_id', $company_id)->where('company_period_id', $period_id)->get()->sum(function($item) use ($labaRugiController) {
                return $labaRugiController->getBukuBesarBalance($item->account_id);
            });
            $labaBersihTahunBerjalan = $pendapatan_lr - $hpp_lr - $biaya_lr;


            $data = [
                'title' => 'LAPORAN NERACA',
                'companyName' => $company ? $company->name : 'Nama Perusahaan Tidak Ditemukan',
                'periodName' => $companyPeriod ? $companyPeriod->name : 'Periode Tidak Ditemukan',
                'date' => now()->format('d F Y'), // Tanggal cetak laporan
                'aktivaLancar' => $aktivaLancar,
                'aktivaTetap' => $aktivaTetap,
                'totalAktivaLancar' => $totalAktivaLancar,
                'totalAktivaTetap' => $totalAktivaTetap,
                'totalAktiva' => $totalAktiva,
                'kewajiban' => $kewajiban,
                'ekuitas' => $ekuitas,
                'totalKewajiban' => $totalKewajiban,
                'totalEkuitas' => $totalEkuitas,
                'totalKewajibanDanEkuitas' => $totalKewajibanDanEkuitas,
                'labaBersihTahunBerjalan' => $labaBersihTahunBerjalan
            ];

            $pdf = PDF::loadView('pdf.neraca', $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('Laporan_Neraca_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Gagal generate PDF Neraca: ' . $e->getMessage());
            return back()->with('error', 'Gagal generate PDF Neraca: ' . $e->getMessage());
        }
    }
}