<?php namespace App\Providers;

use App\Repositories\DNSRepository;
use Illuminate\Support\ServiceProvider;

class DNSServiceProvider extends ServiceProvider
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
        $this->app->bind('dns', function () {
            $server = app('App\Repositories\Contracts\DNSInterface');
            return new DNSRepository($server);
        });
    }
}
