<?php

namespace App\Http\Controllers;

use App\Models\KodeAkun;
use App\Models\KodeBantu;
use App\Models\JurnalUmum;
use App\Models\Company;
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
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $query = JurnalUmum::where('company_id', auth()->user()->active_company_id);
            
            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }

            $jurnals = $query->orderBy('date')
                ->orderBy('id')
                ->get();

            $data = [
                'title' => 'JURNAL UMUM',
                'companyName' => auth()->user()->active_company->name ?? 'Mikrolet Selamet',
                'date' => $endDate ? Carbon::parse($endDate)->format('d F Y') : now()->format('d F Y'),
                'startDate' => $startDate ? Carbon::parse($startDate)->format('d F Y') : null,
                'endDate' => $endDate ? Carbon::parse($endDate)->format('d F Y') : null,
                'jurnals' => $jurnals,
                'totalDebit' => $jurnals->sum('debit'),
                'totalCredit' => $jurnals->sum('credit')
            ];

            $pdf = PDF::loadView('pdf.jurnal-umum', $data);
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download('Jurnal_Umum_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * 4. PDF Buku Besar
     */
    public function downloadBukuBesarPDF(Request $request)
    {
        try {
            $accountId = $request->input('account_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $account = KodeAkun::where('company_id', auth()->user()->active_company_id)
                ->where('account_id', $accountId)
                ->first();

            if (!$account) {
                return back()->with('error', 'Akun tidak ditemukan');
            }

            $query = JurnalUmum::where('company_id', auth()->user()->active_company_id)
                ->where('account_id', $accountId);

            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }

            $transactions = $query->orderBy('date')
                ->orderBy('id')
                ->get();

            // Calculate running balance
            $runningBalance = $account->balance_type == 'DEBIT' ? 
                ($account->debit ?? 0) : ($account->credit ?? 0);
            
            $processedTransactions = [];
            $no = 1;
            
            foreach ($transactions as $transaction) {
                if ($account->balance_type == 'DEBIT') {
                    $runningBalance = $runningBalance + ($transaction->debit ?? 0) - ($transaction->credit ?? 0);
                } else {
                    $runningBalance = $runningBalance + ($transaction->credit ?? 0) - ($transaction->debit ?? 0);
                }
                
                $processedTransactions[] = [
                    'no' => $no++,
                    'date' => $transaction->date,
                    'bukti' => $transaction->transaction_proof ?? '-',
                    'description' => $transaction->description,
                    'debit' => $transaction->debit,
                    'credit' => $transaction->credit,
                    'balance' => $runningBalance
                ];
            }

            $data = [
                'title' => 'BUKU BESAR',
                'companyName' => auth()->user()->active_company->name ?? 'Mikrolet Selamet',
                'date' => $endDate ? Carbon::parse($endDate)->format('d F Y') : now()->format('d F Y'),
                'account' => $account,
                'transactions' => $processedTransactions,
                'startDate' => $startDate ? Carbon::parse($startDate)->format('d F Y') : null,
                'endDate' => $endDate ? Carbon::parse($endDate)->format('d F Y') : null
            ];

            $pdf = PDF::loadView('pdf.buku-besar', $data);
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download('Buku_Besar_' . $account->account_id . '_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * 5. PDF Buku Besar Pembantu
     */
    public function downloadBukuBesarPembantuPDF(Request $request)
    {
        try {
            $kodeBantuId = $request->input('kode_bantu_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $kodeBantu = KodeBantu::where('company_id', auth()->user()->active_company_id)
                ->where('helper_id', $kodeBantuId)
                ->first();

            if (!$kodeBantu) {
                return back()->with('error', 'Kode bantu tidak ditemukan');
            }

            $query = JurnalUmum::where('company_id', auth()->user()->active_company_id)
                ->where('helper_id', $kodeBantuId);

            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }

            $transactions = $query->orderBy('date')
                ->orderBy('id')
                ->get();

            // Calculate running balance
            $runningBalance = $kodeBantu->balance ?? 0;
            $processedTransactions = [];
            $no = 1;
            
            foreach ($transactions as $transaction) {
                $runningBalance = $runningBalance + ($transaction->debit ?? 0) - ($transaction->credit ?? 0);
                
                $processedTransactions[] = [
                    'no' => $no++,
                    'date' => $transaction->date,
                    'bukti' => $transaction->transaction_proof ?? '-',
                    'description' => $transaction->description,
                    'debit' => $transaction->debit,
                    'credit' => $transaction->credit,
                    'balance' => $runningBalance
                ];
            }

            $data = [
                'title' => 'BUKU BESAR PEMBANTU',
                'companyName' => auth()->user()->active_company->name ?? 'Mikrolet Selamet',
                'date' => $endDate ? Carbon::parse($endDate)->format('d F Y') : now()->format('d F Y'),
                'kodeBantu' => $kodeBantu,
                'transactions' => $processedTransactions,
                'startDate' => $startDate ? Carbon::parse($startDate)->format('d F Y') : null,
                'endDate' => $endDate ? Carbon::parse($endDate)->format('d F Y') : null
            ];

            $pdf = PDF::loadView('pdf.buku-besar-pembantu', $data);
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download('Buku_Besar_Pembantu_' . $kodeBantu->helper_id . '_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * 6. PDF Laba Rugi
     */
    public function downloadLabaRugiPDF()
    {
        try {
            // Get revenue accounts based on the data structure from Laba Rugi view
            $pendapatanAccounts = collect();
            $bebanAccounts = collect();

            // Get all accounts that are in LABARUGI report type
            $allAccounts = KodeAkun::where('company_id', auth()->user()->active_company_id)
                ->where('report_type', 'LABARUGI')
                ->get();

            // Calculate current balance from journal entries
            foreach ($allAccounts as $account) {
                $journalDebit = JurnalUmum::where('company_id', auth()->user()->active_company_id)
                    ->where('account_id', $account->account_id)
                    ->sum('debit');
                
                $journalCredit = JurnalUmum::where('company_id', auth()->user()->active_company_id)
                    ->where('account_id', $account->account_id)
                    ->sum('credit');

                if ($account->balance_type == 'CREDIT') {
                    // Revenue accounts
                    $currentBalance = ($account->credit ?? 0) + $journalCredit - $journalDebit;
                    if ($currentBalance > 0) {
                        $account->balance = $currentBalance;
                        $pendapatanAccounts->push($account);
                    }
                } else {
                    // Expense accounts
                    $currentBalance = ($account->debit ?? 0) + $journalDebit - $journalCredit;
                    if ($currentBalance > 0) {
                        $account->balance = $currentBalance;
                        $bebanAccounts->push($account);
                    }
                }
            }

            $totalPendapatan = $pendapatanAccounts->sum('balance');
            $totalBeban = $bebanAccounts->sum('balance');
            $labaRugi = $totalPendapatan - $totalBeban;

            $data = [
                'title' => 'LAPORAN LABA RUGI',
                'companyName' => auth()->user()->active_company->name ?? 'Mikrolet Selamet',
                'period' => now()->format('F Y'),
                'pendapatanAccounts' => $pendapatanAccounts,
                'bebanAccounts' => $bebanAccounts,
                'totalPendapatan' => $totalPendapatan,
                'totalBeban' => $totalBeban,
                'labaRugi' => $labaRugi
            ];

            $pdf = PDF::loadView('pdf.laba-rugi', $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('Laporan_Laba_Rugi_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * 7. PDF Neraca
     */
    public function downloadNeracaPDF()
    {
        try {
            // Get balance sheet accounts
            $aktivaAccounts = collect();
            $passivaAccounts = collect();

            // Get all accounts that are in NERACA report type
            $allAccounts = KodeAkun::where('company_id', auth()->user()->active_company_id)
                ->where('report_type', 'NERACA')
                ->get();

            // Calculate current balance from journal entries
            foreach ($allAccounts as $account) {
                $journalDebit = JurnalUmum::where('company_id', auth()->user()->active_company_id)
                    ->where('account_id', $account->account_id)
                    ->sum('debit');
                
                $journalCredit = JurnalUmum::where('company_id', auth()->user()->active_company_id)
                    ->where('account_id', $account->account_id)
                    ->sum('credit');

                if ($account->balance_type == 'DEBIT') {
                    // Assets
                    $currentBalance = ($account->debit ?? 0) + $journalDebit - $journalCredit;
                    if ($currentBalance > 0) {
                        $account->balance = $currentBalance;
                        $aktivaAccounts->push($account);
                    }
                } else {
                    // Liabilities & Equity
                    $currentBalance = ($account->credit ?? 0) + $journalCredit - $journalDebit;
                    if ($currentBalance > 0) {
                        $account->balance = $currentBalance;
                        $passivaAccounts->push($account);
                    }
                }
            }

            $totalAktiva = $aktivaAccounts->sum('balance');
            $totalPassiva = $passivaAccounts->sum('balance');

            $data = [
                'title' => 'NERACA',
                'companyName' => auth()->user()->active_company->name ?? 'Mikrolet Selamet',
                'date' => now()->format('d F Y'),
                'aktivaAccounts' => $aktivaAccounts,
                'passivaAccounts' => $passivaAccounts,
                'totalAktiva' => $totalAktiva,
                'totalPassiva' => $totalPassiva
            ];

            $pdf = PDF::loadView('pdf.neraca', $data);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('Neraca_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }
}