<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            // Aktifkan file routes/api.php
            Route::prefix('api')
                ->middleware('api')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/api.php'));

            // Aktifkan file routes/web.php
            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/web.php'));
        });
    }
}
