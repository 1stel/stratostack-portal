<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use TaxCloud\Client;

class TaxCloudServiceProvider extends ServiceProvider
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
        $this->app->bind('TaxCloudClient', function () {
            $client = new Client();

            return $client;
        });
    }
}
