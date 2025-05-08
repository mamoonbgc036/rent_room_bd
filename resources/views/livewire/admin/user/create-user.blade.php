<div>
    <!-- Overlay -->
    <div class="overlay" wire:click="closeModal"></div>

    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="w-full max-w-lg p-6 bg-white rounded shadow-lg">
            <h2 class="text-2xl font-semibold mb-6">{{ $user_id ? 'Edit User' : 'Create User' }}</h2>

            <form wire:submit.prevent="store">
                <div class="mb-4">
                    <input type="text" wire:model="name" class="form-control form-control-lg border-0" placeholder="Name">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <input type="email" wire:model="email" class="form-control form-control-lg border-0" placeholder="Email">
                    @error('email') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <input type="password" wire:model="password" class="form-control form-control-lg border-0" placeholder="Password">
                    @error('password') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <input type="password" wire:model="password_confirmation" class="form-control form-control-lg border-0" placeholder="Confirm Password">
                </div>

                <div class="mb-4">
                    <select wire:model="role_id" class="form-control form-control-lg border-0">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="button" wire:click="closeModal" class="btn btn-lg btn-secondary next-button mb-3 mr-2">Cancel</button>
                    <button type="submit" class="btn btn-lg btn-primary next-button mb-3">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
