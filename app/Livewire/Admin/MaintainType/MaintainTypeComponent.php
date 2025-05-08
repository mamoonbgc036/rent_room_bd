<?php

namespace App\Livewire\Admin\MaintainType;

use App\Models\MaintainType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MaintainTypeComponent extends Component
{
    public $maintainTypes;
    public $type;
    public $maintain_type_id;
    public $isOpen = false;

    public function render()
    {
        $user = Auth::user();

        if ($user->roles->pluck('name')->contains('Super Admin')) {
            $this->maintainTypes = MaintainType::all();
        } else {
            $this->maintainTypes = MaintainType::where('user_id', $user->id)->get();
        }

        return view('livewire.admin.maintain-type.maintain-type-component');
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
        $this->maintain_type_id = null;
    }

    public function store()
    {
        $this->validate([
            'type' => 'required',
        ]);

        MaintainType::updateOrCreate(['id' => $this->maintain_type_id], [
            'type' => $this->type,
            'user_id' => Auth::id(),
        ]);

        session()->flash('message', $this->maintain_type_id ? 'Maintain Type Updated Successfully.' : 'Maintain Type Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $maintainType = MaintainType::findOrFail($id);
        $this->maintain_type_id = $id;
        $this->type = $maintainType->type;

        $this->openModal();
    }

    public function delete($id)
    {
        MaintainType::find($id)->delete();
        session()->flash('message', 'Maintain Type Deleted Successfully.');
    }

}
