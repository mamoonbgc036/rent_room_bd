<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Package;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AdminBookingComponent extends Component
{
    public $package;
    public $packageId;
    public $selectedUser;
    public $searchQuery = '';
    public $users = [];
    public $fromDate;
    public $toDate;
    public $name;
    public $email;
    public $phone;
    public $selectedRooms = [];
    public $availableRooms = [];
    public $showCalendar = false;
    public $dateRange = '';
    public $selectedRoom;
    public $disabledDates = [];
    public $selectedDates = [];
    public $calendarView = false;
    public $roomPrices = [];
    public $totalAmount = 0;
    public $bookingPrice = 0;
    public $paymentOption = 'booking_only';
    public $paymentMethod = 'bank_transfer';
    public $bankTransferReference;
    public $priceType;
    public $packages;
    public $priceBreakdown = [];
    public $selectedRoomDetails;
    public $milestone_breakdown;
    public $total_milestones;
    public $milestone_amount;
    public $isSubmitting = false;
    protected $listeners = ['dates-selected' => 'calculateTotals'];

    protected $rules = [
        'selectedUser' => 'required',
        'selectedRoom' => 'required',
        'fromDate' => 'required|date',
        'toDate' => 'required|date|after:fromDate',
        'phone' => 'required|string|max:15',
        'paymentMethod' => 'required|in:cash,card,bank_transfer',
        'bankTransferReference' => 'required_if:paymentMethod,bank_transfer'
    ];


    public function mount()
    {
        $this->packages = Package::with(['rooms.roomPrices'])->get();
        $this->isSubmitting = false;

    }

    private function calculateRoomTotal()
    {
        if (!$this->selectedRoom || !$this->fromDate || !$this->toDate) {
            return 0;
        }

        $room = Room::with('roomPrices')->find($this->selectedRoom);
        if (!$room) return 0;

        // Get price breakdown
        $priceBreakdownData = $this->getPriceBreakdown();

        // Set price type from the first milestone
        if (!empty($priceBreakdownData['breakdown'])) {
            $this->priceType = $priceBreakdownData['breakdown'][0]['type'];
        }

        $this->priceBreakdown = $priceBreakdownData['breakdown'];
        $this->totalAmount = $priceBreakdownData['total'];

        // Get the booking price
        $this->bookingPrice = $room->roomPrices->first()?->booking_price ?? 0;

        return $this->totalAmount;
    }


    private function createBookingRoomPrices($booking)
    {
        $room = Room::with('roomPrices')->find($this->selectedRoom);
        $priceType = $this->priceType ?? 'Day';

        // Get the applicable price for the selected type
        $roomPrice = $room->roomPrices->first(function ($price) use ($priceType) {
            return $price->type === $priceType;
        });

        if ($roomPrice) {
            DB::table('booking_room_prices')->insert([
                'booking_id' => $booking->id,
                'room_id' => $this->selectedRoom,
                'price_type' => $priceType,
                'fixed_price' => $roomPrice->fixed_price,
                'discount_price' => $roomPrice->discount_price,
                'booking_price' => $roomPrice->booking_price,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }


    public function createBooking()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            // Get price breakdown and determine price type
            $priceBreakdownData = $this->getPriceBreakdown();
            if (!empty($priceBreakdownData['breakdown'])) {
                // Set the price type from the first milestone
                $this->priceType = $priceBreakdownData['breakdown'][0]['type'];
            } else {
                throw new \Exception("Unable to determine price type");
            }

            // Calculate payment amount
            $paymentAmount = $this->paymentOption === 'full'
                ? $this->totalAmount + $this->bookingPrice
                : $this->bookingPrice;

            // Create the booking
            $booking = Booking::create([
                'user_id' => $this->selectedUser->id,
                'package_id' => $this->packageId,
                'from_date' => $this->fromDate,
                'to_date' => $this->toDate,
                'room_ids' => json_encode([$this->selectedRoom]),
                'number_of_days' => Carbon::parse($this->fromDate)->diffInDays(Carbon::parse($this->toDate)),
                'price_type' => $this->priceType,
                'price' => $this->totalAmount,
                'booking_price' => $this->bookingPrice,
                'payment_option' => $this->paymentOption,
                'total_amount' => $paymentAmount,
                'payment_status' => 'pending',
                'total_milestones' => count($priceBreakdownData['breakdown']),
                'milestone_amount' => collect($priceBreakdownData['breakdown'])->first()['total'] ?? 0,
                'milestone_breakdown' => $priceBreakdownData['breakdown']
            ]);

            // Create booking room prices
            $this->createBookingRoomPrices($booking);

            // Create milestone payments
            $this->createMilestonePayments($booking);

            // Process payment based on method
            $this->processPayment($booking, $paymentAmount);

            DB::commit();

            // Clear session and redirect
            session()->forget('checkout_data');
            session()->flash('success', 'Booking created and payment processed successfully!');
            return redirect()->route('admin.bookings.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error creating booking: ' . $e->getMessage());
        }
    }

    private function processPayment($booking, $paymentAmount)
    {
        switch ($this->paymentMethod) {
            case 'card':
                // Implement card payment processing (similar to Stripe in CheckoutComponent)
                $paymentResponse = $this->handleStripePayment($booking, $paymentAmount);
                if ($paymentResponse['status'] !== 'success') {
                    throw new \Exception("Card payment failed: " . $paymentResponse['message']);
                }

                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_method' => 'card',
                    'amount' => $paymentAmount,
                    'status' => 'completed',
                    'transaction_id' => $paymentResponse['transaction_id'],
                    'payment_option' => $this->paymentOption,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                break;

            case 'bank_transfer':
                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_method' => 'bank_transfer',
                    'amount' => $paymentAmount,
                    'status' => 'pending',
                    'transaction_id' => $this->bankTransferReference ?? null,
                    'payment_option' => $this->paymentOption,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                break;

            case 'cash':
            default:
                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_method' => 'cash',
                    'amount' => $paymentAmount,
                    'status' => 'pending',
                    'payment_option' => $this->paymentOption,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                break;
        }
    }

    private function createMilestonePayments($booking)
    {
        $startDate = Carbon::parse($booking->from_date);
        $bookingFee = $booking->booking_price;

        // Insert booking fee payment with immediate due date
        DB::table('booking_payments')->insert([
            'booking_id' => $booking->id,
            'milestone_type' => 'Booking Fee',
            'milestone_number' => 0,
            'due_date' => now(),
            'amount' => $bookingFee,
            'payment_status' => 'pending',
            'payment_method' => $this->paymentMethod,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create milestone payments for remaining amount
        foreach ($this->priceBreakdown as $index => $milestone) {
            // Calculate due date based on milestone type
            $dueDate = match ($milestone['type']) {
                'Month' => $startDate->copy()->addMonths($index),  // First payment on check-in date, then same date next months
                'Week' => $startDate->copy()->addWeeks($index),    // First payment on check-in date, then same day next weeks
                'Day' => $startDate->copy()->addDays($index)       // First payment on check-in date, then next days
            };

            DB::table('booking_payments')->insert([
                'booking_id' => $booking->id,
                'milestone_type' => $milestone['type'],
                'milestone_number' => $index + 1,
                'due_date' => $dueDate,
                'amount' => $milestone['total'],
                'payment_status' => 'pending',
                'payment_method' => $this->paymentMethod,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    private function calculateMilestoneDueDate(Carbon $startDate, $milestoneType, $index)
    {
        // Get the day of month from check-in date for monthly payments
        $checkInDay = $startDate->day;

        switch ($milestoneType) {
            case 'Month':
                // For monthly payments, keep the same day of month as check-in
                return $startDate->copy()->addMonths($index)->setDay($checkInDay);

            case 'Week':
                // For weekly payments, set due date to the start of next week from check-in
                $weekStart = $startDate->copy()->addWeeks($index - 1); // Subtract 1 because $index starts from 1
                return $weekStart;

            case 'Day':
                // For daily payments, simply add days
                return $startDate->copy()->addDays($index);

            default:
                return $startDate->copy()->addDays($index);
        }
    }

    public function calculateTotals()
    {
        if ($this->selectedRoom && $this->fromDate && $this->toDate) {
            $this->calculateRoomTotal();
        }
    }
    public function updatedSearchQuery()
    {
        if (strlen($this->searchQuery) >= 2) {
            $this->users = User::where('name', 'like', '%' . $this->searchQuery . '%')
                ->orWhere('email', 'like', '%' . $this->searchQuery . '%')
                ->take(5)
                ->get();
        }
    }

    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->searchQuery = '';
        $this->users = [];
    }

    public function selectRoom($roomId)
    {
        $this->selectedRoom = $roomId;
        $this->calendarView = true;

        $room = Room::with('roomPrices')->find($roomId);
        $this->roomPrices[$roomId] = $room->roomPrices->groupBy('type')->map(function ($prices) {
            return $prices->first();
        })->toArray();

        // Set packageId for the selected room
        $this->packageId = $room->package_id;

        // Reset dates when room changes
        $this->fromDate = null;
        $this->toDate = null;
        $this->totalAmount = 0;
        $this->bookingPrice = 0;

        $this->disabledDates = $this->fetchDisabledDates();
    }

    public function selectDates($dates)
    {
        $this->fromDate = $dates['start'];
        $this->toDate = $dates['end'];

        if ($this->validateDateRange()) {
            $this->calculateRoomTotal();

            if ($this->selectedUser && empty($this->phone)) {
                $this->phone = $this->selectedUser->phone;
            }

            // Update available rooms based on new date range
            $this->fetchAvailableRooms();

            // Dispatch event to re-render component
            $this->dispatch('dates-selected');
        }
    }


    public function updatedPhone()
    {
        $this->validateOnly('phone');
    }


    public function validateDateRange()
    {
        if (!$this->fromDate || !$this->toDate) return false;

        try {
            $this->validateOnly('fromDate');
            $this->validateOnly('toDate');

            $from = Carbon::parse($this->fromDate);
            $to = Carbon::parse($this->toDate);

            // Check for maximum stay duration (if needed)
            if ($to->diffInDays($from) > 30) {
                $this->addError('dateRange', 'Booking duration cannot exceed 30 days.');
                return false;
            }

            // Check for disabled dates
            foreach ($this->disabledDates as $disabledDate) {
                $checkDate = Carbon::parse($disabledDate);
                if ($checkDate->between($from, $to)) {
                    $this->addError('dateRange', 'Some dates in your selection are already booked.');
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            $this->addError('dateRange', 'Invalid date selection.');
            return false;
        }
    }

    public function fetchDisabledDates()
    {
        if (!$this->packageId || !$this->selectedRoom) {
            return [];
        }

        try {
            return Booking::where('package_id', $this->packageId)
                // Filter out cancelled bookings
                ->whereNotIn('payment_status', ['cancelled', 'refunded'])
                ->whereRaw('JSON_CONTAINS(REPLACE(REPLACE(room_ids, "\\\", ""), "\"", ""), ?)', ["[$this->selectedRoom]"])
                ->get()
                ->flatMap(function ($booking) {
                    $bookedDates = [];
                    $from = Carbon::parse($booking->from_date);
                    $to = Carbon::parse($booking->to_date);

                    while ($from->lte($to)) {
                        $bookedDates[] = $from->format('Y-m-d');
                        $from->addDay();
                    }
                    return $bookedDates;
                })
                ->unique()
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Error fetching disabled dates: ' . $e->getMessage());
            return [];
        }
    }

    public function fetchAvailableRooms()
    {
        if (!$this->fromDate || !$this->toDate || !$this->packageId) {
            $this->reset(['availableRooms', 'disabledDates']);
            return [];
        }

        $package = Package::find($this->packageId);
        if (!$package) {
            $this->addError('package', 'Invalid package selected.');
            return [];
        }

        $bookedRoomIds = Booking::where('package_id', $this->packageId)
            ->where(function ($query) {
                $query->whereBetween('from_date', [$this->fromDate, $this->toDate])
                    ->orWhereBetween('to_date', [$this->fromDate, $this->toDate])
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('from_date', '<=', $this->fromDate)
                            ->where('to_date', '>=', $this->toDate);
                    });
            })
            ->get()
            ->flatMap(function ($booking) {
                return json_decode($booking->room_ids, true) ?: [];
            })
            ->unique()
            ->toArray();

        $allRooms = Room::where('package_id', $this->packageId)->get();
        $this->availableRooms = $allRooms->filter(function ($room) use ($bookedRoomIds) {
            return !in_array($room->id, $bookedRoomIds);
        });

        return $this->availableRooms;
    }

    public function getPriceBreakdown()
    {
        if (!$this->selectedRoom || !$this->fromDate || !$this->toDate) {
            return null;
        }

        $room = Room::with('roomPrices')->find($this->selectedRoom);
        if (!$room) return null;

        $startDate = Carbon::parse($this->fromDate);
        $endDate = Carbon::parse($this->toDate);
        $totalDays = $startDate->diffInDays($endDate);

        $priceBreakdown = $this->determineOptimalPriceType($room, $this->fromDate, $this->toDate);
        $prices = $room->roomPrices->keyBy('type');

        $breakdown = [];
        $total = 0;

        // Add monthly breakdown with month names
        if ($priceBreakdown['Month'] > 0 && isset($prices['Month'])) {
            $monthlyPrice = $prices['Month']->discount_price ?? $prices['Month']->fixed_price;
            $startDate = Carbon::parse($this->fromDate);

            for ($i = 0; $i < $priceBreakdown['Month']; $i++) {
                $currentMonth = $startDate->copy()->addMonths($i)->format('F Y');
                $breakdown[] = [
                    'type' => 'Month',
                    'quantity' => 1,
                    'price' => $monthlyPrice,
                    'total' => $monthlyPrice,
                    'description' => $currentMonth,
                    'note' => $i === $priceBreakdown['Month'] - 1 && $endDate->day > 1 ?
                        '(Includes partial month)' : ''
                ];
            }
            $total += $monthlyPrice * $priceBreakdown['Month'];
        }

        // Add weekly breakdown
        if ($priceBreakdown['Week'] > 0 && isset($prices['Week'])) {
            $weeklyPrice = $prices['Week']->discount_price ?? $prices['Week']->fixed_price;
            $weeklyTotal = $weeklyPrice * $priceBreakdown['Week'];
            $total += $weeklyTotal;

            $description = "{$priceBreakdown['Week']} " . ($priceBreakdown['Week'] > 1 ? 'Weeks' : 'Week');
            if ($totalDays % 7 > 0) {
                $description .= " (Includes " . ($totalDays % 7) . " extra days)";
            }

            $breakdown[] = [
                'type' => 'Week',
                'quantity' => $priceBreakdown['Week'],
                'price' => $weeklyPrice,
                'total' => $weeklyTotal,
                'description' => $description
            ];
        }

        // Add daily breakdown
        if ($priceBreakdown['Day'] > 0 && isset($prices['Day'])) {
            $dailyPrice = $prices['Day']->discount_price ?? $prices['Day']->fixed_price;
            $dailyTotal = $dailyPrice * $priceBreakdown['Day'];
            $total += $dailyTotal;
            $breakdown[] = [
                'type' => 'Day',
                'quantity' => $priceBreakdown['Day'],
                'price' => $dailyPrice,
                'total' => $dailyTotal,
                'description' => "{$priceBreakdown['Day']} " .
                    ($priceBreakdown['Day'] > 1 ? 'Days' : 'Day')
            ];
        }

        return [
            'breakdown' => $breakdown,
            'total' => round($total)
        ];
    }

    private function determineOptimalPriceType($room, $startDate, $endDate)
    {
        $availableTypes = collect($room->roomPrices)->pluck('type')->unique();
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        $totalDays = $startDate->diffInDays($endDate);

        $priceBreakdown = [
            'Month' => 0,
            'Week' => 0,
            'Day' => 0
        ];

        // First check if duration is over a month (28 days)
        if ($totalDays >= 28) {
            if (!$availableTypes->contains('Month')) {
                throw new \Exception("Monthly pricing is required for bookings of 28 days or more.");
            }
            return $this->calculateMonthlyBreakdown($startDate, $endDate);
        }

        // Then check if duration is 7 days or more
        if ($totalDays >= 7) {
            if (!$availableTypes->contains('Week')) {
                if ($availableTypes->contains('Month')) {
                    return $this->calculateMonthlyBreakdown($startDate, $endDate);
                }
                throw new \Exception("Weekly pricing is required for bookings of 7 days or more.");
            }
            return $this->calculateWeeklyBreakdown($totalDays);
        }

        // Finally, check for daily bookings (less than 7 days)
        if (!$availableTypes->contains('Day')) {
            if ($availableTypes->contains('Week')) {
                return $this->calculateWeeklyBreakdown($totalDays);
            } elseif ($availableTypes->contains('Month')) {
                return $this->calculateMonthlyBreakdown($startDate, $endDate);
            }
            throw new \Exception("Daily pricing is required for bookings less than 7 days.");
        }

        return [
            'Month' => 0,
            'Week' => 0,
            'Day' => $totalDays
        ];
    }

    private function calculateMonthlyBreakdown($startDate, $endDate)
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        // Get total days
        $totalDays = $startDate->diffInDays($endDate);

        // Get days in first month
        $daysInFirstMonth = $startDate->daysInMonth;

        // If total days is less than or equal to days in first month, return 1 month
        if ($totalDays <= $daysInFirstMonth) {
            return [
                'Month' => 1,
                'Week' => 0,
                'Day' => 0
            ];
        }

        // If days exceed first month, calculate how many months are needed
        // Example: 35 days with 31 days in month = 2 months (31 + 4 days = 2 months)
        $extraDays = $totalDays - $daysInFirstMonth;
        if ($extraDays > 0) {
            // Add 1 for first month plus any additional months needed
            $months = 1 + ceil($extraDays / $endDate->daysInMonth);
        } else {
            $months = 1;
        }

        return [
            'Month' => (int)$months,
            'Week' => 0,
            'Day' => 0
        ];
    }

    private function calculateWeeklyBreakdown($totalDays)
    {
        $fullWeeks = ceil($totalDays / 7);

        return [
            'Month' => 0,
            'Week' => $fullWeeks,
            'Day' => 0
        ];
    }



    public function render()
    {
        // Add this temporarily to debug
        \Log::info('Current State:', [
            'selectedRoom' => $this->selectedRoom,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'totalAmount' => $this->totalAmount,
            'bookingPrice' => $this->bookingPrice,
        ]);

        return view('livewire.admin.admin-booking-component');
    }
}
