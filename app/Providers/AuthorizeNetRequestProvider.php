<?php namespace App\Providers;

use Config;
use AuthorizeNet\Service\Cim\Request;
use Illuminate\Support\ServiceProvider;

class AuthorizeNetRequestProvider extends ServiceProvider
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
        $this->app->bind('AuthorizeNetRequest', function () {
            return new Request(Config::get('authorizenet.api_login_id'), Config::get('authorizenet.transaction_key'));
        });
    }
}
