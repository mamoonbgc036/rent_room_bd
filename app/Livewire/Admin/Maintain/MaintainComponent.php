<?php

namespace App\Livewire\Admin\Maintain;

use App\Models\Maintain;
use App\Models\MaintainType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class MaintainComponent extends Component
{
    use WithFileUploads;

    public $maintains, $maintainTypes;
    public $maintain_type_id, $name, $photo, $maintain_id;
    public $isOpen = false;

    public function render()
    {
        $user = Auth::user();

        if ($user->roles->pluck('name')->contains('Super Admin')) {
            $this->maintains = Maintain::with('maintainType')->get();
        } else {
            $this->maintains = Maintain::with('maintainType')->where('user_id', $user->id)->get();
        }

        $this->maintainTypes = MaintainType::all();
        return view('livewire.admin.maintain.maintain-component');
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
        $this->maintain_type_id = '';
        $this->name = '';
        $this->photo = '';
        $this->maintain_id = null;
    }

    public function store()
    {
        $this->validate([
            'maintain_type_id' => 'required',
            'name' => 'required',
            'photo' => 'nullable|image|max:1024', // 1MB Max
        ]);

        $photoPath = $this->photo ? $this->photo->store('photos', 'public') : null;

        Maintain::updateOrCreate(['id' => $this->maintain_id], [
            'maintain_type_id' => $this->maintain_type_id,
            'name' => $this->name,
            'photo' => $photoPath,
            'user_id' => Auth::id(),
        ]);

        session()->flash('message', $this->maintain_id ? 'Maintain Updated Successfully.' : 'Maintain Created Successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $maintain = Maintain::findOrFail($id);
        $this->maintain_id = $id;
        $this->maintain_type_id = $maintain->maintain_type_id;
        $this->name = $maintain->name;
        $this->photo = $maintain->photo;

        $this->openModal();
    }

    public function delete($id)
    {
        Maintain::find($id)->delete();
        session()->flash('message', 'Maintain Deleted Successfully.');
    }

}
