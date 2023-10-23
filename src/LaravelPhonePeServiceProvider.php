<?php

namespace Dipesh79\LaravelPhonePe;

use Illuminate\Support\ServiceProvider;

class LaravelPhonePeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/config/phonepe.php' => config_path('phonepe.php'),
            ], 'config'
        );
    }
}
