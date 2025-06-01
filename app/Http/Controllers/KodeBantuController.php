<?php

namespace App\Http\Controllers;

use App\Models\KodeBantu;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KodeBantuController extends Controller
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
            return view('staff.kodebantu', ['accounts' => collect()]);
        }

        $accounts = KodeBantu::where('company_id', auth()->user()->active_company_id)
            ->where('company_period_id', auth()->user()->company_period_id)
            ->orderBy('helper_id')
            ->get();
            
        return view('staff.kodebantu', compact('accounts'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'helper_id' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        $exists = KodeBantu::where('company_id', auth()->user()->active_company_id)
                            ->where('company_period_id', auth()->user()->company_period_id)
                            ->where('helper_id', $value)
                            ->exists();
                        
                        if ($exists) {
                            $fail('Kode bantu sudah digunakan dalam periode ini.');
                        }
                    },
                ],
                'name' => 'required|string',
                'status' => 'required|in:PIUTANG,HUTANG',
                'balance' => 'nullable|numeric|min:0'
            ]);

            $validated['company_id'] = auth()->user()->active_company_id;
            $validated['company_period_id'] = auth()->user()->company_period_id;
            
            // Set default value 0 if balance is empty
            $validated['balance'] = $validated['balance'] ?? 0;
            
            $kodeBantu = KodeBantu::create($validated);
            
            return response()->json([
                'success' => true,
                'account' => $kodeBantu
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving kode bantu: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, KodeBantu $kodeBantu)
    {
        if ($kodeBantu->company_id !== auth()->user()->active_company_id || 
            $kodeBantu->company_period_id !== auth()->user()->company_period_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'helper_id' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($kodeBantu) {
                    $exists = KodeBantu::where('company_id', auth()->user()->active_company_id)
                        ->where('company_period_id', auth()->user()->company_period_id)
                        ->where('helper_id', $value)
                        ->where('id', '!=', $kodeBantu->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Kode bantu sudah digunakan dalam periode ini.');
                    }
                },
            ],
            'name' => 'required|string',
            'status' => 'required|in:PIUTANG,HUTANG',
            'balance' => 'nullable|numeric|min:0'
        ]);

        // Set default value 0 if balance is empty
        $validated['balance'] = $validated['balance'] ?? 0;

        $kodeBantu->update($validated);
        
        return response()->json([
            'success' => true,
            'account' => $kodeBantu
        ]);
    }

    public function destroy(KodeBantu $kodeBantu)
    {
        if ($kodeBantu->company_id !== auth()->user()->active_company_id || 
            $kodeBantu->company_period_id !== auth()->user()->company_period_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $kodeBantu->delete();
        
        return response()->json(['success' => true]);
    }

    public function downloadPDF()
    {
        $accounts = KodeBantu::where('company_id', auth()->user()->active_company_id)
            ->where('company_period_id', auth()->user()->company_period_id)
            ->orderBy('helper_id')
            ->get();

        $data = [
            'title' => 'Daftar Kode Bantu',
            'companyName' => auth()->user()->active_company->name ?? 'Perusahaan',
            'headers' => [
                'Kode Bantu',
                'Nama',
                'Status',
                'Saldo Awal'
            ],
            'data' => $accounts->map(function($account) {
                return [
                    $account->helper_id,
                    $account->name,
                    $account->status,
                    number_format($account->balance, 2)
                ];
            }),
            'totals' => [
                number_format($accounts->sum('balance'), 2)
            ]
        ];

        $pdf = PDF::loadView('pdf_template', $data);

        return $pdf->download('Daftar_Kode_Bantu_' . date('YmdHis') . '.pdf');
    }
}