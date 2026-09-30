<?php
namespace App\Http\Controllers;

use App\Actions\Payments\CreateOrder;
use App\Actions\Payments\RecordFailedPayment;
use App\Actions\Payments\VerifyPayment;
use App\Http\Requests\Payment\PaymentFormRequest;
use App\Models\Payment;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Razorpay\Api\Errors\SignatureVerificationError;

class PaymentController extends Controller
{
    // Initialize action class instances
    public function __construct(
        protected CreateOrder $createAction,
        protected VerifyPayment $verifyAction,
        protected RecordFailedPayment $recordAction
    ) {}

    //1. show payment form
    public function formPage()
    {
        return view('payment.index');
    }

    //2. create razorpay order and payment object
    // after form submit
    public function handleOrder(PaymentFormRequest $request)
    {
        try {
            $orderInfo = $this
                ->createAction
                ->execute($request->validated());

            return redirect()
                ->route('payments.process', $orderInfo);

        } catch (\Exception $e) {
            Log::error('Order_Error:: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Razorpay Order Creaion Failed: ' . $e->getMessage());
        }

    }

    //3. process after creating payment object
    public function processPage(Payment $payment)
    {
        if ($payment->status === 'completed') {
            return redirect()->route('payments.success', $payment);
        }

        return view('payment.process', [
            'payment' => $payment,
            'rzpKey'  => config('services.razorpay.key'),
        ]);
    }

    //4. handle verification after payment process
    public function handleVerification(Request $request)
    {
        $data = $request->validate([
            'razorpay_order_id'   => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature'  => ['required', 'string'],
        ]);

        try {
            $payment = $this->verifyAction->execute($data);

            //5. redirect to success page
            return redirect()
                ->route('payments.success', $payment);

        } catch (\RuntimeException $e) {
            Log::error('Order_Error:: ' . $e->getMessage());

            return redirect()
                ->route('payments.create')
                ->with('error', 'Unable locate your order. If any amount was deducted, please contact support.');

        } catch (SignatureVerificationError $e) {
            Log::error('Signature_Error:: ' . $e->getMessage());

            $errorMsg = 'Signature verification failed';
            $payment  = $this->recordAction->execute([
                'order_id'          => $data['razorpay_order_id'],
                'error_description' => $errorMsg,
            ]);

            return redirect()
                ->route('payments.failure', $payment)
                ->with('error', 'Something went wrong while confirming your payment.');
        } catch (\Throwable $e) {
            Log::error('Verification_Error:: ' . $e->getMessage());

            return redirect()->route('payments.create')
                ->with('error', 'Something went wrong while confirming your payment.');
        }
    }

    //5. mark payment status and redirect to failure page
    public function handleFailed(Request $request)
    {
        try {
            $data = $request->validate([
                'order_id'          => ['required', 'string'],
                'error_description' => ['required', 'string'],
            ]);
        } catch (ValidationException $e) {
            Log::error('Validation_Error:: ' . $e->getMessage());

            return redirect()->route('payments.create')
                ->with('error', "RazorPay payment failed. Please try again");
        }

        $payment = $this->recordAction->execute($data);

        if (! $payment) {
            return redirect()->route('payments.create')
                ->with('error', $data['error_description'] ?? 'Payment failed.');
        }

        return redirect()
            ->route('payments.failure', $payment)
            ->with('error', $data['error_description'] ?? 'Payment Failed!');
    }

    //6. show success page and generate download link
    public function successPage(
        Payment $payment, InvoiceService $invoice
    ) {
        if ($payment->status !== 'completed') {
            return redirect()
                ->route('payments.create')
                ->with('error', 'This payment has not been completed.');
        }

        $downloadUrl = $payment->invoice
            ? $invoice->permanentDownloadLink($payment->invoice)
            : null;

        $isInvoiceMailed = $payment->invoice?->mailed_at !== null;

        return view('payment.success', [
            'payment'         => $payment,
            'downloadUrl'     => $downloadUrl,
            'isInvoiceMailed' => $isInvoiceMailed,
        ]);
    }

    //6. show failure page
    public function failurePage(Payment $payment)
    {
        if ($payment->status !== 'failed') {
            return redirect()->route('payments.create')
                ->with('error', 'This payment does not have a failure status.');
        }
        return view('payment.failure', ['payment' => $payment]);
    }

}
