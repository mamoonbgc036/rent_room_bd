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

class AdminBookingEditComponent extends Component
{
    public $booking;
    public $bookingId;
    public $fromDate;
    public $ffromDate;
    public $toDate;
    public $originalToDate;
    public $selectedRoom;
    public $disabledDates = [];
    public $totalAmount = 0;
    public $bookingPrice = 0;
    public $paymentOption = 'booking_only';
    public $paymentMethod = 'bank_transfer';
    public $bankTransferReference;
    public $priceType;
    public $priceBreakdown = [];
    public $useCustomBookingFee = false;
    public $customBookingFee = 0;
    public $isExtendingBooking = false;

    public $useCustomRentAmount = false;
    public $customRentAmount = 0;
    public $originalRentAmount = 0;

    protected $rules = [
        'customBookingFee' => 'required_if:useCustomBookingFee,true|numeric|min:0',
        'customRentAmount' => 'required_if:useCustomRentAmount,true|numeric|min:0',
        'toDate' => 'nullable|date|after:fromDate'
    ];

    protected $casts = [
        'bookingPrice' => 'float',
        'customBookingFee' => 'float',
        'totalAmount' => 'float',
    ];

    public function mount(Booking $booking)
    {
        $this->booking = $booking->load(['user', 'package', 'payments']);
        $this->bookingId = $booking->id;
        $this->fromDate = $booking->from_date;
        $this->ffromDate = $booking->from_date;
        $this->toDate = $booking->to_date;
        $this->originalToDate = $booking->to_date;
        $this->selectedRoom = json_decode($booking->room_ids)[0] ?? null;
        $this->paymentOption = $booking->payment_option;
        $this->totalAmount = $booking->price;
        $this->bookingPrice = $booking->booking_price;
        $this->customBookingFee = $this->bookingPrice;
        $this->priceType = $booking->price_type;

        $this->fetchDisabledDates();
        $this->originalRentAmount = $booking->price;
        $this->customRentAmount = $booking->price;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatedUseCustomBookingFee($value)
    {
        if (!$value) {
            $room = Room::with('roomPrices')->find($this->selectedRoom);
            $this->bookingPrice = $room->roomPrices->first()?->booking_price ?? 0;
            $this->updateBookingFeePayment($this->bookingPrice);
        }
    }

    public function updatedCustomBookingFee($value)
    {
        if ($this->useCustomBookingFee) {
            $this->bookingPrice = (float) $value;
            $this->updateBookingFeePayment($this->bookingPrice);
        }
    }

    private function updateBookingFeePayment($newBookingFee)
    {
        try {
            // Find the booking fee payment record
            $bookingFeePayment = DB::table('booking_payments')
                ->where('booking_id', $this->booking->id)
                ->where('milestone_type', 'Booking Fee')
                ->first();

            if ($bookingFeePayment) {
                // Update existing booking fee payment
                DB::table('booking_payments')
                    ->where('booking_id', $this->booking->id)
                    ->where('milestone_type', 'Booking Fee')
                    ->update([
                        'amount' => $newBookingFee,
                        'updated_at' => now()
                    ]);
            } else {
                // Create new booking fee payment if doesn't exist
                DB::table('booking_payments')->insert([
                    'booking_id' => $this->booking->id,
                    'milestone_type' => 'Booking Fee',
                    'milestone_number' => 0,
                    'due_date' => now(),
                    'amount' => $newBookingFee,
                    'payment_status' => 'pending',
                    'payment_method' => $this->paymentMethod,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating booking fee payment: ' . $e->getMessage());
        }
    }



    public function updatedUseCustomRentAmount($value)
    {
        if (!$value) {
            $this->customRentAmount = $this->originalRentAmount;
            $this->updatePendingMilestones($this->originalRentAmount);
        }
    }

    public function updatedCustomRentAmount($value)
    {
        if ($this->useCustomRentAmount) {
            $this->updatePendingMilestones((float) $value);
        }
    }

    private function updatePendingMilestones($newTotalAmount)
    {
        try {
            DB::beginTransaction();

            // Get all pending milestone payments
            $pendingMilestones = DB::table('booking_payments')
                ->where('booking_id', $this->booking->id)
                ->where('payment_status', 'pending')
                ->where('milestone_type', '!=', 'Booking Fee')
                ->orderBy('milestone_number')
                ->get();

            if ($pendingMilestones->isEmpty()) {
                throw new \Exception('No pending milestones found to update');
            }

            // Calculate the difference in amount
            $paidAmount = DB::table('booking_payments')
                ->where('booking_id', $this->booking->id)
                ->where('payment_status', '!=', 'pending')
                ->where('milestone_type', '!=', 'Booking Fee')
                ->sum('amount');

            $remainingAmount = $newTotalAmount - $paidAmount;

            // Calculate new amount per milestone
            $milestonesCount = $pendingMilestones->count();
            $amountPerMilestone = round($remainingAmount / $milestonesCount, 2);
            $lastMilestoneExtra = $remainingAmount - ($amountPerMilestone * ($milestonesCount - 1));

            // Update each pending milestone
            foreach ($pendingMilestones as $index => $milestone) {
                $newAmount = $index === $milestonesCount - 1 ? $lastMilestoneExtra : $amountPerMilestone;

                DB::table('booking_payments')
                    ->where('id', $milestone->id)
                    ->update([
                        'amount' => $newAmount,
                        'updated_at' => now()
                    ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating milestone payments: ' . $e->getMessage());
        }
    }

    public function updateBooking()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $updateData = [
                'booking_price' => $this->bookingPrice
            ];

            // Update total rent amount if custom amount is used
            if ($this->useCustomRentAmount) {
                $updateData['price'] = $this->customRentAmount;
                $updateData['total_amount'] = $this->paymentOption === 'full' ?
                    ($this->customRentAmount + $this->bookingPrice) : $this->bookingPrice;
            }

            // Add extension details if extending booking
            if ($this->isExtendingBooking) {
                // Validate date extension
                if (Carbon::parse($this->toDate)->lt(Carbon::parse($this->originalToDate))) {
                    session()->flash('error', 'You cannot reduce the checkout date.');
                    return;
                }

                // Get price breakdown for the extended period
                $priceBreakdownData = $this->getPriceBreakdown();

                // Calculate total amount including original booking amount
                $newTotalAmount = $this->booking->price + $priceBreakdownData['total'];

                // Calculate number of days from original check-in (from_date) to new checkout date
                $numberOfDays = Carbon::parse($this->booking->from_date)->diffInDays(Carbon::parse($this->toDate));

                $updateData = array_merge($updateData, [
                    'to_date' => $this->toDate,
                    'number_of_days' => $numberOfDays, // Updated to calculate from from_date
                    'price' => $newTotalAmount,
                    'total_milestones' => count($priceBreakdownData['breakdown']),
                    'milestone_amount' => collect($priceBreakdownData['breakdown'])->first()['total'] ?? 0,
                    'milestone_breakdown' => array_merge(
                        $this->booking->milestone_breakdown ?? [],
                        $priceBreakdownData['breakdown']
                    )
                ]);

                // Create new milestone payments
                $this->updateMilestonePayments($priceBreakdownData['breakdown']);
            }

            // Update the booking
            $this->booking->update($updateData);

            DB::commit();
            session()->flash('success', 'Booking details updated successfully!');
            return redirect()->route('admin.bookings.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating booking: ' . $e->getMessage());
        }
    }

    public function fetchDisabledDates()
    {
        if (!$this->selectedRoom) {
            return [];
        }

        try {
            $room = Room::find($this->selectedRoom);
            if (!$room)
                return [];

            return Booking::where('package_id', $room->package_id)
                ->whereNotIn('payment_status', ['cancelled', 'refunded'])
                ->where('id', '!=', $this->bookingId) // Exclude current booking
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

    public function selectDates($dates)
    {
        $this->isExtendingBooking = true;
        $this->fromDate = $dates['start'];
        $this->toDate = $dates['end'];

        if ($this->validateDateRange()) {
            $this->calculateExtensionTotals();
        }
    }

    public function calculateExtensionTotals()
    {
        if ($this->selectedRoom && $this->fromDate && $this->toDate) {
            $priceBreakdownData = $this->getPriceBreakdown();

            if (!empty($priceBreakdownData['breakdown'])) {
                $this->priceType = $priceBreakdownData['breakdown'][0]['type'];
            }

            $this->priceBreakdown = $priceBreakdownData['breakdown'];
            $this->totalAmount = $priceBreakdownData['total'];
        }
    }

    public function validateDateRange()
    {
        if (!$this->fromDate || !$this->toDate)
            return false;

        $from = Carbon::parse($this->fromDate);
        $to = Carbon::parse($this->toDate);

        // Check if new checkout date is before original
        if ($to->lt(Carbon::parse($this->originalToDate))) {
            $this->addError('dateRange', 'New checkout date cannot be earlier than the original checkout date.');
            return false;
        }

        // Check for disabled dates in the extension period
        $startCheck = Carbon::parse($this->originalToDate);
        while ($startCheck->lte($to)) {
            if (in_array($startCheck->format('Y-m-d'), $this->disabledDates)) {
                $this->addError('dateRange', 'Some dates in your extension period are already booked.');
                return false;
            }
            $startCheck->addDay();
        }

        return true;
    }

    public function getPriceBreakdown()
    {
        if (!$this->selectedRoom || !$this->fromDate || !$this->toDate) {
            return ['breakdown' => [], 'total' => 0];
        }

        $room = Room::with('roomPrices')->find($this->selectedRoom);
        if (!$room)
            return ['breakdown' => [], 'total' => 0];

        // Calculate only for the extension period
        $startDate = Carbon::parse($this->originalToDate);
        $endDate = Carbon::parse($this->toDate);

        // If no extension, return empty breakdown
        if ($endDate->lte($startDate)) {
            return ['breakdown' => [], 'total' => 0];
        }

        $totalDays = $startDate->diffInDays($endDate);
        $priceBreakdown = $this->determineOptimalPriceType($room, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
        $prices = $room->roomPrices->keyBy('type');

        $breakdown = [];
        $total = 0;

        // Add monthly breakdown
        if ($priceBreakdown['Month'] > 0 && isset($prices['Month'])) {
            $monthlyPrice = $prices['Month']->discount_price ?? $prices['Month']->fixed_price;

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
                $total += $monthlyPrice;
            }
        }

        // Add weekly breakdown
        if ($priceBreakdown['Week'] > 0 && isset($prices['Week'])) {
            $weeklyPrice = $prices['Week']->discount_price ?? $prices['Week']->fixed_price;
            $weeklyTotal = $weeklyPrice * $priceBreakdown['Week'];
            $total += $weeklyTotal;

            $description = "Additional {$priceBreakdown['Week']} " .
                ($priceBreakdown['Week'] > 1 ? 'Weeks' : 'Week');

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

            $description = "Additional {$priceBreakdown['Day']} " .
                ($priceBreakdown['Day'] > 1 ? 'Days' : 'Day');

            $breakdown[] = [
                'type' => 'Day',
                'quantity' => $priceBreakdown['Day'],
                'price' => $dailyPrice,
                'total' => $dailyTotal,
                'description' => $description
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

        // Initialize price breakdown
        $priceBreakdown = [
            'Month' => 0,
            'Week' => 0,
            'Day' => 0
        ];

        // For extension period of 28 days or more
        if ($totalDays >= 28 && $availableTypes->contains('Month')) {
            return $this->calculateMonthlyBreakdown($startDate, $endDate);
        }

        // For extension period of 7 days or more
        if ($totalDays >= 7 && $availableTypes->contains('Week')) {
            return $this->calculateWeeklyBreakdown($totalDays);
        }

        // For shorter extensions, use daily rate
        if ($availableTypes->contains('Day')) {
            return [
                'Month' => 0,
                'Week' => 0,
                'Day' => $totalDays
            ];
        }

        // Default to the best available rate type
        if ($availableTypes->contains('Week')) {
            return $this->calculateWeeklyBreakdown($totalDays);
        } elseif ($availableTypes->contains('Month')) {
            return $this->calculateMonthlyBreakdown($startDate, $endDate);
        }

        throw new \Exception("No valid price type found for this duration.");
    }

    private function calculateMonthlyBreakdown($startDate, $endDate)
    {
        $months = ceil($startDate->floatDiffInMonths($endDate));
        return [
            'Month' => $months,
            'Week' => 0,
            'Day' => 0
        ];
    }

    private function calculateWeeklyBreakdown($totalDays)
    {
        $weeks = ceil($totalDays / 7);
        return [
            'Month' => 0,
            'Week' => $weeks,
            'Day' => 0
        ];
    }


    private function updateMilestonePayments($priceBreakdown)
    {
        $startDateBooking = Carbon::parse($this->ffromDate); // Original check-in date
        $startExtension = Carbon::parse($this->originalToDate); // Extension start date

        // Get the existing milestones to find the pattern
        $existingMilestones = DB::table('booking_payments')
            ->where('booking_id', $this->booking->id)
            ->where('milestone_type', '!=', 'Booking Fee')
            ->orderBy('milestone_number')
            ->get();

        // Get the day of month from original check-in for monthly payments
        $checkInDay = $startDateBooking->day;

        // Calculate the next milestone number
        $nextMilestoneNumber = $this->getNextMilestoneNumber();

        foreach ($priceBreakdown as $index => $milestone) {
            // Calculate the due date based on the original check-in date pattern
            $dueDate = match ($milestone['type']) {
                'Month' => $startDateBooking->copy()
                    ->addMonths($nextMilestoneNumber + $index - 1)
                    ->setDay($checkInDay), // Keep same day of month as check-in

                'Week' => $startDateBooking->copy()
                    ->addWeeks($nextMilestoneNumber + $index - 1), // Keep same day of week as check-in

                'Day' => $startDateBooking->copy()
                    ->addDays($nextMilestoneNumber + $index - 1)
            };

            // Only create new payments for dates after the original end date
            if ($dueDate->gt($startExtension)) {
                DB::table('booking_payments')->insert([
                    'booking_id' => $this->booking->id,
                    'milestone_type' => $milestone['type'],
                    'milestone_number' => $nextMilestoneNumber + $index,
                    'due_date' => $dueDate,
                    'amount' => $milestone['total'],
                    'payment_status' => 'pending',
                    'payment_method' => $this->paymentMethod,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    private function getNextMilestoneNumber()
    {
        // Get the highest milestone number for this booking
        $lastMilestone = DB::table('booking_payments')
            ->where('booking_id', $this->booking->id)
            ->where('milestone_number', '>', 0)
            ->orderBy('milestone_number', 'desc')
            ->first();

        return $lastMilestone ? $lastMilestone->milestone_number + 1 : 1;
    }

    public function render()
    {
        return view('livewire.admin.admin-booking-edit-component', [
            'room' => Room::with('roomPrices')->find($this->selectedRoom)
        ]);
    }
}
