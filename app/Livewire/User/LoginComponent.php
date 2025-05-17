<?php

namespace App\Livewire\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LoginComponent extends Component
{
    public $phone;
    public $password;
    public $remember;

    public function login()
    {
        $credentials = $this->validate([
            'phone' => 'required|numeric|digits:11',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $this->remember)) {
            session()->flash('message', 'Login successful.');
            $this->dispatch('auth-success');
            $this->dispatch('hideModal');
        } else {
            throw ValidationException::withMessages([
                'phone' => __('auth.failed'),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.user.login-component');
    }
}
