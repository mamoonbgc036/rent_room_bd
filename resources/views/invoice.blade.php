<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                Booking ID: {{ $booking->id }}<br>
                                Created: {{ $booking->created_at }}<br>
                                Due: {{ $booking->from_date }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                User ID: {{ $booking->user_id }}<br>
                                Package ID: {{ $booking->package_id }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>
                    Payment Method
                </td>
                <td>
                    {{ ucfirst($booking->payment_option) }}
                </td>
            </tr>
            <tr class="details">
                <td>
                    Payment Status
                </td>
                <td>
                    {{ ucfirst($booking->payment_status) }}
                </td>
            </tr>
            <tr class="heading">
                <td>
                    Item
                </td>
                <td>
                    Price
                </td>
            </tr>
            <tr class="item">
                <td>
                    Total Price
                </td>
                <td>
                    ৳{{ $booking->price + $booking->booking_price }}
                </td>
            </tr>
            <tr class="item">
                <td>
                    Booking Price
                </td>
                <td>
                    ৳{{ $booking->booking_price }}
                </td>
            </tr>
            <tr class="item last">
                <td>
                    Total Paid
                </td>
                <td>
                    ৳{{ $booking->total_amount }}
                </td>
            </tr>
            <tr class="total">
                <td></td>
                <td>
                   Due Bill: ৳{{ $booking->price + $booking->booking_price - $booking->total_amount }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
