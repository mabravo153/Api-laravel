<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JwtAuthServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        include_once app_path().'/helpers/jwtAuth.php';
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
