<?php

namespace App\Livewire\User;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Maintain;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Room;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckoutComponent extends Component
{
    public $package;
    public $packageId;
    public $fromDate;
    public $toDate;
    public $name;
    public $email;
    public $phone;
    public $selectedRoom;
    public $roomDetails;
    public $totalNights;
    public $totalAmount = 0;
    public $bookingPrice = 0;
    public $amenitiesTotal = 0;
    public $maintainsTotal = 0;
    public $selectedMaintains = [];
    public $selectedAmenities = [];
    public $paymentOption = 'booking_only';
    public $paymentMethod = '';
    public $bankTransferReference;
    public $showPaymentModal = false;
    public $bankDetails;
    public $bikash;
    public $nogod;
    public $rocket;

    public $priceType;
    public $priceBreakdown;

    protected $rules = [
        'paymentMethod' => 'required|in:cash,bikash,nogod,rocket',
        'bankTransferReference' => 'required',
    ];

    public function mount()
    {
        $data = session()->get('checkout_data');
        if (!$data) {
            return redirect()->route('home')->with('error', 'No checkout data found.');
        }
        $this->packageId = $data['packageId'];
        $this->fromDate = $data['fromDate'];
        $this->toDate = $data['toDate'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->selectedRoom = Room::with('roomPrices')->find($data['selectedRoom']);
        //loop
        $maintainTotal = 0;
        if (!empty($data['selectedMaintains'])) {
            foreach ($data['selectedMaintains'] as $maintain) {
                $break = explode('|', $maintain);
                $maintainMul[0][] = [$break[1], $break[2], $break[0]];
                $maintainTotal += $break[2];
            }
        }
        $maintainMul['sum'] = $maintainTotal;
        $amenityTotal = 0;
        foreach ($data['selectedAmenities'] as $amen) {
            $break = explode('|', $amen);
            $amenityMul[0][] = [$break[1], $break[2], $break[0]];
            $amenityTotal += $break[2];
        }
        $amenityMul['sum'] = $amenityTotal;
        $this->selectedMaintains = $maintainMul ?? [];
        $this->selectedAmenities = $amenityMul ?? [];
        $this->totalNights = Carbon::parse($this->fromDate)->diffInDays(Carbon::parse($this->toDate));
        $this->package = Package::findOrFail($this->packageId);
        $this->bankDetails = "Netsoftuk Solution A/C 17855008 S/C 04-06-05";
        $this->bikash = "Bikash Merchant No. 01111111111";
        $this->nogod = "Nogod Merchant No. 022222222222";
        $this->rocket = "Rocket Merchant No. 022222222222";

        if ($this->selectedRoom) {
            $this->priceBreakdown = $this->calculatePriceBreakdown();

            // Calculate total amount from breakdown
            $this->totalAmount = collect($this->priceBreakdown)->sum('total');

            // Get booking price based on price type
            $this->bookingPrice = $this->selectedRoom->roomPrices
                ->firstWhere('type', $this->priceType)->booking_price ?? 0;
        }

        $this->amenitiesTotal = collect($this->selectedAmenities)->sum('price');
        $this->maintainsTotal = collect($this->selectedMaintains)->sum('price');
    }


    private function determinePriceType()
    {
        $availableTypes = collect($this->selectedRoom->roomPrices)->pluck('type')->unique();
        $totalDays = $this->totalNights;
        $daysInMonth = Carbon::parse($this->fromDate)->daysInMonth;

        // If only one type available, use that regardless of duration
        if ($availableTypes->count() === 1) {
            return $availableTypes->first();
        }

        // If multiple types available
        if ($availableTypes->contains('Month')) {
            if ($totalDays >= $daysInMonth) {
                return 'Month';
            }
        }

        if ($availableTypes->contains('Week')) {
            if ($totalDays < $daysInMonth && $totalDays >= 7) {
                return 'Week';
            }
        }

        if ($availableTypes->contains('Day')) {
            if ($totalDays < 7) {
                return 'Day';
            }
        }

        // Default fallbacks based on available types
        if ($availableTypes->contains('Month')) return 'Month';
        if ($availableTypes->contains('Week')) return 'Week';
        if ($availableTypes->contains('Day')) return 'Day';

        throw new \Exception("No valid price type found");
    }

    private function calculatePriceBreakdown()
    {
        $this->priceType = $this->determinePriceType();
        $totalDays = $this->totalNights;
        $breakdown = [];

        switch ($this->priceType) {
            case 'Month':
                $monthlyPrice = $this->selectedRoom->roomPrices->firstWhere('type', 'Month');
                $price = $monthlyPrice->discount_price ?? $monthlyPrice->fixed_price;
                $months = ceil($totalDays / Carbon::parse($this->fromDate)->daysInMonth);

                for ($i = 0; $i < $months; $i++) {
                    $breakdown[] = [
                        'type' => 'Month',
                        'quantity' => 1,
                        'price' => $price,
                        'total' => $price,
                        'description' => Carbon::parse($this->fromDate)->addMonths($i)->format('F Y')
                    ];
                }
                break;

            case 'Week':
                $weeklyPrice = $this->selectedRoom->roomPrices->firstWhere('type', 'Week');
                $price = $weeklyPrice->discount_price ?? $weeklyPrice->fixed_price;
                $weeks = ceil($totalDays / 7);

                $breakdown[] = [
                    'type' => 'Week',
                    'quantity' => $weeks,
                    'price' => $price,
                    'total' => $price * $weeks,
                    'description' => "{$weeks} " . ($weeks > 1 ? 'Weeks' : 'Week')
                ];
                break;

            case 'Day':
                $dailyPrice = $this->selectedRoom->roomPrices->firstWhere('type', 'Day');
                $price = $dailyPrice->discount_price ?? $dailyPrice->fixed_price;

                $breakdown[] = [
                    'type' => 'Day',
                    'quantity' => $totalDays,
                    'price' => $price,
                    'total' => $price * $totalDays,
                    'description' => "{$totalDays} " . ($totalDays > 1 ? 'Days' : 'Day')
                ];
                break;
        }

        return $breakdown;
    }

    public function calculateTotalAmount()
    {
        $total = $this->totalAmount + $this->selectedAmenities['sum'] + $this->selectedMaintains['sum'];
        return $this->paymentOption === 'full' ? $total + $this->bookingPrice : $this->bookingPrice + $this->selectedAmenities['sum'] + $this->selectedMaintains['sum'];
    }

    public function submitBooking()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->showPaymentModal = true;
    }

