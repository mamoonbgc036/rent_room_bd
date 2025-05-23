<?php

namespace App\Livewire\Admin\Area;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class AreaComponent extends Component
{
    use WithFileUploads;

    public $areas, $countries, $cities = [];
    public $area_id, $country_id, $city_id, $name, $photo;
    public $isOpen = false;

    public function mount()
    {
        // $this->countries = Country::all();
        // Set default cities based on the first country
        // if ($this->countries->isNotEmpty()) {
        //     $this->cities = City::where('country_id', $this->countries->first()->id)->get();
        // }
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
        $this->areas = DB::table('areas')->join('cities', 'areas.district_id', '=', 'cities.id')
            ->select('areas.*', 'cities.id as city_id', 'cities.name as city_name')
            ->get();
        return view('livewire.admin.area.area-component');
    }


    public function create()
    {
        $this->resetInputFields();
        $this->cities = DB::table('cities')->orderBy('id', 'desc')->get();
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
        $this->area_id = '';
        $this->cities = []; // Reset cities
        $this->city_id = '';
        $this->name = '';
        $this->photo = '';
    }

    public function store()
    {
        $this->validate([
            'country_id' => 'required',
            'city_id' => 'required',
            'name' => 'required',
            'photo' => 'nullable|image|max:1024',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('photos', 'public');
        }

        // Create or update the area
        $areaData = [
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'name' => $this->name,
            'photo' => $photoPath,
        ];

        if ($this->area_id) {
            Area::find($this->area_id)->update($areaData);
            session()->flash('message', 'Area Updated Successfully.');
        } else {
            Area::create($areaData);
            session()->flash('message', 'Area Created Successfully.');
        }

        $this->closeModal();
        $this->resetInputFields();
    }


    public function edit($id)
    {
        $area = Area::findOrFail($id);
        $this->area_id = $id;
        $this->country_id = $area->country_id;
        $this->updateCities($this->country_id); // Update cities based on country_id
        $this->city_id = $area->city_id;
        $this->name = $area->name;
        $this->photo = $area->photo;

        $this->openModal();
    }

    public function delete($id)
    {
        Area::find($id)->delete();
        session()->flash('message', 'Area Deleted Successfully.');
    }
}
