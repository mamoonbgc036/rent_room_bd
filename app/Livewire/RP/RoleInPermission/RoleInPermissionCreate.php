<?php

namespace App\Livewire\RP\RoleInPermission;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleInPermissionCreate extends Component
{
    public $roles;
    public $permissions;
    public $selectedRole;
    public $selectedPermissions = [];

    public function mount()
    {
        $this->roles = Role::all();
        $this->permissions = Permission::all();
    }

    public function assignPermissions()
    {
        $role = Role::find($this->selectedRole);
        if ($role) {
            $role->syncPermissions($this->selectedPermissions);
            session()->flash('success', 'Permissions assigned successfully!');
            return redirect()->route('role.in.permission');
        }
    }

    public function render()
    {
        return view('livewire.r-p.role-in-permission.role-in-permission-create');
    }

}
