<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class CountrySelector extends Component
{
    public $countries;
    public $selectedCountry;
    public $selectedCountryPhoto;
    public $cities = [];


    public function mount()
    {
        $this->countries = Country::all();
        $this->selectedCountry = session('selectedCountry', 1);
        $this->loadCities();
        $this->updateSelectedCountryPhoto();
    }

    public function loadCities()
    {
        if ($this->selectedCountry) {
            $this->cities = City::where('country_id', $this->selectedCountry)->get();
        }
    }
    public function updatedSelectedCountry($countryId)
    {
        session(['selectedCountry' => $countryId]);
        $this->updateSelectedCountryPhoto();
    }

    public function updateSelectedCountryPhoto()
    {
        if ($this->selectedCountry) {
            $country = Country::find($this->selectedCountry);
            $this->selectedCountryPhoto = $country ? Storage::url($country->photo) : null;
        } else {
            $this->selectedCountryPhoto = null;
        }
    }

    public function render()
    {
        return view('livewire.country-selector');
    }
}
