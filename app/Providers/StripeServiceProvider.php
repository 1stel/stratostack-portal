<?php

namespace App\Providers;

use Cartalyst\Stripe\Stripe;
use Illuminate\Support\ServiceProvider;

class StripeServiceProvider extends ServiceProvider
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
        $this->app->bind(Stripe::class, function ($app) {
            return new Stripe(config('stripe.api_key'), config('stripe.version'));
        });
    }
}