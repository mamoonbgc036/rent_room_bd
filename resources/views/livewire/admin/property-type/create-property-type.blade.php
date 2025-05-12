<div>
    <!-- Overlay -->
    <div class="overlay" wire:click="closeModal"></div>

    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="w-full max-w-lg p-6 bg-white rounded shadow-lg">
            <h2 class="text-2xl text-center font-semibold mb-6">{{ $propertyType_id ? 'Edit Property Type' : 'Create Property Type' }}</h2>

            <form wire:submit.prevent="store">
                <div class="mb-4">
                    <input type="text" wire:model="type" class="form-control form-control-lg border-0" placeholder="Property Type">
                    @error('type') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <button type="button" wire:click="closeModal" class="btn m-2 btn-sm btn-warning">
                        Cancel
                    </button>
                    <button type="submit" class="btn m-2 btn-sm btn-primary">
                        Save
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>