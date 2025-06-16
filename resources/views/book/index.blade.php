<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f7f9fc;
            font-family: 'Segoe UI', sans-serif;
        }

        .confirmation-card {
            max-width: 500px;
            margin: 80px auto;
            padding: 40px;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .confirmation-icon {
            font-size: 60px;
            color: #28a745;
        }
    </style>
</head>

<body>

    <div class="confirmation-card text-center">
        <div class="mb-4">
            <i class="confirmation-icon bi bi-check-circle-fill"></i>
        </div>
        <h2 class="mb-3">Booking Confirmed!</h2>
        <p class="mb-4">Thank you for your booking. Your transaction was successful.</p>

        <div class="mb-3">
            <strong>Booking ID:</strong> <span class="text-primary">#{{ $booking_id ?? 123 }}</span><br>
            <strong>Date:</strong> {{ date('F j, Y') }}
        </div>

        <a href="{{ route('dashboard') }}" class="btn btn-success">Go to Homepage</a>
    </div>

    <!-- Bootstrap 5 JS (optional, for components like modals, tooltips) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</body>

</html>