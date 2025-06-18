<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $booking->id }}</title>
</head>

<body>
    <h1>Invoice for Booking #{{ $booking->id }}</h1>
    <p><strong>From:</strong> {{ \Carbon\Carbon::parse($booking->from_date)->format('M d, Y') }}</p>
    <p><strong>To:</strong> {{ \Carbon\Carbon::parse($booking->to_date)->format('M d, Y') }}</p>
    <p><strong>Total Price:</strong> ৳{{ number_format($booking->payment_summary['total_price'], 2) }}</p>
    <p><strong>Paid Amount:</strong> ৳{{ number_format($booking->payment_summary['total_paid'], 2) }}</p>
    <p><strong>Remaining Balance:</strong> ৳{{ number_format($booking->payment_summary['remaining_balance'], 2) }}</p>

    <h3>Payment Details:</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($booking->payments as $index => $payment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>৳{{ number_format($payment['amount'], 2) }}</td>
                <td>{{ ucfirst($payment['payment_method']) }}</td>
                <td>{{ ucfirst($payment['status']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>