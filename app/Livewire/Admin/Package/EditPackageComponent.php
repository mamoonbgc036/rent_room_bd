<?php
namespace App\Livewire\Admin\Package;

use App\Models\Amenity;
use App\Models\Area;
use App\Models\Maintain;
use App\Models\Package;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomPrice;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditPackageComponent extends Component
{
    use WithFileUploads;

    public $packageId;

    // Location and Basic Info Properties
    public $countries, $cities = [], $areas = [], $properties;
    public $country_id, $city_id, $area_id, $property_id;
    public $name, $address, $map_link, $expiration_date;

    // Property Details
    public $number_of_kitchens = 0;
    public $number_of_rooms    = 0;
    public $common_bathrooms   = 0;
    public $seating            = 0;
    public $details;
    public $video_link;

    // Room Management
    public $rooms = [];

    // Amenities and Maintains
    public $maintains, $amenities;
    public $freeMaintains = [];
    public $freeAmenities = [];
    public $paidMaintains = [];
    public $paidAmenities = [];

    // Photos
    public $photos       = [];
    public $storedPhotos = [];
    public $instructions = [];

    protected array $rules = [
        'city_id'                         => 'required|exists:areas,id',
        'area_id'                         => 'required|exists:zones,id',
        'property_id'                     => 'required|exists:properties,id',
        'name'                            => 'required|string|max:255',
        'address'                         => 'required|string|max:500',
        'map_link'                        => 'nullable|url|max:1000',
        'expiration_date'                 => 'required|date|after:today',
        'number_of_kitchens'              => 'required|integer|min:0',
        'number_of_rooms'                 => 'required|integer|min:1',
        'common_bathrooms'                => 'required|integer|min:0',
        'seating'                         => 'required|integer|min:0',
        'details'                         => 'nullable|string',
        'video_link'                      => 'nullable|url',
        'rooms.*.name'                    => 'required|string|max:255',
        'rooms.*.number_of_beds'          => 'required|integer|min:1',
        'rooms.*.number_of_bathrooms'     => 'required|integer|min:0',
        'rooms.*.prices.*.type'           => 'required|in:Day,Week,Month',
        'rooms.*.prices.*.fixed_price'    => 'required|numeric|min:0',
        'rooms.*.prices.*.discount_price' => 'nullable|numeric|min:0|lt:rooms.*.prices.*.fixed_price',
        'rooms.*.prices.*.booking_price'  => 'required|numeric|min:0',
        'paidMaintains.*.maintain_id'     => 'required|exists:maintains,id',
        'paidMaintains.*.price'           => 'required|numeric|min:0',
        'paidAmenities.*.amenity_id'      => 'required|exists:amenities,id',
        'paidAmenities.*.price'           => 'required|numeric|min:0',
        'photos.*'                        => 'nullable|image|max:5120',
        'instructions.*.title'            => 'required|string|max:255',
        'instructions.*.description'      => 'required|string',
        'instructions.*.order'            => 'required|integer|min:0',
    ];

    protected array $messages = [
        'rooms.*.prices.*.discount_price.lt' => 'The discount price must be less than the fixed price.',
        'expiration_date.after'              => 'The expiration date must be after today.',
    ];

    public function mount($packageId)
    {
        $this->packageId = $packageId;
        $user            = Auth::user();
        $isAdmin         = $user->roles->pluck('name')->contains('Super Admin');
        // Initialize collections
        $this->countries  = DB::table('cities')->get();
        $this->properties = $isAdmin ? Property::all() : Property::where('user_id', $user->id)->get();
        $this->maintains  = $isAdmin ? Maintain::all() : Maintain::where('user_id', $user->id)->get();
        $this->amenities  = $isAdmin ? Amenity::all() : Amenity::where('user_id', $user->id)->get();
        $this->loadPackage($packageId);
    }

    protected function loadPackage($packageId)
    {
        $package = Package::with(['rooms.prices', 'maintains', 'amenities', 'photos', 'instructions'])
            ->findOrFail($packageId);
        // Load basic info
        $this->country_id         = $package->city_id;
        $this->city_id            = $package->area_id;
        $this->area_id            = $package->zone_id;
        $this->property_id        = $package->property_id;
        $this->name               = $package->name;
        $this->address            = $package->address;
        $this->map_link           = $package->map_link;
        $this->number_of_kitchens = $package->number_of_kitchens;
        $this->number_of_rooms    = $package->number_of_rooms;
        $this->common_bathrooms   = $package->common_bathrooms;
        $this->seating            = $package->seating;
        $this->details            = $package->details;
        $this->video_link         = $package->video_link;
        $this->expiration_date    = $package->expiration_date;

        // Load cities and areas based on selected location

        $this->cities = DB::table('areas')->where('district_id', $this->country_id)->get();
        $this->areas  = Zone::where('area_id', $this->city_id)->get();

        // Load rooms and prices
        $this->rooms = $package->rooms->map(function ($room) {
            return [
                'id'                  => $room->id,
                'name'                => $room->name,
                'number_of_beds'      => $room->number_of_beds,
                'number_of_bathrooms' => $room->number_of_bathrooms,
                'prices'              => $room->prices->map(function ($price) {
                    return [
                        'id'             => $price->id,
                        'type'           => $price->type,
                        'fixed_price'    => $price->fixed_price,
                        'discount_price' => $price->discount_price,
                        'booking_price'  => $price->booking_price,
                    ];
                })->toArray(),
            ];
        })->toArray();

        // Load maintains and amenities
        $this->freeMaintains = $package->maintains->where('pivot.is_paid', false)->pluck('id')->toArray();
        $this->freeAmenities = $package->amenities->where('pivot.is_paid', false)->pluck('id')->toArray();

        $this->paidMaintains = $package->maintains->where('pivot.is_paid', true)
            ->map(function ($maintain) {
                return [
                    'maintain_id' => $maintain->id,
                    'price'       => $maintain->pivot->price,
                ];
            })->toArray();

        $this->paidAmenities = $package->amenities->where('pivot.is_paid', true)
            ->map(function ($amenity) {
                return [
                    'amenity_id' => $amenity->id,
                    'price'      => $amenity->pivot->price,
                ];
            })->toArray();

        // Load photos
        $this->storedPhotos = $package->photos->map(function ($photo) {
            return [
                'id'  => $photo->id,
                'url' => $photo->url,
            ];
        })->toArray();

        // Load instructions
        $this->instructions = $package->instructions()
            ->orderBy('order')
            ->get()
            ->map(function ($instruction) {
                return [
                    'id'          => $instruction->id,
                    'title'       => $instruction->title,
                    'description' => $instruction->description,
                    'order'       => $instruction->order,
                ];
            })->toArray();
    }

    // Location Updates
    public function updatedCountryId($value)
    {
        $this->cities  = Area::where('district_id', $value)->get();
        $this->city_id = null;
        $this->area_id = null;
    }

    public function updatedCityId($value)
    {
        $this->areas   = Zone::where('area_id', $value)->get();
        $this->area_id = null;
    }

    public function updatedAreaId($value)
    {
        $this->properties  = DB::table('properties')->where('zone_id', $value)->get();
        $this->property_id = null;
    }

    // Room Management
    public function addRoom()
    {
        $this->rooms[] = [
            'id'                  => null,
            'name'                => '',
            'number_of_beds'      => 1,
            'number_of_bathrooms' => 0,
            'prices'              => [
                ['id' => null, 'type' => '', 'fixed_price' => 0, 'discount_price' => null, 'booking_price' => 0],
            ],
        ];
    }

    public function removeRoom($index)
    {
        $roomId = $this->rooms[$index]['id'];
        if ($roomId) {
            Room::find($roomId)->delete();
        }
        unset($this->rooms[$index]);
        $this->rooms = array_values($this->rooms);
    }

    // Price Management
    public function addPriceOption($roomIndex)
    {
        if (count($this->rooms[$roomIndex]['prices']) < 3) {
            $this->rooms[$roomIndex]['prices'][] = [
                'id'             => null,
                'type'           => '',
                'fixed_price'    => 0,
                'discount_price' => null,
                'booking_price'  => 0,
            ];
        }
    }

    public function removePriceOption($roomIndex, $priceIndex)
    {
        $priceId = $this->rooms[$roomIndex]['prices'][$priceIndex]['id'] ?? null;
        if ($priceId) {
            RoomPrice::find($priceId)->delete();
        }
        unset($this->rooms[$roomIndex]['prices'][$priceIndex]);
        $this->rooms[$roomIndex]['prices'] = array_values($this->rooms[$roomIndex]['prices']);
    }

    // Maintains Management
    public function addPaidMaintain()
    {
        $this->paidMaintains[] = ['maintain_id' => '', 'price' => 0];
    }

    public function removePaidMaintain($index)
    {
        unset($this->paidMaintains[$index]);
        $this->paidMaintains = array_values($this->paidMaintains);
    }

    // Amenities Management
    public function addPaidAmenity()
    {
        $this->paidAmenities[] = ['amenity_id' => '', 'price' => 0];
    }

    public function removePaidAmenity($index)
    {
        unset($this->paidAmenities[$index]);
        $this->paidAmenities = array_values($this->paidAmenities);
    }

    public function addInstruction()
    {
        $this->instructions[] = [
            'id'          => null,
            'title'       => '',
            'description' => '',
            'order'       => count($this->instructions),
        ];
    }

    public function removeInstruction($index)
    {
        if (isset($this->instructions[$index]['id'])) {
            $package = Package::find($this->packageId);
            $package->instructions()->where('id', $this->instructions[$index]['id'])->delete();
        }

        unset($this->instructions[$index]);
        $this->instructions = array_values($this->instructions);

        foreach ($this->instructions as $key => $instruction) {
            $this->instructions[$key]['order'] = $key;
        }
    }

    // Photo Management
    public function removeStoredPhoto($photoId)
    {
        $photo = Package::find($this->packageId)->photos()->findOrFail($photoId);
        Storage::disk('public')->delete($photo->url);
        $photo->delete();

        $this->storedPhotos = array_values(array_filter($this->storedPhotos,
            fn($p) => $p['id'] !== $photoId
        ));
    }

    public function updatedPhotos()
    {
        $this->validate([
            'photos.*' => 'image|max:5120',
        ]);

        foreach ($this->photos as $photo) {
            $path = $photo->store('photos', 'public');
            Package::find($this->packageId)->photos()->create([
                'url'     => $path,
                'user_id' => Auth::id(),
            ]);
        }

        $this->loadPackage($this->packageId);
        $this->reset('photos');
    }

    public function update()
    {
        $this->validate();
        try {
            DB::beginTransaction();
            $package = Package::findOrFail($this->packageId);
            $this->updatePackage($package);
            $this->updateRooms($package);
            $this->updateAmenitiesAndMaintains($package);
            $this->updateInstructions($package);
            DB::commit();
            session()->flash('message', 'Package updated successfully.');
            return redirect()->route('admin.packages');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            session()->flash('error', 'Error updating package: ' . $e->getMessage());
        }
    }

    protected function updatePackage($package)
    {
        $package->update([
            'city_id'            => $this->country_id,
            'area_id'            => $this->city_id,
            'zone_id'            => $this->area_id,
            'property_id'        => $this->property_id,
            'name'               => $this->name,
            'address'            => $this->address,
            'map_link'           => $this->map_link,
            'number_of_kitchens' => $this->number_of_kitchens,
            'number_of_rooms'    => $this->number_of_rooms,
            'common_bathrooms'   => $this->common_bathrooms,
            'seating'            => $this->seating,
            'details'            => $this->details,
            'video_link'         => $this->video_link,
            'expiration_date'    => $this->expiration_date,
            'status'             => strtotime($this->expiration_date) <= strtotime(now()) ? 'expired' : 'active',
        ]);
    }

    protected function updateRooms($package)
    {
        $currentRoomIds = [];

        foreach ($this->rooms as $roomData) {
            $room = Room::updateOrCreate(
                ['id' => $roomData['id'] ?? null],
                [
                    'package_id'          => $package->id,
                    'name'                => $roomData['name'],
                    'number_of_beds'      => $roomData['number_of_beds'],
                    'number_of_bathrooms' => $roomData['number_of_bathrooms'],
                    'user_id'             => auth()->id(),
                ]);

            $currentRoomIds[] = $room->id;

            // Update prices
            $currentPriceIds = [];
            foreach ($roomData['prices'] as $priceData) {
                $price = RoomPrice::updateOrCreate(
                    ['id' => $priceData['id'] ?? null],
                    [
                        'room_id'        => $room->id,
                        'type'           => $priceData['type'],
                        'fixed_price'    => $priceData['fixed_price'],
                        'discount_price' => $priceData['discount_price'],
                        'booking_price'  => $priceData['booking_price'],
                        'user_id'        => auth()->id(),
                    ]
                );
                $currentPriceIds[] = $price->id;
            }

            // Delete removed prices
            $room->prices()
                ->whereNotIn('id', $currentPriceIds)
                ->delete();
        }

        // Delete removed rooms
        $package->rooms()
            ->whereNotIn('id', $currentRoomIds)
            ->delete();
    }

    protected function updateAmenitiesAndMaintains($package)
    {
        // Clear existing relationships
        $package->maintains()->detach();
        $package->amenities()->detach();

        // Reattach maintains
        foreach ($this->freeMaintains as $maintainId) {
            $package->maintains()->attach($maintainId, [
                'is_paid' => false,
                'user_id' => Auth::id(),
            ]);
        }

        foreach ($this->paidMaintains as $maintainData) {
            $package->maintains()->attach($maintainData['maintain_id'], [
                'is_paid' => true,
                'price'   => $maintainData['price'],
                'user_id' => Auth::id(),
            ]);
        }

        // Reattach amenities
        foreach ($this->freeAmenities as $amenityId) {
            $package->amenities()->attach($amenityId, [
                'is_paid' => false,
                'user_id' => Auth::id(),
            ]);
        }

        foreach ($this->paidAmenities as $amenityData) {
            $package->amenities()->attach($amenityData['amenity_id'], [
                'is_paid' => true,
                'price'   => $amenityData['price'],
                'user_id' => Auth::id(),
            ]);
        }
    }

    protected function updateInstructions($package)
    {
        $currentInstructionIds = [];

        foreach ($this->instructions as $instructionData) {
            $instruction = $package->instructions()->updateOrCreate(
                ['id' => $instructionData['id'] ?? null],
                [
                    'title'       => $instructionData['title'],
                    'description' => $instructionData['description'],
                    'order'       => $instructionData['order'],
                    'user_id'     => Auth::id(),
                ]
            );
            $currentInstructionIds[] = $instruction->id;
        }

        // Delete removed instructions
        $package->instructions()
            ->whereNotIn('id', $currentInstructionIds)
            ->delete();
    }

    public function render()
    {
        return view('livewire.admin.package.edit-package-component', [
            'countries'  => $this->countries,
            'cities'     => $this->cities,
            'areas'      => $this->areas,
            'properties' => $this->properties,
            'maintains'  => $this->maintains,
            'amenities'  => $this->amenities,
        ]);
    }
}
