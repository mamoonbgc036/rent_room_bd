<div>
    <div class="container-fluid">
        @role('Super Admin')
        <!-- Summary Stats Section -->
        <div class="row">
            <!-- Revenue Filter -->
            <div class="col-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-gray-800">Dashboard Overview</h4>
                    <select wire:model="filterPeriod" class="form-control form-control-sm w-auto">
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                        <option value="all">All Time</option>
                    </select>
                </div>
            </div>

            <!-- Main Stats Cards -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                                <div class="mt-2 text-muted small">Active system users</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Partners
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPartner }}</div>
                                <div class="mt-2 text-muted small">Registered partners</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fas fa-handshake fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Packages</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPackages }}</div>
                                <div class="mt-2 text-muted small">Available listings</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-info text-white rounded-circle p-3">
                                    <i class="fas fa-box fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Bookings
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookings }}</div>
                                <div class="mt-2 text-muted small">Active reservations</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-warning text-white rounded-circle p-3">
                                    <i class="fas fa-calendar-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Stats Section -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Rent Income</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ৳ {{ number_format($monthlyRentTotal, 2) }}</div>
                                <div class="mt-2 text-muted small">{{ $totalCompletedRentPayments }} completed payments
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-home fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Booking Income
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ৳{{ number_format($monthlyBookingTotal, 2) }}</div>
                                <div class="mt-2 text-muted small">{{ $totalCompletedBookingPayments }} completed
                                    payments</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fas fa-bookmark fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ৳{{ number_format($monthlyRentTotal + $monthlyBookingTotal, 2) }}</div>
                                <div class="mt-2 text-muted small">Combined income</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-info text-white rounded-circle p-3">
                                    <i class="fas fa-pound-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Success Rate</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($paymentSuccessRate, 1) }}%
                                </div>
                                <div class="mt-2 text-muted small">Payment completion rate</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-danger text-white rounded-circle p-3">
                                    <i class="fas fa-chart-pie fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Expenses
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">৳0.00</div>
                                <div class="mt-2 text-muted small">All time expenses</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-danger text-white rounded-circle p-3">
                                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Profit
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">৳0.00</div>
                                <div class="mt-2 text-muted small">Net profit calculation</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endrole

        @role('User')
        <!-- User Dashboard -->
        <div class="row">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Packages
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activePackages }}</div>
                                <a href="{{ route('user.bookings.index') }}" class="btn btn-sm btn-primary mt-3">View
                                    Details</a>
                            </div>
                            <div class="col-auto">
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-box-open fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Upcoming
                                    Bookings</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $upcomingBookings }}</div>
                                <a href="" class="btn btn-sm btn-success mt-3">View Schedule</a>
                            </div>
                            <div class="col-auto">
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Spent</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ৳{{ number_format($totalSpent, 2) }}</div>
                                <div class="mt-2 text-muted small">Lifetime spending</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-info text-white rounded-circle p-3">
                                    <p style="font-size: 47px;">৳</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('home') }}" class="btn btn-light btn-block py-4 h-100">
                            <i class="fas fa-search fa-2x mb-2 text-gray-500"></i>
                            <div class="mt-2">Browse Packages</div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('package.list') }}" class="btn btn-light btn-block py-4 h-100">
                            <i class="fas fa-calendar-plus fa-2x mb-2 text-gray-500"></i>
                            <div class="mt-2">New Booking</div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('profile') }}" class="btn btn-light btn-block py-4 h-100">
                            <i class="fas fa-user-edit fa-2x mb-2 text-gray-500"></i>
                            <div class="mt-2">Edit Profile</div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="https://wa.me/+8801715111193" class="btn btn-light btn-block py-4 h-100">
                            <i class="fas fa-question-circle fa-2x mb-2 text-gray-500"></i>
                            <div class="mt-2">Get Support</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                <a href="{{ route('user.bookings.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Package Name</th>
                                <th>Created Date</th>
                                <th>Payment Status</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>{{ $booking->package->name }}</td>
                                <td>{{ $booking->created_at->format('d M Y') }}</td>
                                <td>
                                    <span
                                        class="badge badge-{{ $booking->payment_status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($booking->payment_status) }}
                                    </span>
                                </td>
                                <td>৳{{ number_format($booking->total_amount, 2) }}</td>
                                <td>
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No recent bookings found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endrole


        @role('Partner')
        <div class="row">
            <!-- Partner Stats Section -->
            <div class="col-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-gray-800">Partner Dashboard</h4>
                    <select wire:model="filterPeriod" class="form-control form-control-sm w-auto">
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                        <option value="all">All Time</option>
                    </select>
                </div>
            </div>

            <!-- Partner's Total Users -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">My Users</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $partnerUsers }}</div>
                                <div class="mt-2 text-muted small">Users with active bookings</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Partner's Total Packages -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">My Packages</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $partnerPackages }}</div>
                                <div class="mt-2 text-muted small">Total listed packages</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-info text-white rounded-circle p-3">
                                    <i class="fas fa-box fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Partner's Total Bookings -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Bookings
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $partnerBookings }}</div>
                                <div class="mt-2 text-muted small">All time bookings</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-warning text-white rounded-circle p-3">
                                    <i class="fas fa-calendar-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Partner's Revenue -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Revenue
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ৳{{ number_format($partnerRevenue, 2) }}</div>
                                <div class="mt-2 text-muted small">Combined income</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-success text-white rounded-circle p-3">
                                    <p>৳</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partner Revenue Stats -->
        <div class="row">
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Rental Income
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ৳{{ number_format($partnerRentIncome, 2) }}</div>
                                <div class="mt-2 text-muted small">{{ $partnerRentPayments }} completed payments</div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-home fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Booking Income
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ৳{{ number_format($partnerBookingIncome, 2) }}</div>
                                <div class="mt-2 text-muted small">{{ $partnerBookingPayments }} completed payments
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="bg-success text-white rounded-circle p-3">
                                    <i class="fas fa-bookmark fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endrole
    </div>

    <style>
        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        .border-left-success {
            border-left: 4px solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 4px solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 4px solid #f6c23e !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        .shadow {
            box-shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .15) !important;
        }

        .rounded-circle {
            width: 4rem;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .btn-light {
            background-color: #f8f9fc;
            border-color: #f8f9fc;
            transition: all 0.2s;
        }

        .btn-light:hover {
            background-color: #eaecf4;
            border-color: #eaecf4;
            transform: translateY(-2px);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        .font-weight-bold {
            font-weight: 700 !important;
        }

        .text-xs {
            font-size: 0.7rem;
        }

        .table th {
            font-weight: 600;
            background-color: #f8f9fc;
        }
    </style>

</div>