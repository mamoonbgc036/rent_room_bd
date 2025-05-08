<?php

namespace App\Livewire\RP;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleManager extends Component
{
    public $roles;
    public $name;
    public $selectedRole;

    protected $rules = [
        'name' => 'required|unique:roles,name',
    ];

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function createRole()
    {
        $this->validate();

        Role::create([
            'name' => $this->name,
        ]);

        $this->reset();
        $this->mount();
    }

    public function editRole($id)
    {
        $this->selectedRole = Role::findOrFail($id);
        $this->name = $this->selectedRole->name;
    }

    public function updateRole()
    {
        $this->validate([
            'name' => [
                'required',
                Rule::unique('roles')->ignore($this->selectedRole->id),
            ],
        ]);

        $this->selectedRole->update([
            'name' => $this->name,
        ]);

        $this->reset();
        $this->mount();
    }

    public function deleteRole($id)
    {
        Role::findOrFail($id)->delete();
        $this->mount();
    }
    public function render()
    {
        return view('livewire.r-p.role-manager');
    }
}
