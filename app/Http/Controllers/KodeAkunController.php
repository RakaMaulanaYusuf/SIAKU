<?php

namespace App\Http\Controllers;

use App\Models\KodeAkun;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KodeAkunController extends Controller
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
            return view('staff.kodeakun', ['accounts' => collect()]);
        }

        $accounts = KodeAkun::where('company_id', auth()->user()->active_company_id)
            ->where('company_period_id', auth()->user()->company_period_id)
            ->orderBy('account_id')
            ->get();
            
        return view('staff.kodeakun', compact('accounts'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'account_id' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        $exists = KodeAkun::where('company_id', auth()->user()->active_company_id)
                            ->where('company_period_id', auth()->user()->company_period_id)
                            ->where('account_id', $value)
                            ->exists();
                        
                        if ($exists) {
                            $fail('Kode akun sudah digunakan dalam periode ini.');
                        }
                    },
                ],
                'name' => 'required|string',
                'helper_table' => 'nullable|string',
                'balance_type' => 'required|in:DEBIT,CREDIT',
                'report_type' => 'required|in:NERACA,LABARUGI',
                'debit' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->balance_type === 'CREDIT' && !empty($value)) {
                            $fail('Kolom debit harus kosong ketika pos saldo CREDIT.');
                        }
                    },
                ],
                'credit' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->balance_type === 'DEBIT' && !empty($value)) {
                            $fail('Kolom kredit harus kosong ketika pos saldo DEBIT.');
                        }
                    },
                ],
            ]);

            // Automatically add company_id and company_period_id
            $validated['company_id'] = auth()->user()->active_company_id;
            $validated['company_period_id'] = auth()->user()->company_period_id;
            
            // Set the unused field to null based on balance_type
            if ($validated['balance_type'] === 'DEBIT') {
                $validated['credit'] = null;
                $validated['debit'] = $validated['debit'] ?? 0;
            } else {
                $validated['debit'] = null;
                $validated['credit'] = $validated['credit'] ?? 0;
            }
            
            $kodeAkun = KodeAkun::create($validated);
            
            return response()->json([
                'success' => true,
                'account' => $kodeAkun
            ]);
        } catch (\Exception $e) {
            \Log::error('Error saving kode akun: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, KodeAkun $kodeAkun)
    {
        if ($kodeAkun->company_id !== auth()->user()->active_company_id || 
            $kodeAkun->company_period_id !== auth()->user()->company_period_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'account_id' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($kodeAkun) {
                    $exists = KodeAkun::where('company_id', auth()->user()->active_company_id)
                        ->where('company_period_id', auth()->user()->company_period_id)
                        ->where('account_id', $value)
                        ->where('id', '!=', $kodeAkun->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Kode akun sudah digunakan dalam periode ini.');
                    }
                },
            ],
            'name' => 'required|string',
            'helper_table' => 'nullable|string',
            'balance_type' => 'required|in:DEBIT,CREDIT',
            'report_type' => 'required|in:NERACA,LABARUGI',
            'debit' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->balance_type === 'CREDIT' && !empty($value)) {
                        $fail('Kolom debit harus kosong ketika pos saldo CREDIT.');
                    }
                },
            ],
            'credit' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->balance_type === 'DEBIT' && !empty($value)) {
                        $fail('Kolom kredit harus kosong ketika pos saldo DEBIT.');
                    }
                },
            ],
        ]);

        if ($validated['balance_type'] === 'DEBIT') {
            $validated['credit'] = null;
            $validated['debit'] = $validated['debit'] ?? 0;
        } else {
            $validated['debit'] = null;
            $validated['credit'] = $validated['credit'] ?? 0;
        }

        $kodeAkun->update($validated);
        
        return response()->json([
            'success' => true,
            'account' => $kodeAkun
        ]);
    }

    public function destroy(KodeAkun $kodeAkun)
    {
        if ($kodeAkun->company_id !== auth()->user()->active_company_id || 
            $kodeAkun->company_period_id !== auth()->user()->company_period_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $kodeAkun->delete();
        
        return response()->json(['success' => true]);
    }

    public function downloadPDF()
    {
        $accounts = KodeAkun::where('company_id', auth()->user()->active_company_id)
            ->where('company_period_id', auth()->user()->company_period_id)
            ->orderBy('account_id')
            ->get();

        $data = [
            'title' => 'Daftar Kode Akun',
            'companyName' => auth()->user()->active_company->name ?? 'Perusahaan',
            'headers' => [
                'Kode Akun', 
                'Nama Akun', 
                'Tabel Bantuan', 
                'Pos Saldo', 
                'Pos Laporan', 
                'Debet', 
                'Kredit'
            ],
            'data' => $accounts->map(function($account) {
                return [
                    $account->account_id,
                    $account->name,
                    $account->helper_table ?? '-',
                    $account->balance_type,
                    $account->report_type,
                    $account->balance_type == 'DEBIT' ? number_format($account->debit, 2) : '-',
                    $account->balance_type == 'CREDIT' ? number_format($account->credit, 2) : '-'
                ];
            }),
            'totals' => [
                number_format($accounts->sum('debit'), 2),
                number_format($accounts->sum('credit'), 2)
            ]
        ];

        $pdf = PDF::loadView('pdf_template', $data);

        return $pdf->download('Daftar_Kode_Akun_' . date('YmdHis') . '.pdf');
    }
}