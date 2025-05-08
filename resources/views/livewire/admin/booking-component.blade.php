<div>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-calendar-check text-primary mr-2"></i>
            Booking Management
        </h4>
        <div class="d-flex align-items-center">
            <select wire:model.live="perPage" class="form-control form-control-sm mr-2" style="width: 80px;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <select wire:model.live="filters.status" class="form-control form-control-sm mr-2" style="width: 130px;">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white border-right-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                </div>
                <input type="text" class="form-control border-left-0" wire:model.live.debounce.300ms="search"
                    placeholder="Search by booking ref, customer name, or package...">
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Bookings Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="pl-4">#</th>
                            <th>
                                <a href="#" wire:click.prevent="sortBy('id')"
                                    class="text-decoration-none text-dark">
                                    Booking Ref
                                    @if ($sortField === 'id')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Customer</th>
                            <th>Package</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-right pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookingsList as $index => $booking)
                            <tr>
                                <td class="pl-4">
                                    {{ ($bookingsList->currentPage() - 1) * $bookingsList->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <span class="font-weight-medium">
                                        #{{ $booking->id }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light text-primary p-2 mr-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-medium">{{ $booking->user->name }}</div>
                                            <small class="text-muted">{{ $booking->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-weight-medium">{{ $booking->package->name }}</div>
                                        <small class="text-muted">
                                            {{ $booking->rooms->count() }} room(s)
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {{ \Carbon\Carbon::parse($booking->from_date)->format('M d, Y') }}
                                        <small class="text-muted d-block">
                                            {{ $booking->number_of_days }} days
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-weight-bold">৳{{ number_format($booking->price + $booking->booking_price, 2) }}
                                        </div>
                                        <small class="text-muted">
                                            Room: ৳{{ number_format($booking->price, 2) }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusColor = match (strtolower($booking->status)) {
                                            'pending' => 'warning',
                                            'approved', 'paid' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'secondary',
                                        };
                                        $statusIcon = match (strtolower($booking->status)) {
                                            'pending' => 'clock',
                                            'approved', 'paid' => 'check-circle',
                                            'cancelled' => 'times-circle',
                                            default => 'info-circle',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $statusColor }} badge-pill">
                                        <i class="fas fa-{{ $statusIcon }} mr-1"></i>
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>

                                <td class="text-right">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.bookings.show', ['id' => $booking->id]) }}"
                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.bookings.edit', $booking) }}"
                                            class="btn btn-sm btn-outline-primary" title="Edit Booking">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Booking"
                                            wire:click="confirmDelete({{ $booking->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>


                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <p class="mb-0">No bookings found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if ($bookingsList->hasPages())
        <div class="mt-4">
            {{ $bookingsList->links() }}
        </div>
    @endif

    <!-- Delete Modal -->
    @if ($showDeleteModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-trash text-danger mr-2"></i>
                            Confirm Delete
                        </h5>
                        <button type="button" class="close" wire:click="$set('showDeleteModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Are you sure you want to delete this booking? This action cannot be undone.
                        </div>
                        <p class="mb-0 text-danger">
                            <small>
                                <i class="fas fa-info-circle mr-1"></i>
                                This will also delete all related payments and payment links.
                            </small>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">
                            <i class="fas fa-times mr-1"></i>
                            Cancel
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="deleteBooking">
                            <i class="fas fa-trash mr-1"></i>
                            Delete Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        /* Custom styles */
        .table th {
            border-top: none;
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }

        .pagination {
            margin-bottom: 0;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, .02);
        }

        .search-box {
            border-radius: 20px;
            padding: 0.75rem 1.25rem;
        }
    </style>
</div>
