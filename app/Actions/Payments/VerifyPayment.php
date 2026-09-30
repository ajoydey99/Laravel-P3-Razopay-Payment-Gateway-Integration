<?php
namespace App\Actions\Payments;

use App\Jobs\GenerateInvoiceJob;
use App\Models\Payment;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class VerifyPayment
{
    public function __construct(
        protected Api $api,
        protected InvoiceService $invoice
    ) {}

    public function execute(array $data): Payment
    {
        // 2.Retrieve payment info to verify
        $payment = Payment::where(
            'order_id', $data['razorpay_order_id']
        )->first();

        if (! $payment) {
            throw new \RuntimeException(
                'Order not found for order_id: ' . $data['razorpay_order_id']
            );
        }

        try {
            // 3.Signature verification initiated
            $attributes = [
                'razorpay_order_id'   => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature'  => $data['razorpay_signature'],
            ];

            $this->api->utility->verifyPaymentSignature($attributes);

        } catch (SignatureVerificationError $e) {
            // 4. Failed signature verification event triggered
            $payment->markAsFailed('Razorpay signature verification failed!');
            throw $e;
        }

        // 4.Success signature verification event triggered
        $payment->markAsCompleted($attributes);

        try {
            // 5. Dispatch invoice job
            GenerateInvoiceJob::dispatch($payment);
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch invoice generation job', [
                'payment_id' => $payment->id,
                'error'      => $e->getMessage(),
            ]);
        }

        return $payment;
    }
}
