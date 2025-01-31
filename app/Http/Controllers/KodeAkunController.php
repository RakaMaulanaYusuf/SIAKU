<?php

namespace App\Http\Controllers;

use App\Models\KodeAkun;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KodeAkunController extends Controller
{
    public function index()
    {
        $accounts = KodeAkun::where('company_id', auth()->user()->active_company_id)
            ->orderBy('account_id')  // Diubah dari code ke account_id
            ->get();
            
        return view('kodeakun', compact('accounts'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'account_id' => 'required|string',  // Diubah dari code ke account_id
                'name' => 'required|string',
                'helper_table' => 'nullable|string',  // Diubah dari table ke helper_table
                'balance_type' => 'required|in:DEBIT,CREDIT',  // Disesuaikan dengan enum di migration
                'report_type' => 'required|in:NERACA,LABARUGI', // Disesuaikan dengan enum di migration
                'debit' => [
                    'nullable',
                    'numeric',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->balance_type === 'DEBIT' && empty($value)) {
                            $fail('Kolom debit harus diisi ketika pos saldo DEBIT.');
                        }
                        if ($request->balance_type === 'CREDIT' && !empty($value)) {
                            $fail('Kolom debit harus kosong ketika pos saldo CREDIT.');
                        }
                    },
                ],
                'credit' => [
                    'nullable',
                    'numeric',
                    function ($attribute, $value, $fail) use ($request) {
                        if ($request->balance_type === 'CREDIT' && empty($value)) {
                            $fail('Kolom kredit harus diisi ketika pos saldo CREDIT.');
                        }
                        if ($request->balance_type === 'DEBIT' && !empty($value)) {
                            $fail('Kolom kredit harus kosong ketika pos saldo DEBIT.');
                        }
                    },
                ],
            ]);

            if (!auth()->user()->active_company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan pilih perusahaan terlebih dahulu'
                ], 400);
            }

            $validated['company_id'] = auth()->user()->active_company_id;
            
            // Set the unused field to null based on balance_type
            if ($validated['balance_type'] === 'DEBIT') {
                $validated['credit'] = null;
            } else {
                $validated['debit'] = null;
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
        if ($kodeAkun->company_id !== auth()->user()->active_company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'account_id' => 'required|string',  // Diubah dari code ke account_id
            'name' => 'required|string',
            'helper_table' => 'nullable|string',  // Diubah dari table ke helper_table
            'balance_type' => 'required|in:DEBIT,CREDIT',  // Disesuaikan
            'report_type' => 'required|in:NERACA,LABARUGI',  // Disesuaikan
            'debit' => [
                'nullable',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->balance_type === 'DEBIT' && empty($value)) {
                        $fail('Kolom debit harus diisi ketika pos saldo DEBIT.');
                    }
                    if ($request->balance_type === 'CREDIT' && !empty($value)) {
                        $fail('Kolom debit harus kosong ketika pos saldo CREDIT.');
                    }
                },
            ],
            'credit' => [
                'nullable',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->balance_type === 'CREDIT' && empty($value)) {
                        $fail('Kolom kredit harus diisi ketika pos saldo CREDIT.');
                    }
                    if ($request->balance_type === 'DEBIT' && !empty($value)) {
                        $fail('Kolom kredit harus kosong ketika pos saldo DEBIT.');
                    }
                },
            ],
        ]);

        if ($validated['balance_type'] === 'DEBIT') {
            $validated['credit'] = null;
        } else {
            $validated['debit'] = null;
        }

        $kodeAkun->update($validated);
        
        return response()->json([
            'success' => true,
            'account' => $kodeAkun
        ]);
    }

    public function destroy(KodeAkun $kodeAkun)
    {
        if ($kodeAkun->company_id !== auth()->user()->active_company_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $kodeAkun->delete();
        
        return response()->json(['success' => true]);
    }

    public function downloadPDF()
    {
        $accounts = KodeAkun::where('company_id', auth()->user()->active_company_id)
            ->orderBy('account_id')  // Diubah dari code ke account_id
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
                    $account->account_id,  // Diubah dari code ke account_id
                    $account->name,
                    $account->helper_table ?? '-',  // Diubah dari table ke helper_table
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