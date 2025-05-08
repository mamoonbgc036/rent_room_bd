<?php

namespace App\Livewire\Admin;

use App\Models\PaymentLink;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\BookingPayment;
use Livewire\Component;
use Stripe\StripeClient;

class PaymentComponent extends Component
{
    public $uniqueId;
    public $paymentLink;
    public $selectedPaymentMethod = "BankTransfer";
    public $bankReference;
    public $showModal = false;

    public function mount($uniqueId)
    {
        $this->uniqueId = $uniqueId;
        $this->paymentLink = PaymentLink::with([
            'user',
            'booking',
            'bookingPayment'
        ])->where('unique_id', $this->uniqueId)->firstOrFail();
    }

    protected function determinePaymentType()
    {
        // Use the specific booking payment from the payment link
        $bookingPayment = $this->paymentLink->bookingPayment;

        return $bookingPayment->milestone_type === 'Booking Fee' ? 'booking' : 'rent';
    }

    protected function handleBankTransfer()
    {
        if (empty($this->bankReference)) {
            session()->flash('error', 'Please enter a reference number');
            return;
        }

        try {
            $paymentType = $this->determinePaymentType();

            // Create payment record
            $paymentData = [
                'booking_id' => $this->paymentLink->booking_id,
                'payment_method' => 'bank_transfer',
                'payment_type' => $paymentType,
                'amount' => $this->paymentLink->amount,
                'transaction_id' => $this->bankReference,
                'status' => 'pending',
                'booking_payment_id' => $this->paymentLink->booking_payment_id
            ];

            // Create payment record
            $payment = Payment::create($paymentData);

            // Update payment link status - removed transaction_id
            $this->paymentLink->update([
                'status' => 'pending_bank_transfer'
            ]);

            session()->flash('message', 'Bank transfer initiated. Please contact admin with transfer details.');
            return redirect()->route('payment.page', $this->paymentLink->unique_id);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to process bank transfer: ' . $e->getMessage());
            return null;
        }
    }

    protected function handleStripePayment()
    {
        $stripe = new StripeClient(config('stripe.stripe_sk'));

        try {
            $paymentType = $this->determinePaymentType();
            $bookingPayment = $this->paymentLink->bookingPayment;

            $itemName = $paymentType === 'booking'
                ? "Booking Payment #" . $this->paymentLink->booking->id
                : "Rent Payment #" . $this->paymentLink->booking->id;

            $session = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'gbp',
                        'product_data' => [
                            'name' => $itemName,
                            'description' => "Payment for milestone: " . $bookingPayment->milestone_type,
                        ],
                        'unit_amount' => (int)($this->paymentLink->amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}&payment_link=' . $this->uniqueId,
                'cancel_url' => route('payment.cancel') . '?payment_link=' . $this->uniqueId,
                'metadata' => [
                    'payment_link_id' => $this->paymentLink->id,
                    'payment_type' => $paymentType,
                    'booking_id' => $this->paymentLink->booking_id,
                    'booking_payment_id' => $this->paymentLink->booking_payment_id,
                    'amount' => $this->paymentLink->amount
                ],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Payment failed: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function handlePaymentMethod()
    {
        if ($this->selectedPaymentMethod === 'Card') {
            return $this->handleStripePayment();
        } elseif ($this->selectedPaymentMethod === 'BankTransfer') {
            return $this->handleBankTransfer();
        }
    }

    public function showPaymentModal()
    {
        $this->showModal = true;
    }

    public function render()
    {
        return view('livewire.admin.payment-component', [
            'paymentLink' => $this->paymentLink
        ])->layout('layouts.guest');
    }
}
