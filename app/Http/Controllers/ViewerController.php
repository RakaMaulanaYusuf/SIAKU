<?php

namespace App\Http\Controllers;

use App\Models\KodeAkun;
use App\Models\KodeBantu;
use App\Models\JurnalUmum;
use App\Models\Pendapatan;
use App\Models\HPP;
use App\Models\BiayaOperasional;
use App\Models\AktivaLancar;
use App\Models\AktivaTetap;
use App\Models\Kewajiban;
use App\Models\Ekuitas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewerController extends Controller
{
    public function dashboard()
    {
        $company_id = auth()->user()->active_company_id;
        $company = auth()->user()->active_company;

        return view('vdashboard');
    }

    public function kodeakun()
    {
        $accounts = KodeAkun::where('company_id', auth()->user()->active_company_id)
            ->orderBy('account_id')  // Diubah dari code ke account_id
            ->get();
            
        return view('vkodeakun', compact('accounts'));
    }

    public function kodebantu()
   {
       $accounts = KodeBantu::where('company_id', auth()->user()->active_company_id)
           ->orderBy('helper_id')  // Diubah dari code ke helper_id
           ->get();
           
       return view('vkodebantu', compact('accounts'));
   }

    public function jurnalumum()
    {
        $company_id = auth()->user()->active_company_id;
        
        $journals = JurnalUmum::with(['account', 'helper'])
            ->where('company_id', $company_id)
            ->orderBy('date', 'desc')
            ->orderBy('transaction_proof')
            ->get()
            ->map(function($journal) {
                return [
                    'id' => $journal->id,
                    'date' => $journal->date->format('Y-m-d'),
                    'transaction_proof' => $journal->transaction_proof,
                    'description' => $journal->description,
                    'account_id' => $journal->account_id,
                    'account_name' => $journal->account->name,
                    'helper_id' => $journal->helper_id,
                    'helper_name' => $journal->helper?->name,
                    'debit' => $journal->debit,
                    'credit' => $journal->credit,
                ];
            });

        $accounts = KodeAkun::where('company_id', $company_id)
            ->orderBy('account_id')
            ->get()
            ->map(function($account) {
                return [
                    'account_id' => $account->account_id,
                    'name' => $account->name
                ];
            });
            
        $helpers = KodeBantu::where('company_id', $company_id)
            ->orderBy('helper_id')
            ->get()
            ->map(function($helper) {
                return [
                    'helper_id' => $helper->helper_id,
                    'name' => $helper->name
                ];
            });
            
        return view('vjurnalumum', compact('journals', 'accounts', 'helpers'));
    }

    public function bukubesar(Request $request)
    {
        $company_id = auth()->user()->active_company_id;
        
        // Get accounts list if no specific account is requested
        if (!$request->has('account_id')) {
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
            
            return view('vbukubesar', compact('accounts', 'transactions'));
        }
        
        // If account_id is provided, return transactions
        $validated = $request->validate([
            'account_id' => 'required|exists:kode_akun,account_id'
        ]);

        $account_id = $validated['account_id'];
        $transactions = $this->getAccountTransactions($company_id, $account_id);
        
        if ($request->wantsJson()) {
            return response()->json($transactions);
        }

        // If PDF download is requested
        if ($request->has('download')) {
            return $this->downloadPDF($company_id, $account_id, 'buku_besar');
        }
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

    public function bukubesarpembantu(Request $request)
    {
        $company_id = auth()->user()->active_company_id;
        
        // Get helper accounts list if no specific helper is requested
        if (!$request->has('helper_id')) {
            $accounts = KodeBantu::whereHas('journalEntries', function($query) use ($company_id) {
                    $query->where('company_id', $company_id);
                })
                ->where('company_id', $company_id)
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
            
            return view('vbukubesarpembantu', compact('accounts', 'transactions'));
        }

        // If helper_id is provided, return transactions
        $validated = $request->validate([
            'helper_id' => 'required|exists:kode_bantu,helper_id'
        ]);

        $helper_id = $validated['helper_id'];
        $transactions = $this->getHelperTransactions($company_id, $helper_id);
        
        if ($request->wantsJson()) {
            return response()->json($transactions);
        }

        // If PDF download is requested
        if ($request->has('download')) {
            return $this->downloadPDF($company_id, $helper_id, 'buku_besar_pembantu');
        }
    }

    public function getTransactionsHelper(Request $request)
    {
        $validated = $request->validate([
            'helper_id' => 'required|exists:kode_bantu,helper_id'
        ]);

        $company_id = auth()->user()->active_company_id;
        $helper_id = $validated['helper_id'];
        
        $transactions = $this->getHelperTransactions($company_id, $helper_id);
        
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

    private function getHelperTransactions($company_id, $helper_id)
    {
        $helper = KodeBantu::where('company_id', $company_id)
            ->where('helper_id', $helper_id)
            ->first();

        $transactions = JurnalUmum::where('company_id', $company_id)
            ->where('helper_id', $helper_id)
            ->orderBy('date')
            ->orderBy('id')
            ->get();
            
        $running_balance = 0;

        return $transactions->map(function($transaction, $index) use (&$running_balance) {
            $running_balance += ($transaction->debit ?? 0) - ($transaction->credit ?? 0);

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

    public function labarugi() {
        $company_id = auth()->user()->active_company_id;
        
        // Get available accounts for dropdown
        $availableAccounts = KodeAkun::where('company_id', $company_id)
            ->where('report_type', 'LABARUGI')
            ->get()
            ->map(function($account) {
                return [
                    'account_id' => $account->account_id, 
                    'name' => $account->name,
                    'balance' => $this->getBukuBesarBalance($account->account_id)
                ];
            });
    
        // Get existing rows for each category
        $pendapatan = Pendapatan::where('company_id', $company_id)
            ->with('account')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'balance' => $this->getBukuBesarBalance($item->account_id)
                ];
            });
    
        $hpp = HPP::where('company_id', $company_id)
            ->with('account') 
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'balance' => $this->getBukuBesarBalance($item->account_id)
                ];
            });
    
        $biaya = BiayaOperasional::where('company_id', $company_id)
            ->with('account')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'balance' => $this->getBukuBesarBalance($item->account_id)
                ];
            });
    
        return view('vlabarugi', compact('pendapatan', 'hpp', 'biaya', 'availableAccounts'));
    }

    private function getBukuBesarBalance($account_id) {
        $bukuBesarController = new BukuBesarController();
        $balance = $bukuBesarController->getAccountBalance(
            auth()->user()->active_company_id, 
            $account_id
        );
        
        // Get account details
        $account = KodeAkun::where('account_id', $account_id)
            ->where('company_id', auth()->user()->active_company_id)
            ->first();
        
        return $balance;
    }

    public function neraca() {
        $company_id = auth()->user()->active_company_id;
        
        // Get available accounts for dropdown
        $availableAccounts = KodeAkun::where('company_id', $company_id)
            ->where('report_type', 'NERACA')
            ->get()
            ->map(function($account) {
                return [
                    'account_id' => $account->account_id, 
                    'name' => $account->name,
                    'balance' => $this->getBukuBesarBalance($account->account_id)
                ];
            });
    
        // Get existing rows for each category
        $aktivalancar = AktivaLancar::where('company_id', $company_id)
            ->with('account')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'balance' => $this->getBukuBesarBalance($item->account_id)
                ];
            });
    
        $aktivatetap = AktivaTetap::where('company_id', $company_id)
            ->with('account') 
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'balance' => $this->getBukuBesarBalance($item->account_id)
                ];
            });
    
        $kewajiban = Kewajiban::where('company_id', $company_id)
            ->with('account')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'balance' => $this->getBukuBesarBalance($item->account_id)
                ];
            });

        $ekuitas = Ekuitas::where('company_id', $company_id)
            ->with('account')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'balance' => $this->getBukuBesarBalance($item->account_id)
                ];
            });
    
        return view('vneraca', compact('aktivalancar', 'aktivatetap', 'kewajiban', 'ekuitas', 'availableAccounts'));
    }
}
