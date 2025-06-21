<div>

    <!-- Auto Renewal Modal -->
    <div class="modal fade" id="autoRenewalModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-sync-alt mr-2"></i>Auto-Renewal Settings
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($booking->price_type !== 'Month')
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        Auto-renewal is only available for monthly packages.
                    </div>
                    @else
                    <div class="form-group mb-4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="autoRenewalToggle"
                                wire:model.live="autoRenewal" {{ !$canManageAutoRenewal ? 'disabled' : '' }}>
                            <label class="custom-control-label" for="autoRenewalToggle">
                                Enable Auto-Renewal
                            </label>
                        </div>
                    </div>

                    @if ($autoRenewal)
                    <div class="bg-light p-3 rounded mb-3">
                        <div class="small">
                            <i class="fas fa-calendar-alt text-primary mr-1"></i>
                            Next Renewal:
                            <strong>
                                {{ Carbon\Carbon::parse($booking->to_date)->subDays(7)->format('M d, Y') }}
                            </strong>
                        </div>
                    </div>
                    @endif

                    <div class="alert alert-info mt-3">
                        <div class="d-flex">
                            <div class="mr-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading">How Auto-Renewal Works</h6>
                                <p class="mb-0 small">
                                    When enabled, your booking will be automatically renewed 7 days before it
                                    expires.
                                    A new milestone payment will be created for the next month.
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($booking->auto_renewal)
                    <div class="alert alert-warning mt-3">
                        <div class="d-flex">
                            <div class="mr-3">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading">Important Note</h6>
                                <p class="mb-0 small">
                                    Disabling auto-renewal will prevent future automatic renewals,
                                    but won't affect your current booking period.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if (!$canManageAutoRenewal && !$booking->auto_renewal)
                    <div class="alert alert-warning mt-3">
                        <small>
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Auto-renewal cannot be managed because:
                            <ul class="mb-0 mt-1">
                                @if ($booking->status === 'cancelled')
                                <li>This booking has been cancelled</li>
                                @endif
                                @if ($booking->status === 'finished')
                                <li>This booking has been marked as finished</li>
                                @endif
                                @if (!$booking->from_date || !$booking->to_date)
                                <li>Booking dates are not properly set</li>
                                @endif
                                @if ($booking->to_date && Carbon\Carbon::parse($booking->to_date)->isPast())
                                <li>This booking has expired</li>
                                @endif
                            </ul>
                        </small>
                    </div>
                    @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="toggleAutoRenewal">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 text-white">Booking Ref #{{ $booking->id }}</h5>
                    <small>{{ $booking->package->name }}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge badge-{{ $booking->status === 'paid' ? 'success' : 'warning' }} mr-3">
                        {{ ucfirst($booking->status) }}
                    </span>
                    <button type="button" wire:click="showAutoRenewalSettings" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-sync-alt mr-1"></i>Auto-Renewal
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Sub-cards Container -->
                <div class="row">
                    <!-- Booking Information Sub-card -->
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-info-circle mr-2"></i>Booking Information
                                </h6>
                                <div class="d-flex align-items-center">
                                    <span
                                        class="badge {{ $booking->auto_renewal ? 'badge-success' : 'badge-secondary' }} mr-2">
                                        <i class="fas {{ $booking->auto_renewal ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                        {{ $booking->auto_renewal ? 'Auto-Renewal Active' : 'Auto-Renewal Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Package Info -->
                                        <div class="bg-light p-3 rounded mb-3">
                                            <h6 class="text-primary mb-3">Package Details</h6>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 text-muted">Package:</div>
                                                <div class="col-sm-8">{{ $booking->package->name }}</div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 text-muted">Address:</div>
                                                <div class="col-sm-8">{{ $booking->package->address }}</div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 text-muted">Booked:</div>
                                                <div class="col-sm-8">{{ $booking->created_at->format('M d, Y') }}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 text-muted">Check In:</div>
                                                <div class="col-sm-8">
                                                    {{ \Carbon\Carbon::parse($booking->from_date)->format('M d, Y') }}
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 text-muted">Check Out:</div>
                                                <div class="col-sm-8">
                                                    {{ \Carbon\Carbon::parse($booking->to_date)->format('M d, Y') }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4 text-muted">Duration:</div>
                                                <div class="col-sm-8">{{ $booking->number_of_days }} Days</div>
                                            </div>
                                        </div>
                                        <div class="bg-light p-3 rounded">
                                            <h6 class="text-primary mb-3">Place</h6>
                                            <div class="rooms-container">
                                                @php
                                                $roomIds = json_decode($booking->room_ids, true) ?? [];
                                                $rooms = \App\Models\Room::whereIn('id', $roomIds)->get();
                                                @endphp
                                                @foreach ($rooms as $room)
                                                <div class="bg-white p-3 rounded mb-2 shadow-sm">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1">{{ $room->name }}</h6>
                                                            <div class="text-muted small">
                                                                <i class="fas fa-bed mr-1"></i>
                                                                {{ $room->number_of_beds }} Beds
                                                                <span class="mx-2">|</span>
                                                                <i class="fas fa-bath mr-1"></i>
                                                                {{ $room->number_of_bathrooms }} Ensuite
                                                            </div>
                                                        </div>
                                                        @if ($room->is_available)
                                                        <span class="badge badge-success">Available</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Modal -->
                    <div class="modal fade" id="paymentModal" tabindex="-1" wire:ignore.self>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-credit-card mr-2"></i>Make Payment
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Payment Details Alert -->
                                    <div class="alert alert-info mb-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle fa-2x mr-3"></i>
                                            <div>
                                                <h6 class="mb-1">Payment Details</h6>
                                                <p class="mb-0">
                                                    {{ $currentMilestone?->milestone_type }} Payment - Phase
                                                    {{ $currentMilestone?->milestone_number }}
                                                    <br>
                                                    <small class="text-muted">Due Date:
                                                        {{ $currentMilestone ? Carbon\Carbon::parse($currentMilestone->due_date)->format('d M Y') : '' }}</small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Amount -->
                                    <div class="form-group">
                                        <label for="paymentAmount">Payment Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">৳</span>
                                            </div>
                                            <input type="text" class="form-control" readonly
                                                value="{{ number_format($selectedMilestoneAmount ?? 0, 2) }}">
                                        </div>
                                    </div>

                                    <!-- Payment Method -->
                                    <div class="form-group">
                                        <label for="paymentMethod">Payment Method</label>
                                        <select class="form-control @error('paymentMethod') is-invalid @enderror"
                                            wire:model.live="paymentMethod">
                                            <option value="" selected>Select Payment Method</option>
                                            <option value="bikash">Bikash</option>
                                            <option value="nogod">Nogod</option>
                                            <option value="rocket">Rocket</option>
                                        </select>
                                        @error('paymentMethod')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @if(!is_null($paymentMethod))
                                    <div class="form-group">
                                        <p class="text-success">Send Fee To The Following Number : </p>
                                        <p class="text-warning">{{ $paymentMethod == 'bikash' ? 'Bikash' : ($paymentMethod == 'nogod' ? 'Nogod' : 'Rocket') }} Merchant Number : 08888888888</p>
                                    </div>
                                    @endif

                                    <!-- Bank Transfer Section -->
                                    @if ($paymentMethod)
                                    <div class="form-group">
                                        <label for="bankTransferReference">Payment Reference Number</label>
                                        <input type="text"
                                            class="form-control @error('bankTransferReference') is-invalid @enderror"
                                            wire:model="bankTransferReference"
                                            placeholder="Enter your payment reference">
                                        @error('bankTransferReference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="alert alert-light border">
                                        <small class="d-block text-muted">Please use the reference:
                                            BOK-{{ $booking->id }}</small>
                                    </div>
                                    @endif

                                    <!-- Card Payment Info -->
                                    @if ($paymentMethod === 'card')
                                    <div class="alert alert-light border">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        You will be redirected to our secure payment gateway to complete your card
                                        payment.
                                    </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        <i class="fas fa-times mr-2"></i>Cancel
                                    </button>
                                    <button type="button" class="btn btn-primary" wire:click="proceedPayment"
                                        wire:loading.attr="disabled">
                                        <span wire:loading wire:target="proceedPayment">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true"></span>
                                            Processing...
                                        </span>
                                        <span wire:loading.remove>
                                            <i class="fas fa-check mr-2"></i>Proceed Payment
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('livewire:initialized', () => {
                            // Get all modals
                            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
                            const autoRenewalModal = new bootstrap.Modal(document.getElementById('autoRenewalModal'));

                            // Handle modal events
                            Livewire.on('openModal', (modalId) => {
                                if (modalId === 'paymentModal') {
                                    paymentModal.show();
                                } else if (modalId === 'autoRenewalModal') {
                                    autoRenewalModal.show();
                                }
                            });

                            Livewire.on('closeModal', (modalId) => {
                                if (modalId === 'paymentModal') {
                                    paymentModal.hide();
                                } else if (modalId === 'autoRenewalModal') {
                                    autoRenewalModal.hide();
                                }
                            });
                        });
                    </script>

                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-primary">
                                    <i class="fas fa-credit-card mr-2"></i>Payment Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Payment Summary -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="bg-light p-3 rounded text-center">
                                                    <div class="text-muted mb-2">Total Price</div>
                                                    <h5 class="mb-0">
                                                        ৳{{ number_format($booking->price + $booking->booking_price, 2) }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="bg-light p-3 rounded text-center">
                                                    <div class="text-muted mb-2">Paid Amount</div>
                                                    <h5 class="text-success mb-0">
                                                        ৳{{ number_format($payments ? $payments->where('status', 'Paid')->sum('amount') : 0, 2) }}
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="bg-light p-3 rounded text-center">
                                                    <div class="text-muted mb-2">Due Amount</div>
                                                    <h5 class="text-{{ $dueBill > 0 ? 'danger' : 'success' }} mb-0">
                                                        ৳{{ number_format($dueBill, 2) }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Schedule -->
                                <div class="timeline-wrapper">
                                    @foreach ($booking->bookingPayments->sortBy('due_date') as $milestone)
                                    @php
                                    $isPaid = $milestone->payment_status === 'paid';
                                    $dueDate = \Carbon\Carbon::parse($milestone->due_date);
                                    $isOverdue = !$isPaid && $milestone->milestone_type != 'Booking Fee' && $dueDate->isPast();
                                    $isPending = $milestone->transaction_reference && !$isPaid;
                                    $isNextPayment = !$isPaid && $milestone->id === $currentMilestone?->id;
                                    @endphp
                                    <div
                                        class="timeline-item {{ $isPaid ? 'paid' : ($isOverdue ? 'overdue' : '') }}">
                                        <div
                                            class="card border-{{ $isOverdue ? 'danger' : ($isNextPayment ? 'warning' : 'success') }}">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <!-- Milestone Info -->
                                                    <div class="col-md-3">
                                                        <div class="d-flex align-items-center">
                                                            @if ($isPaid)
                                                            <i
                                                                class="fas fa-check-circle text-success fa-2x mr-2"></i>
                                                            @elseif($isOverdue)
                                                            <i
                                                                class="fas fa-exclamation-circle text-danger fa-2x mr-2"></i>
                                                            @else
                                                            <i
                                                                class="fas fa-clock text-warning fa-2x mr-2"></i>
                                                            @endif
                                                            <div>
                                                                <h6 class="mb-0">
                                                                    {{ $milestone->milestone_type }}
                                                                </h6>
                                                                <small class="text-muted">Phase
                                                                    {{ $milestone->milestone_number }}</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Due Date -->
                                                    <div class="col-md-3">
                                                        <div class="text-muted">
                                                            <i class="fas fa-calendar-alt mr-1"></i>
                                                            {{ $dueDate->format('M d, Y') }}
                                                        </div>
                                                    </div>

                                                    <!-- Amount -->
                                                    <div class="col-md-3">
                                                        <div class="h6 mb-0">
                                                            ৳{{ number_format($milestone->amount, 2) }}
                                                        </div>
                                                    </div>

                                                    <!-- Status/Action -->
                                                    <div class="col-md-3 text-right">
                                                        @if ($isPaid)
                                                        <span
                                                            class="badge badge-success text-white">Paid</span>
                                                        @elseif($isOverdue)
                                                        <button class="btn btn-danger btn-sm text-white"
                                                            wire:click="showPaymentM({{ $milestone->id }}, {{ $milestone->amount }})">
                                                            Pay Now (Overdue)
                                                        </button>
                                                        @elseif($isNextPayment)
                                                        <button class="btn btn-warning btn-sm text-white"
                                                            wire:click="showPaymentM({{ $milestone->id }}, {{ $milestone->amount }})">
                                                            Pay Now
                                                        </button>
                                                        @elseif($isPending)
                                                        <span
                                                            class="badge badge-secondary text-white">Pending</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('livewire:initialized', () => {
                            const paymentModal = document.getElementById('paymentModal');
                            const bsPaymentModal = new bootstrap.Modal(paymentModal);

                            Livewire.on('openModal', (modalId) => {
                                if (modalId === 'paymentModal') {
                                    bsPaymentModal.show();
                                }
                            });

                            Livewire.on('closeModal', (modalId) => {
                                if (modalId === 'paymentModal') {
                                    bsPaymentModal.hide();
                                }
                            });
                        });
                    </script>

                    <style>
                        /* Add to your existing styles */
                        .modal-content {
                            border: none;
                            border-radius: 0.5rem;
                        }

                        .modal-header {
                            border-top-left-radius: 0.5rem;
                            border-top-right-radius: 0.5rem;
                        }

                        /* Loading spinner animation */
                        @keyframes spin {
                            0% {
                                transform: rotate(0deg);
                            }

                            100% {
                                transform: rotate(360deg);
                            }
                        }
                    </style>
                    <!-- Instructions Sub-card -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card shadow-sm">
                                    <div
                                        class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-list-alt mr-2 text-primary"></i>Package Instructions
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($booking->package->instructions->isEmpty())
                                        <div class="text-center py-4">
                                            <div class="mb-3">
                                                <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                                            </div>
                                            <p class="text-muted mb-0">No specific instructions provided for this
                                                package.</p>
                                        </div>
                                        @else
                                        <div class="timeline-instructions">
                                            @foreach ($booking->package->instructions->sortBy('order') as $instruction)
                                            <div class="instruction-item mb-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="instruction-number">
                                                        <span
                                                            class="badge badge-primary rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width: 35px; height: 35px;">
                                                            {{ $loop->iteration }}
                                                        </span>
                                                    </div>

                                                    <div class="instruction-content ml-3 flex-grow-1">
                                                        <div class="card bg-light border-0 hover-card">
                                                            <div class="card-body">
                                                                <h6 class="card-title mb-2 text-primary">
                                                                    {{ $instruction->title }}
                                                                </h6>
                                                                <p class="card-text text-muted mb-0">
                                                                    {{ $instruction->description }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>


                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* General Styles */
        .card {
            border: none;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            border-top-left-radius: 0.5rem !important;
            border-top-right-radius: 0.5rem !important;
            padding: 1rem 1.25rem;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        /* Timeline Styles */
        .timeline-wrapper {
            position: relative;
            padding: 1.5rem 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0.75rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item:last-child::before {
            height: 50%;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: 0.25rem;
            top: 1.5rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: #6c757d;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #e9ecef;
        }

        .timeline-item.paid::after {
            background: #28a745;
        }

        .timeline-item.overdue::after {
            background: #dc3545;
        }

        /* Progress Bar */
        .progress {
            height: 25px;
            border-radius: 1rem;
            margin: 1rem 0;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
        }

        /* Instructions Timeline */
        .timeline-instructions {
            position: relative;
            padding-left: 1rem;
        }

        .instruction-item {
            position: relative;
            padding-left: 1rem;
        }

        .instruction-item::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 35px;
            bottom: -15px;
            width: 2px;
            background-color: #e9ecef;
        }

        .instruction-item:last-child::before {
            display: none;
        }

        .hover-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-card:hover {
            transform: translateX(5px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Room Container */
        .rooms-container {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .rooms-container::-webkit-scrollbar {
            width: 6px;
        }

        .rooms-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .rooms-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        /* Toast Notifications */
        .toast {
            min-width: 300px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .fixed-bottom {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }

        /* Print Styles */
        @media print {

            .btn-outline-primary,
            .btn-outline-light {
                display: none;
            }

            .card {
                break-inside: avoid;
            }

            .timeline-instructions {
                padding-left: 0;
            }

            .instruction-item::before {
                display: none;
            }
        }

        .card {
            border: none;
            border-radius: 0.5rem;
        }

        .card-header {
            border-top-left-radius: 0.5rem !important;
            border-top-right-radius: 0.5rem !important;
        }

        /* Sub-cards */
        .card .card {
            border: 1px solid rgba(0, 0, 0, .125);
        }

        .card .card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, .125);
        }

        .rooms-container {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>

    <!-- Place this at the bottom of your blade file -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Get the payment modal
            const paymentModalEl = document.getElementById('paymentModal');
            const paymentModal = new bootstrap.Modal(paymentModalEl);

            // Get the auto renewal modal
            const autoRenewalModalEl = document.getElementById('autoRenewalModal');
            const autoRenewalModal = new bootstrap.Modal(autoRenewalModalEl);

            // Handle modal events
            Livewire.on('openModal', (modalId) => {
                // Check which modal to open
                if (modalId[0] === 'paymentModal') {
                    paymentModal.show();
                } else if (modalId[0] === 'autoRenewalModal') {
                    autoRenewalModal.show();
                }
            });

            // Handle modal close events
            Livewire.on('closeModal', (modalId) => {
                if (modalId[0] === 'paymentModal') {
                    paymentModal.hide();
                } else if (modalId[0] === 'autoRenewalModal') {
                    autoRenewalModal.hide();
                }
            });

            // Initialize tooltips
            $(function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            Livewire.on('contentChanged', () => {
                $('[data-toggle="tooltip"]').tooltip();
            });
        });
    </script>
</div>