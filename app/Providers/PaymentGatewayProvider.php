<?php namespace App\Providers;

use App\Repositories\AuthorizeNetRepository;
use Illuminate\Support\ServiceProvider;

class PaymentGatewayProvider extends ServiceProvider {

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
        $gw = env('PAYMENT_GATEWAY') . 'Repository';



        $this->app->bind('\App\Repositories\Contracts\PaymentRepositoryInterface', function () use ($gw) {
            $blah = 'AuthorizeNetRepository';
//            return app($blah);
            return new AuthorizeNetRepository;
        });

//        $this->app->bind('\App\Repositories\Contracts\PaymentRepositoryInterface', $gw . 'Repository');
	}

}
