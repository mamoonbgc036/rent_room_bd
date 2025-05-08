<div class="container mt-4">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Role Selection -->
    <div class="mb-4">
        <label class="block text-lg font-medium text-gray-700 mb-1" for="role">Select Role</label>
        <select class="form-control border-0 shadow-none form-control-lg mb-2" wire:model="selectedRole" id="role">
            <option value="">-- Select Role --</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
    </div>

    

    <!-- Permissions Selection -->
    <div class="mb-4">
        <label class="form-label">Permissions</label>
        <div class="row">
            @foreach($permissions as $permission)
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->name }}" class="form-check-input" id="permission-{{ $permission->id }}">
                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Assign Button -->
    <div>
        <button wire:click="assignPermissions" class="btn btn-primary">Assign Permissions</button>
    </div>
</div>
