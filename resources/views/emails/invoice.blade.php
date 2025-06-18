<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .content {
            margin-bottom: 30px;
        }

        .footer {
            font-size: 12px;
            color: #666;
            text-align: center;
            margin-top: 30px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2c5282;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Invoice for Your Booking</h2>
        </div>

        <div class="content">
            <p>Dear {{ $userName }},</p>

            <p>Thank you for choosing Rent and Rooms. Please find attached your invoice (#{{ $invoiceNumber }}) for Booking #{{ $booking->id }}.</p>

            <p><strong>Booking Details:</strong><br>
                Package: {{ $booking->package->name }}<br>
                Duration: {{ $booking->number_of_days }} days<br>
                Total Amount: ৳{{ $totalAmount }}<br>
                Due Date: {{ $dueDate }}</p>

            <p>You can pay using the following methods:</p>
            <ul>
                <li>Bank Transfer:<br>
                    Account Name: Netsoftuk Solution<br>
                    Account Number: 17855008<br>
                    Sort Code: 04-06-05
                </li>
                <li>Generate a payment link through your booking portal</li>
            </ul>
        </div>

        <div class="footer">
            <p>If you have any questions about this invoice, please contact us:<br>
                Email: rentandrooms@gmail.com<br>
                Phone: 03301339494</p>

            <p>Rent and Rooms<br>
                60 Sceptre Street<br>
                Newcastle, NE4 6PR</p>
        </div>
    </div>
</body>

</html>