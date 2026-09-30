<?php
namespace App\Actions\Payments;

use App\Models\Payment;
use Razorpay\Api\Api;

class CreateOrder
{
    public function __construct(
        protected Api $api
    ) {}

    public function execute(array $data): Payment
    {
        //1. Create order on Razorpay
        $order = $this->api->order->create([
            'receipt'         => 'order_' . uniqid(),
            'amount'          => $data['amount'] * 100, // convert to paisa
            'currency'        => 'INR',
            'payment_capture' => 1, // Auto-capture payments instantly
        ]);

        //2. Create payment info record
        return Payment::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'amount'   => $data['amount'],
            'order_id' => $order['id'],
            'status'   => 'created',
        ]);
    }
}
