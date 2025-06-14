<div>
    {{-- Alert Messages --}}
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Create New Package</h5>
        </div>

        <div class="card-body">
            <form wire:submit.prevent="save" class="needs-validation">
                <!-- Basic Details -->
                <div class="row g-3 mb-4">
                    <div class=" col-md-3 mb-2">
                        <label class="form-label required">District</label>
                        <select wire:model.live="city_id" class="form-select select2 @error('city_id') is-invalid @enderror">
                            <option value="">Select District</option>
                            @foreach ($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('city_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class=" col-md-3 mb-2">
                        <label class="form-label required">Thana</label>
                        <select wire:model.live="area_id" class="form-select @error('area_id') is-invalid @enderror">
                            <option value="">Select Area</option>
                            @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                        @error('area_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class=" col-md-3 mb-2">
                        <label class="form-label required">Local Area</label>
                        <select wire:model.live="zone_id" class="form-select select2 @error('zone_id') is-invalid @enderror">
                            <option value="">Select Area</option>
                            @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                            @endforeach
                        </select>
                        @error('zone_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class=" col-md-3 mb-2">
                        <label class="form-label required">Property</label>
                        <select wire:model="property_id"
                            class="form-select @error('property_id') is-invalid @enderror">
                            <option value="">Select Property</option>
                            @foreach ($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                            @endforeach
                        </select>
                        @error('property_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class=" col-md-3 mb-2">
                        <label class="form-label required">Property Type</label>
                        <select wire:model="property_type_id"
                            class="form-select @error('property_type_id') is-invalid @enderror">
                            <option value="">Select Property Type</option>
                            @foreach ($property_types as $propertyType)
                            <option value="{{ $propertyType->id }}">{{ $propertyType->type }}</option>
                            @endforeach
                        </select>
                        @error('property_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class=" col-md-6 mb-2">
                        <label class="form-label required">Package Name</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter package name">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class=" col-md-6 mb-2">
                        <label class="form-label required">Expiration Date</label>
                        <input type="date" wire:model="expiration_date"
                            class="form-control @error('expiration_date') is-invalid @enderror">
                        @error('expiration_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label required">Address</label>
                        <input type="text" wire:model="address"
                            class="form-control @error('address') is-invalid @enderror"
                            placeholder="Enter full address">
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Map Link</label>
                        <input type="url" wire:model="map_link"
                            class="form-control @error('map_link') is-invalid @enderror" placeholder="https://...">
                        @error('map_link')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Property Details -->
                <div class="row g-3 mb-4">
                    <div class=" col-md-3 mb-2">
                        <label class="form-label required">Number of Rooms</label>
                        <input type="number" wire:model="number_of_rooms" min="1"
                            class="form-control @error('number_of_rooms') is-invalid @enderror">
                        @error('number_of_rooms')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class=" col-md-3 mb-2">
                        <label class="form-label required">Number of Kitchens</label>
                        <input type="number" wire:model="number_of_kitchens" min="0"
                            class="form-control @error('number_of_kitchens') is-invalid @enderror">
                        @error('number_of_kitchens')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class=" col-md-3 mb-2">
                        <label class="form-label required">Common Bathrooms</label>
                        <input type="number" wire:model="common_bathrooms" min="0"
                            class="form-control @error('common_bathrooms') is-invalid @enderror">
                        @error('common_bathrooms')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class=" col-md-3 mb-2">
                        <label class="form-label required">Seating Capacity</label>
                        <input type="number" wire:model="seating" min="0"
                            class="form-control @error('seating') is-invalid @enderror">
                        @error('seating')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Room Management -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Room Details</h6>
                        <button type="button" class="btn btn-primary btn-sm" wire:click="addRoom">
                            <i class="fas fa-plus mr-1"></i> Add Room
                        </button>
                    </div>
                    @foreach ($rooms as $roomIndex => $room)
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Room {{ $roomIndex + 1 }}</h6>
                                @if (count($rooms) > 1)
                                <button type="button" class="btn btn-danger btn-sm"
                                    wire:click="removeRoom({{ $roomIndex }})">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Room Basic Info -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label required">Room Name</label>
                                    <input type="text" wire:model="rooms.{{ $roomIndex }}.name"
                                        class="form-control @error('rooms.' . $roomIndex . '.name') is-invalid @enderror">
                                    @error('rooms.' . $roomIndex . '.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label required">Number of Beds</label>
                                    <input type="number" wire:model="rooms.{{ $roomIndex }}.number_of_beds"
                                        min="1"
                                        class="form-control @error('rooms.' . $roomIndex . '.number_of_beds') is-invalid @enderror">
                                    @error('rooms.' . $roomIndex . '.number_of_beds')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label required">Attached Bathrooms</label>
                                    <input type="number"
                                        wire:model="rooms.{{ $roomIndex }}.number_of_bathrooms" min="0"
                                        class="form-control @error('rooms.' . $roomIndex . '.number_of_bathrooms') is-invalid @enderror">
                                    @error('rooms.' . $roomIndex . '.number_of_bathrooms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Room Pricing -->
                            <div class="pricing-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Pricing Options</h6>
                                    @if (count($room['prices']) < 3)
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                        wire:click="addPriceOption({{ $roomIndex }})">
                                        <i class="fas fa-plus mr-1"></i> Add Price
                                        </button>
                                        @endif
                                </div>

                                @foreach ($room['prices'] as $priceIndex => $price)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class=" col-md-3 mb-2">
                                                <label class="form-label required">Price Type</label>
                                                <select
                                                    wire:model.live="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.type"
                                                    class="form-select @error('rooms.' . $roomIndex . '.prices.' . $priceIndex . '.type') is-invalid @enderror">
                                                    <option value="">Select Type</option>
                                                    <option value="Day">Day</option>
                                                    <option value="Week">Week</option>
                                                    <option value="Month">Month</option>
                                                </select>
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                '.type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            @if ($price['type'])
                                            <div class=" col-md-3 mb-2">
                                                <label class="form-label required">{{ $price['type'] }}
                                                    Fixed Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">৳</span>
                                                    <input type="number"
                                                        wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.fixed_price"
                                                        step="0.01" min="0"
                                                        class="form-control @error('rooms.' . $roomIndex . '.prices.' . $priceIndex . '.fixed_price') is-invalid @enderror">
                                                </div>
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                '.fixed_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class=" col-md-3 mb-2">
                                                <label class="form-label">{{ $price['type'] }} Discount
                                                    Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">৳</span>
                                                    <input type="number"
                                                        wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.discount_price"
                                                        step="0.01" min="0"
                                                        class="form-control @error('rooms.' . $roomIndex . '.prices.' . $priceIndex . '.discount_price') is-invalid @enderror">
                                                </div>
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                '.discount_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class=" col-md-3 mb-2">
                                                <label class="form-label required">{{ $price['type'] }}
                                                    Booking Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">৳</span>
                                                    <input type="number"
                                                        wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.booking_price"
                                                        step="0.01" min="0"
                                                        class="form-control @error('rooms.' . $roomIndex . '.prices.' . $priceIndex . '.booking_price') is-invalid @enderror">
                                                </div>
                                                @error('rooms.' . $roomIndex . '.prices.' . $priceIndex .
                                                '.booking_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @endif
                                        </div>

                                        @if (count($room['prices']) > 1)
                                        <button type="button" class="btn btn-outline-danger btn-sm mt-3"
                                            wire:click="removePriceOption({{ $roomIndex }}, {{ $priceIndex }})">
                                            <i class="fas fa-times mr-1"></i> Remove Price Option
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mb-4">
                    <div class="card">
                        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Package Instructions</h6>
                            <button type="button" class="btn btn-primary btn-sm" wire:click="addInstruction">
                                <i class="fas fa-plus"></i> Add Instruction
                            </button>
                        </div>
                        <div class="card-body">
                            @foreach ($instructions as $index => $instruction)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">Instruction {{ $index + 1 }}</h6>
                                                @if (count($instructions) > 1)
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    wire:click="removeInstruction({{ $index }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label required">Title</label>
                                            <input type="text"
                                                wire:model="instructions.{{ $index }}.title"
                                                class="form-control @error('instructions.' . $index . '.title') is-invalid @enderror"
                                                placeholder="Enter instruction title">
                                            @error('instructions.' . $index . '.title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label required">Description</label>
                                            <textarea wire:model="instructions.{{ $index }}.description"
                                                class="form-control @error('instructions.' . $index . '.description') is-invalid @enderror" rows="3"
                                                placeholder="Enter instruction details"></textarea>
                                            @error('instructions.' . $index . '.description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Services Section -->
                <div class="row mb-4">
                    <!-- Free Services -->
                    <div class=" col-md-6 mb-2 mb-4 mb-md-0">
                        <div class="card h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">Free Services</h6>
                            </div>
                            <div class="card-body">
                                <!-- Free Maintains -->
                                <div class="mb-4">
                                    <label class="form-label d-block">Free Maintains</label>
                                    <div class="row g-2">
                                        @foreach ($maintains as $maintain)
                                        <div class=" col-md-6 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="freeMaintains"
                                                    value="{{ $maintain->id }}" id="maintain{{ $maintain->id }}"
                                                    class="form-check-input">
                                                <label for="maintain{{ $maintain->id }}"
                                                    class="form-check-label">
                                                    {{ $maintain->name }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Free Amenities -->
                                <div>
                                    <label class="form-label d-block">Free Amenities</label>
                                    <div class="row g-2">
                                        @foreach ($amenities as $amenity)
                                        <div class=" col-md-6 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="freeAmenities"
                                                    value="{{ $amenity->id }}" id="amenity{{ $amenity->id }}"
                                                    class="form-check-input">
                                                <label for="amenity{{ $amenity->id }}" class="form-check-label">
                                                    {{ $amenity->name }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Paid Services -->
                    <div class=" col-md-6 mb-2">
                        <div class="card h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">Paid Services</h6>
                            </div>
                            <div class="card-body">
                                <!-- Paid Maintains -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0">Paid Maintains</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            wire:click="addPaidMaintain">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>

                                    @foreach ($paidMaintains as $index => $maintain)
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-md-7">
                                                    <select
                                                        wire:model="paidMaintains.{{ $index }}.maintain_id"
                                                        class="form-select @error('paidMaintains.' . $index . '.maintain_id') is-invalid @enderror">
                                                        <option value="">Select Maintain</option>
                                                        @foreach ($maintains as $maintain)
                                                        <option value="{{ $maintain->id }}">
                                                            {{ $maintain->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('paidMaintains.' . $index . '.maintain_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="input-group">
                                                        <span class="input-group-text">৳</span>
                                                        <input type="number"
                                                            wire:model="paidMaintains.{{ $index }}.price"
                                                            class="form-control @error('paidMaintains.' . $index . '.price') is-invalid @enderror"
                                                            min="0" step="0.01">
                                                    </div>
                                                    @error('paidMaintains.' . $index . '.price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            @if (count($paidMaintains) > 1)
                                            <button type="button" class="btn btn-outline-danger btn-sm mt-2"
                                                wire:click="removePaidMaintain({{ $index }})">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0">Paid Aminities</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            wire:click="addPaidMaintain">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>

                                    @foreach ($paidAmenities as $index => $amenity)
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-md-7">
                                                    <select
                                                        wire:model="paidAmenities.{{ $index }}.amenity_id"
                                                        class="form-select @error('paidAmenities.' . $index . '.amenity_id') is-invalid @enderror">
                                                        <option value="">Select Maintain</option>
                                                        @foreach ($amenities as $amenity)
                                                        <option value="{{ $amenity->id }}">
                                                            {{ $amenity->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    @error('paidAmenities.' . $index . '.amenity_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="input-group">
                                                        <span class="input-group-text">৳</span>
                                                        <input type="number"
                                                            wire:model="paidAmenities.{{ $index }}.price"
                                                            class="form-control @error('paidAmenities.' . $index . '.price') is-invalid @enderror"
                                                            min="0" step="0.01">
                                                    </div>
                                                    @error('paidAmenities.' . $index . '.price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            @if (count($paidAmenities) > 1)
                                            <button type="button" class="btn btn-outline-danger btn-sm mt-2"
                                                wire:click="removePaidMaintain({{ $index }})">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4">
                        <div class="form-group">
                            <h2 class="mb-3">Photos</h2>

                            <!-- Image Upload Box -->
                            <div class="mb-3">
                                <input type="file" wire:model="photos" multiple class="form-control">
                                @error('photos')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Display Image Previews -->
                            <div class="row">
                                @if ($storedPhotos)
                                @foreach ($storedPhotos as $index => $photo)
                                <div class=" col-md-3 mb-2 mb-4">
                                    <div class="position-relative border p-2 rounded">
                                        <!-- Image Preview -->
                                        <img src="{{ asset('storage/'. $photo) }}" alt="Photo Preview"
                                            class="img-fluid rounded"
                                            style="height: 180px; width: 100%; object-fit: cover;">

                                        <!-- Remove Button -->
                                        <button type="button"
                                            class="btn-close position-absolute top-0 end-0 m-2 bg-danger"
                                            aria-label="Close"
                                            wire:click="removePhoto({{ $index }})"></button>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>



                        <div class="form-group group-2">
                            <label for="video_link">Video Link</label>
                            <input type="text" id="video_link" wire:model="video_link" class="form-control">
                            @error('video_link')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group group-2">
                            <div class="form-group">
                                <label for="details">Package Details</label>
                                <textarea wire:model="details" id="details" class="form-control"></textarea>
                                @error('details')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </div>
            </form>


        </div>
    </div>


    <style>
        /* Style for the label */
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        /* Basic styling for the select dropdown */
        select {
            width: 100%;
            padding: 6px;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            color: #333;
            cursor: pointer;
            appearance: none;
            /* Removes the default arrow for custom styling */
            background-image: url("data:image/svg+xml;utf8,<svg fill='%23333' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'><path d='M7 7l3-3 3 3M7 13l3 3 3-3'/></svg>");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 15px;
            transition: border-color 0.3s ease;
        }

        /* Hover and focus states */
        select:hover {
            border-color: #888;
        }

        select:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* Option styling */
        option {
            padding: 10px;
            font-size: 14px;
        }

        /* Selected option highlight */
        option:checked {
            background-color: #007BFF;
            color: #fff;
        }
    </style>
</div>