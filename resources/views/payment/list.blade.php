<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Payments List
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-6 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Payment List
                        </h3>
                    </div>

                    <x-link-button href="{{ route('payments.create') }}">
                        New Payment
                    </x-link-button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border-collapse">

                        {{-- Table Head --}}
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-3 py-3 text-left text-sm font-semibold text-gray-500 uppercase border-r border-gray-200">
                                    Payment ID
                                </th>
                                <th
                                    class="px-3 py-3 text-left text-sm font-semibold text-gray-500 uppercase border-r border-gray-200">
                                    Name
                                </th>
                                <th
                                    class="px-3 py-3 text-left text-sm font-semibold text-gray-500 uppercase border-r border-gray-200">
                                    Email
                                </th>
                                <th
                                    class="px-3 py-3 text-left text-sm font-semibold text-gray-500 uppercase border-r border-gray-200">
                                    Contact
                                </th>
                                <th
                                    class="px-3 py-3 text-left text-sm font-semibold text-gray-500 uppercase border-r border-gray-200">
                                    Amount
                                </th>
                                <th
                                    class="px-3 py-3 text-left text-sm font-semibold text-gray-500 uppercase border-r border-gray-200">
                                    Created
                                </th>
                                <th
                                    class="px-3 py-3 text-left text-sm font-semibold text-gray-500 uppercase border-r border-gray-200">
                                    Status
                                </th>
                                <th class="px-3 py-3 text-center text-sm font-semibold text-gray-500 uppercase">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        {{-- Table Body --}}
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($payments as $payment)
                                <tr class="hover:bg-gray-50">

                                    <td
                                        class="px-3 py-3 whitespace-nowrap text-sm text-gray-700 border-r border-gray-200">
                                        {{ $payment->razorpay_payment_id ?? '—' }}
                                    </td>

                                    <td
                                        class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">
                                        {{ $payment->name }}
                                    </td>

                                    <td
                                        class="px-3 py-3 whitespace-nowrap text-sm text-gray-700 border-r border-gray-200">
                                        {{ $payment->email }}
                                    </td>

                                    <td
                                        class="px-3 py-3 whitespace-nowrap text-sm text-gray-700 border-r border-gray-200">
                                        {{ $payment->phone }}
                                    </td>

                                    <td
                                        class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">
                                        ₹{{ number_format($payment->amount, 2) }}
                                    </td>

                                    <td
                                        class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">
                                        {{ $payment->created_at->format('d M Y, g:ia') }}
                                    </td>

                                    {{-- Payment Status --}}
                                    <td class="px-3 py-3 whitespace-nowrap border-r border-gray-200">
                                        @switch($payment->status)
                                            @case('completed')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                                    ● Completed
                                                </span>
                                            @break

                                            @case('failed')
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                                                    ✕ Failed
                                                </span>
                                            @break

                                            @default
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">
                                                    ● Pending
                                                </span>
                                        @endswitch
                                    </td>

                                    {{-- Action --}}
                                    <td class="px-3 py-3 text-center whitespace-nowrap">
                                        @if ($payment->status === 'created')
                                            {{-- Verify --}}
                                            <a href="{{ route('payments.process', $payment) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-md text-blue-600 hover:bg-blue-100"
                                                title="Verify Payment">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12.75 11.25 15 15 9.75 M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </a>
                                        @elseif ($payment->status === 'completed' && $payment->invoice)
                                            {{-- Download Invoice --}}
                                            <a href="{{ route('payments.invoice.download', $payment->invoice) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-md text-green-600 hover:bg-green-100"
                                                title="Download Invoice">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 3v12m0 0 4-4m-4 4-4-4M4.5 19.5h15" />
                                                </svg>
                                            </a>
                                        @elseif ($payment->status === 'failed')
                                            {{-- Retry Payment --}}
                                            <a href="{{ route('payments.retry', $payment) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-md text-red-600 hover:bg-red-100"
                                                title="Retry Payment">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.023 9.348h4.992V4.356 M20.015 9.348a8.25 8.25 0 1 0 1.006 6.614" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif

                                        {{-- Delete Payment --}}
                                        <form action="" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-md text-red-600 hover:bg-red-100"
                                                title="Delete Payment"
                                                onclick="return confirm('Are you sure you want to delete this payment?')">

                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 7h12M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v6m4-6v6" />
                                                </svg>

                                            </button>
                                        </form>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                {{-- Pagination --}}
                @if ($payments->hasPages())
                    <div class="px-4 py-4 border-t border-gray-200">
                        {{ $payments->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

</x-app-layout>
