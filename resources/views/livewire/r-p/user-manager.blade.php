<div class="rounded-2xl p-8 bg-white shadow-md">
    <!-- User Selection Section -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold mb-2">Select User</h2>
        <select wire:model="user" class="form-control border-0 shadow-none form-control-lg selectpicker" data-style="btn-lg py-2 h-52">
            <option value="">Select User</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
        @error('user') <span class="text-red-500">{{ $message }}</span> @enderror
    </div>

    <!-- Assign Role Section -->
    @if($user)
        <div>
            <h2 class="text-lg font-semibold mb-2">Assign Role</h2>
            <form wire:submit.prevent="assignRoles">
                <select wire:model="selectedRoles" class="form-control border-0 shadow-none form-control-lg selectpicker mb-2" data-style="btn-lg py-2 h-52">
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('selectedRoles') <span class="text-red-500">{{ $message }}</span> @enderror
                <button type="submit" class="btn btn-lg btn-primary next-button mb-3">Assign Roles</button>
            </form>
        </div>
    @endif

    <!-- User and Role Assignment Table -->
    <div class="mt-8">
        <h2 class="text-lg font-semibold mb-2">Users and Assigned Roles</h2>
        <table class="w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-200 px-4 py-2">User</th>
                    <th class="border border-gray-200 px-4 py-2">Assigned Roles</th>
                    <th class="border border-gray-200 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td class="border border-gray-200 px-4 py-2">{{ $user->name }}</td>
                        <td class="border border-gray-200 px-4 py-2">
                            @foreach($user->roles as $role)
                                {{ $role->name }}@if(!$loop->last),@endif
                            @endforeach
                        </td>
                        <td class="border border-gray-200 px-4 py-2">
                            <button wire:click="editUserRoles({{ $user->id }})" class="btn btn-lg btn-primary next-button mb-3">Edit Roles</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
