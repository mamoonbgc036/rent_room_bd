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

    public $search = '';
    public $search_area;

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
        $this->packages = $this->packages->where('property_type_id', $this->accomodationType);
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
        if (strlen($value) <= 1) {
            $this->search_area = [];
        } else {
            $this->search_area = DB::table('pk_search')->where('name', 'like', '%' . $value . '%')->orderBy('id', 'desc')->get();
        }
    }

    public function selectPackage($areaId)
    {
        $splited_data = explode('/', $areaId);
        $this->areaData = $areaId;
        $this->propertyTypes = DB::table('property_types')->select('id', 'type')->get();
        if ($splited_data[0] == 'di') {
            $this->packages = Package::where('city_id', $splited_data[1])->get();
        } else {
            $this->packages = Package::where('area_id', $splited_data[1])->get();
        }

        if ($this->packages->isEmpty()) {
            $this->dispatch('noPackagesFound');
        }
        $this->search_area = [];
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

    public function searchPackages()
    {
        $query = Package::query();

        if ($this->selectedCountry) {
            $query->where('country_id', $this->selectedCountry);
        }
        if ($this->selectedCity) {
            $query->where('city_id', $this->selectedCity);
        }
        if ($this->selectedArea) {
            $query->where('area_id', $this->selectedArea);
        }
        // if ($this->keyword) {
        //     $query->where(function ($q) {
        //         $q->where('name', 'like', '%' . $this->keyword . '%')
        //             ->orWhere('address', 'like', '%' . $this->keyword . '%');
        //     });
        // }

        $this->packages = $query->get();

        if ($this->packages->isEmpty()) {
            $this->dispatch('noPackagesFound');
        }
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
            ->take(8)
            ->get();
        return view('livewire.user.home-component', [
            'featuredPackages' => $featuredPackages,
        ])->layout('layouts.guest');
    }
}
