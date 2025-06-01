<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckActiveCompany
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // Skip check untuk admin
        if ($user && $user->role === 'admin') {
            return $next($request);
        }
        
        if ($user && $user->role === 'staff' && (!$user->active_company_id || !$user->company_period_id)) {
            return redirect('/listP') // Pakai URL langsung, bukan route()
                ->with('warning', 'Silakan pilih perusahaan dan periode terlebih dahulu');
        }
        
        return $next($request);
    }
}