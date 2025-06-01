<?php

namespace App\Http\Controllers;

use App\Models\Pendapatan;
use App\Models\HPP;
use App\Models\BiayaOperasional;
use Carbon\Carbon;

class DashboardController extends Controller 
{
    public function index()
    {
        $company_id = auth()->user()->active_company_id;
        $company = auth()->user()->active_company;
        
        // Get data from current month
        $currentMonth = Carbon::now();
        
        // Get data from previous month for comparison
        $previousMonth = Carbon::now()->subMonth();

        // Calculate total pendapatan
        $totalPendapatanCurrent = Pendapatan::where('company_id', $company_id)
            ->sum('amount');
        
        $totalPendapatanPrevious = Pendapatan::where('company_id', $company_id)
            // Add where clause for previous month if you have date column
            ->sum('amount');

        // Calculate percentage change for pendapatan
        $pendapatanPercentage = $totalPendapatanPrevious > 0 
            ? (($totalPendapatanCurrent - $totalPendapatanPrevious) / $totalPendapatanPrevious) * 100 
            : 0;

        // Calculate total pengeluaran (HPP + Operasional)
        $totalHPPCurrent = HPP::where('company_id', $company_id)->sum('amount');
        $totalOperasionalCurrent = BiayaOperasional::where('company_id', $company_id)->sum('amount');
        $totalPengeluaranCurrent = $totalHPPCurrent + $totalOperasionalCurrent;

        $totalHPPPrevious = HPP::where('company_id', $company_id)->sum('amount');
        $totalOperasionalPrevious = BiayaOperasional::where('company_id', $company_id)->sum('amount');
        $totalPengeluaranPrevious = $totalHPPPrevious + $totalOperasionalPrevious;

        // Calculate percentage change for pengeluaran
        $pengeluaranPercentage = $totalPengeluaranPrevious > 0 
            ? (($totalPengeluaranCurrent - $totalPengeluaranPrevious) / $totalPengeluaranPrevious) * 100 
            : 0;

        // Calculate laba bersih
        $labaBersihCurrent = $totalPendapatanCurrent - $totalPengeluaranCurrent;
        $labaBersihPrevious = $totalPendapatanPrevious - $totalPengeluaranPrevious;

        // Calculate percentage change for laba bersih
        $labaBersihPercentage = $labaBersihPrevious > 0 
            ? (($labaBersihCurrent - $labaBersihPrevious) / $labaBersihPrevious) * 100 
            : 0;

        // Get total asset from neraca if you have the table
        // For now using dummy data
        $totalAset = 850000000;
        $asetPercentage = 3.2;

        return view('staff.dashboard', compact(
            'company',
            'currentMonth',
            'totalPendapatanCurrent',
            'pendapatanPercentage',
            'totalPengeluaranCurrent', 
            'pengeluaranPercentage',
            'labaBersihCurrent',
            'labaBersihPercentage',
            'totalAset',
            'asetPercentage'
        ));
    }
}