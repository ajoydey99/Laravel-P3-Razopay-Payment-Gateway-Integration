<?php

use App\Http\Middleware\CheckPaymentResolved;
use App\Http\Middleware\PreventBackHistoryCache;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'payment-resolved' => CheckPaymentResolved::class,
            'prevent-back'     => PreventBackHistoryCache::class,
        ])->validateCsrfTokens(except: [
            'webhooks/razorpay',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