    public function proceedPayment()
    {
        $this->validate();
        $paymentAmount = $this->calculateTotalAmount();

        DB::beginTransaction();
        try {
            // Step 1: Create the booking
            $booking = $this->createBooking($paymentAmount);

            // Step 2: Create associated services (amenities and maintains)
            $this->createBookingServices($booking);

            // Step 3: Handle payment
            if ($this->paymentMethod === 'card') {
                DB::commit(); // Commit prior to redirect for Stripe
                return $this->handleStripePayment($booking, $paymentAmount);
            }

            // For non-card payments (bank transfer or cash)
            $this->createPayment($booking, $paymentAmount);

            DB::commit();

            // Step 4: Clear session and redirect
            session()->forget('checkout_data');
            session()->flash('success', 'Booking submitted successfully!');
            return redirect()->route('booking.complete', $booking->id);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error processing payment: ' . $e->getMessage());
            return redirect()->route('checkout');
        }
    }

    private function createBooking($paymentAmount)
    {
        // Get room with prices
        $room = Room::with('roomPrices')->find($this->selectedRoom->id);
        if (!$room) {
            throw new \Exception('Room not found');
        }

        // First determine price type based on available rates
        $this->priceType = $this->determinePriceType();

        // Calculate price breakdown based on determined type
        $this->priceBreakdown = $this->calculatePriceBreakdown();

        // Get booking price for the determined price type
        $roomPrice = $room->roomPrices->firstWhere('type', $this->priceType);
        if (!$roomPrice) {
            throw new \Exception("Price not found for type: {$this->priceType}");
        }

        $this->bookingPrice = $roomPrice->booking_price;
        $this->totalAmount = collect($this->priceBreakdown)->sum('total');

        // Add booking fee milestone
        $bookingFeeMilestone = [
            'type' => 'Booking Fee',
            'quantity' => 1,
            'price' => $this->bookingPrice,
            'total' => $this->bookingPrice,
            'description' => 'Initial Booking Fee'
        ];

        // Combine all milestones

        $fullBreakdown = array_merge([$bookingFeeMilestone], $this->priceBreakdown);

        // Calculate total amount including amenities and maintains
        $totalWithExtras = $this->totalAmount + $this->selectedAmenities['sum'] + $this->selectedMaintains['sum'];

        // Create booking record
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'package_id' => $this->packageId,
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
            'room_ids' => json_encode([$this->selectedRoom->id]),
            'number_of_days' => $this->totalNights,
            'price_type' => $this->priceType,
            'price' => $totalWithExtras,
            'booking_price' => $this->bookingPrice,
            'payment_option' => $this->paymentOption,
            'total_amount' => $paymentAmount,
            'payment_status' => 'pending',
            'total_milestones' => count($this->priceBreakdown),
            'milestone_amount' => $this->totalAmount / count($this->priceBreakdown),
            'milestone_breakdown' => $fullBreakdown,
            'auto_renewal' => false,
            'renewal_period_days' => 30
        ]);

        // Create room prices record
        $this->createBookingRoomPrices($booking);

        // Create milestone payments
        $this->createMilestonePayments($booking, $fullBreakdown);

        return $booking;
    }

    private function createBookingRoomPrices($booking)
    {
        $roomPrice = $this->selectedRoom->roomPrices->firstWhere('type', $this->priceType);

        if ($roomPrice) {
            DB::table('booking_room_prices')->insert([
                'booking_id' => $booking->id,
                'room_id' => $this->selectedRoom->id,
                'price_type' => $this->priceType,
                'fixed_price' => $roomPrice->fixed_price,
                'discount_price' => $roomPrice->discount_price,
                'booking_price' => $roomPrice->booking_price,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    private function createMilestonePayments($booking, $milestones)
    {
        $startDate = Carbon::parse($booking->from_date);

        foreach ($milestones as $index => $milestone) {
            if ($milestone['type'] === 'Booking Fee') {
                DB::table('booking_payments')->insert([
                    'booking_id' => $booking->id,
                    'milestone_type' => 'Booking Fee',
                    'milestone_number' => 0,
                    'due_date' => now(),
                    'amount' => $milestone['total'],
                    'payment_status' => 'pending',
                    'payment_method' => $this->paymentMethod,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                continue;
            }

            // For other payment types, start from check-in date
            $dueDate = match ($milestone['type']) {
                'Month' => $startDate->copy()->addMonths($index - 1),  // First payment on check-in date
                'Week' => $startDate->copy()->addWeeks($index - 1),    // First payment on check-in date
                'Day' => $startDate->copy()->addDays($index - 1),      // First payment on check-in date
                default => $startDate->copy()
            };

            DB::table('booking_payments')->insert([
                'booking_id' => $booking->id,
                'milestone_type' => $milestone['type'],
                'milestone_number' => $index,
                'due_date' => $dueDate,
                'amount' => $milestone['total'],
                'payment_status' => 'pending',
                'payment_method' => $this->paymentMethod,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }



    private function createPayment($booking, $paymentAmount)
    {
        // Create the main payment record
        $payInof = [
            'booking_id' => $booking->id,
            'payment_method' => $this->paymentMethod,
            'amount' => $paymentAmount,
            'status' => 'pending',
            'transaction_id' => $this->bankTransferReference ?? null,
            'paymentOption' => $this->paymentOption,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $payment = Payment::create($payInof);

        // If this is the booking payment, update the first milestone payment
        if ($this->paymentOption === 'booking_only') {
            DB::table('booking_payments')
                ->where('booking_id', $booking->id)
                ->where('milestone_number', 0)
                ->update([
                    'payment_status' => 'pending',
                    'payment_method' => $this->paymentMethod,
                    'transaction_reference' => $this->bankTransferReference ?? null,
                    'updated_at' => now()
                ]);
        } else {
            DB::table('booking_payments')
                ->where('booking_id', $booking->id)
                ->update([
                    'payment_status' => 'pending',
                    'payment_method' => $this->paymentMethod,
                    'transaction_reference' => $this->bankTransferReference ?? null,
                    'updated_at' => now()
                ]);
        }

        return $payment;
    }


    private function updatePaymentStatus($booking, $paymentAmount)
    {
        if ($this->paymentOption === 'booking_only') {
            // Update only the first milestone
            DB::table('booking_payments')
                ->where('booking_id', $booking->id)
                ->where('milestone_number', 1)
                ->update([
                    'payment_status' => 'paid',
                    'paid_at' => now()
                ]);

            $booking->update(['payment_status' => 'partially_paid']);
        } else {
            // Update all milestones
            DB::table('booking_payments')
                ->where('booking_id', $booking->id)
                ->update([
                    'payment_status' => 'paid',
                    'paid_at' => now()
                ]);

            $booking->update(['payment_status' => 'paid']);
        }
    }

    private function createBookingServices($booking)
    {
        if (!empty($this->selectedAmenities['sum'])) {
            foreach ($this->selectedAmenities[0] as $amenity) {
                $amenityData = [
                    'booking_id' => $booking->id,
                    'amenity_id' => $amenity[2],
                    'price' => $amenity[1],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                DB::table('booking_amenities')->insert($amenityData);
            }
        }

        if (!empty($this->selectedMaintains['sum'])) {
            foreach ($this->selectedMaintains[0] as $maintain) {
                $maintainData = [
                    'booking_id' => $booking->id,
                    'maintain_id' => $maintain[2],
                    'price' => $maintain[1],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                DB::table('booking_maintains')->insert($maintainData);
            }
        }
    }


    protected function handleStripePayment($booking, $paymentAmount)
    {
        try {
            Stripe::setApiKey(config('stripe.stripe_sk'));

            $description = $this->paymentOption === 'booking_only'
                ? "Booking Payment for {$this->package->name}"
                : "Full Payment for {$this->package->name}";

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'gbp',
                            'product_data' => [
                                'name' => $description,
                                'description' => "Booking from {$this->fromDate} to {$this->toDate}",
                            ],
                            'unit_amount' => (int) ($paymentAmount * 100),
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('stripe.success', ['booking' => $booking->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel', ['booking' => $booking->id]),
                'metadata' => [
                    'booking_id' => $booking->id,
                    'payment_option' => $this->paymentOption,
                ]
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Stripe payment error: ' . $e->getMessage());
            return redirect()->route('checkout');
        }
    }

    public function render()
    {
        return view('livewire.user.checkout-component', [
            'paymentAmount' => $this->calculateTotalAmount()
        ])->layout('layouts.guest');
    }
}
