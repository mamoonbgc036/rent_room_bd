<?php

namespace App\Livewire\Admin\PropertyType;

use App\Models\PropertyType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PropertyTypeComponent extends Component
{
    public $propertyTypes;
    public $type, $propertyType_id;
    public $isOpen = false;

    public function render()
    {
        $user = Auth::user();

        if ($user->roles->pluck('name')->contains('Super Admin')) {
            $this->propertyTypes = PropertyType::all();
        } else {
            $this->propertyTypes = PropertyType::where('user_id', $user->id)->get();
        }

        return view('livewire.admin.property-type.property-type-component');
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
        $this->propertyType_id = '';
    }

    public function store()
    {
        $this->validate([
            'type' => 'required',
        ]);

        PropertyType::updateOrCreate(['id' => $this->propertyType_id], [
            'type' => $this->type,
            'user_id' => Auth::id(),
        ]);

        session()->flash('message',
            $this->propertyType_id ? 'Property Type Updated Successfully.' : 'Property Type Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $propertyType = PropertyType::findOrFail($id);
        $this->propertyType_id = $id;
        $this->type = $propertyType->type;

        $this->openModal();
    }

    public function delete($id)
    {
        PropertyType::find($id)->delete();
        session()->flash('message', 'Property Type Deleted Successfully.');
    }

}
