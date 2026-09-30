<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RazorpayWebhookController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

// Test Card
// RuPay:: 6527 6589 0000 1005
// Visa:: 4100 2800 0000 1007

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // for language change
    Route::get('/cards', function () {
        return view('cards.index', ['currentLocale' => 'en']);
    })->name('cards');
    Route::get('/cards/lang/{locale}', function (string $locale) {
        if (! in_array($locale, ['en', 'hin', 'ben'])) {
            abort(404);
        }

        App::setLocale($locale);

        return view('cards.index', ['currentLocale' => $locale]);
    })->name('cards.lang');

    // for payment integration
    Route::controller(PaymentController::class)->prefix('payments')->name('payments.')->group(function () {
        // show pages routes
        Route::get('/', 'formPage')->name('create');
        Route::get('/{payment:order_id}/process', 'processPage')
            ->name('process')
            ->middleware(['payment-resolved', 'prevent-back']);
        Route::get('/{payment:order_id}/retry', 'processPage')
            ->name('retry')
            ->middleware('prevent-back');
        Route::get('/{payment:order_id}/success', 'successPage')
            ->name('success')
            ->middleware('prevent-back');
        Route::get('/{payment:order_id}/failure', 'failurePage')
            ->name('failure')
            ->middleware('prevent-back');

        // handler function routes
        Route::post('/', 'handleOrder')->name('store');
        Route::post('/verify', 'handleVerification')->name('verify');
        Route::get('/failed', 'handleFailed')->name('failed');

        // download invoice using download button
        Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoice.download');

    });

    // getting payment lists
    Route::get('/payments/list', PaymentListController::class)->name('payments.list');
});

// download invoice using signed link sent via email
Route::get('/invoices/{invoice}/download/signed', [InvoiceController::class, 'downloadSigned'])
    ->name('invoice.download.signed')
    ->middleware('signed');

// for razorpay webhook
Route::post('webhooks/razorpay', [RazorpayWebhookController::class, 'handle']);

require __DIR__ . '/auth.php';
