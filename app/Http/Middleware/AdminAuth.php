<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica se o admin está logado
        if (!session()->has('admin_logged_in')) {
            return redirect()->route('admin.login')->with('error', 'Você precisa fazer login para acessar o painel admin.');
        }

        return $next($request);
    }
}

