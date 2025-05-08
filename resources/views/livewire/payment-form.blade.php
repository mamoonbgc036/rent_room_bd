<div>
    @if ($errorMessage)
        <div class="error-message">
            {{ $errorMessage }}
        </div>
    @endif

    <form id="payment-form" wire:submit.prevent="createPaymentIntent">
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" wire:model="name" required>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" wire:model="email" required>
        </div>
        <div>
            <label for="amount">Amount (in cents)</label>
            <input type="number" id="amount" wire:model="amount" required>
        </div>
        <div id="card-element">
            <!-- Stripe Element will be inserted here. -->
        </div>
        <div id="card-errors" role="alert"></div>
        <button type="submit" id="submit-button">Pay</button>
    </form>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            var stripe = Stripe('{{ env('STRIPE_KEY') }}');
            var elements = stripe.elements();
            var card = elements.create('card');
            card.mount('#card-element');

            var form = document.getElementById('payment-form');
            var cardErrors = document.getElementById('card-errors');

            Livewire.on('paymentIntentCreated', function (clientSecret) {
                form.addEventListener('submit', async function (event) {
                    event.preventDefault();

                    const { paymentIntent, error } = await stripe.confirmCardPayment(clientSecret, {
                        payment_method: {
                            card: card,
                            billing_details: {
                                name: document.getElementById('name').value,
                                email: document.getElementById('email').value,
                            }
                        }
                    });

                    if (error) {
                        cardErrors.textContent = error.message;
                        console.error(error.message);
                    } else {
                        if (paymentIntent.status === 'succeeded') {
                            console.log('Payment successful!');
                        }
                    }
                });
            });
        });
    </script>
</div>
