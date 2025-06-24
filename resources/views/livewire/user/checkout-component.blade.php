<div class="container mt-5">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white">{{ $package->name }} - Booking Summary</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Check-in:</strong> {{ Carbon\Carbon::parse($fromDate)->format('d M Y') }}</p>
                    <p><strong>Check-out:</strong> {{ Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
                </div>
                <div class="col-md-6 text-md-right">
                    <p><strong>Duration:</strong> {{ $totalNights }} Nights</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Room Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Details</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>{{ $selectedRoom->name }}</h5>
                        <span class="badge badge-primary">৳{{ number_format($totalAmount, 2) }}</span>
                    </div>
                    <div class="room-features small text-muted">
                        <p class="mb-1"><i class="fas fa-bed mr-2"></i>{{ $selectedRoom->number_of_beds }} Beds</p>
                        <p class="mb-0"><i class="fas fa-bath mr-2"></i>{{ $selectedRoom->number_of_bathrooms }}
                            Bathrooms</p>
                    </div>
                </div>
            </div>
            <!-- Additional Services -->
            @if ($selectedAmenities['sum'] || $selectedMaintains['sum'])
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Additional Services</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if ($selectedAmenities['sum'])
                        <div class="{{ empty($selectedMaintains['sum']) ? 'col-md-12' : 'col-md-6' }}">
                            <h6 class="mb-3">Amenities</h6>
                            <ul class="list-group">
                                @foreach ($selectedAmenities[0] as $amenity)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ $amenity[0] }}</span>
                                    <span
                                        class="badge badge-secondary">৳{{ number_format($amenity[1], 2) }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if ($selectedMaintains['sum'])
                        <div class="{{ empty($selectedAmenities['sum']) ? 'col-md-12' : 'col-md-6'}}">
                            <h6 class="mb-3">Maintenance Services</h6>
                            <ul class="list-group">
                                @foreach ($selectedMaintains[0] as $maintain)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ $maintain[0] }}</span>
                                    <span
                                        class="badge badge-secondary">৳{{ number_format($maintain[1], 2) }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Payment Summary -->
        <div class="col-lg-4 mb-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Payment Summary</h6>
                </div>
                <div class="card-body">
                    <div class="price-breakdown">
                        <!-- Room Price Breakdown -->
                        <h6 class="mb-3">Charges</h6>
                        @foreach ($priceBreakdown as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span>
                                @if ($item['type'] === 'Month')
                                {{ $item['description'] }}
                                <small
                                    class="text-muted">(৳{{ number_format($item['price'], 2) }}/month)</small>
                                @elseif($item['type'] === 'Week')
                                {{ $item['quantity'] }} {{ Str::plural('Week', $item['quantity']) }}
                                <small
                                    class="text-muted">(৳{{ number_format($item['price'], 2) }}/week)</small>
                                @else
                                {{ $item['quantity'] }} {{ Str::plural('Day', $item['quantity']) }}
                                <small class="text-muted">(৳{{ number_format($item['price'], 2) }}/day)</small>
                                @endif
                            </span>
                            <span>৳{{ number_format($item['total'], 2) }}</span>
                        </div>
                        @endforeach
                        <div class="d-flex justify-content-between mb-3 font-weight-bold">
                            <span>Subtotal</span>
                            <span>৳{{ number_format($totalAmount, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Booking</span>
                            <span>৳{{ number_format($bookingPrice, 2) }}</span>
                        </div>

                        <!-- Additional Services -->
                        @if($selectedAmenities['sum'] || $selectedMaintains['sum'])
                        <h6 class="mb-2 mt-4">Additional Services</h6>
                        @if($selectedAmenities['sum'])
                        <div class="d-flex justify-content-between mb-2">
                            <span>Amenities</span>
                            <span>৳ {{ $selectedAmenities['sum'] }}</span>
                        </div>
                        @endif


                        @if ($selectedMaintains['sum'])
                        <div class="d-flex justify-content-between mb-2">
                            <span>Maintenance</span>
                            <span>৳{{ number_format($selectedMaintains['sum'], 2) }}</span>
                        </div>
                        @endif
                        @endif

                        <!-- Booking Price -->


                        <!-- Total -->
                        <hr>
                        <div class="d-flex justify-content-between font-weight-bold">
                            <span>Total</span>
                            <span>৳{{ number_format($totalAmount + $selectedAmenities['sum'] + $selectedMaintains['sum'] + $bookingPrice, 2) }}</span>
                        </div>

                        <!-- Optional Alert for Long Stays -->
                        @if (collect($priceBreakdown)->contains('type', 'Month'))
                        <div class="alert alert-info mt-3 mb-0 small">
                            <i class="fas fa-info-circle mr-1"></i>
                            Your stay includes monthly pricing for better value on long-term bookings.
                        </div>
                        @endif
                    </div>

                    <!-- Payment Option Selection -->
                    <div class="form-group mt-4">
                        <label>Payment Option</label>
                        <select class="form-control" wire:model.live="paymentOption">
                            <option value="booking_only">Booking
                                (৳{{ number_format($bookingPrice + $selectedAmenities['sum'] + $selectedMaintains['sum'], 2) }})</option>
                            <option value="full">Full Amount
                                (৳{{ number_format($totalAmount + $selectedMaintains['sum'] + $selectedAmenities['sum'] + $bookingPrice, 2) }})
                            </option>
                        </select>
                    </div>

                    <button class="btn btn-primary btn-block mt-3" wire:click="submitBooking">
                        Proceed to Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    @if ($showPaymentModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Method</h5>
                    <button type="button" class="close" wire:click="$set('showPaymentModal', false)">
                        <span>&times;</span>
                    </button>
                </div>
                <form wire:submit.prevent="proceedPayment">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Select Payment Method</label>
                            <select class="form-control" wire:model.live="paymentMethod">
                                <option value="" selected>Select a payment method</option>
                                <option value="bikash">Bikash</option>
                                <option value="nogod">Nogod</option>
                                <option value="rocket">Rocket</option>
                            </select>
                        </div>
                        @if (in_array($paymentMethod, ['bikash', 'nogod', 'rocket']))
                        <div class="alert alert-info">
                            <p class="mb-2">
                                <strong class="text-success">Send the payment to the following number:</strong>
                            </p>
                            <p class="mb-0">
                                {{ $paymentMethod === 'bikash' ? $bikash : ($paymentMethod === 'nogod' ? $nogod : $rocket) }}
                            </p>
                        </div>
                        <div class="form-group">
                            <label for="bankTransferReference">Reference Number <span class="text-danger">*</span></label>
                            <input type="text"
                                id="bankTransferReference"
                                class="form-control @error('bankTransferReference') is-invalid @enderror"
                                wire:model.defer="bankTransferReference"
                                placeholder="Enter the reference number from your transaction">
                            @error('bankTransferReference')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn"
                            wire:click="$set('showPaymentModal', false)">Cancel</button>
                        <button type="submit" class="btn btn-primary">Complete Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>