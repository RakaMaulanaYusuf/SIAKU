<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use App\Models\KodeAkun;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BukuBesarController extends Controller 
{
    public function index()
    {
        $company_id = auth()->user()->active_company_id;
        
        // Ambil daftar akun yang memiliki transaksi di jurnal umum
        $accounts = KodeAkun::whereHas('journalEntries', function($query) use ($company_id) {
                $query->where('company_id', $company_id);
            })
            ->where('company_id', $company_id)
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
        
        return view('bukubesar', compact('accounts', 'transactions'));
    }
    
    public function getTransactions(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:kode_akun,account_id'
        ]);

        $company_id = auth()->user()->active_company_id;
        $account_id = $validated['account_id'];
        
        $transactions = $this->getAccountTransactions($company_id, $account_id);
        
        return response()->json($transactions);
    }

    private function getAccountTransactions($company_id, $account_id)
    {
        $account = KodeAkun::where('company_id', $company_id)
            ->where('account_id', $account_id)
            ->first();

        $transactions = JurnalUmum::where('company_id', $company_id)
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

    public function getAccountBalance($company_id, $account_id) {
        $account = KodeAkun::where('company_id', $company_id)
            ->where('account_id', $account_id)
            ->first();
    
        if (!$account) {
            return 0; // Jika akun tidak ditemukan, saldo dianggap nol
        }
    
        // Ambil saldo awal berdasarkan saldo debit & kredit dari tabel kode_akun
        $running_balance = ($account->debit ?? 0) - ($account->credit ?? 0);
    
        // Ambil transaksi sesuai urutan (dari yang paling awal ke terbaru)
        $transactions = JurnalUmum::where('company_id', $company_id)
            ->where('account_id', $account_id)
            ->orderBy('date')
            ->orderBy('id')
            ->get();
    
        // Iterasi transaksi untuk mendapatkan saldo akhir
        foreach ($transactions as $transaction) {
            $running_balance += ($transaction->debit ?? 0) - ($transaction->credit ?? 0);
        }
    
        return $running_balance;
    }
    
    public function downloadPDF(Request $request)
    {
        try {
            $company_id = auth()->user()->active_company_id;
            $account_id = $request->account_id;

            if (!$account_id) {
                return redirect()->back()->with('error', 'Pilih akun terlebih dahulu');
            }

            $account = KodeAkun::where('company_id', $company_id)
                ->where('account_id', $account_id)
                ->firstOrFail();

            $transactions = $this->getAccountTransactions($company_id, $account_id);

            $data = [
                'title' => 'Buku Besar',
                'companyName' => auth()->user()->active_company->name ?? 'Perusahaan',
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