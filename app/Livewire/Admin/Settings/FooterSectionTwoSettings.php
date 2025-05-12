<?php

namespace App\Livewire\Admin\Settings;

use App\Models\FooterSectionTwo;
use Livewire\Component;

class FooterSectionTwoSettings extends Component
{
    public $footerSectionTwos = [];
    public $newTitle;
    public $newLink;

    protected $rules = [
        'footerSectionTwos.*.title' => 'required|string|max:255',
        'footerSectionTwos.*.link' => 'required|url|max:255',
        'newTitle' => 'nullable|string|max:255',
        'newLink' => 'nullable|url|max:255',
    ];

    public function mount()
    {
        $this->footerSectionTwos = FooterSectionTwo::all()->toArray();
    }

    public function addFooterSectionTwo()
    {
        $this->validate([
            'newTitle' => 'required|string|max:255',
            'newLink' => 'required|url|max:255',
        ]);

        FooterSectionTwo::create([
            'footer_id' => 1,
            'title' => $this->newTitle,
            'link' => $this->newLink,
        ]);

        $this->reset('newTitle', 'newLink');
        $this->mount();
        session()->flash('message', 'Footer section added successfully.');
    }

    public function updateFooterSectionTwo($index)
    {
        $this->validate();

        $footerSectionTwo = FooterSectionTwo::findOrFail($this->footerSectionTwos[$index]['id']);
        $footerSectionTwo->update([
            'title' => $this->footerSectionTwos[$index]['title'],
            'link' => $this->footerSectionTwos[$index]['link'],
        ]);

        session()->flash('message', 'Footer section updated successfully.');
    }

    public function deleteFooterSectionTwo($index)
    {
        $footerSectionTwo = FooterSectionTwo::findOrFail($this->footerSectionTwos[$index]['id']);
        $footerSectionTwo->delete();

        $this->mount();
        session()->flash('message', 'Footer section deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.footer-section-two-settings');
    }
}
