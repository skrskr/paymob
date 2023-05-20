<?php

declare(strict_types=1);


namespace Skrskr\Paymob;

use Illuminate\Support\ServiceProvider;
use Skrskr\Paymob\Facades\Paymob;



class PaymobServiceProvider extends ServiceProvider
{
    public function boot()
    {

        $this->publishes([
            __DIR__.'./../config/paymob.php' => config_path('paymob.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'./../config/paymob.php', 'paymob');

        // PayMob Facede.
        $this->app->singleton('paymob', function () {
            return new Paymob();
        });
    }
}
