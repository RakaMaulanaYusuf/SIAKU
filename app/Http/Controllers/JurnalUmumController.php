<?php

namespace App\Http\Controllers;

use App\Models\JurnalUmum;
use App\Models\KodeAkun;
use App\Models\KodeBantu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalUmumController extends Controller
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

    private function checkBalance($journals)
    {
        $totalDebit = $journals->sum('debit') ?? 0;
        $totalCredit = $journals->sum('credit') ?? 0;
        
        $status = [
            'is_balanced' => abs($totalDebit - $totalCredit) < 0.01,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'message' => abs($totalDebit - $totalCredit) < 0.01 ? 
                'Total Debit dan Kredit sudah balance' : 
                'Total Debit dan Kredit belum balance'
        ];
        
        return $status;
    }

    public function index()
    {
        if (!auth()->user()->active_company_id || !auth()->user()->company_period_id) {
            return view('jurnalumum', [
                'journals' => collect(),
                'accounts' => collect(),
                'helpers' => collect(),
                'balanceStatus' => null
            ]);
        }

        $company_id = auth()->user()->active_company_id;
        $period_id = auth()->user()->company_period_id;
        
        $journals = JurnalUmum::with(['account', 'helper'])
            ->where('company_id', $company_id)
            ->where('company_period_id', $period_id)
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

        $balanceStatus = $this->checkBalance(collect($journals));

        $accounts = KodeAkun::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->orderBy('account_id')
            ->get()
            ->map(function($account) {
                return [
                    'account_id' => $account->account_id,
                    'name' => $account->name
                ];
            });
            
        $helpers = KodeBantu::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->orderBy('helper_id')
            ->get()
            ->map(function($helper) {
                return [
                    'helper_id' => $helper->helper_id,
                    'name' => $helper->name
                ];
            });
            
        return view('jurnalumum', compact('journals', 'accounts', 'helpers', 'balanceStatus'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'transaction_proof' => 'required|string',
                'description' => 'required|string',
                'account_id' => 'required|exists:kode_akun,account_id',
                'helper_id' => 'nullable|exists:kode_bantu,helper_id',
                'debit' => 'required_without:credit|nullable|numeric|min:0',
                'credit' => 'required_without:debit|nullable|numeric|min:0',
            ]);

            DB::beginTransaction();
            try {
                $journal = JurnalUmum::create([
                    'company_id' => auth()->user()->active_company_id,
                    'company_period_id' => auth()->user()->company_period_id,
                    'date' => $validated['date'],
                    'transaction_proof' => $validated['transaction_proof'],
                    'description' => $validated['description'],
                    'account_id' => $validated['account_id'],
                    'helper_id' => $validated['helper_id'],
                    'debit' => $validated['debit'],
                    'credit' => $validated['credit'],
                ]);

                $journal->load(['account', 'helper']);

                $responseData = [
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

                DB::commit();

                return response()->json([
                    'success' => true,
                    'journal' => $responseData,
                    'message' => 'Data berhasil disimpan'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error saving journal entry: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, JurnalUmum $jurnalUmum)
    {
        try {
            if ($jurnalUmum->company_id !== auth()->user()->active_company_id || 
                $jurnalUmum->company_period_id !== auth()->user()->company_period_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $validated = $request->validate([
                'date' => 'required|date',
                'transaction_proof' => 'required|string',
                'description' => 'required|string',
                'account_id' => 'required|exists:kode_akun,account_id',
                'helper_id' => 'nullable|exists:kode_bantu,helper_id',
                'debit' => 'required_without:credit|nullable|numeric|min:0',
                'credit' => 'required_without:debit|nullable|numeric|min:0',
            ]);

            DB::beginTransaction();
            try {
                $jurnalUmum->update([
                    'date' => $validated['date'],
                    'transaction_proof' => $validated['transaction_proof'],
                    'description' => $validated['description'],
                    'account_id' => $validated['account_id'],
                    'helper_id' => $validated['helper_id'],
                    'debit' => $validated['debit'],
                    'credit' => $validated['credit'],
                ]);

                $jurnalUmum->load(['account', 'helper']);

                $responseData = [
                    'id' => $jurnalUmum->id,
                    'date' => $jurnalUmum->date->format('Y-m-d'),
                    'transaction_proof' => $jurnalUmum->transaction_proof,
                    'description' => $jurnalUmum->description,
                    'account_id' => $jurnalUmum->account_id,
                    'account_name' => $jurnalUmum->account->name,
                    'helper_id' => $jurnalUmum->helper_id,
                    'helper_name' => $jurnalUmum->helper?->name,
                    'debit' => $jurnalUmum->debit,
                    'credit' => $jurnalUmum->credit,
                ];

                DB::commit();

                return response()->json([
                    'success' => true,
                    'journal' => $responseData,
                    'message' => 'Data berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error updating journal entry: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(JurnalUmum $jurnalUmum)
    {
        try {
            if ($jurnalUmum->company_id !== auth()->user()->active_company_id || 
                $jurnalUmum->company_period_id !== auth()->user()->company_period_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            DB::beginTransaction();
            try {
                $jurnalUmum->delete();
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting journal entry: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}