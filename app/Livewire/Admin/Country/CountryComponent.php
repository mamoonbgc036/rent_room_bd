<?php

namespace App\Livewire\Admin\Country;

use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CountryComponent extends Component
{
    use WithFileUploads;

    public $countries, $name, $symbol, $currency, $photo, $country_id;
    public $isOpen = 0;

    public function render()
    {
        $this->countries = Country::all();
        return view('livewire.admin.country.country-component');
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
        $this->name = '';
        $this->symbol = '';
        $this->currency = '';
        $this->photo = null;
        $this->country_id = null;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'symbol' => 'required',
            'currency' => 'required',
            'photo' => 'image|max:1024', // 1MB Max
        ]);

        $photoPath = $this->photo ? $this->photo->store('photos', 'public') : null;

        $country = Country::firstOrNew(['name' => $this->name]);

        $country->symbol = $this->symbol;
        $country->currency = $this->currency;
        $country->photo = $photoPath;

        $country->save();

        session()->flash('message',
            $this->country_id ? 'Country Updated Successfully.' : 'Country Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $country = Country::findOrFail($id);
        $this->country_id = $id;
        $this->name = $country->name;
        $this->symbol = $country->symbol;
        $this->currency = $country->currency;
        $this->photo = $country->photo;

        $this->openModal();
    }

    public function delete($id)
    {
        Country::find($id)->delete();
        session()->flash('message', 'Country Deleted Successfully.');
    }

}
