<div class="container-fluid">
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Package Management</h4>
        <a class="btn btn-primary" href="{{ route('admin.packages.create') }}">
            <i class="fas fa-plus mr-2"></i>Create Package
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Package Name</th>
                            <th>Address</th>
                            <th>Created By</th>
                            <th>Assigned To</th>
                            <th>Current Bookings</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $package)
                            @php
                                $today = \Carbon\Carbon::now();
                                $expirationDate = $package->expiration_date
                                    ? \Carbon\Carbon::parse($package->expiration_date)
                                    : null;
                                $isExpired =
                                    $package->status === 'expired' ||
                                    ($expirationDate && $today->greaterThanOrEqualTo($expirationDate));
                            @endphp
                            <tr class="{{ $isExpired ? 'table-danger' : '' }}">
                                <td>{{ $package->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-building text-primary mr-2"></i>
                                        <strong>{{ $package->name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-secondary mr-2"></i>
                                        {{ $package->address }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light p-2 mr-2">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        {{ $package->creator->name }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($package->assigned_to)
                                            <div class="rounded-circle bg-light p-2 mr-2">
                                                <i class="fas fa-user-check text-success"></i>
                                            </div>
                                            {{ $package->assignedPartner->name }}
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-user-slash mr-1"></i>
                                                Not assigned
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($package->bookings->isEmpty())
                                        <span class="text-muted">No current bookings</span>
                                    @else
                                        <div class="d-flex flex-column">
                                            @foreach ($package->bookings->take(2) as $booking)
                                                <div class="mb-2 border-bottom pb-2">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-user-circle text-primary mr-1"></i>
                                                        <strong>{{ $booking->user->name }}</strong>
                                                    </div>
                                                    <div class="small text-muted ms-3">
                                                        <i class="fas fa-calendar-alt mr-1"></i>
                                                        {{ \Carbon\Carbon::parse($booking->from_date)->format('d M') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($booking->to_date)->format('d M') }}
                                                    </div>
                                                    <div class="ms-3 mt-1">
                                                        @php
                                                            $roomIds = json_decode($booking->room_ids, true) ?? [];
                                                            $rooms = \App\Models\Room::whereIn('id', $roomIds)->get();
                                                        @endphp
                                                        @foreach ($rooms as $room)
                                                            <span class="badge bg-info mr-1 text-white">
                                                                <i class="fas fa-bed mr-1"></i>
                                                                {{ $room->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if ($package->bookings->count() > 2)
                                                <small class="text-primary">
                                                    <i class="fas fa-plus-circle mr-1"></i>
                                                    {{ $package->bookings->count() - 2 }} more bookings
                                                </small>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if (!$isExpired)
                                        <div class="btn-group">
                                            <a href="{{ route('packages.show', ['packageId' => $package->id]) }}"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.package.edit', ['packageId' => $package->id]) }}"
                                                class="btn btn-sm btn-outline-info" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button wire:click="confirmDelete({{ $package->id }})"
                                                class="btn btn-sm btn-outline-danger" title="Delete Package">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            @if (auth()->user()->hasRole('Super Admin') || auth()->id() === $package->user_id)
                                                <button wire:click="openAssignModal({{ $package->id }})"
                                                    class="btn btn-sm btn-outline-warning" title="Assign Partner">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-danger">Expired</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($showAssignModal)
        <div class="modal fade show" tabindex="-1" role="dialog" style="display: block;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <!-- Modal Header -->
                    <div class="modal-header align-items-center bg-light">
                        <h5 class="modal-title d-flex align-items-center">
                            @if ($selectedPackage && $selectedPackage->assigned_to)
                                <i class="fas fa-user-edit fa-fw text-warning mr-2"></i>
                                <span>Manage Package Assignment</span>
                            @else
                                <i class="fas fa-user-plus fa-fw text-primary mr-2"></i>
                                <span>Assign Package to Partner</span>
                            @endif
                        </h5>
                        <button type="button" class="btn-close shadow-none" wire:click="closeModal"></button>
                    </div>

                    <form wire:submit.prevent="assignUser">
                        <div class="modal-body p-4">
                            <!-- Alert Messages -->
                            @if (session()->has('error'))
                                <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                                    <i class="fas fa-exclamation-circle flex-shrink-0 mr-2"></i>
                                    <div>{{ session('error') }}</div>
                                </div>
                            @endif

                            <!-- Package Information Card -->
                            @if ($selectedPackage)
                                <div class="card bg-light border-0 mb-4">
                                    <div class="card-body p-3">
                                        <h6 class="d-flex align-items-center text-muted mb-3">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            Package Details
                                        </h6>

                                        <div class="package-info">
                                            <!-- Package Name -->
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center text-primary">
                                                    <i class="fas fa-box fa-fw mr-2"></i>
                                                    <strong>{{ $selectedPackage->name }}</strong>
                                                </div>
                                            </div>

                                            <!-- Creator Info -->
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-tie fa-fw text-secondary mr-2"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Created by</small>
                                                        <strong>{{ $selectedPackage->creator->name }}</strong>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Current Assignment Info -->
                                            @if ($selectedPackage->assigned_to)
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user-check fa-fw text-success mr-2"></i>
                                                        <div>
                                                            <small class="text-muted d-block">Currently assigned
                                                                to</small>
                                                            <strong>{{ $selectedPackage->assignedPartner->name }}</strong>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Assignment History -->
                                                @if ($selectedPackage->assigned_by && $selectedPackage->assignedBy)
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-history fa-fw text-info mr-2"></i>
                                                        <div>
                                                            <small class="text-muted d-block">Assignment
                                                                history</small>
                                                            <div>
                                                                Assigned by
                                                                <strong>{{ $selectedPackage->assignedBy->name }}</strong>
                                                                @if ($selectedPackage->assigned_at)
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        <i class="far fa-clock mr-1"></i>
                                                                        {{ \Carbon\Carbon::parse($selectedPackage->assigned_at)->diffForHumans() }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Assignment Form -->
                                <div class="form-group">
                                    <label class="form-label d-flex align-items-center mb-2">
                                        @if ($selectedPackage->assigned_to)
                                            <i class="fas fa-exchange-alt text-warning mr-2"></i>
                                            <span>Reassign or Remove Partner</span>
                                        @else
                                            <i class="fas fa-user-plus text-primary mr-2"></i>
                                            <span>Select Partner to Assign</span>
                                        @endif
                                    </label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light">
                                                <i class="fas fa-user-friends text-muted"></i>
                                            </span>
                                        </div>
                                        <select wire:model="selectedUserId"
                                            class="form-control @error('selectedUserId') is-invalid @enderror">
                                            <option value="">
                                                {{ $selectedPackage->assigned_to ? '← Remove current assignment' : 'Choose a partner...' }}
                                            </option>
                                            @foreach ($availablePartners as $partner)
                                                <option value="{{ $partner->id }}">
                                                    {{ $partner->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selectedUserId')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-light" wire:click="closeModal">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                @if ($selectedPackage && $selectedPackage->assigned_to)
                                    @if ($selectedUserId)
                                        <i class="fas fa-exchange-alt mr-2"></i>
                                        Update Assignment
                                    @else
                                        <i class="fas fa-user-minus mr-2"></i>
                                        Remove Assignment
                                    @endif
                                @else
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Assign Partner
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>

        <!-- Additional Styles -->
        <style>
            .modal-content {
                border-radius: 0.5rem;
            }

            .card {
                border-radius: 0.4rem;
            }

            .input-group-text {
                border-right: 0;
            }

            .form-select {
                border-left: 0;
            }

            .form-select:focus {
                border-left: 1px solid #86b7fe;
            }

            .package-info {
                font-size: 0.95rem;
            }

            .package-info .fas,
            .package-info .far {
                width: 20px;
            }

            .modal-header .modal-title {
                font-size: 1.1rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-weight: 500;
            }

            .btn-light {
                background-color: #f8f9fa;
                border-color: #ddd;
            }

            .btn-light:hover {
                background-color: #e9ecef;
                border-color: #ddd;
            }
        </style>
    @endif


    @if ($showDeleteModal)
        <div class="modal fade show" tabindex="-1" role="dialog" style="display: block;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white border-0">
                        <h5 class="modal-title d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span>Confirm Package Deletion</span>
                        </h5>
                        <button type="button" class="close text-white" wire:click="closeDeleteModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        @if ($packageToDelete)
                            <div class="text-center mb-4">
                                <div class="mb-4">
                                    <span class="avatar avatar-lg bg-danger-light rounded-circle mb-2">
                                        <i class="fas fa-trash-alt fa-lg text-danger"></i>
                                    </span>
                                </div>
                                <h5 class="mb-3">Delete Package "{{ $packageToDelete->name }}"?</h5>
                                <p class="text-muted">
                                    Are you sure you want to delete this package? This action cannot be undone and will
                                    remove all associated data.
                                </p>

                                <!-- Package Details -->
                                <div class="alert alert-light text-left mt-4 mb-0">
                                    <div class="mb-2">
                                        <small class="text-muted">Package Details:</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-building text-secondary mr-2"></i>
                                        <strong>{{ $packageToDelete->name }}</strong>
                                    </div>
                                    @if ($packageToDelete->address)
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-map-marker-alt text-secondary mr-2"></i>
                                            <span>{{ $packageToDelete->address }}</span>
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user text-secondary mr-2"></i>
                                        <span>Created by {{ $packageToDelete->creator->name }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="deletePackage">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Delete Package
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>

        <style>
            .avatar {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 64px;
                height: 64px;
            }

            .bg-danger-light {
                background-color: rgba(220, 53, 69, 0.1);
            }

            .modal-content {
                border-radius: 0.5rem;
            }

            .modal-header {
                border-top-left-radius: 0.5rem;
                border-top-right-radius: 0.5rem;
            }

            .modal-header .close {
                opacity: 0.75;
                text-shadow: none;
            }

            .modal-header .close:hover {
                opacity: 1;
            }

            .alert-light {
                background-color: #f8f9fa;
                border-color: #eff2f5;
            }

            .btn {
                padding: 0.5rem 1rem;
                font-weight: 500;
            }

            .btn-secondary {
                background-color: #e9ecef;
                border-color: #ddd;
            }

            .btn-secondary:hover {
                background-color: #dde1e4;
                border-color: #c8ccd0;
            }
        </style>
    @endif

    <style>
        .table th {
            font-weight: 600;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }

        .modal-backdrop {
            opacity: 0.5;
        }

        .modal-content {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            border-radius: 0.5rem;
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .badge {
            padding: 0.35em 0.65em;
        }

        .rounded-circle {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-close {
            box-sizing: content-box;
            width: 1em;
            height: 1em;
            padding: 0.25em 0.25em;
            color: #000;
            background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat;
            border: 0;
            border-radius: 0.25rem;
            opacity: .5;
        }

        .btn-close:hover {
            color: #000;
            text-decoration: none;
            opacity: .75;
        }

        .modal-open {
            overflow: hidden;
        }

        .modal-open .modal {
            overflow-x: hidden;
            overflow-y: auto;
        }
    </style>




</div>
