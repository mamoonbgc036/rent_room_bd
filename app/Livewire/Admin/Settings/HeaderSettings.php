<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Header;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class HeaderSettings extends Component
{
    use WithFileUploads;

    public $logo;
    public $existingLogo;

    public function mount()
    {
        // Load existing header logo if available
        $header = Header::first();
        $this->existingLogo = $header ? $header->logo : null;
    }

    public function updatedLogo()
    {
        $this->validate([
            'logo' => 'image|max:1024', // 1MB Max
        ]);
    }

    public function save()
    {
        $this->validate([
            'logo' => 'nullable|image|max:1024',
        ]);

        $header = Header::firstOrCreate([]);

        if ($this->logo) {
            $logoPath = Storage::put('logos', $this->logo);
            $header->logo = $logoPath;
        }

        $header->save();

        session()->flash('message', 'Header updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.header-settings');
    }
}
