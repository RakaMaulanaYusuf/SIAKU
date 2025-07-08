<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use App\Models\KodeBantu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
            return view('staff.bukubesarpembantu', ['accounts' => collect(), 'transactions' => collect()]);
        }

        $company_id = auth()->user()->active_company_id;
        $period_id = auth()->user()->company_period_id;
            
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
            
        return view('staff.bukubesarpembantu', compact('accounts', 'transactions'));
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

    public function getHelperTransactions($company_id, $period_id, $helper_id)
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
            if ($helper->status === 'PIUTANG') {
                $running_balance += ($transaction->debit ?? 0) - ($transaction->credit ?? 0);
            } else { 
                $running_balance -= ($transaction->debit ?? 0); 
                $running_balance += ($transaction->credit ?? 0); 
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
}