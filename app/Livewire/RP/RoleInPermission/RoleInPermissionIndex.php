<?php

namespace App\Livewire\RP\RoleInPermission;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RoleInPermissionIndex extends Component
{
    public $roles;

    public function mount()
    {
        $this->roles = Role::with('permissions')->get();
    }

    public function render()
    {
        return view('livewire.r-p.role-in-permission.role-in-permission-index');
    }

}
