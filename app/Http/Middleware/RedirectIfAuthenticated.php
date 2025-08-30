<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * Se o usuário já estiver logado, redireciona ele para /dashboard
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        if ($request->user()) {
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
