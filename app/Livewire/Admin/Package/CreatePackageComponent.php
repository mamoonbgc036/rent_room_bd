<?php

namespace App\Livewire\Admin\Package;

use Illuminate\Support\Facades\DB;
use Livewire\{Component, WithFileUploads};
use Illuminate\Support\Facades\{Auth, Storage};
use App\Models\{Amenity, Area, City, Country, Package, Property, RoomPrice, Maintain, PropertyType};

class CreatePackageComponent extends Component
{
    use WithFileUploads;

    // Location and Basic Info Properties
    public $countries, $cities = [], $areas = [], $properties;
    public $country_id, $city_id, $area_id, $property_id;
    public $name, $address, $map_link, $expiration_date;

    // Property Details
    public $number_of_kitchens = 0;
    public $number_of_rooms = 0;
    public $common_bathrooms = 0;
    public $seating = 0;
    public $zones;
    public $zone_id;
    public $details;
    public $video_link;

    public $property_types;

    public $property_type_id;

    // Room Management
    public $rooms = [];

    // Amenities and Maintains
    public $maintains, $amenities;
    public $freeMaintains = [];
    public $freeAmenities = [];
    public $paidMaintains = [];
    public $paidAmenities = [];

    // Photos
    public $photos = [];
    public $storedPhotos = [];
    public $instructions = [];

    protected array $rules = [
        // Basic Information
        // 'country_id' => 'required|exists:countries,id',
        'city_id' => 'required|exists:cities,id',
        'area_id' => 'required|exists:areas,id',
        'property_id' => 'required|exists:properties,id',
        'property_type_id' => 'required',
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:500',
        'map_link' => 'nullable|url|max:1000',
        'expiration_date' => 'required|date|after:today',

        // Property Details
        'number_of_kitchens' => 'required|integer|min:0',
        'number_of_rooms' => 'required|integer|min:1',
        'common_bathrooms' => 'required|integer|min:0',
        'seating' => 'required|integer|min:0',
        'details' => 'nullable|string',
        'video_link' => 'nullable|url',

        // Room Details
        'rooms.*.name' => 'required|string|max:255',
        'rooms.*.number_of_beds' => 'required|integer|min:1',
        'rooms.*.number_of_bathrooms' => 'required|integer|min:0',
        'rooms.*.prices.*.type' => 'required|in:Day,Week,Month',
        'rooms.*.prices.*.fixed_price' => 'required|numeric|min:0',
        'rooms.*.prices.*.discount_price' => 'nullable|numeric|min:0|lt:rooms.*.prices.*.fixed_price',
        'rooms.*.prices.*.booking_price' => 'required|numeric|min:0',

        // Amenities and Maintains
        'paidMaintains.*.maintain_id' => 'required|exists:maintains,id',
        'paidMaintains.*.price' => 'required|numeric|min:0',
        'paidAmenities.*.amenity_id' => 'required|exists:amenities,id',
        'paidAmenities.*.price' => 'required|numeric|min:0',

        // Photos
        'photos.*' => 'nullable|image|max:5120', // 5MB max

        'instructions.*.title' => 'required|string|max:255',
        'instructions.*.description' => 'required|string',
        'instructions.*.order' => 'required|integer|min:0',
    ];

    protected array $messages = [
        'rooms.*.prices.*.discount_price.lt' => 'The discount price must be less than the fixed price.',
        'expiration_date.after' => 'The expiration date must be after today.',
    ];

    public function mount()
    {
        $user = Auth::user();
        $isAdmin = $user->roles->pluck('name')->contains('Super Admin');

        $this->property_types = DB::table('property_types')->select('id', 'type')->get();

        $this->zones = [];

        // Initialize data based on user role
        $this->cities = DB::table('cities')->get();
        $this->properties = $isAdmin ? Property::all() : Property::where('user_id', $user->id)->get();
        //previous
        // $this->maintains = $isAdmin ? Maintain::all() : Maintain::where('user_id', $user->id)->get();
        // mamoon
        $this->maintains = Maintain::all();
        //previous
        $this->amenities = $isAdmin ? Amenity::all() : Amenity::where('user_id', $user->id)->get();
        //mamoon
        $this->amenities = Amenity::all();

        // Initialize first room
        $this->addRoom();
        $this->addPaidMaintain();
        $this->addPaidAmenity();
        $this->addInstruction();
    }

    // Location Updates
    public function updatedAreaId($value)
    {
        $this->zones = DB::table('zones')->where('area_id', $this->area_id)->select('id', 'name')->get();
    }

    public function updatedCityId($value)
    {
        $this->areas = Area::where('district_id', $value)->get();
        $this->area_id = null;
    }

    // Room Management
    public function addRoom()
    {
        $this->rooms[] = [
            'name' => '',
            'number_of_beds' => 1,
            'number_of_bathrooms' => 0,
            'prices' => [
                ['type' => '', 'fixed_price' => 0, 'discount_price' => null, 'booking_price' => 0]
            ]
        ];
    }

    public function removeRoom($index)
    {
        if (count($this->rooms) > 1) {
            unset($this->rooms[$index]);
            $this->rooms = array_values($this->rooms);
        }
    }

    // Price Management
    public function addPriceOption($roomIndex)
    {
        if (count($this->rooms[$roomIndex]['prices']) < 3) {
            $this->rooms[$roomIndex]['prices'][] = [
                'type' => '',
                'fixed_price' => 0,
                'discount_price' => null,
                'booking_price' => 0
            ];
        }
    }

    public function updatedZoneId()
    {
        $this->properties = DB::table('properties')->where('zone_id', $this->zone_id)->get();
    }

