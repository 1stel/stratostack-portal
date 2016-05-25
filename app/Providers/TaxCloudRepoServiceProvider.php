<?php

namespace App\Providers;

use App\Repositories\TaxCloudRepository;
use Config;
use Illuminate\Support\ServiceProvider;

class TaxCloudRepoServiceProvider extends ServiceProvider
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
        $this->app->bind('App\Repositories\TaxCloudRepository', function () {
            $client = app('TaxCloudClient');
            $uspsUserId = Config::get('taxcloud.uspsUserId');

            return new TaxCloudRepository($client, $uspsUserId);
        });
    }
}
