<?php

namespace App\Http\Controllers;


use App\Models\KodeAkun;
use App\Models\KodeBantu;
use App\Models\JurnalUmum;


use App\Models\Pendapatan;
use App\Models\HPP;
use App\Models\BiayaOperasional;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
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
