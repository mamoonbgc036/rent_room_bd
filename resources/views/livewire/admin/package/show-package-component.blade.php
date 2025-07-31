<div class="container-fluid py-4">
    @push('styles')
    <style>
        .package-view .card {
            transition: all 0.3s ease;
        }

        .package-view .card:hover {
            transform: translateY(-2px);
        }

        .package-view .bg-light {
            background-color: #f8f9fa !important;
        }

        /* Add more page-specific styles */
    </style>
    @endpush
    <!-- Package Creator Info -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary text-white p-3 mr-3">
                    <i class="fas fa-user-shield fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Package Created By</h6>
                    <h4 class="mb-0">{{ $package->user->name }}</h4>
                    <small class="text-muted">{{ $package->user->email }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Section -->
    <!-- Bookings Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Current Bookings</h5>
        </div>
        <div class="card-body">
            @if ($bookings->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No bookings available for this package</h6>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Guest</th>
                            <th scope="col">Booked Place</th>
                            <th scope="col">Auto Renewal</th>
                            <th scope="col">Duration</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                        @php
                        $roomIds = json_decode($booking->room_ids, true) ?? [];
                        $rooms = \App\Models\Room::whereIn('id', $roomIds)->get();
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-2 me-2">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $booking->user->name }}</h6>
                                        <small class="text-muted">{{ $booking->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @foreach ($rooms as $room)
                                    <span class="badge bg-info text-light">
                                        <i class="fas fa-bed me-1"></i>
                                        {{ $room->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if ($booking['auto_renewal'])
                                    <span class="text-success">
                                        <i class="fas fa-check-circle mr-1"></i>Enabled
                                    </span>
                                    @else
                                    <span class="text-secondary">
                                        <i class="fas fa-times-circle mr-1"></i>Disabled
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $booking->number_of_days }} days
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0">৳{{ number_format($booking->total_amount, 2) }}</h6>
                                    @if ($booking->payment_status !== 'completed')
                                    <small class="text-danger">
                                        Due:
                                        ৳{{ number_format($booking->price + $booking->booking_price - $booking->total_amount, 2) }}
                                    </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                $statusColor = match ($booking->payment_status) {
                                'completed', 'paid' => 'success',
                                'pending' => 'warning',
                                'cancelled' => 'danger',
                                default => 'secondary',
                                };
                                $statusIcon = match ($booking->payment_status) {
                                'completed', 'paid' => 'check-circle',
                                'pending' => 'clock',
                                'cancelled' => 'ban',
                                default => 'info-circle',
                                };
                                @endphp
                                <span class="badge bg-{{ $statusColor }} text-light">
                                    <i class="fas fa-{{ $statusIcon }} me-1"></i>
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.users.view', ['userId' => $booking->user->id]) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <!-- Package Details -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Package Details</h5>
            <div>
                <a href="{{ route('admin.packages') }}" class="btn btn-outline-secondary btn-sm mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                <a href="{{ route('admin.package.edit', ['packageId' => $package->id]) }}"
                    class="btn btn-primary btn-sm">
                    <i class="fas fa-edit mr-1"></i> Edit Package
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <!-- Basic Information -->
                <div class="col-md-6">
                    <div class="p-4 bg-light rounded">
                        <h6 class="border-bottom pb-2 mb-3">Basic Information</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Package Name</small>
                                <strong>{{ $package->name }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Property Name</small>
                                <strong>{{ $package->property->name }}</strong>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">Address</small>
                                <strong>{{ $package->address }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Country</small>
                                <strong>{{ $package->country?->name ?? 'Bangladesh' }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">City</small>
                                <strong>{{ $package->city->name }}</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Area</small>
                                <strong>{{ $package->area->name }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="col-md-6">
                    <div class="p-4 bg-light rounded">
                        <h6 class="border-bottom pb-2 mb-3">Property Features</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-utensils fa-fw text-primary mr-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Kitchens</small>
                                        <strong>{{ $package->number_of_kitchens }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chair fa-fw text-primary mr-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Seating Capacity</small>
                                        <strong>{{ $package->seating }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt fa-fw text-primary mr-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Location</small>
                                        <a href="{{ $package->map_link }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary mt-1">
                                            <i class="fas fa-map mr-1"></i> View on Map
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rooms -->
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">Available Rooms</h6>
                    <div class="row g-4">
                        @foreach ($package->rooms as $room)
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title d-flex align-items-center">
                                        <i class="fas fa-bed text-primary mr-2"></i>
                                        {{ $room->name }}
                                    </h6>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <small class="text-muted">Beds</small>
                                            <span
                                                class="badge bg-light text-dark">{{ $room->number_of_beds }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <small class="text-muted">Bathrooms</small>
                                            <span
                                                class="badge bg-light text-dark">{{ $room->number_of_bathrooms }}</span>
                                        </div>
                                        @foreach ($room->prices as $price)
                                        <div class="card bg-light border-0 mb-2">
                                            <div class="card-body p-3">
                                                <small
                                                    class="text-muted d-block mb-2">{{ ucfirst($price->type) }}
                                                    Rate</small>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span>Fixed Price</span>
                                                    <strong>৳{{ $price->fixed_price }}</strong>
                                                </div>
                                                @if ($price->discount_price)
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span>Discount Price</span>
                                                    <strong
                                                        class="text-success">৳{{ $price->discount_price }}</strong>
                                                </div>
                                                @endif
                                                @if ($price->booking_price)
                                                <div class="d-flex justify-content-between">
                                                    <span>Booking Price</span>
                                                    <strong
                                                        class="text-primary">৳{{ $price->booking_price }}</strong>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Package Instructions -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0">
                                <i class="fas fa-list-ol text-primary mr-2"></i>
                                Package Instructions
                            </h6>
                        </div>
                        <div class="card-body">
                            @if ($package->instructions->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No instructions available for this package</p>
                            </div>
                            @else
                            <div class="timeline">
                                @foreach ($package->instructions->sortBy('order') as $instruction)
                                <div class="instruction-item mb-4">
                                    <div class="d-flex">
                                        <div class="instruction-number">
                                            <span
                                                class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 30px; height: 30px;">
                                                {{ $loop->iteration }}
                                            </span>
                                        </div>
                                        <div class="instruction-content ml-3 flex-grow-1">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-2">{{ $instruction->title }}</h6>
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

                <!-- Amenities and Maintains -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="border-bottom pb-2 mb-3">Included Services</h6>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">Free Maintains</h6>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($package->maintains()->wherePivot('is_paid', false)->get() as $maintain)
                                        <li class="list-group-item px-0">
                                            <i class="fas fa-check-circle text-success mr-2"></i>
                                            {{ $maintain->name }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">Free Amenities</h6>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($package->amenities()->wherePivot('is_paid', false)->get() as $amenity)
                                        <li class="list-group-item px-0">
                                            <i class="fas fa-check-circle text-success mr-2"></i>
                                            {{ $amenity->name }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="border-bottom pb-2 mb-3">Additional Services</h6>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">Paid Maintains</h6>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($package->maintains()->wherePivot('is_paid', true)->get() as $maintain)
                                        <li
                                            class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                                {{ $maintain->name }}
                                            </span>
                                            <span class="badge bg-primary">৳{{ $maintain->pivot->price }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">Paid Amenities</h6>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($package->amenities()->wherePivot('is_paid', true)->get() as $amenity)
                                        <li
                                            class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-plus-circle text-primary mr-2"></i>
                                                {{ $amenity->name }}
                                            </span>
                                            <span class="badge bg-primary">৳{{ $amenity->pivot->price }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property Photos Gallery -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="border-bottom pb-2 mb-3">Property Gallery</h6>
                            <div class="row g-4">
                                @foreach ($package->photos as $photo)
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm">
                                        <img src="{{ asset('storage/' . $photo->url) }}" class="card-img-top"
                                            alt="{{ $package->name }}" style="height: 200px; object-fit: cover;">
                                        <div class="card-img-overlay d-flex align-items-end">
                                            <button class="btn btn-sm btn-light w-100"
                                                onclick="window.open('{{ asset('storage/' . $photo->url) }}', '_blank')">
                                                <i class="fas fa-expand-alt mr-1"></i> View Full
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Package Description -->
                @if ($package->details)
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="border-bottom pb-2 mb-3">Additional Details</h6>
                            <p class="text-muted mb-0">{{ $package->details }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>