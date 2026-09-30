<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payment Failed</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="rounded-full bg-red-100 p-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>

                <h3 class="text-lg font-semibold text-center text-gray-900">Payment could not be completed</h3>

                @if (session('error'))
                    <p class="text-center text-gray-600 mt-1">{{ session('error') }}</p>
                @endif

                @if ($payment)
                    <dl class="mt-6 divide-y divide-gray-200 text-sm">
                        <div class="flex justify-between py-2">
                            <dt class="text-gray-500">Order ID</dt>
                            <dd class="text-gray-900">{{ $payment->order_id }}</dd>
                        </div>
                        @if ($payment->failure_reason)
                            <div class="flex justify-between py-2">
                                <dt class="text-gray-500">Reason: </dt>
                                <dd class="text-gray-900">{{ $payment->failure_reason }}</dd>
                            </div>
                        @endif
                    </dl>
                @endif

                <div class="mt-6 text-center">
                    <a href="{{ route('payments.retry', $payment) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Try Again
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
