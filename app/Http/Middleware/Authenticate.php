<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Se não estiver autenticado, redireciona para /login (rotas web).
     */
    protected function redirectTo($request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
