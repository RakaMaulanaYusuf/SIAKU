<?php

namespace App\Http\Controllers;

use App\Models\Pendapatan;
use App\Models\HPP;
use App\Models\BiayaOperasional;
use App\Models\KodeAkun;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LabaRugiController extends Controller
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

    public function index() {
        if (!auth()->user()->active_company_id || !auth()->user()->company_period_id) {
            return view('staff.labarugi', [
                'pendapatan' => collect(),
                'hpp' => collect(),
                'biaya' => collect(),
                'availableAccounts' => collect()
            ]);
        }

        $company_id = auth()->user()->active_company_id;
        $period_id = auth()->user()->company_period_id;
        
        $availableAccounts = KodeAkun::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('report_type', 'LABARUGI')
            ->get()
            ->map(function($account) {
                $balance = $this->getBukuBesarBalance($account->account_id);
                return [
                    'account_id' => $account->account_id, 
                    'name' => $account->name,
                    'balance' => $balance
                ];
            });
    
        $pendapatan = Pendapatan::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->with('account')
            ->get()
            ->map(function($item) {
                $balance = $this->getBukuBesarBalance($item->account_id);
                return [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $balance,
                    'balance' => $balance
                ];
            });
    
        $hpp = HPP::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->with('account') 
            ->get()
            ->map(function($item) {
                $balance = $this->getBukuBesarBalance($item->account_id);
                return [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $balance,
                    'balance' => $balance
                ];
            });
    
        $biaya = BiayaOperasional::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->with('account')
            ->get()
            ->map(function($item) {
                $balance = $this->getBukuBesarBalance($item->account_id);
                return [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $balance,
                    'balance' => $balance
                ];
            });
    
        return view('staff.labarugi', compact('pendapatan', 'hpp', 'biaya', 'availableAccounts'));
    }

    public function getBukuBesarBalance($account_id) {
        $bukuBesarController = new \App\Http\Controllers\BukuBesarController(); // Gunakan fully qualified namespace
        $balance = $bukuBesarController->getAccountBalance(
            auth()->user()->active_company_id,
            auth()->user()->company_period_id,
            $account_id
        );
        return $balance;
    }

    public function getBalance($accountId)
    {
        try {
            $bukuBesarController = new BukuBesarController();
            $balance = $bukuBesarController->getAccountBalance(
                auth()->user()->active_company_id,
                auth()->user()->company_period_id,
                $accountId
            );
            
            return response()->json([
                'success' => true,
                'balance' => $balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function getAccountCurrentPosition($account_id)
    {
        $company_id = auth()->user()->active_company_id;
        $period_id = auth()->user()->company_period_id;

        if (Pendapatan::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('account_id', $account_id)
            ->exists()) {
            return 'pendapatan';
        } elseif (HPP::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('account_id', $account_id)
            ->exists()) {
            return 'hpp';
        } elseif (BiayaOperasional::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('account_id', $account_id)
            ->exists()) {
            return 'operasional';
        }
        return null;
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:pendapatan,hpp,operasional',
                'account_id' => 'required|string',
                'name' => 'required|string',
                'amount' => 'required|numeric'
            ]);

            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;
            
            $account = KodeAkun::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->where('account_id', $validated['account_id'])
                ->firstOrFail();

            $currentPosition = $this->getAccountCurrentPosition($validated['account_id']);
            if ($currentPosition && $currentPosition !== $validated['type']) {
                throw new \Exception('Akun ini sudah digunakan di kategori ' . ucfirst($currentPosition));
            }
            
            $model = match($validated['type']) {
                'pendapatan' => Pendapatan::class,
                'hpp' => HPP::class,
                'operasional' => BiayaOperasional::class,
                default => throw new \Exception('Invalid type')
            };

            $record = $model::updateOrCreate(
                [
                    'company_id' => $company_id,
                    'company_period_id' => $period_id,
                    'account_id' => $validated['account_id']
                ],
                [
                    'name' => $validated['name'],
                    'amount' => $validated['amount']
                ]
            );

            $balance = $this->getBukuBesarBalance($record->account_id);
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => [
                    'id' => $record->id,
                    'account_id' => $record->account_id,
                    'name' => $record->name,
                    'amount' => $balance,
                    'balance' => $balance
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $type, $id)
    {
        try {
            $validated = $request->validate([
                'account_id' => 'required|string',
                'name' => 'required|string',
                'amount' => 'required|numeric'
            ]);

            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;

            $account = KodeAkun::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->where('account_id', $validated['account_id'])
                ->firstOrFail();

            $model = match($type) {
                'pendapatan' => Pendapatan::class,
                'hpp' => HPP::class,
                'operasional' => BiayaOperasional::class,
                default => throw new \Exception('Invalid type')
            };

            $item = $model::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->findOrFail($id);
            
            if ($item->account_id !== $validated['account_id']) {
                $currentPosition = $this->getAccountCurrentPosition($validated['account_id']);
                if ($currentPosition && $currentPosition !== $type) {
                    throw new \Exception('Akun ini sudah digunakan di kategori ' . ucfirst($currentPosition));
                }
            }
            
            $item->update($validated);

            $balance = $this->getBukuBesarBalance($item->account_id);
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data' => [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $balance,
                    'balance' => $balance
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($type, $id)
    {
        try {
            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;

            $model = match($type) {
                'pendapatan' => Pendapatan::class,
                'hpp' => HPP::class,
                'operasional' => BiayaOperasional::class,
                default => throw new \Exception('Invalid type')
            };

            $item = $model::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->findOrFail($id);
            
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getAllData($company_id, $period_id)
    {
        // Get Pendapatan data
        $pendapatan = Pendapatan::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->with('account')
            ->get()
            ->map(function($item) {
                return [
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'balance' => $this->getBukuBesarBalance($item->account_id)
                ];
            });

        // Get HPP data
        $hpp = HPP::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->with('account')
            ->get()
            ->map(function($item) {
                return [
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'balance' => $this->getBukuBesarBalance($item->account_id)
                ];
            });

        // Get Biaya Operasional data
        $operasional = BiayaOperasional::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->with('account')
            ->get()
            ->map(function($item) {
                return [
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'balance' => $this->getBukuBesarBalance($item->account_id)
                ];
            });

        return compact('pendapatan', 'hpp', 'operasional');
    }

    public function getDataByAccount($account_id)
    {
        try {
            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;

            // Get account details
            $account = KodeAkun::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->where('account_id', $account_id)
                ->firstOrFail();

            // Get balance from buku besar
            $balance = $this->getBukuBesarBalance($account_id);

            return response()->json([
                'success' => true,
                'data' => [
                    'account_id' => $account->account_id,
                    'name' => $account->name,
                    'balance' => $balance
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function refreshBalances()
    {
        try {
            $company_id = auth()->user()->active_company_id;
            $period_id = auth()->user()->company_period_id;
            
            // Refresh all account balances
            $accounts = KodeAkun::where('company_id', $company_id)
                ->where('company_period_id', $period_id)
                ->where('report_type', 'LABARUGI')
                ->get()
                ->map(function($account) {
                    return [
                        'account_id' => $account->account_id,
                        'name' => $account->name,
                        'balance' => $this->getBukuBesarBalance($account->account_id)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $accounts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}