    public function removePriceOption($roomIndex, $priceIndex)
    {
        if (count($this->rooms[$roomIndex]['prices']) > 1) {
            unset($this->rooms[$roomIndex]['prices'][$priceIndex]);
            $this->rooms[$roomIndex]['prices'] = array_values($this->rooms[$roomIndex]['prices']);
        }
    }

    // Maintains Management
    public function addPaidMaintain()
    {
        $this->paidMaintains[] = ['maintain_id' => '', 'price' => 0];
    }

    public function removePaidMaintain($index)
    {
        if (count($this->paidMaintains) > 1) {
            unset($this->paidMaintains[$index]);
            $this->paidMaintains = array_values($this->paidMaintains);
        }
    }

    // Amenities Management
    public function addPaidAmenity()
    {
        $this->paidAmenities[] = ['amenity_id' => '', 'price' => 0];
    }

    public function removePaidAmenity($index)
    {
        if (count($this->paidAmenities) > 1) {
            unset($this->paidAmenities[$index]);
            $this->paidAmenities = array_values($this->paidAmenities);
        }
    }


    public function addInstruction()
    {
        $this->instructions[] = [
            'title' => '',
            'description' => '',
            'order' => count($this->instructions)
        ];
    }


    public function removeInstruction($index)
    {
        if (count($this->instructions) > 1) {
            unset($this->instructions[$index]);
            $this->instructions = array_values($this->instructions);

            // Reorder remaining instructions
            foreach ($this->instructions as $key => $instruction) {
                $this->instructions[$key]['order'] = $key;
            }
        }
    }

    // Photo Management
    public function updatedPhotos()
    {
        $this->validate([
            'photos.*' => 'image|max:5120'
        ]);

        foreach ($this->photos as $photo) {
            $this->storedPhotos[] = $photo->store('photos-temp');
        }
        $this->reset('photos');
    }

    public function removePhoto($index)
    {
        if (isset($this->storedPhotos[$index])) {
            Storage::disk('public')->delete($this->storedPhotos[$index]);
            unset($this->storedPhotos[$index]);
            $this->storedPhotos = array_values($this->storedPhotos);
        }
    }

    // Save Package
    public function save()
    {
        $this->validate();
        try {
            $package = $this->createPackage();
            $this->saveRooms($package);
            $this->saveAmenitiesAndMaintains($package);
            $this->savePhotos($package);

            session()->flash('message', 'Package created successfully.');
            return redirect()->route('admin.packages');
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating package: ' . $e->getMessage());
        }
    }

    protected function createPackage()
    {
        $data = [
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'property_id' => $this->property_id,
            'name' => $this->name,
            'address' => $this->address,
            'map_link' => $this->map_link,
            'number_of_kitchens' => $this->number_of_kitchens,
            'number_of_rooms' => $this->number_of_rooms,
            'common_bathrooms' => $this->common_bathrooms,
            'seating' => $this->seating,
            'details' => $this->details,
            'video_link' => $this->video_link,
            'expiration_date' => $this->expiration_date,
            'status' => strtotime($this->expiration_date) <= strtotime(now()) ? 'expired' : 'active',
            'user_id' => Auth::id(),
            'property_type_id' => $this->property_type_id,
            'zone_id' => $this->zone_id
        ];

        $package = Package::create($data);

        foreach ($this->instructions as $instruction) {
            $package->instructions()->create([
                'title' => $instruction['title'],
                'description' => $instruction['description'],
                'order' => $instruction['order'],
                'user_id' => Auth::id(),
            ]);
        }

        return $package;
    }

    protected function saveRooms($package)
    {
        foreach ($this->rooms as $roomData) {
            $room = $package->rooms()->create([
                'name' => $roomData['name'],
                'number_of_beds' => $roomData['number_of_beds'],
                'number_of_bathrooms' => $roomData['number_of_bathrooms'],
                'user_id' => Auth::id(),
            ]);

            foreach ($roomData['prices'] as $priceData) {
                $room->prices()->create([
                    'type' => $priceData['type'],
                    'fixed_price' => $priceData['fixed_price'],
                    'discount_price' => $priceData['discount_price'],
                    'booking_price' => $priceData['booking_price'],
                    'user_id' => Auth::id(),
                ]);
            }
        }
    }

    protected function saveAmenitiesAndMaintains($package)
    {
        // Save free maintains and amenities
        foreach ($this->freeMaintains as $maintainId) {
            $package->maintains()->attach($maintainId, ['is_paid' => false, 'user_id' => Auth::id()]);
        }

        foreach ($this->freeAmenities as $amenityId) {
            $package->amenities()->attach($amenityId, ['is_paid' => false, 'user_id' => Auth::id()]);
        }

        // Save paid maintains and amenities
        foreach ($this->paidMaintains as $maintainData) {
            $package->maintains()->attach($maintainData['maintain_id'], [
                'is_paid' => true,
                'price' => $maintainData['price'],
                'user_id' => Auth::id(),
            ]);
        }

        foreach ($this->paidAmenities as $amenityData) {
            $package->amenities()->attach($amenityData['amenity_id'], [
                'is_paid' => true,
                'price' => $amenityData['price'],
                'user_id' => Auth::id(),
            ]);
        }
    }

    protected function savePhotos($package)
    {
        foreach ($this->storedPhotos as $photo) {
            $newFilePath = str_replace('photos-temp', 'photos', $photo);
            Storage::move($photo, $newFilePath);
            $package->photos()->create([
                'url' => $newFilePath,
                'user_id' => Auth::id(),
            ]);
        }
        $this->reset('storedPhotos');
    }

    public function render()
    {
        return view('livewire.admin.package.create-package-component');
    }
}
