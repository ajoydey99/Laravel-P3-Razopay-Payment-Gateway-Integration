<?php
namespace App\Http\Middleware;

use App\Models\Payment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPaymentResolved
{

    // this middleware used for checking payment status
    // before process routes hits
    public function handle(Request $request, Closure $next): Response
    {
        $payment = $request->route('payment'); // retrieving the {payment} parameter from routes

        if ($payment instanceof Payment) {
            if ($payment->status === 'completed') {
                return redirect()
                    ->route('payments.success', $payment);
            }

            if ($payment->status === 'failed') {
                return redirect()->route('payments.failure', $payment);
            }
        }

        return $next($request);

    }
}
