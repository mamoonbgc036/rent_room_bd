<div>
    <!-- Overlay -->
    <div class="overlay" wire:click="closeModal"></div>

    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="w-full max-w-lg p-6 bg-white rounded shadow-lg">
            <h2 class="text-2xl font-semibold mb-6">{{ $property_id ? 'Edit Property' : 'Create Property' }}</h2>

            <form wire:submit.prevent="store">
                <div class="mb-4">
                    <label for="country_id" class="block text-lg font-medium text-gray-700 mb-1">Country</label>
                    <select id="country_id" wire:model="country_id" class="form-control border-0 shadow-none form-control-lg mb-2"
                            wire:change="updateCities($event.target.value)">
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                    @error('country_id') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="city_id" class="block text-lg font-medium text-gray-700 mb-1">City</label>
                    <select id="city_id" wire:model="city_id" class="form-control border-0 shadow-none form-control-lg mb-2">
                        <option value="">Select City</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('city_id') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="property_type_id" class="block text-lg font-medium text-gray-700 mb-1">Property Type</label>
                    <select id="property_type_id" wire:model="property_type_id" class="form-control border-0 shadow-none form-control-lg mb-2">
                        <option value="">Select Property Type</option>
                        @foreach($propertyTypes as $propertyType)
                            <option value="{{ $propertyType->id }}">{{ $propertyType->type }}</option>
                        @endforeach
                    </select>
                    @error('property_type_id') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="name" class="block text-lg font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" id="name" wire:model.defer="name" class="form-control form-control-lg border-0" placeholder="Name">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="button" wire:click="closeModal" class="btn btn-lg btn-secondary next-button mb-3 mr-2">Cancel</button>
                    <button type="submit" class="btn btn-lg btn-primary next-button mb-3">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
