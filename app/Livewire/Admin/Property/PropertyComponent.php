<?php

namespace App\Livewire\Admin\Property;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use Livewire\Component;
use App\Models\Property;
use App\Models\PropertyType;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PropertyComponent extends Component
{

    use WithFileUploads;

    public $properties, $countries, $cities = [], $areas, $propertyTypes = [], $selectedCity = null;
    public $property_id, $country_id, $area_id, $city_id, $property_type_id, $name, $photo;
    public $isOpen = false;

    // public function mount()
    // {
    //     $this->countries = Country::all();
    //     $this->propertyTypes = PropertyType::all();
    //     // Set default country_id based on user's choice
    //     $this->cities = City::all();
    // }

    public function updatedCountryId($value)
    {
        $this->updateCities($value);
    }

    public function updatedCityId($districtId)
    {
        $this->areas = Area::where('district_id', $districtId)->get();
        $this->area_id = null;
    }

    // public function updatedSelectedCity($city_id)
    // {
    //     $this->areas = Area::where('district_id', $city_id)->get();
    // }

    public function updatedSelectedCity($districtId)
    {
        $this->areas = Area::where('district_id', $districtId)->get();
        // $this->area_id = null; // Reset city_id when country changes
    }

    public function render()
    {
        $user = Auth::user();
        if ($user->roles->pluck('name')->contains('Super Admin')) {
            $this->properties = Property::with(['area', 'city', 'propertyType'])->get();
        } else {
            $this->properties = Property::with(['area', 'city', 'propertyType'])->where('user_id', $user->id)->get();
        }
        return view('livewire.admin.property.property-component');
    }


    public function create($forEdit = [])
    {
        $this->city_id = null;
        $this->property_type_id = null;
        $this->city_id = null;
        $this->cities = DB::table('cities')->get();
        $this->propertyTypes = DB::table('property_types')->get();
        $this->openModal();
    }

    public function edit($id)
    {
        $property = Property::findOrFail($id);
        $this->property_id = $id;
        $this->area_id = $property->area_id;
        $this->city_id = $property->city_id;
        $this->cities = DB::table('cities')->get();
        $this->areas = DB::table('areas')->where('district_id', $property->city_id)->get();
        $this->property_type_id = $property->property_type_id;
        $this->propertyTypes = DB::table('property_types')->get();
        $this->name = $property->name;
        $this->photo = $property->photo;
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
            'city_id' => 'required',
            'area_id' => 'required',
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
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'property_type_id' => $this->property_type_id,
            'name' => $this->name,
            'photo' => $photoPath,
            'user_id' => Auth::id(),
        ]);

        session()->flash('message', $this->property_id ? 'Property Updated Successfully.' : 'Property Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        Property::find($id)->delete();
        session()->flash('message', 'Property Deleted Successfully.');
    }
}
