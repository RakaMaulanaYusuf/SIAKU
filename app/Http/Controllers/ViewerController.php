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
use App\Models\CompanyPeriod;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewerController extends Controller
{
    public function __construct()
    {
        // Hanya perlu mengecek apakah user memiliki role viewer
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'viewer') {
                return redirect()->route('login')
                    ->with('error', 'Unauthorized access');
            }
            return $next($request);
        });
    }

    // Method untuk menampilkan daftar periode
    public function listPeriods()
    {
        $company_id = auth()->user()->assigned_company_id;
        $company = auth()->user()->assignedCompany;
        
        $periods = CompanyPeriod::where('company_id', $company_id)
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();
            
        return view('viewer.listPeriods', compact('periods', 'company'));
    }

    // Method untuk set periode aktif
    public function setPeriod(Request $request)
    {
        $validated = $request->validate([
            'period_id' => 'required|exists:company_period,id,company_id,' . auth()->user()->assigned_company_id
        ]);

        auth()->user()->update([
            'company_period_id' => $validated['period_id']
        ]);

        return redirect()->route('vdashboard')
            ->with('success', 'Periode berhasil diubah');
    }

    public function dashboard()
    {
        if (!auth()->user()->company_period_id) {
            return redirect()->route('listPeriods')
                ->with('warning', 'Pilih periode terlebih dahulu');
        }

        return view('viewer.vdashboard');
    }

    public function kodeakun()
    {
        if (!auth()->user()->company_period_id) {
            return redirect()->route('listPeriods')
                ->with('warning', 'Pilih periode terlebih dahulu');
        }

        $company_id = auth()->user()->assigned_company_id;
        $period_id = auth()->user()->company_period_id;

        $accounts = KodeAkun::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
            ->orderBy('account_id')
            ->get();
            
        return view('viewer.vkodeakun', compact('accounts'));
    }

    public function kodebantu()
    {
        if (!auth()->user()->company_period_id) {
            return redirect()->route('listPeriods')
                ->with('warning', 'Pilih periode terlebih dahulu');
        }

        $company_id = auth()->user()->assigned_company_id;
        $period_id = auth()->user()->company_period_id;

        $accounts = KodeBantu::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
            ->orderBy('helper_id')
            ->get();
            
        return view('viewer.vkodebantu', compact('accounts'));
    }

    public function jurnalumum()
    {
        if (!auth()->user()->company_period_id) {
            return redirect()->route('listPeriods')
                ->with('warning', 'Pilih periode terlebih dahulu');
        }

        $company_id = auth()->user()->assigned_company_id;
        $period_id = auth()->user()->company_period_id;
        
        $journals = JurnalUmum::with(['account', 'helper'])
            ->where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
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

        $accounts = KodeAkun::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
            ->orderBy('account_id')
            ->get()
            ->map(function($account) {
                return [
                    'account_id' => $account->account_id,
                    'name' => $account->name
                ];
            });
            
        $helpers = KodeBantu::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
            ->orderBy('helper_id')
            ->get()
            ->map(function($helper) {
                return [
                    'helper_id' => $helper->helper_id,
                    'name' => $helper->name
                ];
            });
            
        return view('viewer.vjurnalumum', compact('journals', 'accounts', 'helpers'));
    }

    public function bukubesar(Request $request)
    {
        if (!auth()->user()->company_period_id) {
            return redirect()->route('listPeriods')
                ->with('warning', 'Pilih periode terlebih dahulu');
        }

        $company_id = auth()->user()->assigned_company_id;
        $period_id = auth()->user()->company_period_id;
        
        // Get accounts list if no specific account is requested
        if (!$request->has('account_id')) {
            $accounts = KodeAkun::whereHas('journalEntries', function($query) use ($company_id, $period_id) {
                    $query->where([
                        'company_id' => $company_id,
                        'company_period_id' => $period_id
                    ]);
                })
                ->where([
                    'company_id' => $company_id,
                    'company_period_id' => $period_id
                ])
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
            
            return view('viewer.vbukubesar', compact('accounts', 'transactions'));
        }
        
        // If account_id is provided, return transactions
        $validated = $request->validate([
            'account_id' => 'required|exists:kode_akun,account_id'
        ]);

        $account_id = $validated['account_id'];
        $transactions = $this->getAccountTransactions($company_id, $period_id, $account_id);
        
        if ($request->wantsJson()) {
            return response()->json($transactions);
        }

        // If PDF download is requested
        if ($request->has('download')) {
            return $this->downloadPDF($company_id, $period_id, $account_id, 'buku_besar');
        }

        return view('viewer.vbukubesar', compact('accounts', 'transactions'));
    }

    public function bukubesarpembantu(Request $request)
    {
        if (!auth()->user()->company_period_id) {
            return redirect()->route('listPeriods')
                ->with('warning', 'Pilih periode terlebih dahulu');
        }

        $company_id = auth()->user()->assigned_company_id;
        $period_id = auth()->user()->company_period_id;
        
        // Get helper accounts list if no specific helper is requested
        if (!$request->has('helper_id')) {
            $accounts = KodeBantu::whereHas('journalEntries', function($query) use ($company_id, $period_id) {
                    $query->where([
                        'company_id' => $company_id,
                        'company_period_id' => $period_id
                    ]);
                })
                ->where([
                    'company_id' => $company_id,
                    'company_period_id' => $period_id
                ])
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
            
            return view('viewer.vbukubesarpembantu', compact('accounts', 'transactions'));
        }

        // If helper_id is provided, return transactions
        $validated = $request->validate([
            'helper_id' => 'required|exists:kode_bantu,helper_id'
        ]);

        $helper_id = $validated['helper_id'];
        $transactions = $this->getHelperTransactions($company_id, $period_id, $helper_id);
        
        if ($request->wantsJson()) {
            return response()->json($transactions);
        }

        // If PDF download is requested
        if ($request->has('download')) {
            return $this->downloadPDF($company_id, $period_id, $helper_id, 'buku_besar_pembantu');
        }

        return view('viewer.vbukubesarpembantu', compact('accounts', 'transactions'));
    }

    /**
     * Get transactions for buku besar
     */
    public function getTransactions(Request $request)
    {
        if (!auth()->user()->company_period_id) {
            return response()->json(['error' => 'No active period selected'], 400);
        }

        $company_id = auth()->user()->assigned_company_id;
        $period_id = auth()->user()->company_period_id;

        // Get account_id from request
        $account_id = $request->account_id;
        
        if (!$account_id) {
            return response()->json(['error' => 'Account ID is required'], 400);
        }

        // Verify account exists
        $accountExists = KodeAkun::where([
            'company_id' => $company_id,
            'company_period_id' => $period_id,
            'account_id' => $account_id
        ])->exists();
        
        if (!$accountExists) {
            return response()->json(['error' => 'Account not found'], 404);
        }

        try {
            // Get transactions
            $transactions = $this->getAccountTransactions($company_id, $period_id, $account_id);
            return response()->json($transactions);
        } catch (\Exception $e) {
            \Log::error('Error getting transactions: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get transactions: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get transactions for buku besar pembantu
     */
    public function getTransactionsHelper(Request $request)
    {
        if (!auth()->user()->company_period_id) {
            return response()->json(['error' => 'No active period selected'], 400);
        }

        $company_id = auth()->user()->assigned_company_id;
        $period_id = auth()->user()->company_period_id;

        // Get helper_id from request
        $helper_id = $request->helper_id;
        
        if (!$helper_id) {
            return response()->json(['error' => 'Helper ID is required'], 400);
        }

        // Verify helper exists
        $helperExists = KodeBantu::where([
            'company_id' => $company_id,
            'company_period_id' => $period_id,
            'helper_id' => $helper_id
        ])->exists();
        
        if (!$helperExists) {
            return response()->json(['error' => 'Helper not found'], 404);
        }

        try {
            // Get transactions
            $transactions = $this->getHelperTransactions($company_id, $period_id, $helper_id);
            return response()->json($transactions);
        } catch (\Exception $e) {
            \Log::error('Error getting helper transactions: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get transactions: ' . $e->getMessage()], 500);
        }
    }

    private function getAccountTransactions($company_id, $period_id, $account_id)
    {
        try {
            $account = KodeAkun::where([
                    'company_id' => $company_id,
                    'company_period_id' => $period_id,
                    'account_id' => $account_id
                ])
                ->first();

            if (!$account) {
                return collect();
            }

            $transactions = JurnalUmum::where([
                    'company_id' => $company_id,
                    'company_period_id' => $period_id,
                    'account_id' => $account_id
                ])
                ->orderBy('date')
                ->orderBy('id')
                ->get();
            
            // Match the same logic as BukuBesarController
            $running_balance = $account->balance_type === 'DEBIT' ? 
                ($account->debit ?? 0) : 
                ($account->credit ?? 0);

            return $transactions->map(function($transaction, $index) use (&$running_balance, $account) {
                // Calculate balance based on account type
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
        } catch (\Exception $e) {
            \Log::error('Error in getAccountTransactions: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getHelperTransactions($company_id, $period_id, $helper_id)
    {
        try {
            $helper = KodeBantu::where([
                    'company_id' => $company_id,
                    'company_period_id' => $period_id,
                    'helper_id' => $helper_id
                ])
                ->first();

            if (!$helper) {
                return collect();
            }

            $transactions = JurnalUmum::where([
                    'company_id' => $company_id,
                    'company_period_id' => $period_id,
                    'helper_id' => $helper_id
                ])
                ->orderBy('date')
                ->orderBy('id')
                ->get();
            
            // Initialize running balance with the initial balance from KodeBantu
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
        } catch (\Exception $e) {
            \Log::error('Error in getHelperTransactions: ' . $e->getMessage());
            throw $e;
        }
    }

    public function labarugi() 
    {
        if (!auth()->user()->company_period_id) {
            return redirect()->route('listPeriods')
                ->with('warning', 'Pilih periode terlebih dahulu');
        }

        $company_id = auth()->user()->assigned_company_id;
        $period_id = auth()->user()->company_period_id;
        
        $availableAccounts = KodeAkun::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id,
                'report_type' => 'LABARUGI'
            ])
            ->get()
            ->map(function($account) {
                return [
                    'account_id' => $account->account_id,
                    'name' => $account->name,
                    'balance' => $this->getBukuBesarBalance($account->account_id)
                ];
            });
    
        $pendapatan = Pendapatan::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
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
    
        $hpp = HPP::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
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
            $biaya = BiayaOperasional::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
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
    
        return view('viewer.vlabarugi', compact('pendapatan', 'hpp', 'biaya', 'availableAccounts'));
    }

    private function getBukuBesarBalance($account_id) 
    {
        $bukuBesarController = new BukuBesarController();
        $balance = $bukuBesarController->getAccountBalance(
            auth()->user()->assigned_company_id,
            auth()->user()->company_period_id,
            $account_id
        );
        
        return $balance;
    }

    public function neraca() 
    {
        if (!auth()->user()->company_period_id) {
            return redirect()->route('listPeriods')
                ->with('warning', 'Pilih periode terlebih dahulu');
        }

        $company_id = auth()->user()->assigned_company_id;
        $period_id = auth()->user()->company_period_id;
        
        $availableAccounts = KodeAkun::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id,
                'report_type' => 'NERACA'
            ])
            ->get()
            ->map(function($account) {
                return [
                    'account_id' => $account->account_id,
                    'name' => $account->name,
                    'balance' => $this->getBukuBesarBalance($account->account_id)
                ];
            });
    
        $aktivalancar = AktivaLancar::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
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
    
        $aktivatetap = AktivaTetap::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
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
    
        $kewajiban = Kewajiban::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
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

        $ekuitas = Ekuitas::where([
                'company_id' => $company_id,
                'company_period_id' => $period_id
            ])
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
    
        return view('viewer.vneraca', compact('aktivalancar', 'aktivatetap', 'kewajiban', 'ekuitas', 'availableAccounts'));
    }
    
    /**
     * Generate PDF for download
     */
    public function downloadPDF($company_id, $period_id, $id, $type)
    {
        // Your PDF generation code here
        // This is a placeholder based on your route definitions
        return redirect()->back()->with('warning', 'PDF generation not implemented');
    }
    
    /**
     * Generate PDF for download (helper version)
     */
    public function downloadPDFHelper(Request $request)
    {
        $helper_id = $request->helper_id;
        $company_id = auth()->user()->assigned_company_id;
        $period_id = auth()->user()->company_period_id;
        
        // Your PDF generation code here
        // This is a placeholder based on your route definitions
        return redirect()->back()->with('warning', 'PDF generation not implemented');
    }
    
    /**
     * Generate PDF for financial reports
     */
    public function generatePDF(Request $request)
    {
        // Your PDF generation code here
        // This is a placeholder based on your route definitions
        return redirect()->back()->with('warning', 'PDF generation not implemented');
    }
}