<div class="container mt-4">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Role Name -->
    <div class="mb-4">
        <h3 class="h4">{{ \Spatie\Permission\Models\Role::find($selectedRole)->name }}</h3>
    </div>

    <!-- Assigned Permissions Selection -->
    <div class="mb-4">
        <label class="form-label">Assigned Permissions</label>
        <div class="row">
            @foreach($permissions as $permission)
                @if(in_array($permission->name, $selectedPermissions))
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $permission->name }}" class="form-check-input" id="permission-{{ $permission->id }}">
                            <label class="form-check-label" for="permission-{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Update Button -->
    <div>
        <button wire:click="assignPermissions" class="btn btn-primary">Update Permissions</button>
    </div>
</div>
