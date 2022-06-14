<?php

namespace App\Providers;

use App\Services\Cloudflare\CloudflareApiInterface;
use App\Services\Cloudflare\CloudflareGuzzleSDK;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(CloudflareApiInterface::class, CloudflareGuzzleSDK::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
