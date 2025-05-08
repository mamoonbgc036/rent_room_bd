<div>
    <div class="container py-4">
        {{-- Alert Messages --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show mb-4">
                <i class="fas fa-check-circle mr-2"></i>{{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="row">
            {{-- Main Content Column --}}
            <div class="col-lg-8">
                {{-- Booking Overview Card --}}
                <div class="card mb-4">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <h4 class="mr-2">Booking #{{ $booking->id }}</h4>
                                <span class="badge badge-pill px-2 h6 py-1 text-white"
                                    style="background-color: {{ $this->statusColor }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                            <div class="text-right">
                                <h4 class="mb-1">৳{{ number_format($booking->price + $booking->booking_price, 2) }}
                                </h4>
                                <small class="text-muted">Total Amount</small>
                            </div>
                        </div>
                    </div>


                    <div class="card-body border-bottom">
                        <h5 class="text-primary mb-4">Customer</h5>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light p-3 mr-3">
                                <i class="fas fa-user-circle fa-2x text-muted"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $booking->user->name }}</h5>
                                <div class="text-muted">
                                    <i class="fas fa-envelope mr-2"></i>{{ $booking->user->email }}
                                    @if ($booking->user->phone)
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-phone mr-2"></i>{{ $booking->user->phone }}
                                    @endif
                                </div>
                            </div>
                            <a href="mailto:{{ $booking->user->email }}" class="btn btn-outline-primary">
                                Contact
                            </a>
                        </div>
                    </div>

                    {{-- Quick Stats --}}
                    <div class="card-body border-bottom bg-light">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card h-100 border-0 bg-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-alt fa-2x text-primary mb-3"></i>
                                        <h6 class="text-muted mb-2">Duration</h6>
                                        <h5 class="mb-0">{{ $booking->number_of_days }} Days</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100 border-0 bg-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-bed fa-2x text-primary mb-3"></i>
                                        <h6 class="text-muted mb-2">Rooms</h6>
                                        <h5 class="mb-0">{{ count($rooms) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100 border-0 bg-white">
                                    <div class="card-body text-center">
                                        <i class="fas fa-pound-sign fa-2x text-primary mb-3"></i>
                                        <h6 class="text-muted mb-2">Due Amount</h6>
                                        <h5 class="mb-0">৳{{ number_format($dueBill, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    {{-- Stay Details --}}
                    <div class="card-body border-bottom">
                        <h5 class="text-primary mb-4">Stay Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <h6 class="text-muted mb-2">Check In</h6>
                                            <h5 class="mb-0">
                                                {{ \Carbon\Carbon::parse($booking->from_date)->format('D, M d, Y') }}
                                            </h5>
                                        </div>
                                        <div>
                                            <h6 class="text-muted mb-2">Check Out</h6>
                                            <h5 class="mb-0">
                                                {{ \Carbon\Carbon::parse($booking->to_date)->format('D, M d, Y') }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <h6 class="text-muted mb-2">Property</h6>
                                            <h5 class="mb-0">{{ $booking->package->name }}</h5>
                                        </div>
                                        <div>
                                            <h6 class="text-muted mb-2">Location</h6>
                                            <h5 class="mb-0">{{ $booking->package->address }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock fa-2x text-primary mr-3"></i>
                                            <div>
                                                <h6 class="text-muted mb-2">Booking Created</h6>
                                                <h5 class="mb-0">
                                                    {{ $booking->created_at->format('D, M d, Y \a\t h:i A') }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        @foreach ($rooms as $room)
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="mb-0">{{ $room->name }}</h5>
                                                <span class="badge badge-light">Room {{ $loop->iteration }}</span>
                                            </div>
                                            <div class="text-muted">
                                                <i class="fas fa-bed mr-2"></i>{{ $room->number_of_beds }} Beds
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-bath mr-2"></i>{{ $room->number_of_bathrooms }} Bath
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>

                    {{-- Booked Rooms --}}

                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="col-lg-4">
                {{-- Payment Actions Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="text-primary mb-4">Payment Summary</h5>
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Room Price</span>
                                <h6 class="mb-0">৳{{ number_format($booking->price, 2) }}</h6>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Booking Fee</span>
                                <h6 class="mb-0">৳{{ number_format($booking->booking_price, 2) }}</h6>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Paid Amount</span>
                                @php
                                    $paidAmount = $booking->payments
                                        ->whereIn('status', ['Paid', 'complete'])
                                        ->sum('amount');
                                @endphp
                                <h6 class="text-success mb-0">৳{{ number_format($paidAmount, 2) }}</h6>
                            </div>
                        </div>

                        @if (
                            $booking->status !== 'cancelled' &&
                                $booking->status !== 'approved' &&
                                $booking->status !== 'rejected' &&
                                $booking->status !== 'paid')
                            <div class="d-grid gap-2">
                                <button wire:click="approveBooking" class="btn btn-primary btn-lg mb-2">
                                    Approve Booking
                                </button>
                                <button wire:click="rejectBooking" class="btn btn-secondary btn-lg">
                                    Reject Booking
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Payment History Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="text-primary mb-4">Payment History</h5>
                        @if ($booking->payments->count() > 0)
                            @foreach ($booking->payments as $payment)
                                <div class="border-bottom mb-3 pb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span
                                            class="badge badge-{{ $payment->status === 'completed' ? 'success' : 'secondary' }} badge-pill px-3">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                        <h6 class="mb-0">৳{{ number_format($payment->amount, 2) }}</h6>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fas fa-credit-card mr-2"></i>{{ ucfirst($payment->payment_method) }}
                                        <span class="mx-2">•</span>
                                        <i
                                            class="fas fa-calendar-alt mr-2"></i>{{ $payment->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-receipt fa-3x mb-3"></i>
                                <p class="mb-0">No payments recorded</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Auto-Renewal Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="text-primary mb-1">Auto-Renewal</h5>
                                <p class="text-muted small mb-0">Monthly Package Management</p>
                            </div>

                            @if ($booking->price_type === 'Month')
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="autoRenewalSwitch"
                                        wire:click="toggleAutoRenewal" {{ $booking->auto_renewal ? 'checked' : '' }}
                                        {{ $canManageAutoRenewal ? '' : 'disabled' }}>
                                    <label class="custom-control-label" for="autoRenewalSwitch"></label>
                                </div>
                            @endif
                        </div>

                        <div class="bg-light rounded p-4">
                            @if ($booking->price_type !== 'Month')
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Auto-renewal is only available for monthly packages.
                                </div>
                            @else
                                @if ($booking->auto_renewal)
                                    <div class="mb-3">
                                        <span class="badge badge-success badge-pill mr-2">
                                            <i class="fas fa-check-circle mr-1"></i> Active
                                        </span>
                                        <span class="text-muted">Package will auto-extend</span>
                                    </div>

                                    @if ($booking->next_renewal_date)
                                        <div class="card bg-white border-0 mb-3">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-calendar-alt text-primary fa-2x mr-3"></i>
                                                    <div>
                                                        <h6 class="mb-1">Next Renewal</h6>
                                                        <p class="mb-0 text-muted">
                                                            {{ Carbon\Carbon::parse($booking->next_renewal_date)->format('M d, Y') }}
                                                            <small class="d-block">
                                                                {{ Carbon\Carbon::parse($booking->next_renewal_date)->diffForHumans() }}
                                                            </small>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Package will automatically extend 7 days before expiry.
                                        A new payment milestone will be created.
                                    </div>
                                @endif
                            @endif
                        </div>

                        @if (!$canManageAutoRenewal && !$booking->auto_renewal && $booking->price_type === 'Month')
                            <div class="alert alert-warning mt-4 mb-0">
                                <div class="d-flex">
                                    <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                                    <div>
                                        <h6 class="font-weight-bold mb-2">Auto-renewal cannot be managed</h6>
                                        <ul class="pl-3 mb-0">
                                            @if ($booking->status === 'cancelled')
                                                <li>Booking has been cancelled</li>
                                            @endif
                                            @if ($booking->payment_status === 'finished')
                                                <li>Booking has been marked as finished</li>
                                            @endif
                                            @if (!$booking->from_date || !$booking->to_date)
                                                <li>Booking dates are not properly set</li>
                                            @endif
                                            @if ($booking->to_date && Carbon\Carbon::parse($booking->to_date)->isPast())
                                                <li>Booking has expired</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Cancel Booking Card --}}
                @if ($booking->status !== 'cancelled')
                    <div class="card">
                        <div class="card-body">
                            <button wire:click="cancelBooking" class="btn btn-danger btn-lg btn-block">
                                <i class="fas fa-times-circle mr-2"></i>Cancel Booking
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Custom Bootstrap 4 Enhancements */
        .badge-pill {
            font-weight: 500;
        }

        .btn-lg {
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .custom-switch .custom-control-label::before {
            height: 1.5rem;
            width: 2.75rem;
            border-radius: 1rem;
        }

        .custom-switch .custom-control-label::after {
            height: calc(1.5rem - 4px);
            width: calc(1.5rem - 4px);
            border-radius: 50%;
        }

        .custom-switch .custom-control-input:checked~.custom-control-label::after {
            transform: translateX(1.25rem);
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        .alert-info {
            background-color: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }

        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #856404;
        }

        .alert-warning ul {
            color: #856404;
        }

        /* Text Utilities */
        .text-primary {
            color: #252525 !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        /* Custom Spacing */
        .card-body {
            padding: 1.5rem;
        }

        /* Badge Colors */
        .badge-success {
            background-color: #28a745;
        }

        .badge-secondary {
            background-color: #6c757d;
        }

        /* Icon Sizes */
        .fa-2x {
            line-height: 1;
        }

        /* Responsive Adjustments */
        @media (max-width: 991.98px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Print Styles */
        @media print {

            .btn,
            .custom-switch {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }
        }
    </style>
</div>
