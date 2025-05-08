<?php

namespace App\Livewire\Admin\City;

use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CityComponent extends Component
{
    use WithFileUploads;

    public $cities, $countries;
    public $city_id, $country_id, $name, $photo;
    public $isOpen = false;

    public function render()
    {
        $this->cities = City::with('country')->get();
        $this->countries = Country::all();
        return view('livewire.admin.city.city-component');
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
        $this->city_id = '';
        $this->country_id = '';
        $this->name = '';
        $this->photo = '';
    }

    public function store()
    {
        $this->validate([
            'country_id' => 'required',
            'name' => 'required',
            'photo' => 'nullable|image|max:1024', // 1MB Max
        ]);

        if ($this->photo) {
            $photoPath = $this->photo->store('photos', 'public');
        } else {
            $photoPath = null;
        }

        City::updateOrCreate(['id' => $this->city_id], [
            'country_id' => $this->country_id,
            'name' => $this->name,
            'photo' => $photoPath,
        ]);

        session()->flash('message',
            $this->city_id ? 'City Updated Successfully.' : 'City Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $city = City::findOrFail($id);
        $this->city_id = $id;
        $this->country_id = $city->country_id;
        $this->name = $city->name;
        $this->photo = $city->photo;

        $this->openModal();
    }

    public function delete($id)
    {
        City::find($id)->delete();
        session()->flash('message', 'City Deleted Successfully.');
    }

}
