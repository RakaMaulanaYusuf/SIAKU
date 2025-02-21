<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use App\Models\KodeBantu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BukuBesarPembantuController extends Controller 
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
            return view('bukubesarpembantu', ['accounts' => collect(), 'transactions' => collect()]);
        }

        $company_id = auth()->user()->active_company_id;
        $period_id = auth()->user()->company_period_id;
            
        // Ambil daftar kode bantu yang memiliki transaksi di jurnal umum
        $accounts = KodeBantu::whereHas('journalEntries', function($query) use ($company_id, $period_id) {
                $query->where('company_id', $company_id)
                      ->where('company_period_id', $period_id);
            })
            ->where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->orderBy('helper_id')
            ->select('helper_id', 'name')
            ->get()
            ->map(function($account) {
                return [
                    'code' => $account->helper_id,
                    'name' => $account->name
                ];
            });
                
        $transactions = collect();
            
        return view('bukubesarpembantu', compact('accounts', 'transactions'));
    }
    
    public function getTransactions(Request $request)
    {
        $validated = $request->validate([
            'helper_id' => 'required|exists:kode_bantu,helper_id'
        ]);

        $company_id = auth()->user()->active_company_id;
        $period_id = auth()->user()->company_period_id;
        $helper_id = $validated['helper_id'];
        
        $transactions = $this->getHelperTransactions($company_id, $period_id, $helper_id);
        
        return response()->json($transactions);
    }

    private function getHelperTransactions($company_id, $period_id, $helper_id)
    {
        $helper = KodeBantu::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('helper_id', $helper_id)
            ->first();

        if (!$helper) {
            return collect();
        }

        $transactions = JurnalUmum::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('helper_id', $helper_id)
            ->orderBy('date')
            ->orderBy('id')
            ->get();
            
        $running_balance = $helper->balance ?? 0;

        return $transactions->map(function($transaction, $index) use (&$running_balance, $helper) {
            // Calculate balance based on helper status
            if ($helper->status === 'PIUTANG') {
                // For PIUTANG: debit increases, credit decreases
                $running_balance += ($transaction->debit ?? 0) - ($transaction->credit ?? 0);
            } else {
                // For HUTANG: debit decreases, credit increases
                $running_balance -= ($transaction->debit ?? 0) - ($transaction->credit ?? 0);
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

    public function downloadPDF(Request $request)
    {
        try {
            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;
            $helper_id = $request->helper_id;

            if (!$helper_id) {
                return redirect()->back()->with('error', 'Pilih kode bantu terlebih dahulu');
            }

            $helper = KodeBantu::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->where('helper_id', $helper_id)
                ->firstOrFail();

            $transactions = $this->getHelperTransactions($company_id, $period_id, $helper_id);

            $data = [
                'title' => 'Buku Besar Pembantu',
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
                    'Kode Bantu' => $helper->helper_id,
                    'Nama' => $helper->name,
                    'Status' => $helper->status,
                    'Saldo Awal' => number_format($helper->balance ?? 0, 2)
                ]
            ];

            $pdf = PDF::loadView('pdf_template', $data);

            return $pdf->download('Buku_Besar_Pembantu_' . $helper->helper_id . '_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh PDF: ' . $e->getMessage());
        }
    }
}