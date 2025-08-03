<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //TODO: Register Services
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::pattern('news', '[0-9]+');

        Route::pattern('file', '[0-9]+');
    }
}
