<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // precisa estar logado e ter um role permitido
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}
