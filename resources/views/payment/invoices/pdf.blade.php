{{-- resources/views/invoices/pdf.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            color: #1f2937;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #157347;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .header-logo {
            display: table-cell;
            width: 60px;
            vertical-align: middle;
        }

        .header-logo img {
            width: 50px;
            height: auto;
        }

        .header-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }

        .header-text h1 {
            color: #157347;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .total {
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('images/laravel-dark.png') }}" alt="Lara Corp Logo">
        </div>
        <div class="header-text">
            <h1>Lara Corp</h1>
            <p>
                <strong>Invoice ID: {{ $invoice->id }}<strong>
            </p>
        </div>
    </div>

    <p>
        <strong>Billed to :</strong>
        <br>{{ $payment->name }}
        <br>{{ $payment->email }}
        <br>{{ $payment->phone }}
    </p>

    <table>
        <tr>
            <th>Description</th>
            <th>Order ID</th>
            <th>Payment ID</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td>Payment</td>
            <td>{{ $payment->order_id }}</td>
            <td>{{ $payment->razorpay_payment_id }}</td>
            <td>{{ number_format($payment->amount, 2) }}</td>
        </tr>
    </table>

    <p class="total" style="text-align: right; margin-top: 16px;">
        Total Paid: Rs {{ number_format($payment->amount, 2) }}
    </p>
    <p style="margin-top: 32px; color: #6b7280; font-size: 12px;">
        Paid on {{ $payment->updated_at->format('d M Y, h:i A') }}
    </p>

</body>

</html>
