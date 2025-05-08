<?php

namespace App\Livewire\RP\RoleInPermission;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleInPermissionEdit extends Component
{
    public $roles;
    public $permissions;
    public $selectedRole;
    public $selectedPermissions = [];

    public function mount($role_id)
    {
        $this->roles = Role::all();
        $this->permissions = Permission::all();
        $this->selectedRole = $role_id;
        $this->loadPermissions();
    }

    public function loadPermissions()
    {
        $role = Role::find($this->selectedRole);
        $this->selectedPermissions = $role ? $role->permissions->pluck('name')->toArray() : [];
    }

    public function assignPermissions()
    {
        $role = Role::find($this->selectedRole);
        if ($role) {
            $role->syncPermissions($this->selectedPermissions);
            session()->flash('success', 'Permissions updated successfully!');
        }
    }

    public function render()
    {
        return view('livewire.r-p.role-in-permission.role-in-permission-edit');
    }

}
