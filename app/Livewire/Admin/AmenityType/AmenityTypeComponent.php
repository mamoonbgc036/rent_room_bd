<?php

namespace App\Livewire\Admin\AmenityType;

use App\Models\AmenityType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AmenityTypeComponent extends Component
{
    public $amenityTypes;
    public $type;
    public $amenity_type_id;
    public $isOpen = false;

    public function render()
    {
        $user = Auth::user();

        if ($user->roles->pluck('name')->contains('Super Admin')) {
            $this->amenityTypes = AmenityType::all();
        } else {
            $this->amenityTypes = AmenityType::where('user_id', $user->id)->get();
        }

        return view('livewire.admin.amenity-type.amenity-type-component');
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
        $this->type = '';
        $this->amenity_type_id = null;
    }

    public function store()
    {
        $this->validate([
            'type' => 'required',
        ]);

        AmenityType::updateOrCreate(['id' => $this->amenity_type_id], [
            'type' => $this->type,
            'user_id' => Auth::id(),
        ]);

        session()->flash('message', $this->amenity_type_id ? 'Amenity Type Updated Successfully.' : 'Amenity Type Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $amenityType = AmenityType::findOrFail($id);
        $this->amenity_type_id = $id;
        $this->type = $amenityType->type;

        $this->openModal();
    }

    public function delete($id)
    {
        AmenityType::find($id)->delete();
        session()->flash('message', 'Amenity Type Deleted Successfully.');
    }

}
