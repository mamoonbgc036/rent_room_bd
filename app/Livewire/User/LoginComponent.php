<?php

namespace App\Livewire\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LoginComponent extends Component
{
    public $email;
    public $password;
    public $remember;

    public function login()
    {
        $credentials = $this->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $this->remember)) {
            session()->flash('message', 'Login successful.');
            $this->dispatch('auth-success');
            $this->dispatch('hideModal');
        } else {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.user.login-component');
    }
}
