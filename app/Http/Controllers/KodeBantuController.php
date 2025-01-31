<?php

namespace App\Http\Controllers;

use App\Models\KodeBantu;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KodeBantuController extends Controller
{
   public function index()
   {
       $accounts = KodeBantu::where('company_id', auth()->user()->active_company_id)
           ->orderBy('helper_id')  // Diubah dari code ke helper_id
           ->get();
           
       return view('kodebantu', compact('accounts'));
   }

   public function store(Request $request)
   {
       try {
           $validated = $request->validate([
               'helper_id' => 'required|string', // Diubah dari code ke helper_id
               'name' => 'required|string',
               'status' => 'required|in:PIUTANG,HUTANG',
               'balance' => 'nullable|numeric|min:0'
           ]);

           if (!auth()->user()->active_company_id) {
               return response()->json([
                   'success' => false,
                   'message' => 'Silakan pilih perusahaan terlebih dahulu'
               ], 400);
           }

           $validated['company_id'] = auth()->user()->active_company_id;
           
           // Check unique helper_id within company
           $exists = KodeBantu::where('company_id', $validated['company_id'])
               ->where('helper_id', $validated['helper_id'])
               ->exists();
               
           if ($exists) {
               return response()->json([
                   'success' => false,
                   'message' => 'Kode bantu sudah digunakan'
               ], 422);
           }
           
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
       if ($kodeBantu->company_id !== auth()->user()->active_company_id) {
           return response()->json([
               'success' => false, 
               'message' => 'Unauthorized'
           ], 403);
       }

       $validated = $request->validate([
           'helper_id' => 'required|string', // Diubah dari code ke helper_id
           'name' => 'required|string',
           'status' => 'required|in:PIUTANG,HUTANG',
           'balance' => 'nullable|numeric|min:0'
       ]);

       // Check unique helper_id within company
       $exists = KodeBantu::where('company_id', $kodeBantu->company_id)
           ->where('helper_id', $validated['helper_id'])
           ->where('id', '!=', $kodeBantu->id)
           ->exists();
           
       if ($exists) {
           return response()->json([
               'success' => false,
               'message' => 'Kode bantu sudah digunakan'
           ], 422);
       }

       $kodeBantu->update($validated);
       
       return response()->json([
           'success' => true,
           'account' => $kodeBantu
       ]);
   }

   public function destroy(KodeBantu $kodeBantu)
   {
       if ($kodeBantu->company_id !== auth()->user()->active_company_id) {
           return response()->json([
               'success' => false, 
               'message' => 'Unauthorized'
           ], 403);
       }

       $kodeBantu->delete();
       
       return response()->json(['success' => true]);
   }

   public function downloadPDF()
   {
       $accounts = KodeBantu::where('company_id', auth()->user()->active_company_id)
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