<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Footer;

class CustomerSupportComponent extends Component
{
    public $footer;

    public function mount()
    {
        $this->footer = Footer::first();

    }
    public function render()
    {
        return view('livewire.user.customer-support-component');
    }
}
