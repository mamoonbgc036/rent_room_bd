<?php

namespace App\Livewire;

use Livewire\Component;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentForm extends Component
{
    public $name;
    public $email;
    public $amount;
    public $errorMessage;

    public function mount()
    {
        // Default values
        $this->amount = 1000; // Example amount in cents ($10.00)
    }

    public function createPaymentIntent()
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $this->amount,
                'currency' => 'usd',
                'payment_method_types' => ['card'],
            ]);

            $this->dispatch('paymentIntentCreated', $paymentIntent->client_secret);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.payment-form');
    }
}
