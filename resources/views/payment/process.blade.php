<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payment Process Window</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">

                @if (session('error'))
                    <div class="mb-6 rounded-lg bg-red-100 border border-red-400 px-4 py-3 text-red-800" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-lg bg-red-100 border border-red-400 px-4 py-3 text-red-800">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="status-text" class="text-gray-700">
                    Opening secure payment window
                    <p>Please wait...</p>
                </div>

                {{-- message for activating browser JavaScript  --}}
                <noscript>
                    <div class="mt-4 rounded-lg bg-yellow-100 border border-yellow-400 px-4 py-3 text-yellow-800">
                        JavaScript is required to complete payment. Please enable it and reload this page.
                    </div>
                </noscript>

                {{-- success verification form --}}
                <form action="{{ route('payments.verify') }}" method="POST" name="razorpaySuccessForm" class="hidden">
                    @csrf
                    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                </form>

                {{-- failure reporting form --}}
                <form action="{{ route('payments.failed') }}" name="razorpayFailureForm" class="hidden">
                    @csrf
                    <input type="hidden" name="order_id" id="failed_order_id">
                    <input type="hidden" name="error_description" id="failed_error_description">
                </form>

                <button id="retryBtn" type="button" onclick="initRazorpayCheckout()"
                    class="mt-6 inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 hidden">
                    Retry Payment
                </button>

                <a id="backBtn" href="{{ route('payments.create') }}"
                    class="mt-6 inline-flex items-center px-4 py-2 border border-gray-800 bg-white text-gray-800 rounded-md hover:text-white hover:bg-gray-700 hidden">
                    Go Back
                </a>
            </div>
        </div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        function initRazorpayCheckout() {
            var statusText = document.getElementById('status-text');

            // When the browser loads this script, Razorpay's 
            // JavaScript code creates a global Razorpay object.
            if (typeof Razorpay === 'undefined') {
                statusText.textContent =
                    'Could not load the payment gateway. Please check your connection and reload.';

                return;
            }

            var options = {
                "key": "{{ $rzpKey }}",
                "amount": "{{ $payment->amount * 100 }}",
                "currency": "INR",
                "name": "Lara Corp",
                "description": "Order #{{ $payment->order_id }}",
                "image": "https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Laravel.svg/250px-Laravel.svg.png",
                "order_id": "{{ $payment->order_id }}",

                // handle after successful transaction
                "handler": function(res) {
                    document.getElementById('razorpay_payment_id').value = res.razorpay_payment_id;
                    document.getElementById('razorpay_order_id').value = res.razorpay_order_id;
                    document.getElementById('razorpay_signature').value = res.razorpay_signature;
                    document.forms['razorpaySuccessForm'].submit();

                    statusText.textContent =
                        'Payment Completed. Redirecting...';
                },
                "prefill": {
                    "name": "{{ $payment->name }}",
                    "email": "{{ $payment->email }}",
                    "contact": "{{ $payment->phone }}"
                },
                "notes": {
                    "address": "Razorpay Corporate Office"
                },
                "theme": {
                    "color": "#157347"
                },
                "modal": {
                    "ondismiss": function() {
                        statusText.textContent =
                            'Payment window closed. Click below to try again.';
                        document.getElementById('retryBtn').classList.remove('hidden')
                        document.getElementById('backBtn').classList.remove('hidden')
                    }
                }
            };

            var rzp = new Razorpay(options);

            // Error handling block
            rzp.on('payment.failed', function(response) {
                document.getElementById('failed_order_id').value = response.error.metadata.order_id;
                document.getElementById('failed_error_description').value = response.error.description;
                document.forms['razorpayFailureForm'].submit();
            });

            rzp.open();
        }

        setTimeout(() => {
            initRazorpayCheckout();
        }, 2000);
    </script>
</x-app-layout>
