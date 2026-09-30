<?php
namespace App\Actions\Payments;

use App\Models\Payment;

class RecordFailedPayment
{
    public function execute(array $data): Payment
    {
        $payment = Payment::where(
            'order_id', $data['order_id'],
        )->first();

        if ($payment && $payment->status !== 'completed') {
            $payment->markAsFailed($data['error_description']);
        }

        return $payment;
    }
}
