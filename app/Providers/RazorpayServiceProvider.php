<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Razorpay\Api\Api;

class RazorpayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Api::class, function ($app) {
            return new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
