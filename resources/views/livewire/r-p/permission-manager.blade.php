<div class="rounded-2xl p-12 card">
    @if ($editingPermissionId)
        <!-- Update Permission Form -->
        <form wire:submit.prevent="updatePermission({{ $editingPermissionId }})" class="mb-4">
    @else
        <!-- Create Permission Form -->
        <form wire:submit.prevent="createPermission" class="mb-4">
    @endif
        <div class="form-group">
            <input type="text" wire:model.defer="name" class="form-control form-control-lg border-0 mb-4" placeholder="Enter Permission Name">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="btn btn-lg btn-primary next-button mb-3">
            @if ($editingPermissionId)
                Update Permission
            @else
                Create Permission
            @endif
        </button>
    </form>

    <!-- List of Permissions -->
    <div class="mt-4">
        <h4>Permissions List</h4>
        <ul class="list-group">
            @foreach($permissions as $permission)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $permission->name }}
                    <div>
                        <button wire:click="editPermission({{ $permission->id }})" class="btn btn-sm btn-warning">Edit</button>
                        <button wire:click="deletePermission({{ $permission->id }})" class="btn btn-sm btn-danger">Delete</button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
