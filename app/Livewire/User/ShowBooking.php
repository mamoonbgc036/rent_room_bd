<?php

namespace App\Livewire\User;

use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Livewire\Component;
use Stripe\StripeClient;

class ShowBooking extends Component
{
    public $booking;
    public $payments;
    public $paymentsDue;
    public $dueBill;
    public $showPaymentModal = false;
    public $showRenewalModal = false;
    public $paymentMethod;
    public $bankTransferReference;
    public $bankDetails = 'Netsoftuk Solution A/C 17855008 S/C 04-06-05';
    public $newFromDate;
    public $newToDate;
    public $paymentPercentage;
    public $currentMilestone;
    public $hasOverdue = false;
    public $selectedMilestoneId = null;
    public $selectedMilestoneAmount = null;
    public bool $autoRenewal = false;
    public int $renewalPeriodDays = 30;
    public bool $showAutoRenewalModal = false;
    public bool $canManageAutoRenewal = false;



    protected $rules = [
        'bankTransferReference' => 'required_if:paymentMethod,bank_transfer',
        'newFromDate' => 'required|date',
        'newToDate' => 'required|date|after_or_equal:newFromDate',
        'autoRenewal' => 'boolean',
        'renewalPeriodDays' => 'integer|min:1|max:365'
    ];



    public function closeAutoRenewalModal()
    {
        // Reset to the original state if the modal is closed without saving
        $this->autoRenewal = (bool) $this->booking->auto_renewal;
        $this->renewalPeriodDays = (int) ($this->booking->renewal_period_days ?? 30);
        $this->dispatch('closeModal', 'autoRenewalModal');
    }

    public function canEnableAutoRenewal(): bool
    {
        // If auto-renewal is already enabled, always return true to allow managing it
        if ($this->booking->auto_renewal) {
            return true;
        }

        // For new auto-renewals, check conditions
        $toDate = Carbon::parse($this->booking->to_date);

        return $this->booking
            && !in_array($this->booking->status, ['cancelled', 'finished', 'rejected'])
            && $this->booking->from_date
            && $this->booking->to_date
            && $toDate->isFuture();
    }

    public function mount($id)
    {
        $this->booking = Booking::with([
            'package.instructions',
            'payments',
            'bookingPayments'
        ])->findOrFail($id);

        $this->autoRenewal = (bool) $this->booking->auto_renewal;
        $this->renewalPeriodDays = (int) ($this->booking->renewal_period_days ?? 30);

        // Set canManageAutoRenewal
        $this->updateCanManageAutoRenewal();

        $this->payments = $this->booking->payments ?? collect();

        $totalPrice = (float) $this->booking->price + (float) $this->booking->booking_price;
        $totalPaid = $this->payments->where('status', 'Paid')->sum('amount');
        $this->dueBill = $totalPrice - $totalPaid;
        $this->paymentPercentage = $totalPrice > 0 ? ($totalPaid / $totalPrice * 100) : 0;
    }

    private function updateCanManageAutoRenewal(): void
    {
        if (!$this->booking) {
            $this->canManageAutoRenewal = false;
            return;
        }

        // Allow managing if already enabled
        if ($this->booking->auto_renewal) {
            $this->canManageAutoRenewal = true;
            return;
        }

        // For new auto-renewals, check conditions
        $toDate = Carbon::parse($this->booking->to_date);

        $this->canManageAutoRenewal =
            $this->booking->price_type === 'Month' && // Only for monthly packages
            !in_array($this->booking->status, ['cancelled', 'finished', 'rejected']) &&
            $this->booking->from_date &&
            $this->booking->to_date &&
            $toDate->isFuture();
    }

