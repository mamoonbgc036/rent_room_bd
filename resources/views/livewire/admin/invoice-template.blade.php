<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            margin: 0;
            font-size: 11px;
            line-height: 1.3;
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            color: #333;
        }
        .invoice-box {
            width: 100%;
            max-width: 100%;
        }
        .header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-name {
            color: #2c5282;
            font-size: 18px;
            font-weight: bold;
        }
        .invoice-title {
            font-size: 16px;
            margin: 0;
            color: #2c5282;
        }
        .info-section {
            margin-bottom: 15px;
            width: 100%;
            clear: both;
        }
        .info-section::after {
            content: "";
            display: table;
            clear: both;
        }
        .left-section {
            float: left;
            width: 48%;
        }
        .right-section {
            float: right;
            width: 48%;
            text-align: right;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .table th {
            background-color: #f8f9fa;
            padding: 6px;
            text-align: left;
            font-size: 10px;
            border-bottom: 1px solid #ddd;
        }
        .table td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
        }
        .amount-table {
            width: 40%;
            margin-left: 60%;
        }
        .text-right {
            text-align: right;
        }
        .booking-info {
            background: #f9f9f9;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
        }
        .total-row {
            font-weight: bold;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <table style="width: 100%; margin-bottom: 15px;">
            <tr>
                <td>
                    <span class="company-name">Rent and Rooms</span>
                </td>
                <td style="text-align: right">
                    <h2 class="invoice-title">INVOICE</h2>
                    Invoice #: {{ $invoice_number }}<br>
                    Date: {{ $date }}<br>
                    Due Date: {{ $due_date }}
                </td>
            </tr>
        </table>

        <!-- Info Sections -->
        <div class="info-section">
            <div class="left-section">
                <strong>From:</strong><br>
                Rent and Rooms<br>
                60 Sceptre Street<br>
                Newcastle, NE4 6PR<br>
                Phone: 03301339494<br>
                Email: rentandrooms@gmail.com
            </div>
            <div class="right-section">
                <strong>Bill To:</strong><br>
                {{ $customer['name'] }}<br>
                {{ $customer['email'] }}<br>
                {{ $customer['phone'] }}
            </div>
        </div>

        <!-- Booking Details -->
        <div class="booking-info">
            <strong>Booking Reference:</strong> #{{ $booking->id }}<br>
            <strong>Package:</strong> {{ $booking->package->name }}<br>
            <strong>Duration:</strong> {{ $booking->number_of_days }} days ({{ \Carbon\Carbon::parse($booking->from_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($booking->to_date)->format('d/m/Y') }})
        </div>

        <!-- Items Table -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50%">Description</th>
                    <th style="width: 25%">Type</th>
                    <th style="width: 25%" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td>{{ $item['type'] }}</td>
                    <td class="text-right">৳{{ number_format($item['amount'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Payment Summary -->
        <table class="amount-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">৳{{ number_format($summary['total_price'], 2) }}</td>
            </tr>
            <tr>
                <td>Amount Paid:</td>
                <td class="text-right">৳{{ number_format($summary['total_paid'], 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Balance Due:</td>
                <td class="text-right">৳{{ number_format($summary['remaining_balance'], 2) }}</td>
            </tr>
        </table>

        <!-- Payment History -->
        @if(count($payments) > 0)
        <table class="table" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>Payment Date</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td>{{ ucfirst($payment->status) }}</td>
                    <td class="text-right">৳{{ number_format($payment->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Footer -->
        <div class="footer">
            <table style="width: 100%">
                <tr>
                    <td style="width: 60%">
                        <strong>Payment Terms:</strong> Due within 7 days<br>
                        <strong>Bank Details:</strong> Netsoftuk Solution | Acc: 17855008 | Sort: 04-06-05
                    </td>
                    <td style="text-align: right">
                        Questions? Email: rentandrooms@gmail.com
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
