<?php

namespace App\Livewire\Admin\Settings;

use App\Models\FooterSectionThree;
use Livewire\Component;

class FooterSectionThreeSettings extends Component
{
    public $footerSectionThrees = [];
    public $newTitle;
    public $newLink;
    public $footerId;

    protected $rules = [
        'footerSectionThrees.*.title' => 'required|string|max:255',
        'footerSectionThrees.*.link' => 'required|url|max:255',
        'newTitle' => 'nullable|string|max:255',
        'newLink' => 'nullable|url|max:255',
    ];

    public function mount()
    {
        $this->footerSectionThrees = FooterSectionThree::all()->toArray();
    }

    public function addFooterSectionThree()
    {
        $this->validate([
            'footerId' => 'required',
            'newTitle' => 'required|string|max:255',
            'newLink' => 'required|url|max:255',
        ]);

        FooterSectionThree::create([
            'footer_id' => $this->footerId,
            'title' => $this->newTitle,
            'link' => $this->newLink,
        ]);

        $this->reset('newTitle', 'newLink');
        $this->mount();
        session()->flash('message', 'Footer section added successfully.');
    }

    public function updateFooterSectionThree($index)
    {
        $this->validate();

        $footerSectionThree = FooterSectionThree::findOrFail($this->footerSectionThrees[$index]['id']);
        $footerSectionThree->update([
            'title' => $this->footerSectionThrees[$index]['title'],
            'link' => $this->footerSectionThrees[$index]['link'],
        ]);

        session()->flash('message', 'Footer section updated successfully.');
    }

    public function deleteFooterSectionThree($index)
    {
        $footerSectionThree = FooterSectionThree::findOrFail($this->footerSectionThrees[$index]['id']);
        $footerSectionThree->delete();

        $this->mount();
        session()->flash('message', 'Footer section deleted successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.footer-section-three-settings');
    }
}
