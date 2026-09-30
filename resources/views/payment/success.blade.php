<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payment Successful</h2>

            <x-link-button href="{{ route('payments.create') }}">
                New Payment
            </x-link-button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="rounded-full bg-green-100 p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-center text-gray-900">
                    Thank you, {{ $payment->name }}!
                </h3>
                <p class="text-center text-gray-600 mt-1">
                    Your payment was completed successfully.
                </p>

                <dl class="mt-6 divide-y divide-gray-200 text-sm">
                    <div class="flex justify-between py-2">
                        <dt class="text-gray-500">Amount</dt>
                        <dd class="text-gray-900 font-medium">
                            ₹{{ number_format($payment->amount, 2) }}
                        </dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-gray-500">Order ID</dt>
                        <dd class="text-gray-900">
                            {{ $payment->order_id }}
                        </dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-gray-500">Payment ID</dt>
                        <dd class="text-gray-900">
                            {{ $payment->razorpay_payment_id }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 text-center">
                    @if ($downloadUrl)
                        <a href="{{ $downloadUrl }}"
                            class="inline-flex items-center px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-800">
                            Download Invoice (PDF)
                        </a>

                        @if ($isInvoiceMailed)
                            <p class="text-xs text-gray-500 mt-2">A copy has also been sent to {{ $payment->email }}</p>
                        @else
                            <p class="text-xs text-red-500 mt-2">
                                We couldn't send a copy to your email, but you can download it above.
                            </p>
                        @endif
                    @else
                        <p class="text-sm text-gray-600" id="invoice-pending-message">
                            Your invoice is being generated — this usually takes a few seconds.
                        </p>
                        <button onclick="window.location.reload()" class="mt-2 text-sm text-blue-600 hover:underline">
                            Refresh to check
                        </button>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
