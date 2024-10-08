<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class WatiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Bind WATI Client to the service container
        $this->app->singleton('wati', function () {
            return new Client([
                'base_uri' => config('wati.api_url'),
                'headers' => [
                    'Authorization' => 'Bearer ' . config('wati.api_key'),
                    'Content-Type' => 'application/json',
                ]
            ]);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