    public function toggleAutoRenewal()
    {
        if (!$this->canManageAutoRenewal) {
            $this->dispatch('notify', [
                'message' => 'Cannot manage auto-renewal for this booking.',
                'type' => 'error'
            ]);
            return;
        }

        try {
            $newState = !$this->booking->auto_renewal;

            // Only allow auto-renewal for monthly packages
            if ($newState && $this->booking->price_type !== 'Month') {
                $this->dispatch('notify', [
                    'message' => 'Auto-renewal is only available for monthly packages.',
                    'type' => 'error'
                ]);
                return;
            }

            // Calculate next renewal date (7 days before package end)
            $nextRenewalDate = $newState
                ? Carbon::parse($this->booking->to_date)->subDays(7)
                : null;

            // Update booking
            $this->booking->update([
                'auto_renewal' => $newState,
                'renewal_period_days' => 30, // Fixed to 30 days for monthly packages
                'next_renewal_date' => $nextRenewalDate,
                'renewal_status' => $newState ? 'pending' : null
            ]);

            // Update local property
            $this->autoRenewal = $newState;

            // Update canManageAutoRenewal
            $this->updateCanManageAutoRenewal();

            // Close modal and show success message
            $this->dispatch('closeModal', 'autoRenewalModal');
            $this->dispatch('notify', [
                'message' => $newState
                    ? "Auto-renewal enabled. Package will be automatically extended by 1 month."
                    : 'Auto-renewal disabled successfully.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            // Reset local state and show error
            $this->autoRenewal = $this->booking->auto_renewal;
            $this->updateCanManageAutoRenewal();
            $this->dispatch('notify', [
                'message' => 'Failed to update auto-renewal settings: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function updateAutoRenewalPeriod()
    {
        $this->validate([
            'renewalPeriodDays' => 'required|integer|min:1|max:365'
        ]);

        try {
            if (!$this->booking->auto_renewal) {
                throw new \Exception('Auto-renewal must be enabled first.');
            }

            // Calculate next renewal date based on current to_date
            $nextRenewalDate = Carbon::parse($this->booking->to_date)->subDays(7);

            $this->booking->update([
                'renewal_period_days' => $this->renewalPeriodDays,
                'next_renewal_date' => $nextRenewalDate
            ]);

            $this->dispatch('notify', [
                'message' => "Renewal period updated to {$this->renewalPeriodDays} days.",
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    protected function getAutoRenewalBlockReason(): ?string
    {
        if (!$this->booking) {
            return 'Booking not found.';
        }

        if ($this->booking->status === 'cancelled') {
            return 'Auto-renewal is not available for cancelled bookings.';
        }

        if ($this->booking->status === 'finished') {
            return 'Auto-renewal is not available for finished bookings.';
        }

        if (!$this->booking->from_date || !$this->booking->to_date) {
            return 'Booking dates must be properly set before enabling auto-renewal.';
        }

        $toDate = Carbon::parse($this->booking->to_date);
        if ($toDate->isPast()) {
            return 'Cannot enable auto-renewal for expired bookings.';
        }

        return null;
    }

    public function showAutoRenewalSettings()
    {
        // If already enabled, always allow access to settings
        if ($this->booking->auto_renewal) {
            $this->dispatch('openModal', 'autoRenewalModal');
            return;
        }

        // For new enablement, check if it's allowed
        if (!$this->canEnableAutoRenewal()) {
            $this->dispatch('notify', [
                'message' => $this->getAutoRenewalBlockReason() ?? 'Auto-renewal cannot be enabled.',
                'type' => 'error'
            ]);
            return;
        }

        $this->dispatch('openModal', 'autoRenewalModal');
    }

    public function render()
    {
        // Pass the computed property to the view explicitly
        return view('livewire.user.show-booking', [
            'canEnableAutoRenewal' => $this->canEnableAutoRenewal()
        ]);
    }

    public function showPaymentM($milestoneId = null, $amount = null)
    {
        try {
            // Calculate current milestone and due bill
            $this->calculatePayments();

            // Set selected milestone details
            if ($milestoneId) {
                $this->selectedMilestoneId = $milestoneId;
                $this->selectedMilestoneAmount = $amount;

                // Get the specific milestone
                $this->currentMilestone = $this->booking->bookingPayments
                    ->where('id', $milestoneId)
                    ->first();
            } else {
                $this->selectedMilestoneId = $this->currentMilestone?->id;
                $this->selectedMilestoneAmount = $this->dueBill;
            }

            if (!$this->selectedMilestoneId && !$this->currentMilestone) {
                session()->flash('error', 'No pending payments found.');
                return;
            }

            // Check for overdue payments
            $this->hasOverdue = $this->booking->bookingPayments
                ->where('payment_status', '!=', 'paid')
                ->where('due_date', '<', now())
                ->isNotEmpty();
            // Dispatch modal open event
            $this->dispatch('openModal', 'paymentModal');
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading payment details: ' . $e->getMessage());
        }
    }

    public function calculatePayments()
    {
        try {
            $totalPrice = (float) $this->booking->price + (float) $this->booking->booking_price;
            $totalPaid = $this->booking->payments->where('status', 'completed')->sum('amount');
            $this->dueBill = $totalPrice - $totalPaid;
            $this->paymentPercentage = $totalPrice > 0 ? ($totalPaid / $totalPrice * 100) : 0;

            // Get next unpaid milestone
            $this->currentMilestone = $this->booking->bookingPayments
                ->where('payment_status', '!=', 'paid')
                ->sortBy('due_date')
                ->first();
        } catch (\Exception $e) {
            session()->flash('error', 'Error calculating payments: ' . $e->getMessage());
        }
    }

    public function proceedPayment()
    {
        // Validate inputs
        $this->validate([
            'paymentMethod' => 'required',
            'bankTransferReference' => 'required',
        ]);
        if (!$this->currentMilestone) {
            throw new \Exception('No pending milestone found.');
        }
        // Handle payment based on method
        if ($this->paymentMethod === 'card') {
            return $this->handleStripePayment();
        } else {
            return $this->handleBankTransfer();
        }
    }

    protected function determinePaymentType()
    {
        return $this->currentMilestone->milestone_type === 'Booking Fee' ? 'booking' : 'rent';
    }

    protected function handleBankTransfer()
    {
        try {
            // Begin transaction
            \DB::beginTransaction();

            $paymentType = $this->determinePaymentType();

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $this->booking->id,
                'payment_method' => $this->paymentMethod,
                'payment_type' => $paymentType,  // Add payment type
                'amount' => $this->currentMilestone->amount,
                'transaction_id' => $this->bankTransferReference,
                'booking_payment_id' => $this->currentMilestone->id,
                'status' => 'pending',
            ]);

            // Update milestone status
            $this->currentMilestone->update([
                'status' => 'pending_payment'
            ]);

            // Update booking status
            $this->booking->update([
                'payment_status' => 'pending'
            ]);

            \DB::commit();

            session()->flash('success', 'Payment transfer initiated. Please contact admin with transfer details.');
            $this->showPaymentModal = false;
            $this->resetForm();
            return redirect()->route('bookings.show', ['id' => $this->booking->id]);
        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Failed to process bank transfer: ' . $e->getMessage());
            return null;
        }
    }

    protected function handleStripePayment()
    {
        try {
            $stripe = new StripeClient(config('stripe.stripe_sk'));

            // Begin transaction
            \DB::beginTransaction();

            $paymentType = $this->determinePaymentType();

            // Prepare payment description
            $description = $paymentType === 'booking'
                ? "Booking Fee Payment"
                : "Rent Payment for " . $this->currentMilestone->milestone_number;

            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'gbp',
                            'product_data' => [
                                'name' => $paymentType === 'booking'
                                    ? "Booking Payment #" . $this->booking->id
                                    : "Rent Payment #" . $this->booking->id,
                                'description' => $description,
                            ],
                            'unit_amount' => (int) ($this->currentMilestone->amount * 100),
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}&booking_id=' . $this->booking->id,
                'cancel_url' => route('payment.cancel') . '?booking_id=' . $this->booking->id,
                'metadata' => [
                    'booking_id' => $this->booking->id,
                    'booking_payment_id' => $this->currentMilestone->id,
                    'payment_type' => $paymentType,
                    'amount' => $this->currentMilestone->amount
                ],
            ]);

            // Create initial payment record
            Payment::create([
                'booking_id' => $this->booking->id,
                'payment_method' => 'card',
                'payment_type' => $paymentType,  // Add payment type
                'amount' => $this->currentMilestone->amount,
                'status' => 'pending',
                'booking_payment_id' => $this->currentMilestone->id,
                'stripe_session_id' => $session->id
            ]);

            // Update milestone status
            $this->currentMilestone->update([
                'status' => 'pending'
            ]);

            \DB::commit();

            return redirect($session->url);
        } catch (\Exception $e) {
            \DB::rollBack();
            session()->flash('error', 'Stripe payment failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    protected function handlePaymentSuccess($payment)
    {
        if ($this->currentMilestone) {
            $this->currentMilestone->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'payment_method' => $payment->payment_method,
                'transaction_reference' => $payment->transaction_id
            ]);

            $this->calculatePayments();

            // Check if all milestones are paid
            if (!$this->currentMilestone) {
                $this->booking->update(['payment_status' => 'paid']);
            }
        }
    }

    private function resetForm()
    {
        $this->paymentMethod = null;
        $this->bankTransferReference = '';
        $this->resetValidation();
    }
    public function cancelBooking()
    {
        // Update booking details
        $this->booking->update([
            'from_date' => null,
            'to_date' => null,
            'status' => 'cancelled',
        ]);

        // Optionally, you may want to remove or handle associated payments here
        Payment::where('booking_id', $this->booking->id)->delete();

        session()->flash('success', 'Booking cancelled successfully!');

        return redirect()->route('user.bookings.index');
    }


    public function renewPackage()
    {
        // Validate the new dates
        $this->validate([
            'newFromDate' => 'required|date',
            'newToDate' => 'required|date|after_or_equal:newFromDate',
        ]);

        // Calculate the number of days for the new booking
        $number_of_days = $this->calculateNumberOfDays($this->newFromDate, $this->newToDate);

        // Create a new booking with updated dates
        $newBooking = $this->booking->replicate(); // Duplicate the booking
        $newBooking->from_date = $this->newFromDate;
        $newBooking->to_date = $this->newToDate;
        $newBooking->number_of_days = $number_of_days;

        // Calculate the price per day from the original booking
        $originalNumberOfDays = $this->booking->number_of_days;
        $singleDayPrice = $this->booking->price / $originalNumberOfDays;

        // Calculate the new price based on the new number of days
        $newBooking->price = $singleDayPrice * $number_of_days;

        // Set the payment status to pending
        $newBooking->payment_status = 'pending';

        // Save the new booking
        $newBooking->save();

        // Duplicate the rooms for the new booking
        if ($this->booking->rooms) { // Ensure the rooms relationship is not null
            foreach ($this->booking->rooms as $room) {
                $newBooking->rooms()->create([
                    'room_id' => $room->room_id,
                    'room_type' => $room->room_type,
                    'price' => $room->price, // Assuming the Room model has these fields
                    // Add any other fields as needed
                ]);
            }
        }

        $this->booking->payment_status = 'finished'; // Assuming the status should be updated
        $this->booking->save();

        flash()->success('Package renewed successfully!');

        return redirect()->route('bookings.show', ['id' => $newBooking->id]);
    }


    public function finishBooking()
    {
        $this->booking->update([
            'payment_status' => 'finished',
            'status' => 'finished', // Assuming you have a `status` field as well
        ]);

        session()->flash('success', 'Booking finished successfully!');

        return redirect()->route('bookings.show', ['id' => $this->booking->id]);
    }




    protected function calculateNumberOfDays($fromDate, $toDate)
    {
        $from = \Carbon\Carbon::parse($fromDate);
        $to = \Carbon\Carbon::parse($toDate);

        // Ensure the 'to' date is always after the 'from' date
        return $from->diffInDays($to) + 1;
    }



    public function showRenewModal()
    {
        // Fetch the latest booking details
        $this->booking = Booking::findOrFail($this->booking->id);

        // Compute the default dates
        $toDate = \Carbon\Carbon::parse($this->booking->to_date);
        $this->newFromDate = $toDate->addDay()->format('Y-m-d'); // Default to the day after the current toDate
        $this->newToDate = $toDate->addDay(2)->format('Y-m-d'); // Default to two days after the new from date

        // Show the renewal modal
        $this->showRenewalModal = true;
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        try {
            $stripe = new StripeClient(config('stripe.stripe_sk'));
            $checkout_session = $stripe->checkout->sessions->retrieve($sessionId);

            if ($checkout_session->payment_status === 'paid') {
                $bookingId = $checkout_session->metadata->booking_id;
                $booking = Booking::findOrFail($bookingId);
                $booking->payment_status = 'paid';
                $booking->save();

                flash()->success('Payment successful! Due bill paid.');
            } else {
                return redirect()->route('booking.cancel')->with('error', 'Payment unsuccessful.');
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return redirect()->route('booking.cancel')->with('error', 'Stripe Error: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('booking.details', $this->booking->id)->with('error', 'Payment canceled.');
    }
}
