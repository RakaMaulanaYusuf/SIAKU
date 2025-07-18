<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check()) {
                if (!in_array($request->route()->getName(), ['listP', 'companies.store', 'companies.setActive', 'periods.store', 'companies.destroy', 'companies.update'])) { // Added 'companies.update'
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
        $companies = Company::with(['periods' => function($query) {
            $query->orderBy('period_year', 'desc')
                    ->orderBy('period_month', 'desc');
        }])->get();

        $activeCompany = Auth::user()->activeCompany;

        return view('staff.listperusahaan', compact('companies', 'activeCompany'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|string|max:255',
                'period_month' => 'required|string',
                'period_year' => 'required|integer|min:2000|max:2099',
            ]);

            $company = Company::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
            ]);

            $period = CompanyPeriod::create([
                'company_id' => $company->id,
                'period_month' => $validated['period_month'],
                'period_year' => $validated['period_year']
            ]);

            DB::commit();

            $company->load('periods');

            return response()->json([
                'success' => true,
                'company' => $company
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan perusahaan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storePeriod(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_id' => 'required|exists:companies,id',
                'period_month' => 'required|string',
                'period_year' => 'required|integer|min:2000|max:2099'
            ]);

            // Check for duplicate period
            $exists = CompanyPeriod::where('company_id', $validated['company_id'])
                ->where('period_month', $validated['period_month'])
                ->where('period_year', $validated['period_year'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tersebut sudah ada untuk perusahaan ini'
                ], 422);
            }

            $period = CompanyPeriod::create([
                'company_id' => $validated['company_id'],
                'period_month' => $validated['period_month'],
                'period_year' => $validated['period_year']
            ]);

            return response()->json([
                'success' => true,
                'period' => $period
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan periode: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setActive(Request $request, Company $company)
    {
        try {
            $validated = $request->validate([
                'period_id' => 'required|exists:company_period,id,company_id,' . $company->id
            ]);

            $user = Auth::user();

            $user->update([
                'active_company_id' => $company->id,
                'company_period_id' => $validated['period_id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan dan periode berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah perusahaan dan periode: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Company $company)
    {
        DB::beginTransaction();
        try {
            // Check if company is currently active for any user
            $activeUsers = \App\Models\User::where('active_company_id', $company->id)->count();

            if ($activeUsers > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus perusahaan yang sedang aktif digunakan'
                ], 422);
            }

            // Delete all periods related to this company
            CompanyPeriod::where('company_id', $company->id)->delete();

            // Delete the company
            $company->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus perusahaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified company in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Company $company)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|string|max:255',
            ]);

            $company->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Perusahaan berhasil diperbarui',
                'company' => $company // Return the updated company data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui perusahaan: ' . $e->getMessage()
            ], 500);
        }
    }
}