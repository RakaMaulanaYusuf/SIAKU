<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check() 
                && auth()->user()->role !== 'admin'
                && !auth()->user()->active_company_id 
                && !in_array($request->route()->getName(), ['listP', 'companies.store', 'companies.setActive'])) {

                return redirect()->route('listP')
                    ->with('warning', 'Silakan pilih perusahaan terlebih dahulu');
            }

            return $next($request);
        });
    }
}