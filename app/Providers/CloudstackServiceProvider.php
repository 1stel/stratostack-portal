<?php namespace App\Providers;

use Cloudstack\CloudStackClient;
use Illuminate\Support\ServiceProvider;
use Config;

class CloudstackServiceProvider extends ServiceProvider
{

//	protected $cloudstack;

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
        $this->app->bind('Cloudstack\CloudStackClient', function () {
            $cfg = Config::get('cloud.mgmtServer');

            // Endpoint, API Key, Secret Key
            try {
                return new CloudStackClient($cfg['url'], $cfg['apiKey'], $cfg['secretKey']);
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        });
    }
}
