<?php
namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PaymentListController extends Controller
{

    public function __invoke(Request $request)
    {
        $page     = $request->input('page', 1);
        $version  = Cache::get('payments.list.version', 1);
        $cacheKey = "payments.list.v{$version}.page.{$page}";

        $payments = Cache::remember($cacheKey, now()->addMinutes(10),
            function () {
                return Payment::with('invoice')
                    ->latest()
                    ->paginate(10);
            });

        return view('payment.list', compact('payments'));
    }

}
