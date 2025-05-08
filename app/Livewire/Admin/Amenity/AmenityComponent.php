<?php

namespace App\Livewire\Admin\Amenity;

use App\Models\Amenity;
use App\Models\AmenityType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AmenityComponent extends Component
{
    public $amenities, $amenityTypes;
    public $amenity_type_id, $name, $amenity_id;
    public $isOpen = false;

    public function render()
    {
        $user = Auth::user();

        if ($user->roles->pluck('name')->contains('Super Admin')) {
            $this->amenities = Amenity::with('amenityType')->get();
        } else {
            $this->amenities = Amenity::with('amenityType')->where('user_id', $user->id)->get();
        }

        $this->amenityTypes = AmenityType::all();
        return view('livewire.admin.amenity.amenity-component');
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
        $this->amenity_type_id = '';
        $this->name = '';
        $this->amenity_id = null;
    }

    public function store()
    {
        $this->validate([
            'amenity_type_id' => 'required',
            'name' => 'required',
        ]);

        Amenity::updateOrCreate(['id' => $this->amenity_id], [
            'amenity_type_id' => $this->amenity_type_id,
            'name' => $this->name,
            'user_id' => Auth::id(),
        ]);

        session()->flash('message', $this->amenity_id ? 'Amenity Updated Successfully.' : 'Amenity Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $amenity = Amenity::findOrFail($id);
        $this->amenity_id = $id;
        $this->amenity_type_id = $amenity->amenity_type_id;
        $this->name = $amenity->name;

        $this->openModal();
    }

    public function delete($id)
    {
        Amenity::find($id)->delete();
        session()->flash('message', 'Amenity Deleted Successfully.');
    }

}
