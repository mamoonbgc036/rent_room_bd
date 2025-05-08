<?php

namespace App\Livewire\Admin\Property;

use App\Models\City;
use App\Models\Country;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PropertyComponent extends Component
{

    use WithFileUploads;

    public $properties, $countries, $cities = [], $propertyTypes = [];
    public $property_id, $country_id, $city_id, $property_type_id, $name, $photo;
    public $isOpen = false;

    public function mount()
    {
        $this->countries = Country::all();
        $this->propertyTypes = PropertyType::all();
        // Set default country_id based on user's choice
        $this->cities = City::where('country_id', $this->country_id)->get();
    }

    public function updatedCountryId($value)
    {
        $this->updateCities($value);
    }

    public function updateCities($countryId)
    {
        $this->cities = City::where('country_id', $countryId)->get();
        $this->city_id = null; // Reset city_id when country changes
    }

    public function render()
    {
        $user = Auth::user();

        if ($user->roles->pluck('name')->contains('Super Admin')) {
            $this->properties = Property::with(['country', 'city', 'propertyType'])->get();
        } else {
            $this->properties = Property::with(['country', 'city', 'propertyType'])->where('user_id', $user->id)->get();
        }

        return view('livewire.admin.property.property-component');
    }


    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->property_id = '';
        $this->country_id = null; // Reset country_id
        $this->cities = []; // Reset cities
        $this->city_id = '';
        $this->property_type_id = '';
        $this->name = '';
        $this->photo = '';
    }

    public function store()
    {
        $this->validate([
            'country_id' => 'required',
            'city_id' => 'required',
            'property_type_id' => 'required',
            'name' => 'required',
            'photo' => 'nullable|image|max:1024', // 1MB Max
        ]);

        if ($this->photo) {
            $photoPath = $this->photo->store('photos', 'public');
        } else {
            $photoPath = null;
        }

        Property::updateOrCreate(['id' => $this->property_id], [
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'property_type_id' => $this->property_type_id,
            'name' => $this->name,
            'photo' => $photoPath,
            'user_id' => Auth::id(),
        ]);

        session()->flash('message', $this->property_id ? 'Property Updated Successfully.' : 'Property Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $property = Property::findOrFail($id);
        $this->property_id = $id;
        $this->country_id = $property->country_id;
        $this->updateCities($this->country_id); // Update cities based on country_id
        $this->city_id = $property->city_id;
        $this->property_type_id = $property->property_type_id;
        $this->name = $property->name;
        $this->photo = $property->photo;

        $this->openModal();
    }

    public function delete($id)
    {
        Property::find($id)->delete();
        session()->flash('message', 'Property Deleted Successfully.');
    }

}
