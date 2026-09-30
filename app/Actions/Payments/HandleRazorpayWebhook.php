<?php
namespace App\Actions\Payments;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class HandleRazorpayWebhook
{
    public function execute(array $payload)
    {
        $event  = $payload['event'] ?? null;
        $entity = $payload['payload']['payment']['entity'] ?? null;

        if (! $entity) {
            Log::warning('Razorpay webhook missing payment entity', [
                'event' => $event, 'payload' => $payload,
            ]);
            return;
        }

        match ($event) {
            'payment.captured' => $this->handleCapturedEvent($entity),
            'payment.failed'   => $this->handleFailedEvent($entity),
            default            => Log::info('Unhandled Razorpay webhook event', [
                'event' => $event,
            ])
        };

    }

    protected function handleCapturedEvent(array $entity)
    {
        // get payment model using order id
        $payment = Payment::where(
            'order_id', $entity['order_id'] ?? null
        )->first();

        if (! $payment || $payment->status === 'completed') {
            return;
        }

        // update payment table
        $payment->update([
            'razorpay_payment_id'  => $entity['id'],
            'status'               => 'completed',
            'webhook_processed_at' => now(),
        ]);
    }

    protected function handleFailedEvent(array $entity)
    {
        // get payment model using order id
        $payment = Payment::where(
            'order_id', $entity['order_id'] ?? null
        )->first();

        if (! $payment || $payment->status === 'failed') {
            return;
        }

        // update payment table
        $payment->update([
            'status'               => 'failed',
            'failure_reason'       => $entity['error_description'] ?? 'Unknown Error',
            'webhook_processed_at' => now(),
        ]);
    }
}
