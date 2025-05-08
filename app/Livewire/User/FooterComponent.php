<?php

namespace App\Livewire\User;

use App\Models\Footer;
use App\Models\FooterSectionTwo;
use App\Models\FooterSectionThree;
use App\Models\FooterSectionFour;
use App\Models\SocialLink;
use Livewire\Component;

class FooterComponent extends Component
{
    public $footer;
    public $footerSectionTwos = [];
    public $footerSectionThrees = [];
    public $footerSectionFour;
    public $socialLinks = [];

    public function mount()
    {
        $this->footer = Footer::first();
        if ($this->footer) {
            $this->footerSectionTwos = FooterSectionTwo::where('footer_id', $this->footer->id)->get();
            $this->footerSectionThrees = FooterSectionThree::where('footer_id', $this->footer->id)->get();
            $this->footerSectionFour = FooterSectionFour::where('footer_id', $this->footer->id)->first();
            if ($this->footerSectionFour) {
                $this->socialLinks = SocialLink::where('footer_section_four_id', $this->footerSectionFour->id)->get();
            }
        }
    }

    public function render()
    {
        return view('livewire.user.footer-component');
    }
}
