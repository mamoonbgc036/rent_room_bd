<div class="container mt-5">
    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card shadow-sm p-4 mb-4">
                <h3>User Information</h3>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Phone Number:</strong> {{ $user->phone }}</p>

                <button class="btn btn-primary" wire:click="openEditModal">Edit</button>
            </div>
        </div>
        @if ($user->packages && $user->packages->isNotEmpty())
            <div class="col-6 col-lg-6">
                <div class="card shadow-sm p-4 mb-4">
                    <h3 class="mb-3">Assigned Packages</h3>
                    @foreach ($user->packages as $package)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <a href="{{ route('packages.show', $package->id) }}"
                                class="text-decoration-none text-primary">
                                {{ $package->name }}
                            </a>
                            <a href="{{ route('packages.show', $package->id) }}" class="btn btn-outline-primary btn-sm">
                                View Details
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif


        @if ($user->hasRole('User'))
            <div class="container-fluid">
                @if ($bookings && count($bookings))
                    <div class="row">
                        @foreach ($bookings as $booking)
                            <div class="col-md-6 mb-4">
                                <div class="card booking-card">
                                    <!-- Card Header -->
                                    <div
                                        class="card-header {{ $booking['payment_status'] === 'cancelled' ? 'bg-danger' : ($booking['payment_status'] === 'pending' ? 'bg-warning' : 'bg-primary') }} text-white d-flex justify-content-between align-items-center py-3">
                                        <div>
                                            <h5 class="mb-0 text-light">
                                                <i class="fas fa-bookmark mr-2"></i>
                                                Booking #{{ $booking['id'] }}
                                            </h5>
                                            <small class="opacity-75">{{ $booking['package']['name'] }}</small>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="btn-group invoice-actions">
                                            @if ($booking['payment_status'] !== 'cancelled')
                                                @if ($booking['payment_summary']['remaining_balance'] > 0)
                                                    <button wire:click="generatePaymentLink({{ $booking['id'] }})"
                                                        wire:loading.attr="disabled"
                                                        class="btn btn-light btn-sm btn-action">
                                                        <div wire:loading.remove
                                                            wire:target="generatePaymentLink({{ $booking['id'] }})">
                                                            <i class="fas fa-link mr-1"></i> Payment Link
                                                        </div>
                                                        <div wire:loading
                                                            wire:target="generatePaymentLink({{ $booking['id'] }})">
                                                            <i class="fas fa-spinner fa-spin mr-1"></i> Processing...
                                                        </div>
                                                    </button>
                                                @endif

                                                <button wire:click="downloadInvoice({{ $booking['id'] }})"
                                                    class="btn btn-info btn-sm btn-action text-white">
                                                    <i class="fas fa-download mr-1"></i> Invoice
                                                </button>

                                                <button wire:click="emailInvoice({{ $booking['id'] }})"
                                                    class="btn btn-success btn-sm btn-action">
                                                    <i class="fas fa-envelope mr-1"></i> Email
                                                </button>
                                            @else
                                                <span class="badge badge-light text-danger">
                                                    <i class="fas fa-ban mr-1"></i> Cancelled
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <!-- Cancelled Alert -->
                                        @if ($booking['payment_status'] === 'cancelled')
                                            <div class="alert alert-modern alert-danger mb-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-exclamation-circle h4 mb-0 mr-3"></i>
                                                    <div>
                                                        <h6 class="alert-heading mb-1">Booking Cancelled</h6>
                                                        <p class="mb-0 small">This booking has been cancelled and is no
                                                            longer active.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Booking Details Section -->
                                        <div class="mb-4">
                                            <h6 class="section-header">
                                                <i class="fas fa-info-circle mr-2"></i>Booking Details
                                            </h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="summary-item">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted"><i
                                                                    class="far fa-calendar-alt mr-2"></i>Check
                                                                In:</span>
                                                            <strong>{{ \Carbon\Carbon::parse($booking['from_date'])->format('M d, Y') }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted"><i
                                                                    class="far fa-calendar-alt mr-2"></i>Check
                                                                Out:</span>
                                                            <strong>{{ \Carbon\Carbon::parse($booking['to_date'])->format('M d, Y') }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span class="text-muted"><i
                                                                    class="far fa-clock mr-2"></i>Duration:</span>
                                                            <strong>{{ $booking['number_of_days'] }} days</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="summary-item">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted"><i
                                                                    class="fas fa-tag mr-2"></i>Price:</span>
                                                            <strong>{{ $booking['price_type'] }}ly</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted"><i
                                                                    class="fas fa-money-bill mr-2"></i>Payment:</span>
                                                            <strong>Booking</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span class="text-muted"><i
                                                                    class="fas fa-sync-alt mr-2"></i>Auto
                                                                Renewal:</span>
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
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <h6 class="section-header">
                                                <i class="fas fa-money-check-alt mr-2"></i>Payment Summary
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table table-minimal">
                                                    <tbody>
                                                        <!-- Initial Charges -->
                                                        <tr class="table-light">
                                                            <td colspan="2">
                                                                <strong><i class="fas fa-receipt mr-2"></i>Initial
                                                                    Charges</strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pl-4">Total Rent:</td>
                                                            <td class="text-right">
                                                                ৳{{ number_format($booking['price'], 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pl-4">Booking:<br>

                                                            </td>
                                                            <td class="text-right">
                                                                ৳{{ number_format($booking['booking_price'], 2) }}</td>
                                                        </tr>

                                                        <!-- All Milestones with Status -->
                                                        <tr class="table-light">
                                                            <td colspan="2" class="border-bottom">
                                                                <strong><i class="fas fa-calendar-alt mr-2"></i>Payment
                                                                    Schedule</strong>
                                                            </td>
                                                        </tr>
                                                        @if (isset($booking['bookingPayments']) && count($booking['bookingPayments']) > 0)
                                                            @foreach ($booking['bookingPayments'] as $index => $milestone)
                                                                @if ($milestone['milestone_type'] !== 'Booking Fee')
                                                                    @php
                                                                        $dueDate = \Carbon\Carbon::parse(
                                                                            $milestone['due_date'],
                                                                        );
                                                                        $now = \Carbon\Carbon::now();

                                                                        if ($milestone['payment_status'] === 'paid') {
                                                                            $status = [
                                                                                'text' => 'Paid',
                                                                                'class' => 'bg-success-soft',
                                                                                'badge' => 'success',
                                                                                'icon' => 'fa-check-circle',
                                                                            ];
                                                                        } elseif ($dueDate->isPast()) {
                                                                            $status = [
                                                                                'text' => 'Overdue',
                                                                                'class' => 'bg-danger-soft',
                                                                                'badge' => 'danger',
                                                                                'icon' => 'fa-exclamation-circle',
                                                                            ];
                                                                        } elseif ($dueDate->isToday()) {
                                                                            $status = [
                                                                                'text' => 'Due Today',
                                                                                'class' => 'bg-warning-soft',
                                                                                'badge' => 'warning',
                                                                                'icon' => 'fa-clock',
                                                                            ];
                                                                        } else {
                                                                            $status = [
                                                                                'text' => 'Pending',
                                                                                'class' => 'bg-light',
                                                                                'badge' => 'secondary',
                                                                                'icon' => 'fa-calendar',
                                                                            ];
                                                                        }
                                                                    @endphp
                                                                    <tr class="milestone-row {{ $status['class'] }}">
                                                                        <td class="py-3 pl-4">
                                                                            <div class="d-flex align-items-center">
                                                                                <div
                                                                                    class="milestone-number rounded-circle bg-{{ $status['badge'] }} text-white mr-3">
                                                                                    {{ $milestone['milestone_number'] }}
                                                                                </div>
                                                                                <div>
                                                                                    <div class="font-weight-medium">
                                                                                        {{ $milestone['milestone_type'] }}
                                                                                        Payment
                                                                                    </div>
                                                                                    <div class="milestone-details mt-1">
                                                                                        <span class="text-muted">
                                                                                            <i
                                                                                                class="far fa-calendar-alt mr-1"></i>
                                                                                            {{ $dueDate->format('d M Y') }}
                                                                                        </span>
                                                                                        <span
                                                                                            class="badge badge-{{ $status['badge'] }} ml-2">
                                                                                            <i
                                                                                                class="fas {{ $status['icon'] }} mr-1"></i>
                                                                                            {{ $status['text'] }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-right align-middle pr-4">
                                                                            <div class="font-weight-bold">
                                                                                ৳{{ number_format($milestone['amount'], 2) }}
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                        <style>
                                                            .milestone-row {
                                                                transition: all 0.2s ease;
                                                                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                                                            }

                                                            .milestone-row:hover {
                                                                transform: translateX(5px);
                                                            }

                                                            .milestone-number {
                                                                width: 28px;
                                                                height: 28px;
                                                                display: flex;
                                                                align-items: center;
                                                                justify-content: center;
                                                                font-weight: 600;
                                                                font-size: 14px;
                                                            }

                                                            .milestone-details {
                                                                font-size: 0.875rem;
                                                            }

                                                            .bg-success-soft {
                                                                background-color: #d4edda;
                                                            }

                                                            .bg-danger-soft {
                                                                background-color: #f8d7da;
                                                            }

                                                            .bg-warning-soft {
                                                                background-color: #fff3cd;
                                                            }

                                                            .badge {
                                                                padding: 0.4em 0.6em;
                                                                font-weight: 500;
                                                            }

                                                            .badge i {
                                                                font-size: 0.875em;
                                                            }

                                                            .font-weight-medium {
                                                                font-weight: 500;
                                                            }
                                                        </style>

                                                        <!-- Overview Section -->
                                                        <tr class="table-light">
                                                            <td colspan="2">
                                                                <strong><i
                                                                        class="fas fa-chart-pie mr-2"></i>Overview</strong>
                                                            </td>
                                                        </tr>
                                                        @php
                                                            // Calculate payable amount (only overdue and due today)
                                                            $payableAmount = collect($booking['bookingPayments'])
                                                                ->filter(function ($payment) {
                                                                    $dueDate = \Carbon\Carbon::parse(
                                                                        $payment['due_date'],
                                                                    );
                                                                    return $payment['payment_status'] &&
                                                                        ($dueDate->isPast() || $dueDate->isToday());
                                                                })
                                                                ->sum('amount');
                                                        @endphp
                                                        <tr>
                                                            <td class="pl-4">Payable Amount:</td>
                                                            <td class="text-right">
                                                                ৳{{ number_format($payableAmount, 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pl-4">Amount Paid:</td>
                                                            <td class="text-right text-success">
                                                                ৳{{ number_format($booking['payment_summary']['total_paid'], 2) }}
                                                            </td>
                                                        </tr>
                                                        <tr class="border-top">
                                                            <td class="pl-4"><strong>Current Due Balance:</strong>
                                                            </td>
                                                            <td class="text-right">
                                                                <strong
                                                                    class="text-{{ $payableAmount - $booking['payment_summary']['total_paid'] > 0 ? 'danger' : 'success' }}">
                                                                    ৳{{ number_format($payableAmount - $booking['payment_summary']['total_paid'], 2) }}
                                                                </strong>

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                {{-- @if ($payableAmount > 0)
                                                    <div class="alert alert-info mt-3 mb-0">
                                                        <i class="fas fa-info-circle mr-2"></i>
                                                        @if ($booking['price_type'] === 'Month')
                                                            Monthly payments are due at the start of each month
                                                        @elseif($booking['price_type'] === 'Week')
                                                            Weekly payments are due at the start of each week
                                                        @else
                                                            Daily payments are due at the start of each day
                                                        @endif
                                                    </div>
                                                @endif --}}
                                            </div>
                                        </div>

                                        <style>
                                            .table-minimal td {
                                                padding: 0.5rem;
                                                border-color: #eee;
                                            }

                                            .table-light {
                                                background-color: #f8f9fa;
                                            }
                                        </style>

                                        <!-- Payment History Section -->
                                        @if ($booking['payment_status'] !== 'cancelled' && !empty($booking['payments']))
                                            <div class="mb-4">
                                                <h6 class="section-header">
                                                    <i class="fas fa-history mr-2"></i>Payment History
                                                </h6>
                                                <div class="table-responsive">
                                                    <table class="table table-minimal">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Amount</th>
                                                                <th>Method</th>
                                                                <th>Reference</th>
                                                                <th>Status</th>
                                                                <th class="text-right">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($booking['payments'] as $index => $payment)
                                                                <tr
                                                                    class="payment-row {{ $payment['status'] === 'Paid' ? 'paid' : '' }}">
                                                                    <td>{{ $index + 1 }}</td>
                                                                    <td>৳{{ number_format($payment['amount'], 2) }}
                                                                    </td>
                                                                    <td>{{ ucfirst($payment['payment_method']) }}</td>
                                                                    <td>{{ $payment['transaction_id'] ?? 'N/A' }}</td>
                                                                    <td>
                                                                        <span
                                                                            class="badge badge-{{ $payment['status'] === 'Paid' ? 'success' : 'warning' }} text-white">
                                                                            {{ ucfirst($payment['status']) }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <div class="btn-group">
                                                                            @if ($payment['status'] !== 'Paid')
                                                                                <button
                                                                                    class="btn btn-success btn-sm text-white"
                                                                                    wire:click="updatePaymentStatusForPayment({{ $payment['id'] }}, 'Paid')">
                                                                                    <i class="fas fa-check"></i>
                                                                                </button>
                                                                            @endif
                                                                            @if ($payment['status'] !== 'Pending')
                                                                                <button
                                                                                    class="btn btn-warning btn-sm text-white"
                                                                                    wire:click="updatePaymentStatusForPayment({{ $payment['id'] }}, 'Pending')">
                                                                                    <i class="fas fa-clock"></i>
                                                                                </button>
                                                                            @endif
                                                                            <button
                                                                                class="btn btn-danger btn-sm text-white"
                                                                                wire:click="confirmDeletePayment({{ $payment['id'] }})"
                                                                                title="Delete Payment">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-modern alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        No bookings found for this user.
                    </div>
                @endif

                <!-- Update the copy script to handle multiple links -->
                <script>
                    function copyToClipboard(bookingId) {
                        const paymentLink = document.getElementById(`paymentLink-${bookingId}`).href;
                        navigator.clipboard.writeText(paymentLink).then(() => {
                            const successElement = document.getElementById(`copySuccess-${bookingId}`);
                            successElement.style.display = 'block';
                            setTimeout(() => {
                                successElement.style.display = 'none';
                            }, 2000);
                        }).catch(err => {
                            console.error('Failed to copy:', err);
                        });
                    }
                </script>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card shadow-sm p-4">
                    @if ($showForm)
                        <h3>Upload Documents</h3>
                        <form wire:submit.prevent="saveDocuments" enctype="multipart/form-data">
                            @foreach ($documents as $index => $document)
                                <div class="card mt-2 p-3">
                                    <div class="form-group">
                                        <label>Person Name</label>
                                        <input type="text" class="form-control"
                                            wire:model.defer="documents.{{ $index }}.person_name">
                                        @error('documents.' . $index . '.person_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-2">
                                        <label>Passport (PDF/Image)</label>
                                        <input type="file" class="form-control"
                                            wire:model.defer="documents.{{ $index }}.passport">
                                        @if (isset($document['passport']) && !($document['passport'] instanceof \Illuminate\Http\UploadedFile))
                                            <p>Current File: <a href="{{ Storage::url($document['passport']) }}"
                                                    target="_blank">View File</a></p>
                                        @endif
                                        @error('documents.' . $index . '.passport')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-2">
                                        <label>NID/Other (PDF/Image)</label>
                                        <input type="file" class="form-control"
                                            wire:model.defer="documents.{{ $index }}.nid_or_other">
                                        @if (isset($document['nid_or_other']) && !($document['nid_or_other'] instanceof \Illuminate\Http\UploadedFile))
                                            <p>Current File: <a href="{{ Storage::url($document['nid_or_other']) }}"
                                                    target="_blank">View File</a></p>
                                        @endif
                                        @error('documents.' . $index . '.nid_or_other')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- New Payslip Field -->
                                    <div class="form-group mt-2">
                                        <label>Payslip (PDF/Image)</label>
                                        <input type="file" class="form-control"
                                            wire:model.defer="documents.{{ $index }}.payslip">
                                        @if (isset($document['payslip']) && !($document['payslip'] instanceof \Illuminate\Http\UploadedFile))
                                            <p>Current File: <a href="{{ Storage::url($document['payslip']) }}"
                                                    target="_blank">View File</a></p>
                                        @endif
                                        @error('documents.' . $index . '.payslip')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- New Student Card Field -->
                                    <div class="form-group mt-2">
                                        <label>Student Card (PDF/Image)</label>
                                        <input type="file" class="form-control"
                                            wire:model.defer="documents.{{ $index }}.student_card">
                                        @if (isset($document['student_card']) && !($document['student_card'] instanceof \Illuminate\Http\UploadedFile))
                                            <p>Current File: <a href="{{ Storage::url($document['student_card']) }}"
                                                    target="_blank">View File</a></p>
                                        @endif
                                        @error('documents.' . $index . '.student_card')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    @if ($index > 0)
                                        <button type="button" class="btn btn-danger mt-2"
                                            wire:click="removePerson({{ $index }})">Remove Person</button>
                                    @endif
                                </div>
                            @endforeach

                            <button type="button" class="btn btn-success mt-3" wire:click="addPerson">Add
                                Person</button>
                            <br>
                            <button type="submit" class="btn btn-primary mt-3">Save Documents</button>
                        </form>

                        @if (session()->has('message'))
                            <div class="alert alert-success mt-3">
                                {{ session('message') }}
                            </div>
                        @endif
                    @else
                        <!-- Show the list if the form is hidden -->
                        <h4>Uploaded Documents</h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Person Name</th>
                                        <th>Passport</th>
                                        <th>NID/Other</th>
                                        <th>Payslip/Others</th>
                                        <th>Student/Employee Card</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->documents as $index => $document)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $document->person_name }}</td>
                                            <td>
                                                @if ($document->passport)
                                                    <a href="{{ Storage::url($document->passport) }}"
                                                        class="btn btn-sm btn-outline-info" target="_blank">Download
                                                        Passport</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if ($document->nid_or_other)
                                                    <a href="{{ Storage::url($document->nid_or_other) }}"
                                                        class="btn btn-sm btn-outline-info" target="_blank">Download
                                                        NID/Other</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if ($document->payslip)
                                                    <a href="{{ Storage::url($document->payslip) }}"
                                                        class="btn btn-sm btn-outline-info" target="_blank">Download
                                                        Payslip/Other</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if ($document->student_card)
                                                    <a href="{{ Storage::url($document->student_card) }}"
                                                        class="btn btn-sm btn-outline-info" target="_blank">Download
                                                        Student/Employee Card</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    wire:click="deleteDocument({{ $document->id }})">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No documents uploaded yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>


                        <!-- Add an Edit button to show the form -->
                        <button type="button" class="btn btn-primary mt-3" wire:click="toggleForm">Edit
                            Documents</button>
                    @endif
                </div>
            </div>
        @endif

    </div>


    @if ($showMilestoneSelectionModal)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payment Milestones</h5>
                        <button type="button" class="close"
                            wire:click="$set('showMilestoneSelectionModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if (session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session()->has('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($milestoneOptions as $milestone)
                                        @php
                                            $statusInfo = $this->getMilestoneStatus($milestone);
                                        @endphp
                                        <tr class="{{ $statusInfo['status'] === 'paid' ? 'table-success' : '' }}">
                                            <td>
                                                {{ $milestone['description'] }}
                                                @if ($milestone['is_booking_fee'])
                                                    <span class="badge badge-primary text-white">Booking Fee</span>
                                                @endif
                                            </td>
                                            <td>{{ $milestone['due_date'] }}</td>
                                            <td>৳{{ number_format($milestone['amount'], 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $statusInfo['badge_class'] }} text-white">
                                                    {{ $statusInfo['message'] }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($statusInfo['can_generate_link'])
                                                    @if ($statusInfo['has_link'] ?? false)
                                                        <div class="btn-group">
                                                            <a href="{{ $statusInfo['link'] }}"
                                                                class="btn btn-info btn-sm text-white"
                                                                target="_blank">
                                                                <i class="fas fa-link mr-1"></i> View Link
                                                            </a>
                                                            <button class="btn btn-primary btn-sm text-white"
                                                                wire:click="createPaymentLinkForMilestone({{ $milestone['id'] }})">
                                                                <i class="fas fa-sync-alt mr-1"></i> Regenerate
                                                            </button>
                                                        </div>
                                                    @else
                                                        <button class="btn btn-primary btn-sm text-white"
                                                            wire:click="createPaymentLinkForMilestone({{ $milestone['id'] }})">
                                                            <i class="fas fa-plus mr-1"></i> Generate Link
                                                        </button>
                                                    @endif
                                                @else
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="fas fa-lock mr-1"></i> No Action Required
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No milestones found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showMilestoneSelectionModal', false)">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($isEditModalOpen)
        <!-- Modal Overlay -->
        <div class="modal-backdrop fade show"></div>

        <!-- Modal -->
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="display: block;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User Information</h5>
                        <button type="button" class="close" wire:click="closeEditModal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="updateUser">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" wire:model="userData.name"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email"
                                    wire:model="userData.email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone"
                                    wire:model="userData.phone" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click="closeEditModal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif



    @if ($user->hasRole('Partner'))
        <!-- Partner Information Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-tie mr-2"></i>Partner Information
                </h5>
            </div>
            <div class="card-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="partnerTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="packages-tab" data-toggle="tab" href="#packages"
                            role="tab">
                            <i class="fas fa-box mr-2"></i>Package Documents
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="details-tab" data-toggle="tab" href="#details" role="tab">
                            <i class="fas fa-info-circle mr-2"></i>Partner Details
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="partnerTabContent">
                    <!-- Package Documents Tab -->
                    <div class="tab-pane fade show active" id="documents" role="tabpanel">
                        @forelse($packages as $package)
                            <div class="border-bottom mb-4 pb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <i class="fas fa-box mr-2"></i>{{ $package->name }}
                                        <span class="badge badge-info ml-2">ID: {{ $package->id }}</span>
                                    </h6>
                                    <button type="submit" form="package-form-{{ $package->id }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-save mr-1"></i>Update Documents
                                    </button>
                                </div>

                                <form id="package-form-{{ $package->id }}"
                                    action="{{ route('admin.packages.documents.update', $package->id) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">
                                        <!-- Document Cards -->
                                        @foreach ([
        'gas_certificate' => ['icon' => 'file-contract', 'label' => 'Gas Certificate'],
        'electric_certificate' => ['icon' => 'bolt', 'label' => 'Electric Certificate'],
        'landlord_certificate' => ['icon' => 'home', 'label' => 'Landlord Certificate'],
        'building_insurance' => ['icon' => 'shield-alt', 'label' => 'Building Insurance'],
    ] as $type => $details)
                                            <div class="col-md-6 col-lg-3 mb-3">
                                                <div class="card border h-100">
                                                    <div class="card-body p-3">
                                                        <input type="hidden" name="document_types[]"
                                                            value="{{ $type }}">

                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <label class="mb-0 text-primary">
                                                                <i class="fas fa-{{ $details['icon'] }} mr-1"></i>
                                                                {{ $details['label'] }}
                                                            </label>
                                                            @if ($doc = $package->documents->where('type', $type)->first())
                                                                <span class="badge badge-success">
                                                                    <i class="fas fa-check-circle"></i>
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                id="{{ $type }}_{{ $package->id }}"
                                                                name="documents[{{ $type }}]">
                                                            <label class="custom-file-label"
                                                                for="{{ $type }}_{{ $package->id }}">
                                                                Choose file
                                                            </label>
                                                        </div>

                                                        @if ($doc)
                                                            <div class="mt-2">
                                                                <a href="{{ Storage::url($doc->path) }}"
                                                                    class="btn btn-sm btn-outline-primary btn-block"
                                                                    target="_blank">
                                                                    <i class="fas fa-eye mr-1"></i>View
                                                                </a>
                                                                <small class="d-block text-muted text-center mt-1">
                                                                    Expires:
                                                                    {{ $doc->expires_at ? \Carbon\Carbon::parse($doc->expires_at)->format('d/m/Y') : 'N/A' }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </form>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <p class="mb-0">No packages found.</p>
                                <small class="text-muted">Add packages to manage their documents.</small>
                            </div>
                        @endforelse
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Handle custom file inputs
                            const handleFileInput = () => {
                                const fileInputs = document.querySelectorAll('.custom-file-input');
                                fileInputs.forEach(input => {
                                    input.addEventListener('change', function() {
                                        const fileName = this.value.split('\\').pop();
                                        const label = this.nextElementSibling;
                                        if (label) {
                                            label.classList.add('selected');
                                            label.innerHTML = fileName || 'Choose file';
                                        }
                                    });
                                });
                            };

                            handleFileInput();
                        });
                    </script>

                    <!-- Partner Details Tab -->
                    <div class="tab-pane fade" id="details" role="tabpanel">
                        <div class="row">
                            <!-- Bank Details -->
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title border-bottom pb-2">
                                            <i class="fas fa-university mr-2"></i>Bank Details
                                        </h6>
                                        <form wire:submit.prevent="updateBankDetails">
                                            <div class="form-group">
                                                <label>Account Holder Name</label>
                                                <input type="text" class="form-control"
                                                    wire:model="bankDetail.name">
                                                @error('bankDetail.name')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Sort Code</label>
                                                <input type="text" class="form-control"
                                                    wire:model="bankDetail.sort_code">
                                                @error('bankDetail.sort_code')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Account Number</label>
                                                <input type="text" class="form-control"
                                                    wire:model="bankDetail.account">
                                                @error('bankDetail.account')
                                                    <span class="text-danger small">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save mr-1"></i>Update Bank Details
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Agreement Details -->
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title border-bottom pb-2">
                                            <i class="fas fa-file-contract mr-2"></i>Agreement Details
                                        </h6>
                                        <form wire:submit.prevent="updateAgreement">
                                            <div class="form-group">
                                                <label>Agreement Type</label>
                                                <input type="text" class="form-control"
                                                    wire:model="agreementDetail.agreement_type">
                                            </div>
                                            <div class="form-group">
                                                <label>Duration</label>
                                                <input type="text" class="form-control"
                                                    wire:model="agreementDetail.duration">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Amount</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">৳</span>
                                                            </div>
                                                            <input type="number" step="0.01" class="form-control"
                                                                wire:model="agreementDetail.amount">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Deposit</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">৳</span>
                                                            </div>
                                                            <input type="number" step="0.01" class="form-control"
                                                                wire:model="agreementDetail.deposit">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save mr-1"></i>Update Agreement
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!-- Delete Payment Confirmation Modal -->
    @if ($showDeletePaymentModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-trash text-danger mr-2"></i>
                            Confirm Delete Payment
                        </h5>
                        <button type="button" class="close" wire:click="$set('showDeletePaymentModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Are you sure you want to delete this payment? This action cannot be undone.
                        </div>
                        <p class="mb-0 text-muted small">
                            <i class="fas fa-info-circle mr-1"></i>
                            Deleting this payment will update the booking's payment status and remaining balance.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary text-white"
                            wire:click="$set('showDeletePaymentModal', false)">
                            <i class="fas fa-times mr-1"></i>
                            Cancel
                        </button>
                        <button type="button" class="btn btn-danger text-white" wire:click="deletePayment">
                            <i class="fas fa-trash mr-1"></i>
                            Delete Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .table> :not(caption)>*>* {
            padding: 0.75rem;
        }

        .badge {
            font-weight: 500;
        }

        .progress {
            border-radius: 0.5rem;
        }

        .alert {
            border-left: 4px solid;
        }

        .alert-success {
            border-left-color: #198754;
        }

        .alert-danger {
            border-left-color: #dc3545;
        }

        .alert-info {
            border-left-color: #0dcaf0;
        }

        i {
            margin-right: 10px;
        }

        .btn-group .btn {
            min-width: 100px;
            transition: all 0.2s ease;
        }

        .btn-group .btn:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }

        .fa-spin {
            animation-duration: 1s;
        }

        .alert {
            margin-top: 1rem;
            margin-bottom: 0;
        }

        /* Card enhancements */
        .booking-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .booking-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Status badges */
        .status-badge {
            padding: 0.5rem 1rem;
            font-weight: 500;
            border-radius: 50px;
        }

        /* Custom table styles */
        .table-minimal {
            font-size: 0.9rem;
        }

        .table-minimal td,
        .table-minimal th {
            padding: 0.75rem;
            vertical-align: middle;
        }

        /* Payment history row styles */
        .payment-row {
            transition: all 0.2s ease;
        }

        .payment-row:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .payment-row.paid {
            background-color: rgba(40, 167, 69, 0.1);
        }

        /* Custom button styles */
        .btn-action {
            border-radius: 50px;
            padding: 0.375rem 1rem;
            font-weight: 500;
        }

        /* Section headers */
        .section-header {
            font-size: 1rem;
            color: #495057;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        /* Summary card styles */
        .summary-item {
            padding: 1rem;
            border-radius: 0.5rem;
            background: #f8f9fa;
            margin-bottom: 0.5rem;
        }

        /* Invoice action buttons */
        .invoice-actions .btn {
            margin-left: 0.5rem;
        }

        /* Alert enhancements */
        .alert-modern {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }


        .modal {
            padding-right: 17px;
        }

        .modal-content {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
        }

        .btn-group .btn:not(:last-child) {
            margin-right: 0.25rem;
        }

        .badge {
            padding: 0.5em 0.75em;
        }

        .payment-row.paid {
            background-color: rgba(40, 167, 69, 0.05);
        }
    </style>

</div>
