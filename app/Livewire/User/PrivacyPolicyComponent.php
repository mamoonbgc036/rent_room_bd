<?php

namespace App\Livewire\User;

use App\Models\PrivacyPolicy;
use Livewire\Component;

class PrivacyPolicyComponent extends Component
{
    public $ppTitle;
    public $ppContent;

    public function mount()
    {
        // Load data from model or repository if needed
        $policy = PrivacyPolicy::first(); // Example assuming one privacy policy entry
        if ($policy) {
            $this->ppTitle = $policy->title;
            $this->ppContent = $policy->content;
        }
    }
    public function render()
    {
        return view('livewire.user.privacy-policy-component')->layout('layouts.guest');
    }
}
