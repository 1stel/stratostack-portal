<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\SecurityGroupRepo;

class SecurityGroupRepoProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\SecurityGroupRepo', function ($app) {
            return new SecurityGroupRepo($app['cloudstack']);
        });
    }
}
