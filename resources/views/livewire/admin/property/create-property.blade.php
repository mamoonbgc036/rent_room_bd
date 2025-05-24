<div>
    <!-- Overlay -->
    <div class="overlay" wire:click="closeModal"></div>

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-indigo-900 bg-opacity-95 backdrop-blur-sm">
        <!-- Modal Container -->
        <div class="w-full max-w-lg p-8 bg-white rounded-2xl shadow-2xl transform transition-all duration-300 scale-100">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">{{ $property_id ? 'Edit Property' : 'Create Property' }}</h2>

            <form wire:submit.prevent="store">
                <!-- District -->
                <div class="mb-5">
                    <label for="city_id" class="block text-base font-semibold text-gray-700 mb-1">District</label>
                    <select wire:model.live="city_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="" selected>Select District</option>
                        @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ $city->id == $city_id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('city_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Thana -->
                <div class="mb-5">
                    <label for="area_id" class="block text-base font-semibold text-gray-700 mb-1">Thana</label>
                    <select id="area_id" wire:model.live="area_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="" selected>Select Thana</option>
                        @if(!empty($areas))
                        @foreach($areas as $thana)
                        <option value="{{ $thana->id }}" {{ $thana->id == $area_id ? 'selected' : '' }}>{{ $thana->name }}</option>
                        @endforeach
                        @endif
                    </select>
                    @error('area_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-5">
                    <label for="zone_id" class="block text-base font-semibold text-gray-700 mb-1">Local Area</label>
                    <select id="zone_id" wire:model="zone_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="" selected>Select Local Area</option>
                        @if(!empty($zones))
                        @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" {{ $zone->id == $zone_id ? 'selected' : '' }}>{{ $zone->name }}</option>
                        @endforeach
                        @endif
                    </select>
                    @error('zone_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Property Type -->
                <div class="mb-5">
                    <label for="property_type_id" class="block text-base font-semibold text-gray-700 mb-1">Property Type</label>
                    <select id="property_type_id" wire:model="property_type_id" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Property Type</option>
                        @foreach($propertyTypes as $property_type)
                        <option value="{{ $property_type->id }}">{{ $property_type->type }}</option>
                        @endforeach
                    </select>
                    @error('property_type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Name -->
                <div class="mb-6">
                    <label for="name" class="block text-base font-semibold text-gray-700 mb-1">Name</label>
                    <input type="text" id="name" wire:model.defer="name" placeholder="Property Name"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="closeModal"
                        class="px-6 btn-sm btn-warning mx-2 py-2 text-sm font-semibold text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition duration-200">Cancel</button>
                    <button type="submit"
                        class="px-6 btn-sm btn-success mx-2 py-2 text-sm font-semibold text-gray-700 bg-indigo-600 hover:bg-indigo-700 rounded-lg transition duration-200">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>