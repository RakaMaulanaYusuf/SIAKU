<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('/login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']])) {
            $user = Auth::user();
            $request->session()->regenerate();
            
            if ($user->role === 'viewer') {
                $user->update(['active_company_id' => $user->assigned_company_id]);
                return redirect()->route('vdashboard');
            } else {
                return redirect()->route('listP');
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
