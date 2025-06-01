<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use App\Models\KodeAkun;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BukuBesarController extends Controller 
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->active_company_id || !auth()->user()->company_period_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan pilih perusahaan dan periode terlebih dahulu'
                ], 400);
            }
            return $next($request);
        })->except(['index']);
    }

    public function index()
    {
        if (!auth()->user()->active_company_id || !auth()->user()->company_period_id) {
            return view('staff.bukubesar', ['accounts' => collect(), 'transactions' => collect()]);
        }

        $company_id = auth()->user()->active_company_id;
        $period_id = auth()->user()->company_period_id;
        
        // Ambil daftar akun yang memiliki transaksi di jurnal umum
        $accounts = KodeAkun::whereHas('journalEntries', function($query) use ($company_id, $period_id) {
                $query->where('company_id', $company_id)
                      ->where('company_period_id', $period_id);
            })
            ->where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->orderBy('account_id')
            ->select('account_id', 'name')
            ->get()
            ->map(function($account) {
                return [
                    'code' => $account->account_id,
                    'name' => $account->name
                ];
            });
            
        $transactions = collect();
        
        return view('staff.bukubesar', compact('accounts', 'transactions'));
    }
    
    public function getTransactions(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:kode_akun,account_id'
        ]);

        $company_id = auth()->user()->active_company_id;
        $period_id = auth()->user()->company_period_id;
        $account_id = $validated['account_id'];
        
        $transactions = $this->getAccountTransactions($company_id, $period_id, $account_id);
        
        return response()->json($transactions);
    }

    private function getAccountTransactions($company_id, $period_id, $account_id)
    {
        $account = KodeAkun::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('account_id', $account_id)
            ->first();

        $transactions = JurnalUmum::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('account_id', $account_id)
            ->orderBy('date')
            ->orderBy('id')
            ->get();
            
        $running_balance = $account->balance_type === 'DEBIT' ? 
            ($account->debit ?? 0) : 
            ($account->credit ?? 0);

        return $transactions->map(function($transaction, $index) use (&$running_balance, $account) {
            if ($account->balance_type === 'DEBIT') {
                $running_balance += ($transaction->debit ?? 0) - ($transaction->credit ?? 0);
            } else {
                $running_balance += ($transaction->credit ?? 0) - ($transaction->debit ?? 0);
            }

            return [
                'no' => $index + 1,
                'date' => $transaction->date->format('Y-m-d'),
                'bukti' => $transaction->transaction_proof,
                'description' => $transaction->description,
                'debit' => $transaction->debit,
                'credit' => $transaction->credit,
                'balance' => $running_balance
            ];
        });
    }

    public function getAccountBalance($company_id, $period_id, $account_id) 
    {
        $account = KodeAkun::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('account_id', $account_id)
            ->first();
    
        if (!$account) {
            return 0;
        }
        
        $pos_saldo = $account->balance_type;
        
        // Inisialisasi saldo awal berdasarkan pos saldo
        if ($pos_saldo === 'DEBIT') {
            $running_balance = ($account->debit ?? 0) - ($account->credit ?? 0);
        } else {
            $running_balance = ($account->credit ?? 0) - ($account->debit ?? 0);
        }
    
        // Ambil transaksi sesuai urutan
        $transactions = JurnalUmum::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('account_id', $account_id)
            ->orderBy('date')
            ->orderBy('id')
            ->get();
    
        foreach ($transactions as $transaction) {
            if ($pos_saldo === 'DEBIT') {
                $running_balance = $running_balance + ($transaction->debit ?? 0) - ($transaction->credit ?? 0);
            } else {
                $running_balance = $running_balance + ($transaction->credit ?? 0) - ($transaction->debit ?? 0);
            }
        }
    
        return $running_balance;
    }
    
    public function downloadPDF(Request $request)
    {
        try {
            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;
            $account_id = $request->account_id;

            if (!$account_id) {
                return redirect()->back()->with('error', 'Pilih akun terlebih dahulu');
            }

            $account = KodeAkun::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->where('account_id', $account_id)
                ->firstOrFail();

            $transactions = $this->getAccountTransactions($company_id, $period_id, $account_id);

            $data = [
                'title' => 'Buku Besar',
                'companyName' => auth()->user()->active_company->name ?? 'Perusahaan',
                'periodInfo' => auth()->user()->activePeriod ? 
                    auth()->user()->activePeriod->period_month . ' ' . auth()->user()->activePeriod->period_year : 
                    'Semua Periode',
                'headers' => [
                    'No', 
                    'Tanggal', 
                    'Bukti Transaksi', 
                    'Keterangan', 
                    'Debet', 
                    'Kredit',
                    'Saldo'
                ],
                'data' => $transactions->map(function($transaction) {
                    return [
                        $transaction['no'],
                        date('d/m/Y', strtotime($transaction['date'])),
                        $transaction['bukti'],
                        $transaction['description'],
                        $transaction['debit'] ? number_format($transaction['debit'], 2) : '-',
                        $transaction['credit'] ? number_format($transaction['credit'], 2) : '-',
                        number_format($transaction['balance'], 2)
                    ];
                }),
                'totals' => [
                    number_format($transactions->sum('debit'), 2),
                    number_format($transactions->sum('credit'), 2),
                    number_format($transactions->last()['balance'] ?? 0, 2)
                ],
                'additionalInfo' => [
                    'Kode Akun' => $account->account_id,
                    'Nama Akun' => $account->name,
                    'Pos Saldo' => $account->balance_type
                ]
            ];

            $pdf = PDF::loadView('pdf_template', $data);

            return $pdf->download('Buku_Besar_' . $account->account_id . '_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh PDF: ' . $e->getMessage());
        }
    }
}