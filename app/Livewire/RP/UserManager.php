<?php

namespace App\Livewire\RP;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManager extends Component
{
    public $users;
    public $user;
    public $selectedRoles = [];

    public function mount()
    {
        // Fetch all users from the database
        $this->users = User::all();
    }

    public function assignRoles()
    {
        // Find the user by ID
        $userModel = User::findOrFail($this->user); 
        // Sync the roles with the user
        $userModel->syncRoles($this->selectedRoles);

        // Reset form fields and emit event
        $this->reset(['user', 'selectedRoles']);
        $this->dispatch('rolesAssigned');
    }

    public function editUserRoles($userId)
    {
        // Set the current user ID for editing
        $this->user = $userId;

        // Get the user's current roles
        $userRoles = User::findOrFail($userId)->roles->pluck('name')->toArray();

        // Set the selected roles for the user
        $this->selectedRoles = $userRoles;
    }

    public function render()
    {
        // Fetch all users from the database
        $users = User::all();

        // Fetch all roles
        $roles = Role::all();

        return view('livewire.r-p.user-manager', compact('users', 'roles'));
    }
}
