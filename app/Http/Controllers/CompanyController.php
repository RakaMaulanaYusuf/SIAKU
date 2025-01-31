<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->middleware(function ($request, $next) {
            if (auth()->check()) {
                if (!in_array($request->route()->getName(), ['listP', 'companies.store', 'companies.setActive'])) {
                    if (!auth()->user()->active_company_id) {
                        return redirect()->route('listP')
                            ->with('warning', 'Silakan pilih perusahaan terlebih dahulu');
                    }
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        $companies = Company::all();
        $activeCompany = Auth::user()->activeCompany;
        
        return view('listperusahaan', compact('companies', 'activeCompany'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'period_month' => 'required|string',
            'period_year' => 'required|integer|min:2000|max:2099',
        ]);

        $company = Company::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'period_month' => $validated['period_month'],
            'period_year' => $validated['period_year'],
            'status' => 'Aktif'
        ]);

        return response()->json([
            'success' => true,
            'company' => $company
        ]);
    }

    public function setActive(Company $company)
    {
        $user = Auth::user();
        $user->active_company_id = $company->id;
        $user->save();

        return redirect()->back()->with('success', 'Perusahaan aktif berhasil diubah');
    }
}