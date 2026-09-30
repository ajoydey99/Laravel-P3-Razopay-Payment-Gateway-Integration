<?php
namespace App\Providers;

use App\Services\InvoiceService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(InvoiceService::class, function ($app) {
            return new InvoiceService(
                disk: Storage::disk(config('invoices.disk')),
                downloadLinkExpiry: config('invoices.link_expiry_days')
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
