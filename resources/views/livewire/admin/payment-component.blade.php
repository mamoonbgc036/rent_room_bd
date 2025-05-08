<div class="container mt-4">
    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Card for Payment Details --}}
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Payment Details for {{ $paymentLink->user->name }}</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted">Payment Details</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Amount to Pay:</strong></td>
                            <td class="text-end"><strong>৳{{ number_format($paymentLink->amount, 2) }}</strong></td>
                        </tr>
                        @if ($paymentLink->bookingPayment)
                            <tr>
                                <td><strong>Payment For:</strong></td>
                                <td class="text-end">
                                    @if ($paymentLink->bookingPayment->milestone_type === 'Month')
                                        Month {{ \Carbon\Carbon::parse($paymentLink->bookingPayment->due_date)->format('d M Y') }} Payment
                                    @elseif($paymentLink->bookingPayment->milestone_type === 'Week')
                                        Week {{ \Carbon\Carbon::parse($paymentLink->bookingPayment->due_date)->format('d M Y') }} Payment
                                    @elseif($paymentLink->bookingPayment->milestone_type === 'Booking Fee')
                                        Booking Fee {{ \Carbon\Carbon::parse($paymentLink->bookingPayment->due_date)->format('d M Y') }} Payment
                                    @else
                                        Day {{ \Carbon\Carbon::parse($paymentLink->bookingPayment->due_date)->format('d M Y') }} Payment
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Due Date:</strong></td>
                                <td class="text-end">
                                    {{ \Carbon\Carbon::parse($paymentLink->bookingPayment->due_date)->format('d M Y') }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td><strong>Booking Reference:</strong></td>
                            <td class="text-end">#{{ $paymentLink->booking->id }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6 class="text-muted">Payment Status</h6>
                    <span class="badge bg-{{ $paymentLink->status === 'pending' ? 'warning' : 'success' }}">
                        {{ ucfirst($paymentLink->status) }}
                    </span>
                </div>
            </div>

            {{-- Conditional Button or Message --}}
            @if ($paymentLink->status === 'pending')
                <button wire:click="showPaymentModal" class="btn btn-primary">
                    Complete Payment
                </button>
            @else
                <div class="alert alert-info mt-3" role="alert">
                    Payment request already sent. Please wait for confirmation.
                </div>
            @endif
        </div>
    </div>

    {{-- Payment Modal --}}
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Complete Payment</h5>
                        <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6 class="mb-0">Amount to Pay:
                                <strong>৳{{ number_format($paymentLink->amount, 2) }}</strong></h6>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Select Payment Method</label>
                            <select class="form-select" wire:model.live="selectedPaymentMethod">
                                <option value="Card">Credit/Debit Card</option>
                                <option value="BankTransfer">Bank Transfer</option>
                            </select>
                        </div>
                        @if ($selectedPaymentMethod === 'BankTransfer')
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">Bank Transfer Details</h6>
                                <p class="mb-1"><strong>Account Name:</strong> Netsoftuk Solution</p>
                                <p class="mb-1"><strong>Account Number:</strong> 17855008</p>
                                <p class="mb-1"><strong>Sort Code:</strong> 04-06-05</p>
                                <p class="mb-1"><strong>Payment For:</strong>
                                    @if($paymentLink->bookingPayment)
                                        Booking #{{ $paymentLink->booking->id }} -
                                        {{ $paymentLink->bookingPayment->milestone_type }}
                                        {{ $paymentLink->bookingPayment->milestone_number }} Payment
                                    @else
                                        Booking #{{ $paymentLink->booking->id }}
                                    @endif
                                </p>
                                <div class="form-group">
                                    <label class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" wire:model="bankReference"
                                           placeholder="Enter your transfer reference">
                                </div>
                            </div>
                        </div>
                    @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showModal', false)">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="handlePaymentMethod"
                            wire:loading.attr="disabled">
                            Proceed to Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
