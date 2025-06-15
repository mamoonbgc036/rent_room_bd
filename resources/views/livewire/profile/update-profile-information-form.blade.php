<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public ?string $phone = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user?->name ?? '';
        $this->email = $user?->email ?? '';
        $this->phone = $user?->phone ?? null;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'], // Validate phone field
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>


<section>
    <header>
        <h2 class="h4 text-dark">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-muted">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit.prevent="updateProfileInformation" class="mt-4">
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input wire:model="name" id="name" name="name" type="text" class="form-control" required autofocus autocomplete="name">
            @error('name') <div class="text-danger mt-2">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input wire:model="email" id="email" name="email" type="email" class="form-control" required autocomplete="username">
            @error('email') <div class="text-danger mt-2">{{ $message }}</div> @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
            <div class="mt-3">
                <p class="text-muted">
                    {{ __('Your email address is unverified.') }}
                    <button wire:click.prevent="sendVerification" class="btn btn-link p-0">{{ __('Click here to re-send the verification email.') }}</button>
                </p>

                @if (session('status') === 'verification-link-sent')
                <p class="mt-2 text-success">
                    {{ __('A new verification link has been sent to your email address.') }}
                </p>
                @endif
            </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('Phone') }}</label>
            <input wire:model="phone" id="phone" name="phone" type="text" class="form-control" autocomplete="tel">
            @error('phone') <div class="text-danger mt-2">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            <div wire:loading wire:target="updateProfileInformation" class="text-muted">{{ __('Saving...') }}</div>

            <div wire:loading.remove wire:target="updateProfileInformation">
                @if (session('status') === 'profile-updated')
                <div class="text-success">
                    {{ __('Saved.') }}
                </div>
                @endif
            </div>
        </div>
    </form>
</section>