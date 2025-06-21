<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class DashboardComponent extends Component
{
    public $totalUsers;
    public $totalPartner;
    public $totalPackages;
    public $totalBookings;
    public $monthlyRevenue;
    public $totalBookingRevenue;

    public $activePackages = 0;
    public $upcomingBookings = 0;
    public $totalSpent = 0;
    public $recentBookings = [];
    public $monthlyRentTotal = 0;
    public $monthlyBookingTotal = 0;
    public $totalRentIncome = 0;
    public $totalBookingIncome = 0;

    public $filterPeriod = 'month';
    public $totalCompletedRentPayments = 0;
    public $totalCompletedBookingPayments = 0;
    public $paymentSuccessRate = 0;
    public $partnerUsers = 0;
    public $partnerPackages = 0;
    public $partnerBookings = 0;
    public $partnerRevenue = 0;
    public $partnerRentIncome = 0;
    public $partnerBookingIncome = 0;
    public $partnerRentPayments = 0;
    public $partnerBookingPayments = 0;

    public function updatedFilterPeriod($value)
    {
        $this->loadRevenueData();
    }

    private function loadRevenueData()
    {
        $dateRange = $this->getDateRange();

        // Get rent payments
        $rentPayments = Payment::where('payment_type', 'rent')
            ->whereIn('status', ['completed', 'paid']);

        // Get booking payments
        $bookingPayments = Payment::where('payment_type', 'booking')
            ->whereIn('status', ['completed', 'paid']);

        if ($this->filterPeriod !== 'all') {
            $rentPayments->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            $bookingPayments->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }

        // Calculate totals
        $this->monthlyRentTotal = $rentPayments->sum('amount');
        $this->monthlyBookingTotal = $bookingPayments->sum('amount');

        // Get payment counts
        $this->totalCompletedRentPayments = $rentPayments->count();
        $this->totalCompletedBookingPayments = $bookingPayments->count();

        // Calculate success rate
        $totalPayments = Payment::when($this->filterPeriod !== 'all', function ($query) use ($dateRange) {
            return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        })->count();

        $completedPayments = Payment::whereIn('status', ['completed', 'paid'])
            ->when($this->filterPeriod !== 'all', function ($query) use ($dateRange) {
                return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })->count();

        $this->paymentSuccessRate = $totalPayments > 0
            ? ($completedPayments / $totalPayments) * 100
            : 0;
    }

    private function getDateRange()
    {
        $now = now();

        return match ($this->filterPeriod) {
            'month' => [
                'start' => $now->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
            'year' => [
                'start' => $now->startOfYear(),
                'end' => $now->copy()->endOfYear(),
            ],
            default => [
                'start' => null,
                'end' => null,
            ],
        };
    }


    public function mount()
    {
        $user = Auth::user();

        // Fetch total number of users
        $this->totalUsers = User::role('User')->count();


        $this->activePackages = $user->bookings()->active()->count();
        $this->upcomingBookings = $user->bookings()->upcoming()->count();
        $this->totalSpent = $user->bookings()->where('payment_status', 'completed')->sum('total_amount');
        $this->totalPackages = Package::where('user_id', $user->id)->count();
        $this->totalBookings = Booking::whereHas('package', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        if ($user->hasRole('Super Admin')) {
            $this->loadRevenueData();
            $this->totalPackages = Package::all()->count();
            $this->totalBookings = Booking::all()->count();
        }

        if ($user->hasRole('Partner')) {
            // Get Partner Stats using assigned_to
            $this->partnerPackages = Package::where('assigned_to', $user->id)->count();

            $this->partnerBookings = Booking::whereHas('package', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })->count();

            $this->partnerUsers = User::whereHas('bookings.package', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })->distinct()->count();

            // Get Partner Revenue
            $partnerPayments = Payment::whereHas('booking.package', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })->whereIn('status', ['completed', 'paid']);

            if ($this->filterPeriod !== 'all') {
                $dateRange = $this->getDateRange();
                $partnerPayments->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            }

            $this->partnerRevenue = $partnerPayments->sum('amount');

            // Get Rent/Booking Income
            $this->partnerRentIncome = $partnerPayments->where('payment_type', 'rent')->sum('amount');
            $this->partnerBookingIncome = $partnerPayments->where('payment_type', 'booking')->sum('amount');

            // Get Payment Counts
            $this->partnerRentPayments = $partnerPayments->where('payment_type', 'rent')->count();
            $this->partnerBookingPayments = $partnerPayments->where('payment_type', 'booking')->count();
        }

        // Common data
        $this->totalPartner = User::role('Partner')->count();
        $this->recentBookings = $user->bookings()
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard.dashboard-component', [
            'totalUsers' => $this->totalUsers,
            'totalPartner' => $this->totalPartner,
            'totalPackages' => $this->totalPackages,
        ]);
    }
}
