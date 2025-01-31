<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

        if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']])) {
            $user = Auth::user();
            Session::put('active_role', $user->role);
            
            return redirect()->route('listP');
        }

        return redirect('login')->with('error', 'Username atau password salah');
    }

    public function logout(Request $request)
    {
        Session::forget('active_role');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}