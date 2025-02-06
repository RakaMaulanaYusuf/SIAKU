<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [];

    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Session\Middleware\StartSession::class,
        ],
    ];

    protected $middlewareAliases = [
        'login' => \App\Http\Middleware\LoginMiddleware::class,
        'check.active.company' => \App\Http\Middleware\CheckActiveCompany::class,
    ];
}