<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']], true)) {
            $user = Auth::user();
            
            $request->session()->regenerate();
            $request->session()->save();
            
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard'); // Pakai URL langsung, bukan route()
            } 
            // elseif ($user->role === 'viewer') {
            //     $user->update(['active_company_id' => $user->assigned_company_id]);
            //     $user->update(['company_period_id' => $user->assigned_company_period_id]);
            //     return redirect('/vdashboard');
            // } 
            else {
                return redirect('/listP'); // Pakai URL langsung, bukan route()
            }
        }

        return back()->withErrors(['username' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}