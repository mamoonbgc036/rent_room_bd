<?php

namespace App\Livewire\User;

use Carbon\Carbon;
use App\Models\Area;
use App\Models\City;
use App\Models\User;
use App\Models\Country;
use App\Models\Message;
use App\Models\Package;
use Livewire\Component;
use App\Models\HeroSection;
use App\Models\PropertyType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HomeComponent extends Component
{
    public $cities = [];
    public $areas = [];
    public $packages = [];

    public $selectedCountry;
    public $selectedCity = null;
    public $selectedArea = null;
    public $keyword = '';
    public $noPackagesFound;

    public $propertyTypes;

    public $accomodationType;

    public $phone;

    public $isSearchComplete = true;

    public $search = '';
    public $search_area;

    public $zones = [];
    public $zone_id;

    public $areaData;

    public $backgroundImage;
    public $titleSmall;
    public $titleBig;
    public $heroSection;
    protected $listeners = ['noPackagesFound', 'countryUpdated'];

    public function mount()
    {
        $this->selectedCountry = session('selectedCountry', 1); // Default to United Kingdom's ID
        $this->loadCities();
        $this->heroSection = HeroSection::first();

        $threshold = Carbon::now()->subHours(24);
        Message::where('created_at', '<', $threshold)->delete();
    }

    public function updatedaccomodationType()
    {
        $this->selectedPackageRefresh();
        if (!is_null($this->accomodationType)) {
            $this->packages = $this->packages->where('property_type_id', $this->accomodationType);
        }
    }

    public function updatedZoneId()
    {
        $this->updatedaccomodationType();
        if (!is_null($this->zone_id)) {
            $this->packages = $this->packages->where('zone_id', $this->zone_id);
        }
    }

    public function selectedPackageRefresh()
    {
        $splited_data = explode('/', $this->areaData);
        if ($splited_data[0] == 'di') {
            $this->packages = Package::where('city_id', $splited_data[1])->get();
        } else {
            $this->packages = Package::where('area_id', $splited_data[1])->get();
        }
    }

    public function getFirstAvailablePrice($prices)
    {
        $types = ['Day', 'Week', 'Month'];
        foreach ($types as $type) {
            foreach ($prices as $price) {
                if ($price->type === $type) {
                    return [
                        'price' => $price,
                        'type' => $type
                    ];
                }
            }
        }
        return null;
    }

    public function updatedSearch($value)
    {
        if (strlen($value) <= 0) {
            $this->search_area = [];
        } else {
            $this->search_area = DB::table('pk_search')->where('name', 'like', '%' . $value . '%')->orderBy('id', 'desc')->get();
        }
    }

    public function selectPackage($areaId)
    {
        $splited_data = explode('/', $areaId);
        $this->isSearchComplete = false;
        $this->areaData = $areaId;
        $this->propertyTypes = DB::table('property_types')->select('id', 'type')->get();
        if ($splited_data[0] == 'di') {
            $this->packages = Package::where('city_id', $splited_data[1])->get();
            $this->zones = DB::table('zones')->where('city_id', $splited_data[1])->select('id', 'name')->get();
        } else {
            $this->packages = Package::where('area_id', $splited_data[1])->get();
            $this->zones = DB::table('zones')->where('area_id', $splited_data[1])->select('id', 'name')->get();
        }
        if ($this->packages->isEmpty()) {
            $this->dispatch('noPackagesFound');
        }
        $this->search_area = [];
    }

    public function searchPackages()
    {
        $search_area = DB::table('pk_search')->where('name', 'like', '%' . $this->search . '%')->orderBy('id', 'desc')->first();
        // look for pk_search table
        if ($search_area->district_id == null) {
            $queryString = 'th/' . $search_area->id;
        } else {
            $queryString = 'di/' . $search_area->id;
        }
        $this->selectPackage($queryString);
    }

    public function getPriceIndicator($type)
    {
        switch ($type) {
            case 'Day':
                return '(P/N by Room)';
            case 'Week':
                return '(P/W by Room)';
            case 'Month':
                return '(P/M by Room)';
            default:
                return '';
        }
    }
    public function getPropertyPriceIndicator($type)
    {
        switch ($type) {
            case 'Day':
                return '(P/N by Property)';
            case 'Week':
                return '(P/W by Property)';
            case 'Month':
                return '(P/M by Property)';
            default:
                return '';
        }
    }



    public function loadCities()
    {
        // $this->cities = City::where('country_id', $this->selectedCountry)->get();
        $this->cities = City::all();
    }

    public function countryUpdated($countryId)
    {
        $this->selectedCountry = $countryId;
        $this->loadCities();
        $this->selectedCity = null;
        $this->areas = [];
        $this->packages = [];
    }



    public function updatedSelectedCity($cityId)
    {
        $this->areas = Area::where('district_id', $cityId)->get();
        $this->selectedArea = null;
        $this->packages = [];
    }

    public function updatedSelectedArea($areaId)
    {
        $this->searchPackages();
    }

    public function updatedKeyword()
    {
        $this->searchPackages();
    }


    public function noPackagesFound()
    {
        // Handle the event, for example, setting a flag
        $this->noPackagesFound = true;
    }


    public function register()
    {
        // Validation (you can customize this based on your requirements)
        $this->validate([
            'phone' => 'required|unique',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new user
        User::create([
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
        ]);

        return redirect()->to('/dashboard');
    }

    public function render()
    {
        $featuredPackages = Package::with(['country', 'city', 'area', 'rooms', 'photos'])
            ->orderBy('id', 'desc')->get();
        return view('livewire.user.home-component', [
            'featuredPackages' => $featuredPackages,
        ])->layout('layouts.guest');
    }
}
