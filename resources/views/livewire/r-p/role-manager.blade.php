<div class="rounded-2xl p-12 card">
    <!-- Role Form -->
    <form @if ($selectedRole) wire:submit.prevent="updateRole" @else wire:submit.prevent="createRole" @endif class="mb-4">
        <!-- Role Name Input -->
        <div class="form-group">
            <label for="title" class="text-heading">Title <span class="text-muted">(mandatory)</span></label>
            {{-- <input type="text" class="form-control form-control-lg border-0" id="title" name="title"> --}}
            <input type="text" wire:model.defer="name" class="form-control form-control-lg border-0" placeholder="Enter Role Name">
            @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-lg btn-primary next-button mb-3">
            @if ($selectedRole)
                Update Role
            @else
                Create Role
            @endif
        </button>
    </form>

    <!-- Role Table -->
    <div class="table-responsive">
        <table class="table table-hover bg-white border rounded-lg">
          <thead class="thead-sm thead-black">
            <tr>
              <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Name</th>
              <th scope="col" class="border-top-0 px-6 pt-5 pb-4">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($roles as $role)
                <tr class="shadow-hover-xs-2 bg-hover-white">
                <td class="align-middle">{{ $role->name }}</td>
                <td class="align-middle">
                    <button wire:click="editRole({{ $role->id }})" class="btn btn-lg btn-primary next-button mb-3">Edit</button>
                    <button wire:click="deleteRole({{ $role->id }})" class="btn btn-lg btn-primary next-button mb-3">Delete</button>
                </td>
                </tr>
            @endforeach
          </tbody>
        </table>
      </div>
</div>
