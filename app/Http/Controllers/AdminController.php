<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\CompanyPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalStaff = User::where('role', 'staff')->count();
        $totalViewers = User::where('role', 'viewer')->count();
        $totalCompanies = Company::count();
        $recentUsers = User::latest()->take(5)->get();
        $unassignedViewers = User::where('role', 'viewer')
            ->whereNull('assigned_company_id')
            ->count();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalStaff', 'totalViewers', 
            'totalCompanies', 'recentUsers', 'unassignedViewers'
        ));
    }

    // Kelola Akun Staff
    public function manageAccounts()
    {
        $users = User::whereIn('role', ['staff', 'viewer'])
            ->with(['assignedCompany', 'assignedPeriod'])
            ->latest()
            ->get();
        
        return view('admin.manage-accounts', compact('users'));
    }

    // Create User
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:staff,viewer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat',
            'user' => $user
        ]);
    }

    // Update User
    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'role' => 'required|in:staff,viewer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate',
            'user' => $user
        ]);
    }

    // Delete User
    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak dapat dihapus'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }

    // Reset Password
    public function resetPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset'
        ]);
    }

    // Daftar Perusahaan
    public function listCompanies()
    {
        $companies = Company::with(['periods', 'assignedViewers'])->get();
        return view('admin.companies', compact('companies'));
    }

    // Assign Company to Viewer
    public function assignCompany()
    {
        $viewers = User::where('role', 'viewer')->get();
        $companies = Company::with('periods')->get();
        
        return view('admin.assign-company', compact('viewers', 'companies'));
    }

    // Store Company Assignment
    public function storeAssignment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'viewer_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
            'period_id' => 'required|exists:company_period,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $viewer = User::findOrFail($request->viewer_id);
        
        if ($viewer->role !== 'viewer') {
            return response()->json([
                'success' => false,
                'message' => 'User harus memiliki role viewer'
            ], 422);
        }

        $period = CompanyPeriod::where('id', $request->period_id)
            ->where('company_id', $request->company_id)
            ->first();

        if (!$period) {
            return response()->json([
                'success' => false,
                'message' => 'Periode tidak valid untuk perusahaan yang dipilih'
            ], 422);
        }

        $viewer->update([
            'assigned_company_id' => $request->company_id,
            'assigned_company_period_id' => $request->period_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perusahaan berhasil diassign ke viewer'
        ]);
    }

    // Get Company Periods (AJAX)
    public function getCompanyPeriods($companyId)
    {
        $periods = CompanyPeriod::where('company_id', $companyId)
            ->orderBy('period_year', 'desc')
            ->orderByRaw("FIELD(period_month, 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember')")
            ->get()
            ->map(function ($period) {
                return [
                    'id' => $period->id,
                    'period_name' => $period->period_month . ' ' . $period->period_year,
                    'period_month' => $period->period_month,
                    'period_year' => $period->period_year
                ];
            });

        return response()->json($periods);
    }

    // Unassign Company from Viewer
    public function unassignCompany(User $user)
    {
        if ($user->role !== 'viewer') {
            return response()->json([
                'success' => false,
                'message' => 'User bukan viewer'
            ], 422);
        }

        $user->update([
            'assigned_company_id' => null,
            'assigned_company_period_id' => null,
            'active_company_id' => null,
            'company_period_id' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment perusahaan berhasil dihapus'
        ]);
    }
}