<?php
namespace App\Observers;

use App\Models\Payment;
use Illuminate\Support\Facades\Cache;

class PaymentObserver
{
    // use a new cache version, old cache expire naturally
    protected function invalidateListCache()
    {
        Cache::increment('payments.list.version');
    }

    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        $this->invalidateListCache();
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        if ($payment->wasChanged(['status', 'razorpay_payment_id'])) {
            $this->invalidateListCache();
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        $this->invalidateListCache();
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }

}
