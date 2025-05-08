<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Footer;
use Livewire\Component;
use Livewire\WithFileUploads;

class FooterSettings extends Component
{
    use WithFileUploads;

    public $footerId;
    public $footer_logo;
    public $address;
    public $email;
    public $contact_number;
    public $website;
    public $terms_title;
    public $terms_link;
    public $privacy_title;
    public $privacy_link;
    public $rights_reserves_text;
    public $existing_footer_logo;

    protected $rules = [
        'footer_logo' => 'nullable|image|max:1024', // 1MB Max
        'address' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'contact_number' => 'required|string|max:15',
        'website' => 'required|max:255',
        'terms_title' => 'required|string|max:255',
        'terms_link' => 'required|url|max:255',
        'privacy_title' => 'required|string|max:255',
        'privacy_link' => 'required|url|max:255',
        'rights_reserves_text' => 'required|string|max:255',
    ];

    public function mount()
    {
        $footer = Footer::first();
        if ($footer) {
            $this->footerId = $footer->id;
            $this->existing_footer_logo = $footer->footer_logo;
            $this->address = $footer->address;
            $this->email = $footer->email;
            $this->contact_number = $footer->contact_number;
            $this->website = $footer->website;
            $this->terms_title = $footer->terms_title;
            $this->terms_link = $footer->terms_link;
            $this->privacy_title = $footer->privacy_title;
            $this->privacy_link = $footer->privacy_link;
            $this->rights_reserves_text = $footer->rights_reserves_text;
        }
    }

    public function save()
    {
        $this->validate();

        if ($this->footer_logo instanceof \Livewire\TemporaryUploadedFile) {
            $footerLogoPath = $this->footer_logo->store('footers', 'public');
        } else {
            $footerLogoPath = $this->existing_footer_logo;
        }

        Footer::updateOrCreate(
            ['id' => $this->footerId],
            [
                'footer_logo' => $footerLogoPath,
                'address' => $this->address,
                'email' => $this->email,
                'contact_number' => $this->contact_number,
                'website' => $this->website,
                'terms_title' => $this->terms_title,
                'terms_link' => $this->terms_link,
                'privacy_title' => $this->privacy_title,
                'privacy_link' => $this->privacy_link,
                'rights_reserves_text' => $this->rights_reserves_text,
            ]
        );

        session()->flash('message', 'Footer settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.footer-settings');
    }
}
