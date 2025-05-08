<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\TermsCondition;

class TermsConditionComponent extends Component
{
    public $terms;

    public function mount()
    {
        $this->terms = TermsCondition::all();
    }

    public function render()
    {
        return view('livewire.user.terms-condition-component')
            ->layout('layouts.guest');
    }
}
