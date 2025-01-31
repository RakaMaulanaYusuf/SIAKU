<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginMiddleware
{
    public function handle(Request $request, Closure $next, string $roleName): Response
    {
        if (!Auth::check()) {
            return redirect('login')->with('error', 'Please login first');
        }

        $user = Auth::user();

        if (!$user->hasRole($roleName)) {
            return redirect('login')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}