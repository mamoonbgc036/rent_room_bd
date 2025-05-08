<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function stripe() {
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        $stripe = new \Stripe\StripeClient(config('stripe.stripe_sk'));

        $response = $stripe->checkout->sessions->create([
        'line_items' => [
            [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => ['name' => 'T-shirt'],
                'unit_amount' => 2000,
            ],
            'quantity' => 1,
            ],
        ],
        'mode' => 'payment',
        'success_url' => route('sucsess').'?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => route('cancel'),
        ]);

        if(isset($response->id) && $response->id != ''){
            return redirect($response->url);
        } else {
            return redirect()->route('cancel');
        }
    }

    public function sucsess(Request $request) {

    }
    public function cancel() {

    }
}
