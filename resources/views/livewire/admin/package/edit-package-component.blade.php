<div class="container mt-5">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="update">
        <div class="group-2 form-grid mb-4 room-section">
            <div class="form-group">
                <label for="country_id">District</label>
                <select wire:model.live.prevent="country_id" id="country_id" class="form-control">
                    <option value="">Select District</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
                @error('country_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="city_id">Thana</label>
                <select wire:model.live="city_id" id="city_id" class="form-control">
                    <option value="">Select Thana</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
                @error('city_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="area_id">Local Area</label>
                <select wire:model.live="area_id" id="area_id" class="form-control">
                    <option value="">Select Area</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->name }}</option>
                    @endforeach
                </select>
                @error('area_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="property_id">Property</label>
                <select wire:model.live="property_id" id="property_id" class="form-control">
                    <option value="">Select Property</option>
                    @foreach ($properties as $property)
                        <option value="{{ $property->id }}">{{ $property->name }}</option>
                    @endforeach
                </select>
                @error('property_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="expiration_date">Expiration Date</label>
                <input type="date" id="expiration_date" class="form-control" wire:model="expiration_date" required>
                @error('expiration_date')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">Package Name</label>
                <input type="text" wire:model="name" id="name" class="form-control">
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" wire:model="address" id="address" class="form-control">
                @error('address')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="map_link">Map Link</label>
                <input type="text" wire:model="map_link" id="map_link" class="form-control">
                @error('map_link')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="number_of_rooms">Rooms</label>
                <input type="number" wire:model="number_of_rooms" id="number_of_rooms" class="form-control">
                @error('number_of_rooms')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label for="number_of_kitchens">Kitchens</label>
                <input type="number" wire:model="number_of_kitchens" id="number_of_kitchens" class="form-control">
                @error('number_of_kitchens')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="common_bathrooms">Common Bathrooms</label>
                <input type="number" wire:model="common_bathrooms" id="common_bathrooms" class="form-control">
                @error('common_bathrooms')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="seating">Seating</label>
                <input type="number" wire:model="seating" id="seating" class="form-control">
                @error('seating')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>


        <div>

            <div class="form-group group-2">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Rooms</h2>
                    <button type="button" class="btn btn-primary" wire:click="addRoom">
                        <i class="fas fa-plus mr-2"></i>Add Room
                    </button>
                </div>

                @foreach ($rooms as $roomIndex => $room)
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Room Details</h5>
                            <button type="button" class="btn btn-danger btn-sm"
                                wire:click="removeRoom({{ $roomIndex }})">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Room Basic Info -->
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="rooms-{{ $roomIndex }}-name">Room Name</label>
                                        <input type="text" wire:model="rooms.{{ $roomIndex }}.name"
                                            id="rooms-{{ $roomIndex }}-name" class="form-control">
                                        @error('rooms.' . $roomIndex . '.name')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="rooms-{{ $roomIndex }}-number_of_beds">Beds</label>
                                        <input type="number" wire:model="rooms.{{ $roomIndex }}.number_of_beds"
                                            id="rooms-{{ $roomIndex }}-number_of_beds" class="form-control">
                                        @error('rooms.' . $roomIndex . '.number_of_beds')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="rooms-{{ $roomIndex }}-number_of_bathrooms">Attached
                                            Bathrooms</label>
                                        <input type="number"
                                            wire:model="rooms.{{ $roomIndex }}.number_of_bathrooms"
                                            id="rooms-{{ $roomIndex }}-number_of_bathrooms" class="form-control">
                                        @error('rooms.' . $roomIndex . '.number_of_bathrooms')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing Options -->
                            <div class="mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Pricing Options</h6>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        wire:click="addPriceOption({{ $roomIndex }})">
                                        <i class="fas fa-plus mr-1"></i>Add Price
                                    </button>
                                </div>

                                @foreach ($room['prices'] as $priceIndex => $price)
                                    <div class="card mb-3 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="form-group mb-0 flex-grow-1 mr-3">
                                                    <label
                                                        for="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-type">Price
                                                        Type</label>
                                                    <select
                                                        wire:model.live="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.type"
                                                        id="rooms-{{ $roomIndex }}-prices-{{ $priceIndex }}-type"
                                                        class="form-control">
                                                        <option value="">Select Option</option>
                                                        <option value="Day">Day</option>
                                                        <option value="Week">Week</option>
                                                        <option value="Month">Month</option>
                                                    </select>
                                                </div>
                                                @if (count($room['prices']) > 1)
                                                    <button type="button" class="btn btn-danger btn-sm mt-4"
                                                        wire:click="removePriceOption({{ $roomIndex }}, {{ $priceIndex }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            @if ($price['type'])
                                                <div class="form-row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>{{ $price['type'] }} Fixed Price</label>
                                                            <input type="number" class="form-control"
                                                                wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.fixed_price">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>{{ $price['type'] }} Discount Price</label>
                                                            <input type="number" class="form-control"
                                                                wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.discount_price">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>{{ $price['type'] }} Booking Price</label>
                                                            <input type="number" class="form-control"
                                                                wire:model="rooms.{{ $roomIndex }}.prices.{{ $priceIndex }}.booking_price">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
        <div class="form-group group-2">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Package Instructions</h5>
                    <button type="button" class="btn btn-primary btn-sm" wire:click="addInstruction">
                        <i class="fas fa-plus"></i> Add Instruction
                    </button>
                </div>
                <div class="card-body">
                    @foreach ($instructions as $index => $instruction)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Instruction {{ $index + 1 }}</h6>
                                    @if (count($instructions) > 1)
                                        <button type="button" class="btn btn-danger btn-sm"
                                            wire:click="removeInstruction({{ $index }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" wire:model="instructions.{{ $index }}.title"
                                        class="form-control @error('instructions.' . $index . '.title') is-invalid @enderror"
                                        placeholder="Enter instruction title">
                                    @error('instructions.' . $index . '.title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Description</label>
                                    <textarea wire:model="instructions.{{ $index }}.description"
                                        class="form-control @error('instructions.' . $index . '.description') is-invalid @enderror" rows="3"
                                        placeholder="Enter instruction details"></textarea>
                                    @error('instructions.' . $index . '.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if (empty($instructions))
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No instructions added yet. Click the button above to add
                                instructions.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        <div class="form-group group-2">
            <h2 class="mb-4">Package Photos</h2>

            {{-- Existing Photos --}}
            <div class="row mb-4">
                @foreach ($storedPhotos as $photo)
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card h-100 position-relative">
                            <img src="{{ asset('storage/public/'.$photo['url']) }}" class="card-img-top img-fluid"
                                style="height: 200px; object-fit: cover;" alt="Package Photo">
                            <button type="button" wire:click="removeStoredPhoto({{ $photo['id'] }})"
                                class="btn btn-danger btn-sm position-absolute" style="top: 10px; right: 10px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Upload New Photos --}}
            <div class="card mb-4">
                <div class="card-body text-center p-5">
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                    <h5>Upload New Photos</h5>
                    <input type="file" wire:model="photos" multiple id="photo-upload" class="form-control">
                    @error('photos')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- New Photos Preview --}}
            @if ($photos)
                <div class="row">
                    @foreach ($photos as $photo)
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card h-100">
                                <img src="{{ $photo->temporaryUrl() }}" class="card-img-top img-fluid"
                                    style="height: 200px; object-fit: cover;" alt="Photo Preview">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
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

        <button type="submit" class="btn btn-success">Update</button>
    </form>


    <style>
        .card {
            transition: transform 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-danger {
            opacity: 0.9;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #photo-upload {
            max-width: 300px;
            margin: 0 auto;
        }

        .card {
            border: 1px solid rgba(0, 0, 0, .125);
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, .125);
        }

        .btn-sm {
            padding: .25rem .5rem;
            font-size: .875rem;
        }
    </style>
</div>
