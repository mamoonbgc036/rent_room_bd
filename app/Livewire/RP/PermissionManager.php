<?php

namespace App\Livewire\RP;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionManager extends Component
{
    public $permissions;
    public $name;
    public $selectedRole;
    public $roles;
    public $editingPermissionId = null;
    public $selectedPermissionId = null; // New property for assigning permissions

    protected $rules = [
        'name' => 'required|unique:permissions,name',
    ];

    public function mount()
    {
        $this->permissions = Permission::all();
        $this->roles = Role::all();
    }

    public function createPermission()
    {
        $this->validate();

        Permission::create([
            'name' => $this->name,
        ]);

        $this->reset(['name']);
        $this->mount();
    }

    public function editPermission($id)
    {
        $permission = Permission::findOrFail($id);
        $this->name = $permission->name;
        $this->editingPermissionId = $id;
    }

    public function updatePermission($id)
    {
        $this->validate([
            'name' => ['required', Rule::unique('permissions')->ignore($id)],
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update(['name' => $this->name]);

        $this->reset(['name', 'editingPermissionId']);
        $this->mount();
    }

    public function deletePermission($id)
    {
        Permission::findOrFail($id)->delete();
        $this->reset(['name']);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.r-p.permission-manager');
    }
}
