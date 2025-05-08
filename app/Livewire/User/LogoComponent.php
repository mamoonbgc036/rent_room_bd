<?php

namespace App\Livewire\User;

use App\Models\Header;
use Livewire\Component;

class LogoComponent extends Component
{
    public $headerLogo;

    public function mount()
    {
        $header = Header::first();
        $this->headerLogo = $header ? $header->logo : 'default-logo.png'; // Use a default logo if none is found
    }

    public function render()
    {
        return view('livewire.user.logo-component');
    }
}
