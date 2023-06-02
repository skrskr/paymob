<?php

declare(strict_types=1);


namespace Skrskr\Paymob;

use Illuminate\Support\ServiceProvider;
use Skrskr\Paymob\Paymob;



class PaymobServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/paymob.php' => config_path('paymob.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/paymob.php', 'paymob');

        // PayMob Facede.
        $this->app->bind('paymob', function () {
            return new Paymob();
        });
    }
}
