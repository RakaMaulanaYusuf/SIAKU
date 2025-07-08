<?php

namespace App\Http\Controllers;

use App\Models\AktivaLancar;
use App\Models\AktivaTetap;
use App\Models\Kewajiban;
use App\Models\Ekuitas;
use App\Models\KodeAkun;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class NeracaController extends Controller
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
            return view('staff.neraca', [
                'aktivalancar' => collect(),
                'aktivatetap' => collect(),
                'kewajiban' => collect(),
                'ekuitas' => collect(),
                'availableAccounts' => collect()
            ]);
        }

        $company_id = auth()->user()->active_company_id;
        $period_id = auth()->user()->company_period_id;
        
        $availableAccounts = KodeAkun::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('report_type', 'NERACA')
            ->get()
            ->map(function($account) {
                return [
                    'account_id' => $account->account_id, 
                    'name' => $account->name,
                    'balance' => $this->getBukuBesarBalance($account->account_id)
                ];
            });
    
        $aktivalancar = AktivaLancar::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
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
            ->where('company_period_id', $period_id)
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
            ->where('company_period_id', $period_id)
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
            ->where('company_period_id', $period_id)
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
    
        return view('staff.neraca', compact('aktivalancar', 'aktivatetap', 'kewajiban', 'ekuitas', 'availableAccounts'));
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

        if (AktivaLancar::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('account_id', $account_id)
            ->exists()) {
            return 'aktivalancar';
        } elseif (AktivaTetap::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('account_id', $account_id)
            ->exists()) {
            return 'aktivatetap';
        } elseif (Kewajiban::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('account_id', $account_id)
            ->exists()) {
            return 'kewajiban';
        } elseif (Ekuitas::where('company_id', $company_id)
            ->where('company_period_id', $period_id)
            ->where('account_id', $account_id)
            ->exists()) {
            return 'ekuitas';
        }
        return null;
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:aktivalancar,aktivatetap,kewajiban,ekuitas',
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
                'aktivalancar' => AktivaLancar::class,
                'aktivatetap' => AktivaTetap::class,
                'kewajiban' => Kewajiban::class,
                'ekuitas' => Ekuitas::class,
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
                'aktivalancar' => AktivaLancar::class,
                'aktivatetap' => AktivaTetap::class,
                'kewajiban' => Kewajiban::class,
                'ekuitas' => Ekuitas::class,
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
                    'aktivalancar' => AktivaLancar::class,
                    'aktivatetap' => AktivaTetap::class,
                    'kewajiban' => Kewajiban::class,
                    'ekuitas' => Ekuitas::class,
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
            $data = [
                'aktivalancar' => AktivaLancar::where('company_id', $company_id)
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
                    }),
    
                'aktivatetap' => AktivaTetap::where('company_id', $company_id)
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
                    }),
    
                'kewajiban' => Kewajiban::where('company_id', $company_id)
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
                    }),
    
                'ekuitas' => Ekuitas::where('company_id', $company_id)
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
                    })
            ];
    
            return $data;
        }
    }