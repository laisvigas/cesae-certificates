<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * O caminho para onde usuários autenticados devem ser redirecionados.
     */
    public const HOME = '/dashboard';
}
