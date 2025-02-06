<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckActiveCompany
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if ($user->role === 'staff' && !$user->active_company_id) {
            return redirect()->route('listP')
                ->with('warning', 'Silakan pilih perusahaan terlebih dahulu');
        }
        
        return $next($request);
    }
}