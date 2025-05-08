<div class="container-fluid py-4">
    <div class="row">
        @forelse ($bookings as $booking)
            <div class="col-12 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="row no-gutters">
                        <!-- Image -->
                        <div class="col-md-3" style="max-height: 200px;">
                            @if ($booking->package->photos->isNotEmpty())
                                <img src="{{ asset('storage/' . $booking->package->photos->first()->url) }}"
                                    alt="{{ $booking->package->name }}" class="img-thumbnail w-100 h-100"
                                    style="object-fit: cover;">
                            @else
                                <img src="{{ asset('default-thumbnail.jpg') }}" alt="Default thumbnail"
                                    class="img-thumbnail w-100 h-100" style="object-fit: cover;">
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="col-md-7 p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0 font-weight-bold">{{ $booking->package->name }}</h6>
                                <small class="text-muted">Ref: {{ $booking->id }}</small>
                            </div>

                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $booking->package->address }}
                            </p>

                            <div class="d-flex mb-2 small">
                                <div class="mr-3">
                                    <i class="fas fa-bed mr-1"></i>
                                    Sleeps {{ $booking->package->sleeps }}
                                </div>
                                <div>
                                    <i class="fas fa-door-open mr-1"></i>
                                    Bedrooms {{ $booking->package->bedrooms }}
                                </div>
                            </div>

                            <div class="small">
                                <div class="mb-1">
                                    {{ Carbon\Carbon::parse($booking->from_date)->format('d M Y') }} -
                                    {{ Carbon\Carbon::parse($booking->to_date)->format('d M Y') }}
                                </div>
                                <div class="text-muted">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $booking->number_of_days }} Days
                                </div>
                            </div>
                        </div>

                        <!-- Status & Action -->
                        <div class="col-md-2 p-3 bg-light">
                            <div class="d-flex flex-column h-100 justify-content-between">
                                <div class="text-right">
                                    @php
                                        $statusColor = match ($booking->status) {
                                            'finished' => '#252525',
                                            'cancelled' => '#404040',
                                            'pending' => '#666666',
                                            default => '#808080',
                                        };
                                    @endphp
                                    <span class="badge text-white" style="background-color: {{ $statusColor }};">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('bookings.show', $booking->id) }}"
                                        class="btn btn-sm btn-outline-secondary btn-block">
                                        Booking details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-4">
                <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                <h6 class="text-muted">No bookings found</h6>
                <a href="{{ route('package.list') }}" class="btn btn-sm btn-secondary mt-2">
                    Browse Packages
                </a>
            </div>
        @endforelse
    </div>

    @if ($bookings->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $bookings->links() }}
        </div>
    @endif

    <style>
        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .badge {
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
        }
    </style>
</div>
