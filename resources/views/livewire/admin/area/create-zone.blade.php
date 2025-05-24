<div>
    <!-- Overlay -->
    <div class="overlay" wire:click="closeModal"></div>
    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="w-full max-w-lg p-6 bg-white rounded shadow-lg">
            <h2 class="text-2xl font-semibold mb-6">{{ $zone_id ? 'Edit Local Area' : 'Create Local Area' }}</h2>
            <form wire:submit.prevent="store">
                <div class="mb-4">
                    <label for="area_id" class="block text-lg font-medium text-gray-700 mb-1">City</label>
                    <select id="area_id" wire:model.live="city_id" class="form-control border-0 shadow-none form-control-lg mb-2">
                        <option value="">Select City</option>
                        @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('area_id') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="area_id" class="block text-lg font-medium text-gray-700 mb-1">Area</label>
                    <select id="area_id" wire:model="area_id" class="form-control border-0 shadow-none form-control-lg mb-2">
                        <option value="">Select Area</option>
                        @foreach($areas as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('area_id') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <input type="text" wire:model="name" class="form-control form-control-lg border-0" placeholder="Area Name">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="button" wire:click="closeModal" class="btn btn-lg btn-secondary next-button mb-3 mr-2">Cancel</button>
                    <button type="submit" class="btn btn-lg btn-primary next-button mb-3">{{ $zone_id ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>