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
    public function index() {
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
    
        return view('labarugi', compact('pendapatan', 'hpp', 'biaya', 'availableAccounts'));
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

    private function getAccountCurrentPosition($account_id)
    {
        $company_id = auth()->user()->active_company_id;

        // Check current account position (pendapatan/hpp/biaya)
        if (Pendapatan::where('company_id', $company_id)->where('account_id', $account_id)->exists()) {
            return 'pendapatan';
        } elseif (HPP::where('company_id', $company_id)->where('account_id', $account_id)->exists()) {
            return 'hpp';
        } elseif (BiayaOperasional::where('company_id', $company_id)->where('account_id', $account_id)->exists()) {
            return 'biaya';
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
            
            // Check if account exists and belongs to company
            $account = KodeAkun::where('company_id', $company_id)
                ->where('account_id', $validated['account_id'])
                ->firstOrFail();

            // Check if account is already used in another category
            $currentPosition = $this->getAccountCurrentPosition($validated['account_id']);
            if ($currentPosition && $currentPosition !== $validated['type']) {
                throw new \Exception('Akun ini sudah digunakan di kategori ' . ucfirst($currentPosition));
            }
            
            // Determine which model to use based on type
            $model = match($validated['type']) {
                'pendapatan' => Pendapatan::class,
                'hpp' => HPP::class,
                'operasional' => BiayaOperasional::class,
                default => throw new \Exception('Invalid type')
            };

            // Create or update the record
            $record = $model::updateOrCreate(
                [
                    'company_id' => $company_id,
                    'account_id' => $validated['account_id']
                ],
                [
                    'name' => $validated['name'],
                    'amount' => $validated['amount']
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => [
                    'id' => $record->id,
                    'account_id' => $record->account_id,
                    'name' => $record->name,
                    'amount' => $record->amount,
                    'balance' => $this->getBukuBesarBalance($record->account_id)
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

            // Check if account exists and belongs to company
            $account = KodeAkun::where('company_id', $company_id)
                ->where('account_id', $validated['account_id'])
                ->firstOrFail();

            // Determine which model to use
            $model = match($type) {
                'pendapatan' => Pendapatan::class,
                'hpp' => HPP::class,
                'operasional' => BiayaOperasional::class,
                default => throw new \Exception('Invalid type')
            };

            $item = $model::where('company_id', $company_id)
                ->findOrFail($id);
            
            // Check if new account_id is different and already used
            if ($item->account_id !== $validated['account_id']) {
                $currentPosition = $this->getAccountCurrentPosition($validated['account_id']);
                if ($currentPosition && $currentPosition !== $type) {
                    throw new \Exception('Akun ini sudah digunakan di kategori ' . ucfirst($currentPosition));
                }
            }
            
            $item->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diupdate',
                'data' => [
                    'id' => $item->id,
                    'account_id' => $item->account_id,
                    'name' => $item->name,
                    'amount' => $item->amount,
                    'balance' => $this->getBukuBesarBalance($item->account_id)
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

            $model = match($type) {
                'pendapatan' => Pendapatan::class,
                'hpp' => HPP::class,
                'operasional' => BiayaOperasional::class,
                default => throw new \Exception('Invalid type')
            };

            $item = $model::where('company_id', $company_id)
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

    public function generatePDF()
    {
        try {
            $company_id = auth()->user()->active_company_id;
            
            $data = $this->getAllData($company_id);
            
            // Calculate totals
            $totalPendapatan = $data['pendapatan']->sum('balance');
            $totalHPP = $data['hpp']->sum('balance');
            $totalBiaya = $data['operasional']->sum('balance');
            $labaBersih = $totalPendapatan - ($totalHPP + $totalBiaya);

            $data['totals'] = [
                'pendapatan' => $totalPendapatan,
                'hpp' => $totalHPP,
                'operasional' => $totalBiaya,
                'laba_bersih' => $labaBersih
            ];

            $data['company'] = auth()->user()->active_company;
            $data['tanggal'] = now()->translatedFormat('d F Y');

            $pdf = PDF::loadView('pdf.labarugi', $data);
            
            return $pdf->download('laporan-laba-rugi-' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat generate PDF: ' . $e->getMessage());
        }
    }

    private function getAllData($company_id)
    {
        // Get Pendapatan data
        $pendapatan = Pendapatan::where('company_id', $company_id)
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
        $biaya = BiayaOperasional::where('company_id', $company_id)
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

            // Get account details
            $account = KodeAkun::where('company_id', $company_id)
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
            
            // Refresh all account balances
            $accounts = KodeAkun::where('company_id', $company_id)
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