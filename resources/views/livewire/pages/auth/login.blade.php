<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        // Regenerate session to prevent fixation attacks
        Session::regenerate();

        // Redirect to intended URL or fallback to the dashboard
        $this->redirectIntended(default: route('dashboard'), navigate: true);
    }
}; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>
                <div class="card-body">
                    <form wire:submit.prevent="login">
                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Phone') }}</label>
                            <input wire:model="form.phone" id="phone" class="form-control" type="phone" name="phone" required autofocus autocomplete="username">
                            <x-input-error :messages="$errors->get('form.phone')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input wire:model="form.password" id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
                            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                        </div>

                        <!-- Remember Me -->
                        <div class="form-check mb-3">
                            <input wire:model="form.remember" id="remember" type="checkbox" class="form-check-input" name="remember">
                            <label for="remember" class="form-check-label">{{ __('Remember me') }}</label>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}" wire:navigate>
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif

                            <button type="submit" class="btn btn-primary">
                                {{ __('Log in') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>