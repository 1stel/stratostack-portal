<?php namespace App\Providers;

use Config;
use Illuminate\Support\ServiceProvider;
use App\DNS\PowerDNSAPI;
use GuzzleHttp\Client;

class DNSInterfaceServiceProvider extends ServiceProvider {

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
        $this->app->bind('App\Repositories\Contracts\DNSInterface',  function() {
            $client = new Client(['base_uri' => Config::get('powerdns.api_url'), 'headers' => ['X-API-Key' => Config::get('powerdns.api_key')]]);

            return new PowerDNSAPI($client);
        });
	}

}
