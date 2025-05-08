<div class="booking-page bg-light">
    <div class="container-fluid px-4 py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Create New Booking</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item active">New Booking</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="#" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Bookings
                </a>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Main Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form wire:submit.prevent="createBooking">
                    <!-- User Selection Section -->
                    <div class="form-section mb-4">
                        <h5 class="form-section-title">
                            <i class="fas fa-user-circle text-primary mr-2"></i>User Information
                        </h5>
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <div class="position-relative">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                        </div>
                                        <input type="text"
                                            class="form-control border-left-0 shadow-none @error('selectedUser') is-invalid @enderror"
                                            wire:model.live="searchQuery" placeholder="Search user by name or email"
                                            autocomplete="off">
                                    </div>

                                    @if (!empty($users))
                                        <div
                                            class="position-absolute w-100 mt-1 bg-white border rounded shadow-sm search-results">
                                            @foreach ($users as $user)
                                                <div class="user-option p-3 border-bottom hover-light cursor-pointer"
                                                    wire:click="selectUser({{ $user->id }})">
                                                    <div class="d-flex align-items-center">
                                                        <div class="user-avatar mr-3">
                                                            <i class="fas fa-user-circle fa-2x text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-weight-bold">{{ $user->name }}</div>
                                                            <div class="text-muted small">{{ $user->email }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                @if ($selectedUser)
                                    <div class="selected-user mt-3">
                                        <div class="card border bg-white">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="user-avatar mr-3">
                                                            <i class="fas fa-user-check text-success fa-2x"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ $selectedUser->name }}</h6>
                                                            <p class="mb-0 text-muted small">{{ $selectedUser->email }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-link text-danger"
                                                        wire:click="$set('selectedUser', null)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @error('selectedUser')
                                    <div class="invalid-feedback d-block mt-2">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Package Selection Section -->
                    <div class="form-section mb-4">
                        <h5 class="form-section-title">
                            <i class="fas fa-box text-primary mr-2"></i>Package Selection
                        </h5>
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <select class="custom-select shadow-none @error('packageId') is-invalid @enderror"
                                    wire:model.live="packageId">
                                    <option value="">Choose a package...</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id }}">{{ $package->name }}</option>
                                    @endforeach
                                </select>
                                @error('packageId')
                                    <div class="invalid-feedback d-block mt-2">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if ($packageId)
                        <div class="form-section mb-4">
                            <h5 class="form-section-title">
                                <i class="fas fa-bed text-primary mr-2"></i>Room Selection
                            </h5>
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    @if ($packages->find($packageId)->rooms->count() > 0)
                                        <div class="row">
                                            @foreach ($packages->find($packageId)->rooms as $room)
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div wire:key="room-{{ $room->id }}"
                                                        wire:click="selectRoom({{ $room->id }})"
                                                        class="room-card card h-100 border {{ $selectedRoom == $room->id ? 'selected' : '' }}">
                                                        <div class="card-body p-3">
                                                            <div
                                                                class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <h6 class="card-title mb-3">{{ $room->name }}
                                                                    </h6>
                                                                    <div class="room-features">
                                                                        <div class="feature-item mb-2">
                                                                            <i class="fas fa-bed text-primary mr-2"></i>
                                                                            <span>{{ $room->number_of_beds }}
                                                                                {{ Str::plural('Bed', $room->number_of_beds) }}</span>
                                                                        </div>
                                                                        <div class="feature-item">
                                                                            <i
                                                                                class="fas fa-bath text-primary mr-2"></i>
                                                                            <span>{{ $room->number_of_bathrooms }}
                                                                                {{ Str::plural('Bathroom', $room->number_of_bathrooms) }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @if ($selectedRoom == $room->id)
                                                                    <div class="selected-badge">
                                                                        <span class="badge badge-success">
                                                                            <i class="fas fa-check mr-1"></i>Selected
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <span>No rooms available for this package</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Date Selection Section -->
                    @if ($selectedRoom && $calendarView)
                        <div class="form-section mb-4">
                            <h5 class="form-section-title">
                                <i class="fas fa-calendar-alt text-primary mr-2"></i>Date Selection
                            </h5>
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <div x-data="datePickerComponent({{ json_encode($disabledDates) }})" wire:ignore.self class="date-picker-container">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label font-weight-bold">Check-in Date</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-white">
                                                            <i class="fas fa-calendar-alt text-primary"></i>
                                                        </span>
                                                    </div>
                                                    <input x-ref="checkInPicker" type="text"
                                                        class="form-control @error('fromDate') is-invalid @enderror"
                                                        placeholder="Select check-in date" readonly
                                                        {{ !Auth::check() ? 'disabled' : '' }}>
                                                </div>
                                                @error('fromDate')
                                                    <div class="invalid-feedback d-block">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label font-weight-bold">Check-out Date</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text bg-white">
                                                            <i class="fas fa-calendar-alt text-primary"></i>
                                                        </span>
                                                    </div>
                                                    <input x-ref="checkOutPicker" type="text"
                                                        class="form-control @error('toDate') is-invalid @enderror"
                                                        placeholder="Select check-out date" readonly
                                                        {{ !Auth::check() ? 'disabled' : '' }}>
                                                </div>
                                                @error('toDate')
                                                    <div class="invalid-feedback d-block">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        @error('dateRange')
                                            <div class="alert alert-danger mt-3 mb-0">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                                    <span>{{ $message }}</span>
                                                </div>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <script>
                        function datePickerComponent(disabledDates) {
                            return {
                                disabledDates: disabledDates,
                                checkInDate: null,
                                checkOutDate: null,

                                init() {
                                    // Initialize check-in picker
                                    const checkInPicker = flatpickr(this.$refs.checkInPicker, {
                                        dateFormat: 'Y-m-d',
                                        minDate: 'today',
                                        disable: this.disabledDates.map(date => new Date(date)),
                                        onChange: (selectedDates) => {
                                            if (selectedDates.length > 0) {
                                                this.checkInDate = selectedDates[0];
                                                // Reset check-out date when check-in date changes
                                                this.checkOutDate = null;
                                                checkOutPicker.clear();
                                                // Update check-out picker min date
                                                checkOutPicker.set('minDate', selectedDates[0]);
                                                // Call Livewire method with only start date
                                                @this.set('fromDate', selectedDates[0].toISOString().split('T')[0]);
                                                @this.set('toDate', null);
                                            }
                                        }
                                    });

                                    // Initialize check-out picker
                                    const checkOutPicker = flatpickr(this.$refs.checkOutPicker, {
                                        dateFormat: 'Y-m-d',
                                        minDate: 'today',
                                        disable: this.disabledDates.map(date => new Date(date)),
                                        onChange: (selectedDates) => {
                                            if (selectedDates.length > 0 && this.checkInDate) {
                                                this.checkOutDate = selectedDates[0];
                                                // Call Livewire method with complete date range
                                                @this.call('selectDates', {
                                                    start: this.checkInDate.toISOString().split('T')[0],
                                                    end: selectedDates[0].toISOString().split('T')[0]
                                                });
                                            }
                                        }
                                    });

                                    // Watch for changes in disabled dates and update both pickers
                                    this.$watch('disabledDates', (newValue) => {
                                        const disabledDatesArray = newValue.map(date => new Date(date));
                                        checkInPicker.set('disable', disabledDatesArray);
                                        checkOutPicker.set('disable', disabledDatesArray);
                                    });

                                    // Listen for dates-selected event to handle UI updates
                                    window.addEventListener('dates-selected', () => {
                                        if (!this.checkInDate || !this.checkOutDate) {
                                            checkInPicker.clear();
                                            checkOutPicker.clear();
                                        }
                                    });
                                }
                            };
                        }
                    </script>

                    @if ($fromDate && $toDate)
                        <div class="form-section mb-4">
                            <h5 class="form-section-title">
                                <i class="fas fa-phone text-primary mr-2"></i>Contact Information
                            </h5>
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <div class="form-group mb-0">
                                        <label class="form-label font-weight-bold">Phone Number</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white">
                                                    <i class="fas fa-phone text-primary"></i>
                                                </span>
                                            </div>
                                            <input type="text"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                wire:model="phone" placeholder="Enter phone number">
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback d-block">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Booking Summary Section -->
                    @if ($fromDate && $toDate && $totalAmount > 0)
                        <div class="form-section mb-4">
                            <h5 class="form-section-title">
                                <i class="fas fa-receipt text-primary mr-2"></i>Booking Summary
                            </h5>
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clipboard-list mr-2"></i>
                                        <h5 class="mb-0 text-white">Reservation Details</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Booking Details -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="info-group mb-3">
                                                <div class="info-label text-muted mb-1">Check-in Date</div>
                                                <div class="info-value">
                                                    <i class="far fa-calendar-alt text-primary mr-2"></i>
                                                    {{ Carbon\Carbon::parse($fromDate)->format('D, d M Y') }}
                                                </div>
                                            </div>
                                            <div class="info-group mb-3">
                                                <div class="info-label text-muted mb-1">Check-out Date</div>
                                                <div class="info-value">
                                                    <i class="far fa-calendar-alt text-primary mr-2"></i>
                                                    {{ Carbon\Carbon::parse($toDate)->format('D, d M Y') }}
                                                </div>
                                            </div>
                                            <div class="info-group">
                                                <div class="info-label text-muted mb-1">Duration</div>
                                                <div class="info-value">
                                                    <i class="far fa-clock text-primary mr-2"></i>
                                                    {{ Carbon\Carbon::parse($fromDate)->diffInDays(Carbon\Carbon::parse($toDate)) }}
                                                    days
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-group mb-3">
                                                <div class="info-label text-muted mb-1">Price Type</div>
                                                <div class="info-value">
                                                    <i class="fas fa-tag text-primary mr-2"></i>
                                                    {{ $priceType }} Rate
                                                </div>
                                            </div>
                                            <div class="info-group">
                                                <div class="info-label text-muted mb-1">Payment Option</div>
                                                <div class="info-value">
                                                    <i class="fas fa-money-bill text-primary mr-2"></i>
                                                    {{ $paymentOption === 'full' ? 'Full Payment' : 'Booking Price Only' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Breakdown -->
                                    <div class="payment-breakdown">
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <!-- Initial Charges -->
                                                    <tr class="bg-light rounded">
                                                        <th colspan="2" class="py-3">
                                                            <i class="fas fa-calculator text-primary mr-2"></i>
                                                            <span class="font-weight-bold">Initial Charges</span>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <td class="pl-4">Base Price</td>
                                                        <td class="text-right pr-4">
                                                            ৳{{ number_format($totalAmount, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pl-4">Booking Fee</td>
                                                        <td class="text-right pr-4">
                                                            ৳{{ number_format($bookingPrice, 2) }}</td>
                                                    </tr>

                                                    <!-- Payment Schedule -->
                                                    <tr class="bg-light">
                                                        <th colspan="2" class="py-3">
                                                            <i class="fas fa-clock text-primary mr-2"></i>
                                                            <span class="font-weight-bold">Payment Schedule
                                                                ({{ $priceType }})</span>
                                                        </th>
                                                    </tr>
                                                    @if (!empty($priceBreakdown))
                                                        @foreach ($priceBreakdown as $milestone)
                                                            <tr>
                                                                <td class="pl-4">
                                                                    {{ $milestone['description'] }}
                                                                    @if (isset($milestone['note']))
                                                                        <small
                                                                            class="text-muted d-block">{{ $milestone['note'] }}</small>
                                                                    @endif
                                                                </td>
                                                                <td class="text-right pr-4">
                                                                    ৳{{ number_format($milestone['total'], 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif

                                                    <!-- Total Section -->
                                                    <tr>
                                                        <td colspan="2">
                                                            <hr class="my-3">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pl-4 font-weight-bold">Total Amount</td>
                                                        <td class="text-right pr-4">
                                                            ৳{{ number_format($totalAmount + $bookingPrice, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pl-4 font-weight-bold">Amount to Pay Now</td>
                                                        <td class="text-right pr-4">
                                                            <span class="h5 text-primary mb-0 font-weight-bold">
                                                                ৳{{ number_format($paymentOption === 'full' ? $totalAmount + $bookingPrice : $bookingPrice, 2) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Payment Schedule Info -->
                                        <div class="alert alert-info mt-4 mb-0">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-info-circle fa-lg mr-3"></i>
                                                <div>
                                                    <strong>Payment Schedule:</strong>
                                                    @if ($priceType === 'Month')
                                                        Monthly payments are due at the start of each month
                                                    @elseif($priceType === 'Week')
                                                        Weekly payments are due at the start of each week
                                                    @else
                                                        Daily payments are due at the start of each day
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details Section -->
                        <div class="form-section mb-4">
                            <h5 class="form-section-title">
                                <i class="fas fa-credit-card text-primary mr-2"></i>Payment Details
                            </h5>
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Payment Option</label>
                                            <select class="custom-select" wire:model.live="paymentOption">
                                                <option value="booking_only">Pay Booking Price Only
                                                    (৳{{ number_format($bookingPrice, 2) }})</option>
                                                <option value="full">Pay Full Amount
                                                    (৳{{ number_format($totalAmount + $bookingPrice, 2) }})</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label font-weight-bold">Payment Method</label>
                                            <select class="custom-select" wire:model.live="paymentMethod">
                                                <option value="bank_transfer">Bank Transfer</option>
                                                <option value="card">Card Payment</option>
                                            </select>
                                        </div>
                                    </div>

                                    @if ($paymentMethod === 'bank_transfer')
                                        <div class="bank-transfer-details mt-3">
                                            <div class="card bg-light border">
                                                <div class="card-body">
                                                    <h6 class="card-title">
                                                        <i class="fas fa-university text-primary mr-2"></i>Bank
                                                        Transfer Details
                                                    </h6>
                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <div class="bank-info mb-3">
                                                                <div class="text-muted small mb-1">Account Name</div>
                                                                <div class="font-weight-bold">Netsoftuk Solution</div>
                                                            </div>
                                                            <div class="bank-info mb-3">
                                                                <div class="text-muted small mb-1">Account Number</div>
                                                                <div class="font-weight-bold">17855008</div>
                                                            </div>
                                                            <div class="bank-info">
                                                                <div class="text-muted small mb-1">Sort Code</div>
                                                                <div class="font-weight-bold">04-06-05</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-0">
                                                                <label class="form-label font-weight-bold">Reference
                                                                    Number</label>
                                                                <input type="text"
                                                                    class="form-control @error('bankTransferReference') is-invalid @enderror"
                                                                    wire:model="bankTransferReference"
                                                                    placeholder="Enter bank transfer reference">
                                                                @error('bankTransferReference')
                                                                    <div class="invalid-feedback d-block">
                                                                        <i
                                                                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button Section -->
                        <div class="form-section">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg px-5"
                                    wire:loading.class="disabled" wire:target="createBooking">
                                    <span wire:loading.remove wire:target="createBooking">
                                        <i class="fas fa-check-circle mr-2"></i>Create Booking
                                    </span>
                                    <span wire:loading wire:target="createBooking">
                                        <span class="spinner-border spinner-border-sm mr-2" role="status"></span>
                                        Processing Booking...
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <style>
        .booking-page {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .form-section-title {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .search-results {
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
        }

        .user-option {
            transition: all 0.2s ease;
        }

        .user-option:hover {
            background-color: #f8f9fa;
        }

        .hover-light:hover {
            background-color: #f8f9fa;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .custom-select {
            height: calc(1.5em + 1rem + 2px);
            padding: 0.5rem 1rem;
            background-color: #fff;
            border: 1px solid #ced4da;
        }

        .custom-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .input-group-text {
            background-color: transparent;
            border-right: none;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #80bdff;
        }

        .card {
            transition: all 0.3s ease;
        }

        .selected-user .card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: #fff;
        }

        .alert {
            border: none;
            border-radius: 0.25rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            font-size: 1.2rem;
            line-height: 1;
            color: #6c757d;
        }

        .room-item {
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
            color: white;
        }

        .room-item:hover {
            background-color: #f8f9fa;
            border-color: #0d6efd;
            color: #252525;
        }

        .room-item.selected {
            background-color: #e8f0fe;
            border-color: #0d6efd;
            color: #252525;
        }

        .z-10 {
            z-index: 1000;
        }


        .room-card {
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .room-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        }

        .room-card.selected {
            border-color: #28a745 !important;
            background-color: #f8fff9;
        }

        .room-card .feature-item {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .selected-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        /* Date Picker */
        .input-group-text {
            border-right: none;
            background-color: transparent;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-color: #ced4da;
            box-shadow: none;
        }

        .form-control:disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        /* Calendar Customization */
        .flatpickr-calendar {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            border: none !important;
        }

        .flatpickr-day.selected {
            background: #007bff !important;
            border-color: #007bff !important;
        }

        .flatpickr-day.today {
            border-color: #007bff !important;
        }

        /* Form Section */
        .form-section-title {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 0.25rem;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-info {
            background-color: #e2f3ff;
            color: #0c5460;
        }

        .invalid-feedback {
            font-size: 80%;
            color: #dc3545;
            margin-top: 0.25rem;
        }

        /* Form Sections */
        .form-section-title {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        /* Info Groups */
        .info-group {
            background-color: #fff;
            padding: 0.75rem;
            border-radius: 0.25rem;
        }

        .info-label {
            font-size: 0.85rem;
        }

        .info-value {
            font-weight: 500;
        }

        /* Bank Info */
        .bank-info {
            background-color: #fff;
            padding: 0.75rem;
            border-radius: 0.25rem;
        }

        /* Table Styles */
        .table-borderless td,
        .table-borderless th {
            border: 0;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        /* Form Controls */
        .custom-select {
            height: calc(1.5em + 1rem + 2px);
            padding: 0.5rem 1rem;
        }

        .input-group-text {
            background-color: transparent;
            border-right: none;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #80bdff;
        }

        /* Button Styles */
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn.disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }

        /* Alerts */
        .alert-info {
            background-color: #e2f3ff;
            color: #0c5460;
            border: none;
        }

        /* Spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
        }

        /* Cards */
        .card {
            margin-bottom: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background-color: #007bff;
            border-bottom: 0;
        }

        /* Error States */
        .invalid-feedback {
            font-size: 80%;
            color: #dc3545;
            margin-top: 0.25rem;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    </style>

</div>
