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
    public function index() {
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
    
        return view('neraca', compact('aktivalancar', 'aktivatetap', 'kewajiban', 'ekuitas', 'availableAccounts'));
    }

    private function getBukuBesarBalance($account_id) {
        $bukuBesarController = new BukuBesarController();
        $balance = $bukuBesarController->getAccountBalance(
            auth()->user()->active_company_id, 
            $account_id
        );
        
        $account = KodeAkun::where('account_id', $account_id)
            ->where('company_id', auth()->user()->active_company_id)
            ->first();
        
        return $balance;
    }

    private function getAccountCurrentPosition($account_id)
    {
        $company_id = auth()->user()->active_company_id;

        if (AktivaLancar::where('company_id', $company_id)->where('account_id', $account_id)->exists()) {
            return 'aktivalancar';
        } elseif (AktivaTetap::where('company_id', $company_id)->where('account_id', $account_id)->exists()) {
            return 'aktivatetap';
        } elseif (Kewajiban::where('company_id', $company_id)->where('account_id', $account_id)->exists()) {
            return 'kewajiban';
        } elseif (Ekuitas::where('company_id', $company_id)->where('account_id', $account_id)->exists()) {
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
                'aktivalancar' => AktivaLancar::class,
                'aktivatetap' => AktivaTetap::class,
                'kewajiban' => Kewajiban::class,
                'ekuitas' => Ekuitas::class,
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
                'aktivalancar' => AktivaLancar::class,
                'aktivatetap' => AktivaTetap::class,
                'kewajiban' => Kewajiban::class,
                'ekuitas' => Ekuitas::class,
                default => throw new \Exception('Invalid type')
            };

            $item = $model::where('company_id', $company_id)->findOrFail($id);
            
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
                'aktivalancar' => AktivaLancar::class,
                'aktivatetap' => AktivaTetap::class,
                'kewajiban' => Kewajiban::class,
                'ekuitas' => Ekuitas::class,
                default => throw new \Exception('Invalid type')
            };

            $item = $model::where('company_id', $company_id)->findOrFail($id);
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
            $totalAktivaLancar = $data['aktivalancar']->sum('balance');
            $totalAktivaTetap = $data['aktivatetap']->sum('balance');
            $totalKewajiban = $data['kewajiban']->sum('balance');
            $totalEkuitas = $data['ekuitas']->sum('balance');

            $data['totals'] = [
                'aktiva_lancar' => $totalAktivaLancar,
                'aktiva_tetap' => $totalAktivaTetap,
                'total_aktiva' => $totalAktivaLancar + $totalAktivaTetap,
                'kewajiban' => $totalKewajiban,
                'ekuitas' => $totalEkuitas,
                'total_pasiva' => $totalKewajiban + $totalEkuitas
            ];

            $data['company'] = auth()->user()->active_company;
            $data['tanggal'] = now()->translatedFormat('d F Y');

            $pdf = PDF::loadView('pdf.neraca', $data);
            
            return $pdf->download('laporan-neraca-' . now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat generate PDF: ' . $e->getMessage());
        }
    }

    private function getAllData($company_id)
    {
        $data = [
            'aktivalancar' => AktivaLancar::where('company_id', $company_id)
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