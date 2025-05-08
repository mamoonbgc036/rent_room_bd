<div>
    <!-- Overlay -->
    <div class="overlay" wire:click="closeModal"></div>

    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="w-full max-w-lg p-6 bg-white rounded shadow-lg">
            <h2 class="text-2xl font-semibold mb-6">{{ $country_id ? 'Edit Country' : 'Create Country' }}</h2>

            <form wire:submit.prevent="store">
                <div class="mb-4">
                    <input type="text" wire:model="name" class="form-control form-control-lg border-0" placeholder="Country Name">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <input type="text" wire:model="symbol" class="form-control form-control-lg border-0" placeholder="Country Symbol">
                    @error('symbol') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <input type="text" wire:model="currency" class="form-control form-control-lg border-0" placeholder="Country Currency">
                    @error('currency') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <input type="file" wire:model="photo" class="form-control form-control-lg border-0">
                    @if ($photo)
                        @if (is_string($photo))
                            <img src="{{ asset('storage/' . $photo) }}" class="mt-2 w-24 h-24 rounded-full">
                        @else
                            <img src="{{ $photo->temporaryUrl() }}" class="mt-2 w-24 h-24 rounded-full">
                        @endif
                    @endif
                    @error('photo') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="button" wire:click="closeModal" class="btn btn-lg btn-secondary next-button mb-3 mr-2">Cancel</button>
                    <button type="submit" class="btn btn-lg btn-primary next-button mb-3">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
