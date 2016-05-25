<?php

namespace App\Providers;

use App\Repositories\InstanceRepo;
use Illuminate\Support\ServiceProvider;

class InstanceRepoProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind('App\Repositories\InstanceRepo', function ($app) {
            return new InstanceRepo($app['Cloudstack\CloudStackClient']);
        });
    }
}
