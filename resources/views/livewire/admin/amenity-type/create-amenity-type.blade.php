<div>
    <!-- Overlay -->
    <div class="overlay" wire:click="closeModal"></div>

    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="w-full max-w-lg p-6 bg-white rounded shadow-lg">
            <h2 class="text-2xl font-semibold mb-6">{{ $amenity_type_id ? 'Edit Amenity Type' : 'Create Amenity Type' }}</h2>

            <form wire:submit.prevent="store">
                <div class="mb-4">
                    <label for="type" class="block text-lg font-medium text-gray-700 mb-1">Type</label>
                    <input type="text" id="type" wire:model.defer="type" class="form-control form-control-lg border-0" placeholder="Type">
                    @error('type') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="button" wire:click="closeModal" class="btn btn-lg btn-secondary next-button mb-3 mr-2">Cancel</button>
                    <button type="submit" class="btn btn-lg btn-primary next-button mb-3">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


