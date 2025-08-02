<?php

namespace App\Livewire\Admin\Settings;

use App\Models\FooterSectionFour;
use App\Models\SocialLink;
use Livewire\Component;

class FooterSectionFourSettings extends Component
{
    public $footerSectionFour;
    public $footerId;
    public $title;
    public $description;
    public $socialLinks = [];
    public $newIconClass;
    public $newLink;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'socialLinks.*.icon_class' => 'required|string|max:255',
        'socialLinks.*.link' => 'required|url|max:255',
        'newIconClass' => 'nullable|string|max:255',
        'newLink' => 'nullable|url|max:255',
    ];

    public function mount()
    {
        $this->footerSectionFour = FooterSectionFour::first();
        if ($this->footerSectionFour) {
            $this->footerId = $this->footerSectionFour->footer_id;
            $this->title = $this->footerSectionFour->title;
            $this->description = $this->footerSectionFour->description;
            $this->socialLinks = $this->footerSectionFour->socialLinks->toArray();
        }
    }

    public function saveFooterSectionFour()
    {
        $this->validate();

        $footer_data = [
                'footer_id' => $this->footerId,
                'title' => $this->title,
                'description' => $this->description,
        ]; 

        if ($this->footerSectionFour) {
            $this->footerSectionFour->update($footer_data);
        } else {
            $this->footerSectionFour = FooterSectionFour::create($footer_data);
        }

        session()->flash('message', 'Footer section saved successfully.');
    }

    public function addSocialLink()
    {
        $this->validate([
            'newIconClass' => 'required|string|max:255',
            'newLink' => 'required|url|max:255',
        ]);

        SocialLink::create([
            'footer_section_four_id' => $this->footerSectionFour->id,
            'icon_class' => $this->newIconClass,
            'link' => $this->newLink,
        ]);

        $this->reset('newIconClass', 'newLink');
        $this->mount();
        session()->flash('message', 'Social link added successfully.');
    }

    public function updateSocialLink($index)
    {
        $this->validate();

        $socialLink = SocialLink::findOrFail($this->socialLinks[$index]['id']);
        $socialLink->update([
            'icon_class' => $this->socialLinks[$index]['icon_class'],
            'link' => $this->socialLinks[$index]['link'],
        ]);

        session()->flash('message', 'Social link updated successfully.');
    }

    public function deleteSocialLink($index)
    {
        $socialLink = SocialLink::findOrFail($this->socialLinks[$index]['id']);
        $socialLink->delete();

        $this->mount();
        session()->flash('message', 'Social link deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.footer-section-four-settings');
    }
}
