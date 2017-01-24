<?php namespace App\Providers;

use App\Repositories\AuthorizeNetRepository;
use Illuminate\Support\ServiceProvider;

class PaymentGatewayProvider extends ServiceProvider
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
        $this->app->bind('\App\Repositories\Contracts\PaymentRepositoryInterface', function () {
            return new AuthorizeNetRepository;
        });
    }
}
