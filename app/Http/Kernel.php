<?php
namespace App\Http;
use App\Http\Middleware\EnsureUserIsAuthenticated;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middlewareAliases = [
        // ... otros middlewares
        'products.auth' => EnsureUserIsAuthenticated::class,
    ];
}
