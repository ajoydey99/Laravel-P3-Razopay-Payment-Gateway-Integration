<?php
namespace App\Http\Controllers;

use App\Actions\Payments\HandleRazorpayWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayWebhookController extends Controller
{
    public function __construct(
        protected HandleRazorpayWebhook $handleEvent,
        protected Api $api
    ) {}

    public function handle(Request $request)
    {
        //1. Get razorpay signature and content
        $signature = $request->header('X-Razorpay-Signature');
        $payload   = $request->getContent();

        if (empty($signature) || empty($payload)) {
            return response()->json([
                'message' => 'Missing signature or data'], 400
            );
        }

        //2. Verify signature using webhook secret
        try {
            $this->api->utility->verifyWebhookSignature(
                $payload, $signature,
                config('services.razorpay.webhook_secret')
            );
        } catch (SignatureVerificationError $e) {
            // If signature verification failed
            Log::warning('Invalid Razorpay webhook signature received');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // If signature verification passed
        try {
            Log::info(
                'Razorpay webhook recieved',
                ['event' => $request->input('event')]
            );
            //3. Handle webhook events
            $this->handleEvent->execute($request->all());

        } catch (\Throwable $e) {
            Log::error(
                'Error processing Razorpay webhook',
                ['error' => $e->getMessage()]
            );
        }

        return response()->json(['status' => 'ok']);
    }
}
