<div>
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mt-4">Edit Booking #{{ $booking->id }}</h2>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Bookings
            </a>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card mb-4 mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Edit Booking Details</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="updateBooking">
                    <!-- Current Booking Info -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Current Booking Details</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Check-in:</strong>
                                    {{ Carbon\Carbon::parse($fromDate)->format('d M Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Current Checkout:</strong>
                                    {{ Carbon\Carbon::parse($originalToDate)->format('d M Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-0"><strong>Current Duration:</strong>
                                    {{ Carbon\Carbon::parse($fromDate)->diffInDays(Carbon\Carbon::parse($originalToDate)) }}
                                    days
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Fee Section -->
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Booking Fee</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Current Booking Fee</label>
                                <div class="d-flex align-items-center">
                                    @if (!$useCustomBookingFee)
                                        <div class="flex-grow-1">
                                            ৳{{ number_format($bookingPrice, 2) }}
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary"
                                            wire:click="$set('useCustomBookingFee', true)">
                                            <i class="fas fa-edit mr-2"></i>Edit Fee
                                        </button>
                                    @else
                                        <div class="input-group">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" class="form-control"
                                                wire:model.live="customBookingFee" step="0.01" min="0"
                                                placeholder="Enter new booking fee">
                                            <button type="button" class="btn btn-outline-secondary"
                                                wire:click="$set('useCustomBookingFee', false)">
                                                <i class="fas fa-undo mr-2"></i>Cancel
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @error('customBookingFee')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Optional Booking Extension -->
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Optional Booking Extension</h6>
                        </div>
                        <div class="card-body">
                            <div x-data="datePickerComponent(@js(['disabledDates' => $disabledDates, 'fromDate' => $originalToDate, 'toDate' => $toDate]))" class="mb-3">
                                <label class="form-label">New Checkout Date (Optional)</label>
                                <input x-ref="dateRangePicker" type="text" class="form-control w-full"
                                    placeholder="Select new checkout date" readonly>
                                <small class="text-muted">Leave blank if not extending the booking</small>

                                @error('toDate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Extension Price Breakdown (if applicable) -->
                    @if ($fromDate && $toDate && $totalAmount > 0)
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Extension Price Breakdown</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <!-- Extension Details -->
                                            <tr>
                                                <td>Extension Period:</td>
                                                <td class="text-end">
                                                    {{ Carbon\Carbon::parse($originalToDate)->format('d M Y') }} to
                                                    {{ Carbon\Carbon::parse($toDate)->format('d M Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Additional Days:</td>
                                                <td class="text-end">
                                                    {{ Carbon\Carbon::parse($originalToDate)->diffInDays(Carbon\Carbon::parse($toDate)) }}
                                                    days
                                                </td>
                                            </tr>

                                            <!-- Price Breakdown -->
                                            @foreach ($priceBreakdown as $milestone)
                                                <tr>
                                                    <td>{{ $milestone['description'] }}</td>
                                                    <td class="text-end">৳{{ number_format($milestone['total'], 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <tr class="table-light">
                                                <td><strong>Additional Amount:</strong></td>
                                                <td class="text-end">
                                                    <strong>৳{{ number_format($totalAmount, 2) }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Rent Amount Section -->
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Total Rent Amount</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Current Rent Amount</label>
                                <div class="d-flex align-items-center">
                                    @if (!$useCustomRentAmount)
                                        <div class="flex-grow-1">
                                            ৳{{ number_format($originalRentAmount, 2) }}
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary"
                                            wire:click="$set('useCustomRentAmount', true)">
                                            <i class="fas fa-edit mr-2"></i>Edit Amount
                                        </button>
                                    @else
                                        <div class="input-group">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" class="form-control"
                                                wire:model.live="customRentAmount" step="0.01" min="0"
                                                placeholder="Enter new rent amount">
                                            <button type="button" class="btn btn-outline-secondary"
                                                wire:click="$set('useCustomRentAmount', false)">
                                                <i class="fas fa-undo mr-2"></i>Cancel
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @error('customRentAmount')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            @if ($useCustomRentAmount && $customRentAmount != $originalRentAmount)
                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    This will adjust all pending milestone payments proportionally.
                                    <div class="mt-2">
                                        <strong>Original Amount:</strong>
                                        ৳{{ number_format($originalRentAmount, 2) }}<br>
                                        <strong>New Amount:</strong> ৳{{ number_format($customRentAmount, 2) }}<br>
                                        <strong>Difference:</strong>
                                        ৳{{ number_format($customRentAmount - $originalRentAmount, 2) }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Update Button -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                            <span wire:loading wire:target="updateBooking">
                                <span class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                Processing...
                            </span>
                            <span wire:loading.remove>
                                Update Booking
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function datePickerComponent(config) {
            return {
                disabledDates: config.disabledDates,
                init() {
                    const picker = flatpickr(this.$refs.dateRangePicker, {
                        mode: 'single',
                        dateFormat: 'Y-m-d',
                        minDate: config.fromDate,
                        disable: this.disabledDates.map(date => new Date(date)),
                        defaultDate: config.toDate,
                        onChange: (selectedDates) => {
                            if (selectedDates.length === 1) {
                                @this.call('selectDates', {
                                    start: config.fromDate,
                                    end: selectedDates[0].toISOString().split('T')[0]
                                });
                            }
                        }
                    });

                    // Watch for changes in disabled dates
                    this.$watch('disabledDates', (newValue) => {
                        picker.set('disable', newValue.map(date => new Date(date)));
                    });
                }
            };
        }
    </script>

    <style>
        .room-item {
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .room-item:hover {
            background-color: #f8f9fa;
            border-color: #0d6efd;
        }

        .room-item.selected {
            background-color: #e8f0fe;
            border-color: #0d6efd;
        }
    </style>
</div>
