<?php

namespace App\Livewire\Admin\Settings;

use App\Models\HeroSection;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class HeroSettings extends Component
{
    use WithFileUploads;

    public $heroSections, $background_image, $title_small, $title_big, $selectedId;
    public $isEditing = false;

    protected $rules = [
        'background_image' => 'nullable|image',
        'title_small' => 'required|string|max:255',
        'title_big' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->heroSections = HeroSection::all();
    }

    public function render()
    {
        return view('livewire.admin.settings.hero-settings');
    }

    public function store()
    {
        $this->validate();

        $heroSection = new HeroSection();

        if ($this->background_image) {
            $path = Storage::put('background_images', $this->background_image);
            $heroSection->background_image = $path;
        }

        $heroSection->title_small = $this->title_small;
        $heroSection->title_big = $this->title_big;
        $heroSection->save();

        $this->resetForm();
        $this->heroSections = HeroSection::all();
        session()->flash('message', 'Hero Section created successfully.');
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->isEditing = true;

        $heroSection = HeroSection::find($id);
        $this->selectedId = $heroSection->id;
        $this->background_image = $heroSection->background_image;
        $this->title_small = $heroSection->title_small;
        $this->title_big = $heroSection->title_big;
    }

    public function update()
    {
        $this->validate();

        $heroSection = HeroSection::find($this->selectedId);

        if ($this->background_image) {
            if ($heroSection->background_image) {
                Storage::delete($heroSection->background_image);
            }
            $path = $this->background_image->store('public/background_images');
            $heroSection->background_image = $path;
        }

        $heroSection->title_small = $this->title_small;
        $heroSection->title_big = $this->title_big;
        $heroSection->save();

        $this->resetForm();
        $this->heroSections = HeroSection::all();
        $this->isEditing = false;
        session()->flash('message', 'Hero Section updated successfully.');
    }

    public function delete($id)
    {
        $heroSection = HeroSection::find($id);
        if ($heroSection->background_image) {
            Storage::delete($heroSection->background_image);
        }
        $heroSection->delete();
        $this->heroSections = HeroSection::all();
        session()->flash('message', 'Hero Section deleted successfully.');
    }

    private function resetForm()
    {
        $this->background_image = null;
        $this->title_small = '';
        $this->title_big = '';
        $this->selectedId = null;
    }
}
