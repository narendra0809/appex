<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force IPv4 for all HTTP requests globally
        // This is needed because CVL KRA API only supports IPv4 whitelisting
        Http::globalOptions([
            'force_ip_resolve' => 'v4',
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);
    }
}
